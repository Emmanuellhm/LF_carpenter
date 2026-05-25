from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.db.models import Q, Avg, Count
from django.core.paginator import Paginator

from carpinteros.models import Carpintero, Portafolio, Comentario, Review
from proyectos.models import SolicitudProyecto, EstadoFabricacion
from core.pagination_utils import paginate_queryset
from .forms import UserProfileForm


@login_required
def panel_usuario(request):
    """Panel principal del usuario"""
    if request.user.role != 'user':
        return redirect('home')

    todas_solicitudes = SolicitudProyecto.objects.filter(user=request.user)
    solicitudes_recientes = todas_solicitudes.order_by('-created_at')[:5]
    
    total_solicitudes = todas_solicitudes.count()
    solicitudes_activas = todas_solicitudes.filter(status__in=['pending', 'reviewing', 'accepted', 'in_progress']).count()
    solicitudes_completadas = todas_solicitudes.filter(status='completed').count()

    context = {
        'solicitudes_recientes': solicitudes_recientes,
        'total_solicitudes': total_solicitudes,
        'solicitudes_activas': solicitudes_activas,
        'solicitudes_completadas': solicitudes_completadas,
    }
    return render(request, 'usuarios/panel_usuario.html', context)


@login_required
def ver_carpinteros(request):
    """Listado de carpinteros disponibles con búsqueda avanzada y paginación"""
    # Queryset base - solo carpinteros aprobados y verificados
    carpinteros = Carpintero.objects.filter(
        approved=True,
        is_verified=True,
        user__is_active=True
    ).select_related('user').annotate(
        avg_rating=Avg('reviews__rating')
    ).distinct()

    # Búsqueda por texto
    query = request.GET.get('q', '').strip()
    if query:
        carpinteros = carpinteros.filter(
            Q(user__full_name__icontains=query) |
            Q(specialties__icontains=query) |
            Q(description__icontains=query) |
            Q(user__city__icontains=query)
        )

    # Filtros
    especialidad = request.GET.get('especialidad', '').strip()
    if especialidad:
        carpinteros = carpinteros.filter(specialties__icontains=especialidad)

    ciudad = request.GET.get('ciudad', '').strip()
    if ciudad:
        carpinteros = carpinteros.filter(user__city__icontains=ciudad)

    # Filtro de años de experiencia
    experiencia_min = request.GET.get('experiencia_min')
    if experiencia_min and experiencia_min.isdigit():
        carpinteros = carpinteros.filter(experience_years__gte=int(experiencia_min))

    experiencia_max = request.GET.get('experiencia_max')
    if experiencia_max and experiencia_max.isdigit():
        carpinteros = carpinteros.filter(experience_years__lte=int(experiencia_max))

    # Filtro de calificación mínima
    calificacion_min = request.GET.get('calificacion_min')
    if calificacion_min and calificacion_min.replace('.', '').isdigit():
        try:
            calificacion = float(calificacion_min)
            carpinteros = carpinteros.filter(rating_avg__gte=calificacion)
        except ValueError:
            pass

    # Ordenamiento
    orden = request.GET.get('orden', '-created_at')
    orden_valido = ['-created_at', 'created_at', '-rating_avg', 'rating_avg',
                    '-experience_years', 'experience_years', 'user__full_name']
    if orden not in orden_valido:
        orden = '-created_at'

    carpinteros = carpinteros.order_by(orden)

    # Paginación
    page_obj = paginate_queryset(carpinteros, request, per_page=12)

    # Obtener valores únicos para los filtros
    especialidades = Carpintero.objects.filter(
        approved=True, is_verified=True
    ).values_list('specialties', flat=True).distinct()

    ciudades = Carpintero.objects.filter(
        approved=True, is_verified=True
    ).select_related('user').values_list('user__city', flat=True).distinct()

    context = {
        'page_obj': page_obj,
        'query': query,
        'especialidad': especialidad,
        'ciudad': ciudad,
        'experiencia_min': experiencia_min,
        'experiencia_max': experiencia_max,
        'calificacion_min': calificacion_min,
        'orden': orden,
        'especialidades': especialidades,
        'ciudades': [c for c in ciudades if c],
        'total_resultados': carpinteros.count(),
    }
    return render(request, 'usuarios/ver_carpinteros.html', context)


