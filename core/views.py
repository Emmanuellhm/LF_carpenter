from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login, logout, authenticate
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.db import transaction
from django.contrib.auth.tokens import default_token_generator
from django.utils.http import urlsafe_base64_decode
from django.utils.encoding import force_str
from django.views.decorators.http import require_http_methods
from django.views.decorators.cache import never_cache

from .models import User
from .forms import (
    UserRegistroForm, CarpinteroRegistroForm, LoginForm,
    PasswordRecoveryForm, SetNewPasswordForm, ContactForm
)
from .email_utils import email_service
from carpinteros.models import Carpintero


def home(request):
    """Página de inicio"""
    carpinteros = Carpintero.objects.filter(approved=True, is_verified=True)[:4]
    return render(request, 'core/home.html', {'carpinteros': carpinteros})


def login_view(request):
    """Vista de inicio de sesión"""
    if request.user.is_authenticated:
        return redirect('home')

    if request.method == 'POST':
        form = LoginForm(request.POST)
        if form.is_valid():
            email = form.cleaned_data['email']
            password = form.cleaned_data['password']
            remember_me = form.cleaned_data.get('remember_me', False)

            user = authenticate(request, username=email, password=password)

            if user is not None and user.is_active:
                login(request, user)

                # Configurar sesión persistente si remember_me
                if not remember_me:
                    request.session.set_expiry(0)
                else:
                    request.session.set_expiry(1209600)  # 2 semanas

                messages.success(request, f'¡Bienvenido, {user.full_name}!')

                # Redirigir según rol
                if user.role == 'carpenter':
                    return redirect('carpinteros:panel')
                elif user.role == 'admin':
                    return redirect('admin:index')
                else:
                    return redirect('usuarios:panel')
            else:
                messages.error(request, 'Credenciales inválidas o usuario inactivo.')
    else:
        form = LoginForm()

    return render(request, 'core/login.html', {'form': form})


def logout_view(request):
    """Cerrar sesión"""
    logout(request)
    messages.info(request, 'Has cerrado sesión correctamente.')
    return redirect('home')


@transaction.atomic
def registro_usuario(request):
    """Registro de usuarios normales (clientes)"""
    if request.user.is_authenticated:
        return redirect('home')

    if request.method == 'POST':
        form = UserRegistroForm(request.POST)
        if form.is_valid():
            user = User.objects.create_user(
                username=form.cleaned_data['email'],
                email=form.cleaned_data['email'],
                password=form.cleaned_data['password'],
                full_name=form.cleaned_data['full_name'],
                phone=form.cleaned_data.get('phone', ''),
                city=form.cleaned_data.get('city', ''),
                role='user'
            )
            
            # Desactivar usuario hasta que confirme email
            user.is_active = False
            user.save()

            # Enviar email de confirmación en lugar de bienvenida
            email_service.send_confirmation_email(user, request)

            messages.success(
                request,
                '¡Registro casi completo! Por favor revisa tu correo electrónico para confirmar tu cuenta y poder iniciar sesión.'
            )
            return redirect('login')
    else:
        form = UserRegistroForm()

    return render(request, 'core/registro_usuario.html', {'form': form})


@transaction.atomic
def registro_carpintero(request):
    """Registro de carpinteros (requiere aprobación)"""
    if request.user.is_authenticated:
        return redirect('home')

    if request.method == 'POST':
        form = CarpinteroRegistroForm(request.POST, request.FILES)
        if form.is_valid():
            user = User.objects.create_user(
                username=form.cleaned_data['email'],
                email=form.cleaned_data['email'],
                password=form.cleaned_data['password'],
                full_name=form.cleaned_data['nombre'],
                phone=form.cleaned_data['telefono'],
                city=form.cleaned_data['ciudad'],
                role='carpenter'
            )
            
            # Desactivar usuario hasta que confirme email
            user.is_active = False
            user.save()

            # Crear perfil de carpintero (pendiente de aprobación)
            Carpintero.objects.create(
                user=user,
                specialties=form.cleaned_data['especialidad'],
                experience_years=form.cleaned_data['experiencia'],
                description=form.cleaned_data.get('descripcion', ''),
                portfolio_url=form.cleaned_data.get('portafolio', ''),
                hoja_vida=request.FILES.get('hoja_vida')
            )

            # Enviar email de confirmación
            email_service.send_confirmation_email(user, request)

            messages.success(
                request,
                '¡Solicitud enviada! Por favor revisa tu correo electrónico para confirmar tu cuenta. '
                'Una vez confirmada, tu perfil será revisado por un administrador.'
            )
            return redirect('registro_enviado')
    else:
        form = CarpinteroRegistroForm()

    return render(request, 'core/registro_carpintero.html', {'form': form})


