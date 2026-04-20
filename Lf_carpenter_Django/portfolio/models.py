from django.db import models
from django.conf import settings

class Project(models.Model):
    carpenter = models.ForeignKey(
        settings.AUTH_USER_MODEL, 
        on_delete=models.CASCADE, 
        limit_choices_to={'role': 'carpenter'},
        related_name='projects'
    )
    title = models.CharField(max_length=255)
    description = models.TextField()
    image = models.ImageField(upload_to='portfolio/')
    price = models.DecimalField(max_digits=12, decimal_places=2, default=0)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.title} - {self.carpenter.username}"

class Comment(models.Model):
    project = models.ForeignKey(Project, on_delete=models.CASCADE, related_name='comments')
    user = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)
    text = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Comentario de {self.user.username} en {self.project.title}"

class ProjectRequest(models.Model):
    STATUS_CHOICES = (
        ('pending', 'Pendiente'),
        ('accepted', 'Aceptado'),
        ('in_progress', 'En Progreso'),
        ('ready', 'Listo para entrega'),
        ('completed', 'Completado'),
        ('rejected', 'Rechazado'),
    )
    client = models.ForeignKey(
        settings.AUTH_USER_MODEL, 
        on_delete=models.CASCADE, 
        limit_choices_to={'role': 'client'},
        related_name='sent_requests'
    )
    carpenter = models.ForeignKey(
        settings.AUTH_USER_MODEL, 
        on_delete=models.CASCADE, 
        limit_choices_to={'role': 'carpenter'},
        related_name='received_requests'
    )
    description = models.TextField()
    contact_info = models.CharField(max_length=255, blank=True, null=True)
    
    # Nuevos campos para coincidir con el formulario
    budget = models.DecimalField(max_digits=12, decimal_places=2, blank=True, null=True)
    deadline = models.DateField(blank=True, null=True)
    reference_image = models.ImageField(upload_to='requests/', blank=True, null=True)
    dimensions = models.CharField(max_length=255, blank=True, null=True)
    materials = models.TextField(blank=True, null=True)

    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='pending')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"Solicitud de {self.client.username} a {self.carpenter.username} ({self.get_status_display()})"
