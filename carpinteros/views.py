from django.shortcuts import render, redirect, get_object_or_404
from django.http import JsonResponse
import json
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.db.models import Avg, Count
from django.utils import timezone
import threading

from .models import Carpintero, Portafolio, Review
from .forms import CarpinteroPerfilForm, PortafolioForm
from proyectos.models import SolicitudProyecto, EstadoFabricacion
from core.utils.emails import send_status_update_email


def _get_carpintero_or_403(request):
    """Helper: obtiene el perfil del carpintero o redirige si no tiene acceso."""
    if request.user.role != 'carpenter':
        messages.error(request, 'Acceso restringido a carpinteros.')
        return None, redirect('home')
    try:
        carpintero = request.user.perfil_carpintero
        return carpintero, None
    except Carpintero.DoesNotExist:
        messages.error(request, 'No se encontró tu perfil de carpintero.')
        return None, redirect('home')


@login_required
def panel_carpintero(request):
    """Dashboard principal del carpintero con estadísticas reales."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    # Estadísticas del portafolio
    proyectos = Portafolio.objects.filter(carpenter=carpintero).order_by('-created_at')
    total_proyectos = proyectos.count()
    proyectos_recientes = proyectos[:4]

    # Estadísticas de solicitudes
    solicitudes_qs = SolicitudProyecto.objects.filter(carpenter=carpintero)
    total_solicitudes = solicitudes_qs.count()
    solicitudes_pendientes = solicitudes_qs.filter(status='pending').count()
    solicitudes_recientes = solicitudes_qs.order_by('-created_at')[:5]

    # Calificación promedio
    rating_data = Review.objects.filter(carpenter=carpintero).aggregate(
        avg=Avg('rating'), total=Count('id')
    )
    rating_avg = rating_data['avg'] or 0
    total_reviews = rating_data['total']

    context = {
        'carpintero': carpintero,
        'total_proyectos': total_proyectos,
        'proyectos_recientes': proyectos_recientes,
        'total_solicitudes': total_solicitudes,
        'solicitudes_pendientes': solicitudes_pendientes,
        'solicitudes_recientes': solicitudes_recientes,
        'rating_avg': round(rating_avg, 1),
        'total_reviews': total_reviews,
        'is_approved': carpintero.approved,
    }
    return render(request, 'carpinteros/panel_carpintero.html', context)


@login_required
def editar_perfil(request):
    """Permite al carpintero editar su perfil profesional."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    if request.method == 'POST':
        form = CarpinteroPerfilForm(request.POST, request.FILES, instance=carpintero)
        # También actualizar campos del usuario base
        full_name = request.POST.get('full_name', '').strip()
        phone = request.POST.get('phone', '').strip()
        city = request.POST.get('city', '').strip()

        if form.is_valid():
            form.save()
            # Actualizar datos del usuario
            user = request.user
            if full_name:
                user.full_name = full_name
            if phone:
                user.phone = phone
            if city:
                user.city = city
            user.save()

            messages.success(request, '¡Perfil actualizado correctamente!')
            return redirect('carpinteros:panel')
    else:
        form = CarpinteroPerfilForm(instance=carpintero)

    context = {'form': form, 'carpintero': carpintero}
    return render(request, 'carpinteros/editar_perfil.html', context)


@login_required
def mis_proyectos(request):
    """Lista el portafolio de proyectos del carpintero."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    proyectos = Portafolio.objects.filter(carpenter=carpintero).order_by('-created_at')
    context = {
        'proyectos': proyectos,
        'carpintero': carpintero,
        'total': proyectos.count(),
    }
    return render(request, 'carpinteros/mis_proyectos.html', context)


@login_required
def subir_proyecto(request):
    """Permite al carpintero subir un nuevo proyecto al portafolio."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    if request.method == 'POST':
        form = PortafolioForm(request.POST, request.FILES)
        if form.is_valid():
            proyecto = form.save(commit=False)
            proyecto.carpenter = carpintero
            proyecto.save()
            messages.success(request, f'¡Proyecto "{proyecto.title}" publicado en tu portafolio!')
            return redirect('carpinteros:mis_proyectos')
    else:
        form = PortafolioForm()

    context = {'form': form, 'carpintero': carpintero, 'accion': 'Subir'}
    return render(request, 'carpinteros/subir_proyecto.html', context)


