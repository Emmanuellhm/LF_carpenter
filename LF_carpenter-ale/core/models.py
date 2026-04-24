from django.contrib.auth.models import AbstractUser
from django.db import models

from django.core.validators import RegexValidator

class User(AbstractUser):
    """Modelo de usuario personalizado para LF Carpinter"""
    ROLE_CHOICES = (
        ('user', 'Usuario'),
        ('carpenter', 'Carpintero'),
        ('admin', 'Administrador'),
    )

    email = models.EmailField('Correo electrónico', unique=True)
    full_name = models.CharField(
        'Nombre completo', 
        max_length=255,
        validators=[
            RegexValidator(
                regex=r'^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$',
                message='El nombre solo puede contener letras y espacios.'
            )
        ]
    )
    phone = models.CharField('Teléfono', max_length=20, blank=True)
    city = models.CharField('Ciudad', max_length=100, blank=True)
    role = models.CharField('Rol', max_length=20, choices=ROLE_CHOICES, default='user')
    is_active = models.BooleanField('Activo', default=True)
    created_at = models.DateTimeField('Fecha de registro', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    # Hacer que username no sea requerido (usamos email como identificador)
    username = models.CharField(
        'Nombre de usuario',
        max_length=150,
        unique=True,
        blank=True,
        null=True,
        help_text='Nombre de usuario (opcional)'
    )

    # Campo para autenticación con email
    USERNAME_FIELD = 'email'
    REQUIRED_FIELDS = ['full_name', 'username']

    class Meta:
        verbose_name = 'Usuario'
        verbose_name_plural = 'Usuarios'
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.full_name} ({self.email})'

    def save(self, *args, **kwargs):
        # Si no hay username, usar el email como username
        if not self.username:
            self.username = self.email
        super().save(*args, **kwargs)

    @property
    def is_carpenter(self):
        return self.role == 'carpenter'

    @property
    def is_admin(self):
        return self.role == 'admin' or self.is_superuser