from django.shortcuts import render, redirect
from users.models import User, CarpenterProfile
from .models import Project, ProjectRequest
from django.contrib.auth.decorators import login_required
from django.contrib import messages

def carpenter_list(request):
    carpenters = User.objects.filter(role='carpenter').select_related('carpenter_profile')
    
    # Pre-calculate project counts
    for c in carpenters:
        c.project_count = c.projects.count()

    context = {
        'carpenters': carpenters,
    }
    return render(request, 'portfolio/carpenter_list.html', context)

def project_list(request):
    projects = Project.objects.all().select_related('carpenter', 'carpenter__carpenter_profile').order_by('-created_at')
    context = {
        'projects': projects,
    }
    return render(request, 'portfolio/project_list.html', context)

@login_required
def request_project(request):
    if request.method == 'POST':
        carpenter_id = request.POST.get('carpenter_id')
        title = request.POST.get('title')
        description = request.POST.get('description')
        budget = request.POST.get('budget')
        deadline = request.POST.get('deadline')
        dimensions = request.POST.get('dimensions')
        materials = request.POST.get('materials')
        image = request.FILES.get('reference_image')

        carpenter = User.objects.get(id=carpenter_id)
        
        ProjectRequest.objects.create(
            client=request.user,
            carpenter=carpenter,
            description=description,
            budget=budget if budget else None,
            deadline=deadline if deadline else None,
            reference_image=image,
            dimensions=dimensions,
            materials=materials
        )
        messages.success(request, 'Solicitud enviada correctamente')
        return redirect('user_panel')
    
    return redirect('carpenter_list')