@login_required
def editar_proyecto(request, proyecto_id):
    """Permite editar un proyecto del portafolio (solo el dueño)."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    proyecto = get_object_or_404(Portafolio, id=proyecto_id, carpenter=carpintero)

    if request.method == 'POST':
        form = PortafolioForm(request.POST, request.FILES, instance=proyecto)
        if form.is_valid():
            form.save()
            messages.success(request, f'¡Proyecto "{proyecto.title}" actualizado!')
            return redirect('carpinteros:mis_proyectos')
    else:
        form = PortafolioForm(instance=proyecto)

    context = {'form': form, 'proyecto': proyecto, 'carpintero': carpintero, 'accion': 'Editar'}
    return render(request, 'carpinteros/subir_proyecto.html', context)


@login_required
def eliminar_proyecto(request, proyecto_id):
    """Elimina un proyecto del portafolio (solo el dueño, requiere POST)."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    proyecto = get_object_or_404(Portafolio, id=proyecto_id, carpenter=carpintero)

    if request.method == 'POST':
        titulo = proyecto.title
        proyecto.delete()
        messages.success(request, f'Proyecto "{titulo}" eliminado correctamente.')
        return redirect('carpinteros:mis_proyectos')

    context = {'proyecto': proyecto, 'carpintero': carpintero}
    return render(request, 'carpinteros/eliminar_proyecto.html', context)


@login_required
def ver_solicitudes(request):
    """Centro de Proyectos: lista inteligente con urgencia y acciones rápidas."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    estado_filtro = request.GET.get('estado', '')
    q = request.GET.get('q', '').strip()

    solicitudes_qs = SolicitudProyecto.objects.filter(
        carpenter=carpintero
    ).select_related('user').order_by('-updated_at')

    if estado_filtro in [s[0] for s in SolicitudProyecto.STATUS_CHOICES]:
        solicitudes_qs = solicitudes_qs.filter(status=estado_filtro)

    if q:
        from django.db.models import Q
        solicitudes_qs = solicitudes_qs.filter(
            Q(title__icontains=q) | Q(user__full_name__icontains=q)
        )

    solicitudes = solicitudes_qs

    base_qs = SolicitudProyecto.objects.filter(carpenter=carpintero)
    conteos = {
        'todas':        base_qs.count(),
        'pendientes':   base_qs.filter(status='pending').count(),
        'activas':      base_qs.filter(status__in=['budgeting','accepted','in_progress','finishing','ready']).count(),
        'completadas':  base_qs.filter(status='completed').count(),
        'rechazadas':   base_qs.filter(status='rejected').count(),
    }

    context = {
        'solicitudes': solicitudes,
        'carpintero': carpintero,
        'estado_filtro': estado_filtro,
        'conteos': conteos,
        'q': q,
    }
    return render(request, 'carpinteros/ver_solicitudes.html', context)


@login_required
def detalle_solicitud(request, solicitud_id):
    """Muestra el detalle de una solicitud y permite cambiar su estado."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    solicitud = get_object_or_404(SolicitudProyecto, id=solicitud_id, carpenter=carpintero)

    if request.method == 'POST':
        nuevo_estado = request.POST.get('estado')
        mensaje_respuesta = request.POST.get('response_message', '').strip()
        ESTADOS_VALIDOS = ['accepted', 'rejected', 'completed']

        if nuevo_estado in ESTADOS_VALIDOS:
            solicitud.status = nuevo_estado
            if mensaje_respuesta:
                solicitud.response_message = mensaje_respuesta
            solicitud.save()
            messages.success(request, f'Estado de la solicitud actualizado a "{solicitud.get_status_display()}".')
            return redirect('carpinteros:ver_solicitudes')
        else:
            messages.error(request, 'Estado no válido.')

    context = {
        'solicitud': solicitud,
        'carpintero': carpintero,
        'status_choices': SolicitudProyecto.STATUS_CHOICES,
        'editable_statuses': ['budgeting', 'accepted', 'in_progress', 'finishing', 'ready', 'completed'],
    }
    return render(request, 'carpinteros/detalle_solicitud.html', context)


