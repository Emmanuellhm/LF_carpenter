from django.contrib.auth.models import AbstractUser
from django.db import models


class User(AbstractUser):
    """Modelo de usuario personalizado para LF Carpinter"""
    ROLE_CHOICES = (
        ('user', 'Usuario'),
        ('carpenter', 'Carpintero'),
        ('admin', 'Administrador'),
    )

    email = models.EmailField('Correo electrónico', unique=True)
    full_name = models.CharField('Nombre completo', max_length=255)
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


class Notification(models.Model):
    """Modelo para manejar notificaciones globales del sistema"""
    TYPE_CHOICES = (
        ('tracking', 'Actualización de Tracking'),
        ('quote', 'Nueva Cotización'),
        ('message', 'Nuevo Mensaje'),
        ('system', 'Aviso del Sistema'),
    )
    
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='notifications', verbose_name='Usuario')
    title = models.CharField('Título', max_length=255)
    message = models.TextField('Mensaje')
    link = models.CharField('Enlace de redirección', max_length=255, blank=True, help_text='Ruta URL donde ir al hacer clic')
    notification_type = models.CharField('Tipo', max_length=20, choices=TYPE_CHOICES, default='system')
    is_read = models.BooleanField('Leída', default=False)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)

    class Meta:
        verbose_name = 'Notificación'
        verbose_name_plural = 'Notificaciones'
        ordering = ['-created_at']

    def __str__(self):
        return f"{self.title} - {self.user.email}"