def registro_enviado(request):
    """Página de confirmación de registro enviado"""
    return render(request, 'core/registro_enviado.html')


def recuperar_contrasena(request):
    """Recuperación de contraseña - Solicitar email"""
    if request.user.is_authenticated:
        return redirect('home')

    if request.method == 'POST':
        form = PasswordRecoveryForm(request.POST)
        if form.is_valid():
            email = form.cleaned_data['email']
            try:
                user = User.objects.get(email=email, is_active=True)
                # Enviar email con token
                email_service.send_password_reset_email(user, request)
                messages.success(
                    request,
                    'Se ha enviado un correo con instrucciones para restablecer tu contraseña.'
                )
                return redirect('login')
            except User.DoesNotExist:
                # No revelar si el email existe o no
                messages.success(
                    request,
                    'Si el correo existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.'
                )
                return redirect('login')
    else:
        form = PasswordRecoveryForm()

    return render(request, 'core/recuperar_contrasena.html', {'form': form})


@never_cache
def restablecer_contrasena(request, uidb64, token):
    """Vista para restablecer la contraseña con token"""
    if request.user.is_authenticated:
        return redirect('home')

    try:
        uid = force_str(urlsafe_base64_decode(uidb64))
        user = User.objects.get(pk=uid)
    except (TypeError, ValueError, OverflowError, User.DoesNotExist):
        user = None

    if user is not None and default_token_generator.check_token(user, token):
        if request.method == 'POST':
            form = SetNewPasswordForm(user, request.POST)
            if form.is_valid():
                form.save()
                messages.success(
                    request,
                    '¡Contraseña actualizada! Ya puedes iniciar sesión con tu nueva contraseña.'
                )
                return redirect('login')
        else:
            form = SetNewPasswordForm(user)
        return render(request, 'core/restablecer_contrasena.html', {
            'form': form,
            'validlink': True
        })
    else:
        return render(request, 'core/restablecer_contrasena.html', {
            'validlink': False
        })


@never_cache
def confirmar_email(request, uidb64, token):
    """Confirmar cuenta de email"""
    try:
        uid = force_str(urlsafe_base64_decode(uidb64))
        user = User.objects.get(pk=uid)
    except (TypeError, ValueError, OverflowError, User.DoesNotExist):
        user = None

    if user is not None and default_token_generator.check_token(user, token):
        # Activar usuario si no está activo
        if not user.is_active:
            user.is_active = True
            user.save()

        messages.success(
            request,
            '¡Cuenta confirmada! Ya puedes iniciar sesión.'
        )
        return redirect('login')
    else:
        messages.error(
            request,
            'El enlace de confirmación es inválido o ha expirado.'
        )
        return redirect('home')


def contactanos(request):
    """Página de contacto"""
    if request.method == 'POST':
        form = ContactForm(request.POST)
        if form.is_valid():
            nombre = form.cleaned_data['nombre']
            correo = form.cleaned_data['correo']
            mensaje = form.cleaned_data['mensaje']

            # Enviar email de contacto
            email_service.send_contact_form_email(nombre, correo, mensaje)

            messages.success(
                request,
                '¡Mensaje enviado correctamente! Te contactaremos pronto.'
            )
            return redirect('contactanos')
    else:
        form = ContactForm()

    return render(request, 'core/contactanos.html', {'form': form})