@login_required
def notificaciones(request):
    """Vista de notificaciones del carpintero."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    # Solicitudes nuevas (pendientes) son las "notificaciones" principales
    nuevas_solicitudes = SolicitudProyecto.objects.filter(
        carpenter=carpintero, status='pending'
    ).select_related('user').order_by('-created_at')[:10]

    context = {
        'carpintero': carpintero,
        'nuevas_solicitudes': nuevas_solicitudes,
    }
    return render(request, 'carpinteros/notificaciones.html', context)


@login_required
def kanban_board(request):
    """Vista del tablero Kanban para gestionar solicitudes."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    solicitudes = SolicitudProyecto.objects.filter(
        carpenter=carpintero
    ).exclude(status='rejected').select_related('user').order_by('-updated_at')

    # Agrupar por estado para el Kanban
    columnas = {
        'pending': [],
        'budgeting': [],
        'accepted': [],
        'in_progress': [],
        'finishing': [],
        'ready': [],
        'completed': []
    }

    for sol in solicitudes:
        if sol.status in columnas:
            columnas[sol.status].append(sol)
        else:
            # Fallback en caso de estados huérfanos
            columnas['pending'].append(sol)

    context = {
        'carpintero': carpintero,
        'columnas': columnas,
    }
    return render(request, 'carpinteros/kanban_board.html', context)


@login_required
def api_actualizar_estado(request, solicitud_id):
    """Endpoint API para actualizar el estado desde el Kanban Drag & Drop."""
    if request.method != 'POST':
        return JsonResponse({'error': 'Método no permitido'}, status=405)

    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return JsonResponse({'error': 'No autorizado'}, status=403)

    try:
        data = json.loads(request.body)
        nuevo_estado = data.get('status')
        
        # Validar el nuevo estado contra las opciones del modelo
        estados_validos = dict(SolicitudProyecto.STATUS_CHOICES).keys()
        if nuevo_estado not in estados_validos:
            return JsonResponse({'error': 'Estado inválido'}, status=400)

        solicitud = get_object_or_404(SolicitudProyecto, id=solicitud_id, carpenter=carpintero)
        solicitud.status = nuevo_estado
        solicitud.save()

        # Notificar al cliente por email en hilo separado (no bloquea la respuesta)
        threading.Thread(
            target=send_status_update_email,
            args=(solicitud,),
            daemon=True
        ).start()

        return JsonResponse({
            'success': True,
            'status_display': solicitud.get_status_display(),
            'message': 'Estado actualizado'
        })
    except json.JSONDecodeError:
        return JsonResponse({'error': 'JSON inválido'}, status=400)
    except Exception as e:
        return JsonResponse({'error': str(e)}, status=500)


@login_required
def actualizar_estado_fabricacion(request, solicitud_id):
    """Permite al carpintero registrar un avance en la fabricación del proyecto."""
    carpintero, error_redirect = _get_carpintero_or_403(request)
    if error_redirect:
        return error_redirect

    solicitud = get_object_or_404(
        SolicitudProyecto, pk=solicitud_id, carpenter=carpintero
    )

    if request.method == 'POST':
        nuevo_status = request.POST.get('status')
        notes = request.POST.get('notes', '').strip()
        photo = request.FILES.get('photo')

        status_validos = [s[0] for s in SolicitudProyecto.STATUS_CHOICES]
        if nuevo_status not in status_validos:
            messages.error(request, 'Estado inválido.')
            return redirect('carpinteros:detalle_solicitud', solicitud_id=solicitud_id)

        # Registrar el hito en la línea de tiempo
        EstadoFabricacion.objects.create(
            solicitud=solicitud,
            status=nuevo_status,
            notes=notes,
            photo=photo if photo else None,
        )

        # Actualizar el estado principal de la solicitud
        solicitud.status = nuevo_status
        solicitud.save()

        # Enviar notificación en tiempo real al cliente
        from core.utils.notifications import crear_notificacion
        from django.urls import reverse
        crear_notificacion(
            user=solicitud.user,
            title="Actualización de Proyecto",
            message=f"Tu proyecto '{solicitud.title}' ha avanzado a la fase: {solicitud.get_status_display()}",
            link=reverse('usuarios:tracking_proyecto', args=[solicitud.id]),
            notification_type='tracking'
        )

        messages.success(request, f'Estado actualizado a "{solicitud.get_status_display()}".')
        return redirect('carpinteros:detalle_solicitud', solicitud_id=solicitud_id)

    return redirect('carpinteros:detalle_solicitud', solicitud_id=solicitud_id)