@login_required
def ver_carpintero_detalle(request, carpintero_id):
    """Detalle de un carpintero con calificaciones"""
    carpintero = get_object_or_404(
        Carpintero.objects.select_related('user').annotate(
            avg_rating=Avg('reviews__rating'),
            total_reviews=Count('reviews')
        ),
        pk=carpintero_id,
        approved=True
    )
    portafolio = carpintero.portafolio.all()
    comentarios = Comentario.objects.filter(
        proyecto__carpenter=carpintero
    ).select_related('user')[:10]

    # Reseñas paginadas
    reviews = Review.objects.filter(
        carpenter=carpintero
    ).select_related('user').order_by('-created_at')
    reviews_page = paginate_queryset(reviews, request, per_page=5, param_name='reviews_page')

    # Verificar si el usuario puede calificar
    puede_calificar = False
    if request.user.is_authenticated and request.user.role == 'user':
        # Solo usuarios con proyectos completados pueden calificar
        proyectos_completados = SolicitudProyecto.objects.filter(
            user=request.user,
            carpenter=carpintero,
            status='completed'
        ).exists()
        # Y que no hayan calificado ya
        ya_califico = Review.objects.filter(
            user=request.user,
            carpenter=carpintero
        ).exists()
        puede_calificar = proyectos_completados and not ya_califico

    # Registrar interacción
    from contrataciones.models import Interaccion
    Interaccion.objects.create(user=request.user, carpenter=carpintero, action='viewed')

    context = {
        'carpintero': carpintero,
        'portafolio': portafolio,
        'comentarios': comentarios,
        'reviews': reviews_page,
        'puede_calificar': puede_calificar,
    }
    return render(request, 'usuarios/ver_carpintero_detalle.html', context)


@login_required
def mis_solicitudes(request):
    """Historial de solicitudes del usuario con paginación"""
    solicitudes = SolicitudProyecto.objects.filter(
        user=request.user
    ).select_related('carpenter', 'carpenter__user').order_by('-created_at')

    # Filtros
    status = request.GET.get('status')
    if status:
        solicitudes = solicitudes.filter(status=status)

    # Paginación
    page_obj = paginate_queryset(solicitudes, request, per_page=10)

    context = {
        'page_obj': page_obj,
        'status_filter': status,
    }
    return render(request, 'usuarios/mis_solicitudes.html', context)


@login_required
def update_profile(request):
    """Actualizar perfil de usuario"""
    if request.method == 'POST':
        form = UserProfileForm(request.POST, instance=request.user)
        if form.is_valid():
            form.save()
            messages.success(request, 'Perfil actualizado correctamente.')
            return redirect('usuarios:panel')
    else:
        form = UserProfileForm(instance=request.user)

    return render(request, 'usuarios/update_profile.html', {'form': form})


@login_required
def historial(request):
    """Historial de actividades del usuario con paginación"""
    solicitudes = SolicitudProyecto.objects.filter(user=request.user)
    interacciones = request.user.interacciones.all()[:20]

    context = {
        'solicitudes': solicitudes,
        'interacciones': interacciones,
    }
    return render(request, 'usuarios/historial.html', context)


@login_required
def dejar_resena(request, carpintero_id):
    """Procesa el envío de una nueva reseña para un carpintero"""
    if request.method == 'POST' and request.user.role == 'user':
        carpintero = get_object_or_404(Carpintero, pk=carpintero_id, approved=True)
        
        # Verificar permisos (solo usuarios con proyectos completados que no han calificado)
        proyectos_completados = SolicitudProyecto.objects.filter(
            user=request.user,
            carpenter=carpintero,
            status='completed'
        ).exists()
        
        ya_califico = Review.objects.filter(
            user=request.user,
            carpenter=carpintero
        ).exists()
        
        if proyectos_completados and not ya_califico:
            try:
                rating = int(request.POST.get('rating', 0))
                comment = request.POST.get('comment', '').strip()
                
                if 1 <= rating <= 5:
                    Review.objects.create(
                        user=request.user,
                        carpenter=carpintero,
                        rating=rating,
                        comment=comment
                    )
                    messages.success(request, '¡Gracias! Tu reseña ha sido guardada correctamente.')
                else:
                    messages.error(request, 'La calificación debe estar entre 1 y 5 estrellas.')
            except ValueError:
                messages.error(request, 'Datos inválidos en la calificación.')
        else:
            messages.error(request, 'No tienes permisos para calificar a este carpintero o ya lo has hecho.')
            
    return redirect('usuarios:mis_solicitudes')


@login_required
def tracking_proyecto(request, solicitud_id):
    """Vista de línea de tiempo de fabricación para el cliente."""
    from django.db.models import Q
    solicitud = get_object_or_404(
        SolicitudProyecto,
        Q(pk=solicitud_id) & (Q(user=request.user) | Q(carpenter__user=request.user))
    )
    estados = solicitud.estados_fabricacion.order_by('created_at')

    # Orden canónico de fases para la barra de progreso
    STATUS_STEPS = [
        ('pending',     'Nueva Solicitud',   'fa-inbox'),
        ('budgeting',   'Cotizando',         'fa-file-invoice-dollar'),
        ('accepted',    'Materiales / Inicio','fa-boxes'),
        ('in_progress', 'En Taller',         'fa-hammer'),
        ('finishing',   'Acabados',          'fa-paint-brush'),
        ('ready',       'Listo p/ Entrega',  'fa-box-open'),
        ('completed',   'Entregado',         'fa-check-circle'),
    ]

    current_index = next(
        (i for i, s in enumerate(STATUS_STEPS) if s[0] == solicitud.status), 0
    )

    context = {
        'solicitud': solicitud,
        'estados': estados,
        'status_steps': STATUS_STEPS,
        'current_index': current_index,
    }
    return render(request, 'usuarios/tracking_proyecto.html', context)