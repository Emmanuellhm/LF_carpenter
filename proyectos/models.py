from django.db import models
from core.models import User
from carpinteros.models import Carpintero


class SolicitudProyecto(models.Model):
    """Solicitudes de proyectos personalizados"""
    STATUS_CHOICES = (
        ('pending', 'Pendiente'),
        ('accepted', 'Aceptada'),
        ('rejected', 'Rechazada'),
        ('completed', 'Completada'),
    )

    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='solicitudes')
    carpenter = models.ForeignKey(Carpintero, on_delete=models.CASCADE, related_name='solicitudes_recibidas')
    title = models.CharField('Título del proyecto', max_length=255)
    description = models.TextField('Descripción del proyecto')
    budget = models.DecimalField('Presupuesto', max_digits=12, decimal_places=2, null=True, blank=True)
    deadline = models.DateField('Fecha límite', null=True, blank=True)
    dimensions = models.CharField('Dimensiones', max_length=255, blank=True)
    materials = models.CharField('Materiales preferidos', max_length=255, blank=True)
    reference_image = models.ImageField('Imagen de referencia', upload_to='solicitudes/', blank=True, null=True)
    contact_info = models.CharField('Información de contacto', max_length=255, blank=True)
    response_message = models.TextField('Mensaje de respuesta', blank=True)
    status = models.CharField('Estado', max_length=20, choices=STATUS_CHOICES, default='pending')
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    class Meta:
        verbose_name = 'Solicitud de proyecto'
        verbose_name_plural = 'Solicitudes de proyectos'
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.title} - {self.user.username} -> {self.carpenter.user.username}'


class Material(models.Model):
    """Materiales asociados a una solicitud de proyecto"""
    solicitud = models.ForeignKey(SolicitudProyecto, on_delete=models.CASCADE, related_name='materiales')
    name = models.CharField('Nombre del material', max_length=100)
    quantity = models.DecimalField('Cantidad', max_digits=10, decimal_places=2, null=True, blank=True)
    unit = models.CharField('Unidad', max_length=50, blank=True)
    cost = models.DecimalField('Costo', max_digits=10, decimal_places=2, null=True, blank=True)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    class Meta:
        verbose_name = 'Material'
        verbose_name_plural = 'Materiales'

    def __str__(self):
        return f'{self.name} - {self.quantity} {self.unit}'


class EstadoFabricacion(models.Model):
    """Registro de avance de fabricación para una solicitud de proyecto."""
    solicitud = models.ForeignKey(SolicitudProyecto, on_delete=models.CASCADE, related_name='fabricacion')
    status = models.CharField('Estado', max_length=20, choices=SolicitudProyecto.STATUS_CHOICES)
    notes = models.TextField('Notas', blank=True)
    photo = models.ImageField('Foto', upload_to='fabricacion/', blank=True, null=True)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)

    class Meta:
        verbose_name = 'Estado de fabricación'
        verbose_name_plural = 'Estados de fabricación'
        ordering = ['-created_at']

    def __str__(self):
        return f"{self.solicitud.title} - {self.get_status_display()}"
