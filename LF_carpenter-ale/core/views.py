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
from contrataciones.models import FailedLogin, Notificacion, ActivityLog
from django.utils import timezone
from datetime import timedelta


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

            now = timezone.now()
            five_mins_ago = now - timedelta(minutes=5)
            recent_fails = FailedLogin.objects.filter(email_attempted=email, attempt_time__gte=five_mins_ago).count()
            
            if recent_fails >= 10:
                messages.error(request, 'Cuenta bloqueada temporalmente por demasiados intentos fallidos. Intenta en 5 minutos.')
                return render(request, 'core/login.html', {'form': form})

            user = authenticate(request, username=email, password=password)

            if user is not None and user.is_active:
                login(request, user)
                
                # Clear failed logins on success
                FailedLogin.objects.filter(email_attempted=email).delete()

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
                    return redirect('admin_dashboard')
                else:
                    return redirect('usuarios:panel')
            else:
                FailedLogin.objects.create(
                    email_attempted=email,
                    ip_address=request.META.get('REMOTE_ADDR'),
                    fail_reason='Invalid credentials or inactive user'
                )
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
        return redirect('login')
        
    from carpinteros.models import Portafolio, Comentario
    from proyectos.models import SolicitudProyecto
    from django.db.models import Count
    
    from django.core.paginator import Paginator
    
    # KPIs Básicos
    total_usuarios = User.objects.count()
    clientes = User.objects.filter(role='user').count()
    carpinteros = User.objects.filter(role='carpenter').count()
    
    carpinteros_pendientes_qs = Carpintero.objects.filter(status='pending').select_related('user').order_by('-created_at')
    total_pendientes = carpinteros_pendientes_qs.count()
    
    # Paginación para la tabla de pendientes (5 por página)
    paginator_pendientes = Paginator(carpinteros_pendientes_qs, 5)
    page_pendientes = request.GET.get('page_pendientes')
    carpinteros_pendientes = paginator_pendientes.get_page(page_pendientes)
    
    # Carpinteros activos (aprobados)
    carpinteros_activos_qs = Carpintero.objects.filter(status='approved').select_related('user').order_by('-created_at')
    
    # Paginación para la tabla de activos (5 por página)
    paginator_activos = Paginator(carpinteros_activos_qs, 5)
    page_activos = request.GET.get('page_activos')
    carpinteros_activos = paginator_activos.get_page(page_activos)
    
    total_proyectos = Portafolio.objects.count()
    
    # Solicitudes
    solicitudes_pendientes = SolicitudProyecto.objects.filter(status='pending').count()
    solicitudes_completadas = SolicitudProyecto.objects.filter(status='completed').count()
    
    # Acciones (Aprobar carpintero por POST)
    if request.method == 'POST':
        action = request.POST.get('action')
        if action == 'approve_carpenter':
            carpintero_id = request.POST.get('carpintero_id')
            c = get_object_or_404(Carpintero, id=carpintero_id)
            c.approved = True
            c.status = 'approved'
            c.save()
            email_service.send_carpenter_approval_email(c)
            messages.success(request, f'Carpintero {c.user.full_name} aprobado correctamente.')
            return redirect('admin_dashboard')
            
        elif action == 'reject_carpenter':
            carpintero_id = request.POST.get('carpintero_id')
            reason = request.POST.get('rejection_reason', '')
            c = get_object_or_404(Carpintero, id=carpintero_id)
            c.approved = False
            c.status = 'rejected'
            c.rejection_reason = reason
            c.save()
            
            Notificacion.objects.create(
                user=c.user,
                message=f'Tu solicitud de carpintero ha sido rechazada. Motivo: {reason}'
            )
            
            messages.success(request, f'Carpintero {c.user.full_name} rechazado.')
            return redirect('admin_dashboard')
            
        elif action == 'create_admin':
            if not request.user.is_superuser:
                messages.error(request, 'Solo el superadministrador puede crear otros administradores.')
                return redirect('admin_dashboard')
                
            email = request.POST.get('email')
            name = request.POST.get('full_name')
            password = request.POST.get('password')
            
            if User.objects.filter(email=email).exists():
                messages.error(request, 'Ya existe un usuario con ese correo electrónico.')
            else:
                new_admin = User.objects.create_user(
                    username=email,
                    email=email,
                    full_name=name,
                    password=password,
                    role='admin',
                    is_staff=True
                )
                ActivityLog.objects.create(
                    user=request.user,
                    action_type='CREATE_ADMIN',
                    description=f'Creado administrador: {email}'
                )
                messages.success(request, 'Administrador creado exitosamente.')
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