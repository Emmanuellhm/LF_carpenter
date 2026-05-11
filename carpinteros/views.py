from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.db.models import Count
from .models import Carpintero, Portafolio
from proyectos.models import SolicitudProyecto
from contrataciones.models import Notificacion
from .forms import PortafolioForm, CarpinteroProfileForm

def _get_carpintero(user):
    try:
        return user.perfil_carpintero
    except Carpintero.DoesNotExist:
        return None

@login_required
def panel_carpintero(request):
    if request.user.role != 'carpenter':
        return redirect('home')
        
    carpintero = _get_carpintero(request.user)
    if not carpintero:
        messages.error(request, 'No tienes un perfil de carpintero asociado.')
        return redirect('home')

    # Estadísticas básicas
    total_proyectos = Portafolio.objects.filter(carpenter=carpintero).count()
    solicitudes_pendientes = SolicitudProyecto.objects.filter(carpenter=carpintero, status='pending').count()
    solicitudes_completadas = SolicitudProyecto.objects.filter(carpenter=carpintero, status='completed').count()
    
    # Solicitudes recientes
    solicitudes_recientes = SolicitudProyecto.objects.filter(carpenter=carpintero).order_by('-created_at')[:5]

    context = {
        'carpintero': carpintero,
        'total_proyectos': total_proyectos,
        'solicitudes_pendientes': solicitudes_pendientes,
        'solicitudes_completadas': solicitudes_completadas,
        'solicitudes_recientes': solicitudes_recientes
    }
    return render(request, 'carpinteros/panel_carpintero.html', context)

@login_required
def mis_proyectos(request):
    carpintero = _get_carpintero(request.user)
    proyectos = Portafolio.objects.filter(carpenter=carpintero).order_by('-created_at')
    return render(request, 'carpinteros/mis_proyectos.html', {'proyectos': proyectos})

@login_required
def subir_proyecto(request):
    carpintero = _get_carpintero(request.user)
    if request.method == 'POST':
        form = PortafolioForm(request.POST, request.FILES)
        if form.is_valid():
            proyecto = form.save(commit=False)
            proyecto.carpenter = carpintero
            proyecto.save()
            messages.success(request, 'Proyecto subido a tu portafolio.')
            return redirect('carpinteros:mis_proyectos')
    else:
        form = PortafolioForm()
    
    return render(request, 'carpinteros/subir_proyecto.html', {'form': form})

@login_required
def editar_proyecto(request, proyecto_id):
    carpintero = _get_carpintero(request.user)
    proyecto = get_object_or_404(Portafolio, id=proyecto_id, carpenter=carpintero)
    
    if request.method == 'POST':
        form = PortafolioForm(request.POST, request.FILES, instance=proyecto)
        if form.is_valid():
            form.save()
            messages.success(request, 'Proyecto actualizado.')
            return redirect('carpinteros:mis_proyectos')
    else:
        form = PortafolioForm(instance=proyecto)
        
    return render(request, 'carpinteros/editar_proyecto.html', {'form': form, 'proyecto': proyecto})

@login_required
def eliminar_proyecto(request, proyecto_id):
    carpintero = _get_carpintero(request.user)
    proyecto = get_object_or_404(Portafolio, id=proyecto_id, carpenter=carpintero)
    if request.method == 'POST':
        proyecto.delete()
        messages.success(request, 'Proyecto eliminado.')
    return redirect('carpinteros:mis_proyectos')

@login_required
def ver_solicitudes(request):
    carpintero = _get_carpintero(request.user)
    solicitudes = SolicitudProyecto.objects.filter(carpenter=carpintero).order_by('-created_at')
    return render(request, 'carpinteros/ver_solicitudes.html', {'solicitudes': solicitudes})

@login_required
def detalle_solicitud(request, solicitud_id):
    carpintero = _get_carpintero(request.user)
    solicitud = get_object_or_404(SolicitudProyecto, id=solicitud_id, carpenter=carpintero)
    
    if request.method == 'POST':
        action = request.POST.get('action')
        if action in ['accepted', 'rejected', 'completed']:
            solicitud.status = action
            solicitud.save()
            
            # Notificar al cliente
            Notificacion.objects.create(
                user=solicitud.user,
                message=f'El estado de tu solicitud "{solicitud.title}" ha cambiado a: {action}.'
            )
            messages.success(request, f'Solicitud marcada como {action}.')
            return redirect('carpinteros:detalle_solicitud', solicitud_id=solicitud.id)
            
    return render(request, 'carpinteros/detalle_solicitud.html', {'solicitud': solicitud})

@login_required
def notificaciones(request):
    nots = Notificacion.objects.filter(user=request.user).order_by('-created_at')
    if request.method == 'POST':
        # Marcar todas como leídas
        nots.update(is_read=True)
        return redirect('carpinteros:notificaciones')
        
    return render(request, 'carpinteros/notificaciones.html', {'notificaciones': nots})

@login_required
def editar_perfil(request):
    carpintero = _get_carpintero(request.user)
    if request.method == 'POST':
        form = CarpinteroProfileForm(request.POST, request.FILES, instance=carpintero)
        if form.is_valid():
            form.save()
            messages.success(request, 'Perfil actualizado correctamente.')
            return redirect('carpinteros:panel')
    else:
        form = CarpinteroProfileForm(instance=carpintero)
        
    return render(request, 'carpinteros/editar_perfil.html', {'form': form})
