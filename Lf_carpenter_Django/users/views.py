from django.shortcuts import render, redirect
from django.contrib.auth import login
from .forms import ClientRegistrationForm, CarpenterRegistrationForm

def register_user(request):
    if request.method == 'POST':
        form = ClientRegistrationForm(request.POST)
        if form.is_valid():
            user = form.save()
            login(request, user)
            return redirect('user_panel')
    else:
        form = ClientRegistrationForm()
    return render(request, 'core/register_user.html', {'form': form})

def register_carpenter(request):
    if request.method == 'POST':
        form = CarpenterRegistrationForm(request.POST, request.FILES)
        if form.is_valid():
            user = form.save()
            # No logeamos automáticamente al carpintero si requiere aprobación (opcional)
            # login(request, user)
            return render(request, 'core/register_carpenter.html', {'success': True})
    else:
        form = CarpenterRegistrationForm()
    return render(request, 'core/register_carpenter.html', {'form': form})
