from django.db.models.signals import post_save, post_delete
from django.dispatch import receiver
from django.db.models import Avg
from .models import Review

@receiver(post_save, sender=Review)
@receiver(post_delete, sender=Review)
def update_carpintero_rating(sender, instance, **kwargs):
    """
    Actualiza el promedio de calificación de un carpintero
    cuando se crea, edita o elimina una reseña.
    """
    carpintero = instance.carpenter
    
    # Calcular el nuevo promedio usando aggregation
    promedio = Review.objects.filter(carpenter=carpintero).aggregate(Avg('rating'))['rating__avg']
    
    # Actualizar el carpintero (si es None, se asigna None)
    carpintero.rating_avg = promedio
    carpintero.save(update_fields=['rating_avg'])
