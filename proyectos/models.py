from django.db import models
from core.models import User
from carpinteros.models import Carpintero


class SolicitudProyecto(models.Model):
    """Solicitudes de proyectos personalizados"""
    STATUS_CHOICES = (
        ('pending',     'Nueva Solicitud'),
        ('budgeting',   'Cotizando'),
        ('accepted',    'Materiales / Inicio'),
        ('in_progress', 'En Taller'),
        ('finishing',   'Acabados'),
        ('ready',       'Listo para Entrega'),
        ('completed',   'Entregado'),
        ('rejected',    'Rechazada'),
    )
    PROJECT_TYPE_CHOICES = (
        ('custom', 'Encargo Personalizado'),
        ('purchase', 'Compra Directa'),
    )

    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='solicitudes')
    carpenter = models.ForeignKey(Carpintero, on_delete=models.CASCADE, related_name='solicitudes_recibidas')
    portafolio_item = models.ForeignKey('carpinteros.Portafolio', on_delete=models.SET_NULL, null=True, blank=True, related_name='compras_directas')
    title = models.CharField('Título del proyecto', max_length=255)
    description = models.TextField('Descripción del proyecto')
    budget = models.DecimalField('Presupuesto', max_digits=12, decimal_places=2, null=True, blank=True)
    deadline = models.DateField('Fecha límite', null=True, blank=True)
    dimensions = models.CharField('Dimensiones', max_length=255, blank=True)
    materials = models.CharField('Materiales preferidos', max_length=255, blank=True)
    reference_image = models.ImageField('Imagen de referencia', upload_to='solicitudes/', blank=True, null=True)
    contact_info = models.CharField('Información de contacto', max_length=255, blank=True)
    project_type = models.CharField('Tipo de proyecto', max_length=20, choices=PROJECT_TYPE_CHOICES, default='custom')
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


class CotizacionOficial(models.Model):
    """Cotización formal enviada por el carpintero al cliente"""
    solicitud = models.OneToOneField(SolicitudProyecto, on_delete=models.CASCADE, related_name='cotizacion')
    total_price = models.DecimalField('Precio Total', max_digits=12, decimal_places=2)
    estimated_days = models.PositiveIntegerField('Días estimados de entrega')
    blueprint_file = models.FileField('Plano o Boceto', upload_to='planos/', blank=True, null=True)
    details = models.TextField('Detalles y Especificaciones')
    is_accepted = models.BooleanField('Aceptada por el cliente', default=False)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)
    
    class Meta:
        verbose_name = 'Cotización Oficial'
        verbose_name_plural = 'Cotizaciones Oficiales'
        
    def __str__(self):
        return f'Cotización para: {self.solicitud.title}'


class EstadoFabricacion(models.Model):
    """Registro de la línea de tiempo de fabricación de un proyecto"""
    solicitud = models.ForeignKey(SolicitudProyecto, on_delete=models.CASCADE, related_name='estados_fabricacion')
    status = models.CharField('Estado', max_length=20, choices=SolicitudProyecto.STATUS_CHOICES)
    notes = models.TextField('Notas', blank=True)
    photo = models.ImageField('Foto de avance', upload_to='tracking/', blank=True, null=True)
    created_at = models.DateTimeField('Fecha de actualización', auto_now_add=True)
    
    class Meta:
        verbose_name = 'Estado de Fabricación'
        verbose_name_plural = 'Estados de Fabricación'
        ordering = ['created_at']
        
    def __str__(self):
        return f'{self.get_status_display()} - {self.solicitud.title}'