@login_required
def admin_dashboard(request):
    """Dashboard personalizado para administradores"""
    if request.user.role != 'admin':
        messages.error(request, 'No tienes permisos para acceder al dashboard de administración.')
        return redirect('home')
        
    from carpinteros.models import Portafolio, Comentario
    from proyectos.models import SolicitudProyecto
    from django.db.models import Count
    
    from django.core.paginator import Paginator
    
    # KPIs Básicos
    total_usuarios = User.objects.count()
    clientes = User.objects.filter(role='user').count()
    carpinteros = User.objects.filter(role='carpenter').count()
    
    carpinteros_pendientes_qs = Carpintero.objects.filter(approved=False).select_related('user').order_by('-created_at')
    total_pendientes = carpinteros_pendientes_qs.count()
    
    # Paginación para la tabla de pendientes (5 por página)
    paginator_pendientes = Paginator(carpinteros_pendientes_qs, 5)
    page_pendientes = request.GET.get('page_pendientes')
    carpinteros_pendientes = paginator_pendientes.get_page(page_pendientes)
    
    # Carpinteros activos (aprobados)
    carpinteros_activos_qs = Carpintero.objects.filter(approved=True).select_related('user').order_by('-created_at')
    
    # Paginación para la tabla de activos (5 por página)
    paginator_activos = Paginator(carpinteros_activos_qs, 5)
    page_activos = request.GET.get('page_activos')
    carpinteros_activos = paginator_activos.get_page(page_activos)
    
    total_proyectos = Portafolio.objects.count()
    
    # Solicitudes
    solicitudes_pendientes = SolicitudProyecto.objects.filter(status='pending').count()
    solicitudes_completadas = SolicitudProyecto.objects.filter(status='completed').count()
    
    # Acciones por POST
    if request.method == 'POST':
        action = request.POST.get('action')
        carpintero_id = request.POST.get('carpintero_id')
        
        if action == 'approve_carpenter' and carpintero_id:
            c = get_object_or_404(Carpintero, id=carpintero_id)
            c.approved = True
            c.save()
            messages.success(request, f'Carpintero {c.user.full_name} aprobado correctamente.')
            return redirect('admin_dashboard')
            
        elif action == 'reject_carpenter' and carpintero_id:
            c = get_object_or_404(Carpintero, id=carpintero_id)
            nombre = c.user.full_name
            u = c.user
            c.delete()
            u.delete()  # Se elimina el usuario completo ya que su registro fue rechazado
            messages.success(request, f'La solicitud de {nombre} ha sido rechazada.')
            return redirect('admin_dashboard')
            
        elif action == 'toggle_block_carpenter' and carpintero_id:
            c = get_object_or_404(Carpintero, id=carpintero_id)
            u = c.user
            u.is_active = not u.is_active
            u.save()
            estado = "reactivado" if u.is_active else "bloqueado"
            messages.success(request, f'El perfil de {u.full_name} ha sido {estado}.')
            return redirect('admin_dashboard')
            
    context = {
        'total_usuarios': total_usuarios,
        'clientes': clientes,
        'carpinteros_count': carpinteros,
        'carpinteros_pendientes': carpinteros_pendientes,
        'total_pendientes': total_pendientes,
        'carpinteros_activos': carpinteros_activos,
        'total_activos': carpinteros_activos_qs.count(),
        'total_proyectos': total_proyectos,
        'solicitudes_pendientes': solicitudes_pendientes,
        'solicitudes_completadas': solicitudes_completadas,
    }
    
    return render(request, 'core/admin_dashboard.html', context)


def sobre_nosotros(request):
    """Página Sobre Nosotros"""
    return render(request, 'core/sobre_nosotros.html')


def faq(request):
    """Página de Preguntas Frecuentes"""
    return render(request, 'core/faq.html')


def terminos(request):
    """Página de Términos y Condiciones"""
    return render(request, 'core/terminos.html')

def completar_registro_social(request):
    """
    Vista a la que redirige el pipeline de social auth cuando un usuario nuevo
    se registra con Google. Pide los datos faltantes y reanuda el pipeline.
    """
    partial = request.session.get('partial_pipeline')
    if not partial:
        return redirect('login')

    if request.method == 'POST':
        role = request.POST.get('role')
        phone = request.POST.get('phone', '')
        city = request.POST.get('city', '')

        if role in ['user', 'carpenter']:
            request.session['saved_role'] = role
            request.session['saved_phone'] = phone
            request.session['saved_city'] = city
            backend = partial.get('backend')
            return redirect('social:complete', backend=backend)
        else:
            messages.error(request, 'Debes seleccionar un rol válido (Cliente o Carpintero).')

    return render(request, 'core/completar_social.html')


