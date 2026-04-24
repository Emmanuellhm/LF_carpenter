from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from carpinteros.models import Carpintero
from .models import SolicitudProyecto


@login_required
def solicitar_proyecto(request, carpintero_id):
    """Solicitar un proyecto personalizado a un carpintero"""
    carpintero = get_object_or_404(Carpintero, id=carpintero_id, approved=True)

    if request.method == 'POST':
        title = request.POST.get('title')
        description = request.POST.get('description')
        budget = request.POST.get('budget')
        deadline = request.POST.get('deadline')
        dimensions = request.POST.get('dimensions')
        materials = request.POST.get('materials')
        reference_image = request.FILES.get('reference_image')

        if title and description:
            SolicitudProyecto.objects.create(
                user=request.user,
                carpenter=carpintero,
                title=title,
                description=description,
                budget=budget if budget else None,
                deadline=deadline if deadline else None,
                dimensions=dimensions,
                materials=materials,
                reference_image=reference_image
            )
            messages.success(request, 'Solicitud enviada correctamente.')
            return redirect('usuarios:mis_solicitudes')
        else:
            messages.error(request, 'Por favor completa los campos requeridos.')

    context = {
        'carpintero': carpintero,
    }
    return render(request, 'proyectos/solicitar_proyecto.html', context)