from django.db import models
from core.models import User
from carpinteros.models import Carpintero


class Notificacion(models.Model):
    """Notificaciones del sistema"""
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='notificaciones')
    message = models.TextField('Mensaje')
    is_read = models.BooleanField('Leída', default=False)
    created_at = models.DateTimeField('Fecha', auto_now_add=True)

    class Meta:
        verbose_name = 'Notificación'
        verbose_name_plural = 'Notificaciones'
        ordering = ['-created_at']

    def __str__(self):
        return f'Notificación para {self.user.username}'


class Interaccion(models.Model):
    """Registro de interacciones de usuarios con carpinteros"""
    ACTION_CHOICES = (
        ('viewed', 'Visualizó perfil'),
        ('contacted', 'Contactó'),
        ('saved', 'Guardó perfil'),
    )

    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='interacciones')
    carpenter = models.ForeignKey(Carpintero, on_delete=models.CASCADE, related_name='interacciones')
    action = models.CharField('Acción', max_length=20, choices=ACTION_CHOICES)
    created_at = models.DateTimeField('Fecha', auto_now_add=True)

    class Meta:
        verbose_name = 'Interacción'
        verbose_name_plural = 'Interacciones'
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.user.username} - {self.action} - {self.carpenter.user.username}'


class ActivityLog(models.Model):
    """Registro de actividad del sistema"""
    user = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='activity_logs')
    action_type = models.CharField('Tipo de acción', max_length=100)
    description = models.TextField('Descripción', blank=True)
    ip_address = models.GenericIPAddressField('Dirección IP', null=True, blank=True)
    user_agent = models.TextField('User Agent', blank=True)
    created_at = models.DateTimeField('Fecha', auto_now_add=True)

    class Meta:
        verbose_name = 'Registro de actividad'
        verbose_name_plural = 'Registros de actividad'
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.action_type} - {self.user}'


class FailedLogin(models.Model):
    """Registro de intentos fallidos de login"""
    user = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='failed_logins')
    email_attempted = models.CharField('Email intentado', max_length=100, blank=True)
    ip_address = models.GenericIPAddressField('Dirección IP', null=True, blank=True)
    user_agent = models.TextField('User Agent', blank=True)
    fail_reason = models.CharField('Motivo del fallo', max_length=100, blank=True)
    attempt_time = models.DateTimeField('Hora del intento', auto_now_add=True)

    class Meta:
        verbose_name = 'Login fallido'
        verbose_name_plural = 'Logins fallidos'
        ordering = ['-attempt_time']

    def __str__(self):
        return f'Intento fallido - {self.email_attempted}'


class Traceability(models.Model):
    """Trazabilidad de acciones en el sistema"""
    AUTHORITY_CHOICES = (
        ('user', 'Usuario'),
        ('carpenter', 'Carpintero'),
        ('admin', 'Administrador'),
    )

    action_type = models.CharField('Tipo de acción', max_length=100)
    performed_by = models.ForeignKey(User, on_delete=models.CASCADE, related_name='actions_performed')
    affected_user = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='actions_received')
    affected_table = models.CharField('Tabla afectada', max_length=50, blank=True)
    affected_id = models.IntegerField('ID afectado', null=True, blank=True)
    old_value = models.TextField('Valor anterior', blank=True)
    new_value = models.TextField('Valor nuevo', blank=True)
    authority_level = models.CharField('Nivel de autoridad', max_length=20, choices=AUTHORITY_CHOICES, default='user')
    created_at = models.DateTimeField('Fecha', auto_now_add=True)

    class Meta:
        verbose_name = 'Trazabilidad'
        verbose_name_plural = 'Trazabilidad'
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.action_type} por {self.performed_by}'


class UserBehavior(models.Model):
    """Registro de comportamiento del usuario"""
    BEHAVIOR_CHOICES = (
        ('view_profile', 'Ver perfil'),
        ('view_portfolio', 'Ver portafolio'),
        ('send_request', 'Enviar solicitud'),
        ('leave_review', 'Dejar reseña'),
        ('search', 'Búsqueda'),
        ('filter', 'Filtro'),
        ('click_whatsapp', 'Click WhatsApp'),
        ('visit_certified', 'Visitar certificado'),
    )
    TARGET_CHOICES = (
        ('carpenter', 'Carpintero'),
        ('portfolio', 'Portafolio'),
        ('request', 'Solicitud'),
        ('review', 'Reseña'),
        ('search', 'Búsqueda'),
    )

    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='behaviors')
    behavior_type = models.CharField('Tipo de comportamiento', max_length=30, choices=BEHAVIOR_CHOICES)
    target_type = models.CharField('Tipo objetivo', max_length=20, choices=TARGET_CHOICES)
    target_id = models.IntegerField('ID objetivo', null=True, blank=True)
    action_data = models.JSONField('Datos de acción', null=True, blank=True)
    duration_seconds = models.IntegerField('Duración (s)', null=True, blank=True)
    occurred_at = models.DateTimeField('Fecha', auto_now_add=True)

    class Meta:
        verbose_name = 'Comportamiento de usuario'
        verbose_name_plural = 'Comportamientos de usuario'
        ordering = ['-occurred_at']

    def __str__(self):
        return f'{self.user.username} - {self.behavior_type}'


class UserPreference(models.Model):
    """Preferencias de usuario"""
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name='preferences')
    preferred_materials = models.CharField('Materiales preferidos', max_length=255, blank=True)
    preferred_styles = models.CharField('Estilos preferidos', max_length=255, blank=True)
    notifications_enabled = models.BooleanField('Notificaciones habilitadas', default=True)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    class Meta:
        verbose_name = 'Preferencia de usuario'
        verbose_name_plural = 'Preferencias de usuario'

    def __str__(self):
        return f'Preferencias de {self.user.username}'