def explorar_carpinteros(request):
    """Catálogo público de carpinteros aprobados con Full-Text Search — RF10"""
    from carpinteros.models import Carpintero
    from django.db.models import Avg, Count, Q, Value as V
    from django.contrib.postgres.search import (
        SearchVector, SearchQuery, SearchRank, TrigramSimilarity
    )

    q = request.GET.get('q', '').strip()
    ciudad_filtro = request.GET.get('ciudad', '').strip()
    especialidad_filtro = request.GET.get('especialidad', '').strip()

    carpinteros = Carpintero.objects.filter(
        approved=True
    ).select_related('user').annotate(
        avg_rating=Avg('reviews__rating'),
        total_reviews=Count('reviews'),
        total_proyectos=Count('portafolio'),
    )

    if q:
        # Construir vector con pesos: nombre (A=mayor relevancia), especialidades (B), ciudad (C)
        search_vector = (
            SearchVector('user__full_name', weight='A') +
            SearchVector('specialties', weight='B') +
            SearchVector('description', weight='C') +
            SearchVector('user__city', weight='C')
        )
        search_query = SearchQuery(q, config='spanish')

        # Combinar FTS con TrigramSimilarity como fallback para tolerancia a errores
        carpinteros = carpinteros.annotate(
            search=search_vector,
            rank=SearchRank(search_vector, search_query),
            similarity=TrigramSimilarity('user__full_name', q),
        ).filter(
            Q(search=search_query) | Q(similarity__gt=0.2)
        ).order_by('-rank', '-similarity', '-avg_rating')
    else:
        carpinteros = carpinteros.order_by('-avg_rating', '-created_at')

    if ciudad_filtro:
        carpinteros = carpinteros.filter(user__city__icontains=ciudad_filtro)
    if especialidad_filtro:
        carpinteros = carpinteros.filter(specialties__icontains=especialidad_filtro)

    # Ciudades únicas para el filtro lateral
    ciudades = Carpintero.objects.filter(approved=True).values_list(
        'user__city', flat=True
    ).exclude(user__city='').distinct().order_by('user__city')

    context = {
        'carpinteros': carpinteros,
        'total': carpinteros.count(),
        'q': q,
        'ciudad_filtro': ciudad_filtro,
        'especialidad_filtro': especialidad_filtro,
        'ciudades': ciudades,
    }
    return render(request, 'core/explorar_carpinteros.html', context)


def perfil_publico_carpintero(request, carpintero_id):
    """Perfil público detallado de un carpintero — RF08.1 / RF10"""
    from carpinteros.models import Carpintero, Review
    from django.db.models import Avg, Count
    from proyectos.models import SolicitudProyecto

    carpintero = get_object_or_404(Carpintero, id=carpintero_id, approved=True)

    portafolio = carpintero.portafolio.all().order_by('-created_at')
    reviews = carpintero.reviews.select_related('user').order_by('-created_at')[:10]
    rating_data = carpintero.reviews.aggregate(avg=Avg('rating'), total=Count('id'))
    rating_avg = rating_data['avg'] or 0
    total_reviews = rating_data['total']

    # Manejo de solicitud de contacto POST
    if request.method == 'POST' and request.user.is_authenticated:
        from proyectos.models import SolicitudProyecto
        titulo = request.POST.get('titulo', '').strip()
        descripcion = request.POST.get('descripcion', '').strip()
        presupuesto = request.POST.get('presupuesto') or None
        if titulo and descripcion:
            SolicitudProyecto.objects.create(
                user=request.user,
                carpenter=carpintero,
                title=titulo,
                description=descripcion,
                budget=presupuesto,
                contact_info=request.user.phone or request.user.email,
                project_type='custom',
            )
            messages.success(request, f'¡Solicitud enviada a {carpintero.user.full_name}! Te contactará pronto.')
            return redirect('perfil_publico_carpintero', carpintero_id=carpintero_id)
        else:
            messages.error(request, 'Por favor completa el título y descripción de tu proyecto.')

    context = {
        'carpintero': carpintero,
        'portafolio': portafolio,
        'reviews': reviews,
        'rating_avg': round(rating_avg, 1),
        'total_reviews': total_reviews,
        'rating_range': range(1, 6),
        'can_contact': request.user.is_authenticated and request.user.role == 'user',
    }
    return render(request, 'core/perfil_publico_carpintero.html', context)

@login_required
def marcar_notificacion_leida(request, notif_id):
    import json
    from django.http import JsonResponse
    from .models import Notification
    from django.shortcuts import get_object_or_404

    if request.method == 'POST':
        notif = get_object_or_404(Notification, id=notif_id, user=request.user)
        notif.is_read = True
        notif.save()
        return JsonResponse({'success': True})
    return JsonResponse({'success': False}, status=400)