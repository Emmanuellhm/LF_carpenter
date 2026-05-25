from .models import Notification

def notificaciones_globales(request):
    """
    Context processor para inyectar notificaciones globales en todas las plantillas.
    """
    if request.user.is_authenticated:
        # Obtenemos el conteo de no leídas
        unread_count = request.user.notifications.filter(is_read=False).count()
        # Obtenemos las últimas 5 notificaciones
        latest_notifications = request.user.notifications.order_by('-created_at')[:5]
        return {
            'notificaciones_unread_count': unread_count,
            'notificaciones_latest': latest_notifications
        }
    return {
        'notificaciones_unread_count': 0,
        'notificaciones_latest': []
    }
