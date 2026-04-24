from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.db import transaction
from django.db.models import Avg, Count

from .models import Carpintero, Portafolio, Comentario, Review
from proyectos.models import SolicitudProyecto
from contrataciones.models import Notificacion
from core.pagination_utils import paginate_queryset
from core.email_utils import email_service


def carpintero_required(view_func):
    """Decorador para verificar que el usuario es carpintero"""
    def wrapper(request, *args, **kwargs):
        if not request.user.is_authenticated:
            return redirect('login')
        if request.user.role != 'carpenter':
            messages.error(request, 'No tienes permisos de carpintero. Inicia sesión con la cuenta adecuada.')
            return redirect('login')
        try:
            request.user.perfil_carpintero
        except:
            messages.error(request, 'No tienes un perfil de carpintero registrado.')
            return redirect('login')
        return view_func(request, *args, **kwargs)
    return wrapper

def approved_carpenter_required(view_func):
    """Decorador para verificar que el carpintero está aprobado"""
    def wrapper(request, *args, **kwargs):
        if not request.user.is_authenticated or request.user.role != 'carpenter':
            messages.error(request, 'Acceso denegado.')
            return redirect('login')
        perfil = request.user.perfil_carpintero
        if not perfil.approved:
            messages.warning(request, 'Tu solicitud aún está pendiente o fue rechazada. No puedes modificar tu portafolio.')
            return redirect('carpinteros:panel')
        return view_func(request, *args, **kwargs)
    return wrapper


@login_required
def panel_carpintero(request):
    """Panel principal del carpintero con estadísticas"""
    if request.user.role != 'carpenter':
        return redirect('home')

    try:
        perfil = request.user.perfil_carpintero
    except Carpintero.DoesNotExist:
        messages.error(request, 'No tienes un perfil de carpintero.')
        return redirect('home')

    # Estadísticas
    proyectos_count = perfil.portafolio.count()
    solicitudes_pendientes = SolicitudProyecto.objects.filter(
        carpenter=perfil, status='pending'
    ).count()
    comentarios_count = Comentario.objects.filter(proyecto__carpenter=perfil).count()
    notificaciones_no_leidas = Notificacion.objects.filter(
        user=request.user, is_read=False
    ).count()

    # Calificación promedio
    avg_rating = Review.objects.filter(carpenter=perfil).aggregate(
        avg=Avg('rating')
    )['avg']

    context = {
        'perfil': perfil,
        'proyectos_count': proyectos_count,
        'solicitudes_pendientes': solicitudes_pendientes,
        'comentarios_count': comentarios_count,
        'notificaciones_no_leidas': notificaciones_no_leidas,
        'avg_rating': avg_rating,
    }
    return render(request, 'carpinteros/panel_carpintero.html', context)


@login_required
@approved_carpenter_required
def mis_proyectos(request):
    """Ver y gestionar proyectos del portafolio con paginación"""
    perfil = request.user.perfil_carpintero
    proyectos = perfil.portafolio.all()

    # Paginación
    page_obj = paginate_queryset(proyectos, request, per_page=12)

    context = {
        'page_obj': page_obj,
        'perfil': perfil,
    }
    return render(request, 'carpinteros/mis_proyectos.html', context)


@login_required
@approved_carpenter_required
def subir_proyecto(request):
    """Subir nuevo proyecto al portafolio"""
    perfil = request.user.perfil_carpintero

    if request.method == 'POST':
        title = request.POST.get('title')
        description = request.POST.get('description')
        price = request.POST.get('price', 0)
        image = request.FILES.get('image')

        if title and description:
            Portafolio.objects.create(
                carpenter=perfil,
                title=title,
                description=description,
                price=price,
                image=image
            )
            messages.success(request, 'Proyecto subido correctamente.')
            return redirect('carpinteros:mis_proyectos')
        else:
            messages.error(request, 'Por favor completa todos los campos requeridos.')

    return render(request, 'carpinteros/subir_proyecto.html', {'perfil': perfil})


@login_required
@approved_carpenter_required
def editar_proyecto(request, proyecto_id):
    """Editar proyecto del portafolio"""
    perfil = request.user.perfil_carpintero
    proyecto = get_object_or_404(Portafolio, id=proyecto_id, carpenter=perfil)

    if request.method == 'POST':
        proyecto.title = request.POST.get('title', proyecto.title)
        proyecto.description = request.POST.get('description', proyecto.description)
        proyecto.price = request.POST.get('price', proyecto.price)
        if request.FILES.get('image'):
            proyecto.image = request.FILES.get('image')
        proyecto.save()
        messages.success(request, 'Proyecto actualizado correctamente.')
        return redirect('carpinteros:mis_proyectos')

    context = {
        'proyecto': proyecto,
        'perfil': perfil,
    }
    return render(request, 'carpinteros/editar_proyecto.html', context)


