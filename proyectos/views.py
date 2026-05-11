from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from .models import SolicitudProyecto
from .forms import SolicitudProyectoForm
from carpinteros.models import Carpintero
from contrataciones.models import Notificacion

@login_required
def solicitar_proyecto(request, carpintero_id):
    """Vista para que un cliente solicite un proyecto a un carpintero"""
    if request.user.role != 'user':
        messages.error(request, 'Solo los clientes pueden solicitar proyectos.')
        return redirect('home')

    carpintero = get_object_or_404(Carpintero, id=carpintero_id, approved=True)

    if request.method == 'POST':
        form = SolicitudProyectoForm(request.POST, request.FILES)
        if form.is_valid():
            solicitud = form.save(commit=False)
            solicitud.user = request.user
            solicitud.carpenter = carpintero
            solicitud.status = 'pending'
            solicitud.save()

            # Crear notificación para el carpintero
            Notificacion.objects.create(
                user=carpintero.user,
                message=f'Has recibido una nueva solicitud de proyecto: "{solicitud.title}" de {request.user.full_name}.'
            )

            messages.success(request, '¡Tu solicitud ha sido enviada exitosamente al carpintero!')
            return redirect('usuarios:mis_solicitudes')
    else:
        form = SolicitudProyectoForm()

    context = {
        'form': form,
        'carpintero': carpintero
    }
    return render(request, 'proyectos/solicitar_proyecto.html', context)
