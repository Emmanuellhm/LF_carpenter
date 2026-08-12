from django.db import models
from django.core.validators import MinValueValidator, MaxValueValidator
from core.models import User


class Carpintero(models.Model):
    """Perfil de carpintero con información profesional"""
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name='perfil_carpintero')
    specialties = models.CharField('Especialidades', max_length=255, blank=True)
    experience_years = models.IntegerField('Años de experiencia', default=0)
    description = models.TextField('Descripción', blank=True)
    portfolio_url = models.URLField('URL Portafolio', blank=True)
    hoja_vida = models.FileField('Hoja de vida', upload_to='cvs/', blank=True, null=True)
    is_verified = models.BooleanField('Verificado', default=False)
    approved = models.BooleanField('Aprobado', default=False)
    approved_at = models.DateTimeField('Fecha aprobación', null=True, blank=True)
    approved_by = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='carpinteros_aprobados')
    rating_avg = models.DecimalField('Calificación promedio', max_digits=3, decimal_places=1, null=True, blank=True)
    budget_range = models.DecimalField('Presupuesto base', max_digits=10, decimal_places=2, null=True, blank=True)
    created_at = models.DateTimeField('Fecha de registro', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    class Meta:
        verbose_name = 'Carpintero'
        verbose_name_plural = 'Carpinteros'

    def __str__(self):
        return f'{self.user.username} - {self.specialties or "Carpintero"}'

    @property
    def is_approved(self):
        return self.approved


class Portafolio(models.Model):
    """Proyectos/portafolio de un carpintero"""
    carpenter = models.ForeignKey(Carpintero, on_delete=models.CASCADE, related_name='portafolio')
    title = models.CharField('Título', max_length=255)
    description = models.TextField('Descripción')
    image = models.ImageField('Imagen', upload_to='portafolio/')
    price = models.DecimalField('Precio', max_digits=12, decimal_places=2, default=0)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)

    class Meta:
        verbose_name = 'Proyecto de portafolio'
        verbose_name_plural = 'Portafolios'

    def __str__(self):
        return self.title


class Comentario(models.Model):
    """Comentarios/calificaciones en proyectos del portafolio"""
    proyecto = models.ForeignKey(Portafolio, on_delete=models.CASCADE, related_name='comentarios')
    user = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True)
    comment = models.TextField('Comentario')
    rating = models.IntegerField('Calificación', default=5)
    created_at = models.DateTimeField('Fecha', auto_now_add=True)

    class Meta:
        verbose_name = 'Comentario'
        verbose_name_plural = 'Comentarios'
        ordering = ['-created_at']

    def __str__(self):
        return f'Comentario en {self.proyecto.title}'


class Certificacion(models.Model):
    """Certificaciones de carpinteros"""
    carpenter = models.ForeignKey(Carpintero, on_delete=models.CASCADE, related_name='certificaciones')
    name = models.CharField('Nombre', max_length=100)
    issuer = models.CharField('Emisor', max_length=100, blank=True)
    issued_date = models.DateField('Fecha de emisión', null=True, blank=True)
    verified = models.BooleanField('Verificada', default=False)
    created_at = models.DateTimeField('Fecha de creación', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    class Meta:
        verbose_name = 'Certificación'
        verbose_name_plural = 'Certificaciones'

    def __str__(self):
        return f'{self.name} - {self.carpenter.user.full_name}'


class Review(models.Model):
    """Reseñas de usuarios hacia carpinteros"""
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='reviews')
    carpenter = models.ForeignKey(Carpintero, on_delete=models.CASCADE, related_name='reviews')
    rating = models.IntegerField(
        'Calificación',
        validators=[MinValueValidator(1), MaxValueValidator(5)]
    )
    comment = models.TextField('Comentario', blank=True)
    created_at = models.DateTimeField('Fecha', auto_now_add=True)
    updated_at = models.DateTimeField('Fecha de actualización', auto_now=True)

    class Meta:
        verbose_name = 'Reseña'
        verbose_name_plural = 'Reseñas'
        ordering = ['-created_at']

    def __str__(self):
        return f'Reseña de {self.user.full_name} para {self.carpenter.user.full_name}'