@login_required
@approved_carpenter_required
def eliminar_proyecto(request, proyecto_id):
    """Eliminar proyecto del portafolio"""
    perfil = request.user.perfil_carpintero
    proyecto = get_object_or_404(Portafolio, id=proyecto_id, carpenter=perfil)

    if request.method == 'POST':
        proyecto.delete()
        messages.success(request, 'Proyecto eliminado correctamente.')
        return redirect('carpinteros:mis_proyectos')

    return render(request, 'carpinteros/eliminar_proyecto.html', {'proyecto': proyecto})


@login_required
@approved_carpenter_required
def ver_solicitudes(request):
    """Ver solicitudes de proyectos recibidas con paginación y filtros"""
    perfil = request.user.perfil_carpintero
    solicitudes = SolicitudProyecto.objects.filter(
        carpenter=perfil
    ).select_related('user').order_by('-created_at')

    # Filtrar por estado
    status = request.GET.get('status')
    if status:
        solicitudes = solicitudes.filter(status=status)

    # Paginación
    page_obj = paginate_queryset(solicitudes, request, per_page=10)

    context = {
        'page_obj': page_obj,
        'perfil': perfil,
        'status_filter': status,
    }
    return render(request, 'carpinteros/ver_solicitudes.html', context)


@login_required
@approved_carpenter_required
def detalle_solicitud(request, solicitud_id):
    """Ver detalle de una solicitud y responder"""
    perfil = request.user.perfil_carpintero
    solicitud = get_object_or_404(SolicitudProyecto, id=solicitud_id, carpenter=perfil)

    if request.method == 'POST':
        action = request.POST.get('action')
        response_message = request.POST.get('response_message', '')

        if action == 'accept':
            solicitud.status = 'accepted'
            solicitud.response_message = response_message
            solicitud.save()

            # Notificar al usuario
            Notificacion.objects.create(
                user=solicitud.user,
                message=f'Tu solicitud "{solicitud.title}" ha sido aceptada por {request.user.full_name}'
            )

            # Enviar email
            email_service.send_project_status_email(solicitud, 'accepted')

            messages.success(request, 'Solicitud aceptada correctamente.')

        elif action == 'reject':
            solicitud.status = 'rejected'
            solicitud.response_message = response_message
            solicitud.save()

            # Notificar al usuario
            Notificacion.objects.create(
                user=solicitud.user,
                message=f'Tu solicitud "{solicitud.title}" ha sido rechazada.'
            )

            # Enviar email
            email_service.send_project_status_email(solicitud, 'rejected')

            messages.success(request, 'Solicitud rechazada.')

        elif action == 'complete':
            solicitud.status = 'completed'
            solicitud.response_message = response_message
            solicitud.save()

            # Enviar email
            email_service.send_project_status_email(solicitud, 'completed')

            messages.success(request, 'Proyecto marcado como completado.')

        return redirect('carpinteros:ver_solicitudes')

    context = {
        'solicitud': solicitud,
        'perfil': perfil,
    }
    return render(request, 'carpinteros/detalle_solicitud.html', context)


@login_required
@carpintero_required
def notificaciones(request):
    """Ver notificaciones del carpintero con paginación"""
    perfil = request.user.perfil_carpintero
    notificaciones = Notificacion.objects.filter(
        user=request.user
    ).order_by('-created_at')

    # Paginación
    page_obj = paginate_queryset(notificaciones, request, per_page=20)

    # Marcar como leídas
    if request.method == 'POST':
        notificaciones.update(is_read=True)
        messages.success(request, 'Todas las notificaciones marcadas como leídas.')
        return redirect('carpinteros:notificaciones')

    context = {
        'page_obj': page_obj,
        'perfil': perfil,
    }
    return render(request, 'carpinteros/notificaciones.html', context)


@login_required
@carpintero_required
def editar_perfil(request):
    """Permite al carpintero editar su perfil profesional"""
    perfil = request.user.perfil_carpintero
    
    from .forms import CarpinteroProfileForm
    from core.models import User
    
    if request.method == 'POST':
        form = CarpinteroProfileForm(request.POST, request.FILES, instance=perfil)
        full_name = request.POST.get('full_name', '').strip()
        phone = request.POST.get('phone', '').strip()
        city = request.POST.get('city', '').strip()
        
        if form.is_valid():
            form.save()
            # Actualizar datos del User también
            user = request.user
            if full_name:
                user.full_name = full_name
            user.phone = phone
            user.city = city
            user.save()
            messages.success(request, '¡Perfil actualizado correctamente!')
            return redirect('carpinteros:panel')
    else:
        form = CarpinteroProfileForm(instance=perfil)
    
    context = {
        'form': form,
        'perfil': perfil,
    }
    return render(request, 'carpinteros/editar_perfil.html', context)