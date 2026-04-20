from django.db import models
from django.contrib.auth.models import AbstractUser

class User(AbstractUser):
    ROLE_CHOICES = (
        ('client', 'Cliente'),
        ('carpenter', 'Carpintero'),
        ('admin', 'Administrador'),
    )
    role = models.CharField(max_length=20, choices=ROLE_CHOICES, default='client')
    phone = models.CharField(max_length=15, blank=True, null=True)
    city = models.CharField(max_length=100, blank=True, null=True)

    def __str__(self):
        return f"{self.username} - {self.get_role_display()}"

class CarpenterProfile(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name='carpenter_profile')
    specialties = models.TextField(blank=True, null=True, help_text="Especialidades separadas por comas")
    years_of_experience = models.PositiveIntegerField(default=0)
    cv_file = models.FileField(upload_to='cvs/', blank=True, null=True)
    is_verified = models.BooleanField(default=False)
    is_approved = models.BooleanField(default=False)
    description = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Perfil Pro de {self.user.username}"
