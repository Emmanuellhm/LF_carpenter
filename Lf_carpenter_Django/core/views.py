from django.shortcuts import render, redirect
from django.contrib.auth import login as auth_login
from users.models import User, CarpenterProfile
from portfolio.models import Project, ProjectRequest
from django.contrib import messages
from django.contrib.auth.decorators import login_required

def index(request):
    return render(request, 'core/index.html')

def register_user(request):
    if request.method == 'POST':
        # Obtener datos del formulario
        username = request.POST.get('username')
        email = request.POST.get('email')
        password = request.POST.get('password')
        full_name = request.POST.get('full_name')
        phone = request.POST.get('phone')
        city = request.POST.get('city')

        # Crear usuario
        user = User.objects.create_user(
            username=username,
            email=email,
            password=password,
            first_name=full_name,
            role='client',
            phone=phone,
            city=city
        )
        # Mostrar modal de éxito
        return render(request, 'core/register_user.html', {'success': True})

    return render(request, 'core/register_user.html')

def register_carpenter(request):
    if request.method == 'POST':
        # Datos básicos
        username = request.POST.get('username')
        email = request.POST.get('email')
        password = request.POST.get('password')
        full_name = request.POST.get('full_name')
        phone = request.POST.get('phone')
        city = request.POST.get('city')
        
        # Datos del perfil
        specialties = request.POST.get('specialties')
        experience = request.POST.get('experience', 0)
        cv_file = request.FILES.get('cv_file')

        # Crear usuario con rol carpintero
        user = User.objects.create_user(
            username=username,
            email=email,
            password=password,
            first_name=full_name,
            role='carpenter',
            phone=phone,
            city=city
        )
        
        # Crear perfil (pendiente de aprobación)
        CarpenterProfile.objects.create(
            user=user,
            specialties=specialties,
            years_of_experience=experience,
            cv_file=cv_file,
            is_approved=False # Importante: requiere aprobación administrativa
        )

        return render(request, 'core/register_carpenter.html', {'success': True})

    return render(request, 'core/register_carpenter.html')

@login_required
def user_panel(request):
    if request.user.role != 'client':
        return redirect('index')
    
    if request.method == 'POST' and request.POST.get('action') == 'update_profile':
        user = request.user
        user.first_name = request.POST.get('full_name')
        user.email = request.POST.get('email')
        user.phone = request.POST.get('phone')
        user.city = request.POST.get('city')
        user.save()
        messages.success(request, 'Perfil actualizado correctamente')
        return redirect('user_panel')

    requests = request.user.sent_requests.all().select_related('carpenter').order_by('-created_at')
    
    context = {
        'requests': requests,
    }
    return render(request, 'core/user_panel.html', context)

@login_required
def carpenter_panel(request):
    if request.user.role != 'carpenter':
        return redirect('index')
    
    carpenter_profile = request.user.carpenter_profile
    projects = request.user.projects.all().order_by('-created_at')
    requests = request.user.received_requests.all().order_by('-created_at')

    if request.method == 'POST':
        action = request.POST.get('action')
        
        if action == 'update_profile':
            user = request.user
            user.first_name = request.POST.get('full_name')
            user.email = request.POST.get('email')
            user.phone = request.POST.get('phone')
            user.city = request.POST.get('city')
            user.save()
            messages.success(request, 'Perfil actualizado correctamente')
            return redirect('carpenter_panel')
            
        elif action == 'upload_project':
            title = request.POST.get('project_name')
            description = request.POST.get('project_description')
            price = request.POST.get('project_price', 0)
            image = request.FILES.get('project_image')
            
            Project.objects.create(
                carpenter=request.user,
                title=title,
                description=description,
                price=price,
                image=image
            )
            messages.success(request, 'Proyecto subido con éxito')
            return redirect('carpenter_panel')

    context = {
        'profile': carpenter_profile,
        'projects': projects,
        'requests': requests,
    }
    return render(request, 'core/carpenter_panel.html', context)
