from channels.layers import get_channel_layer
from asgiref.sync import async_to_sync
from core.models import Notification

def crear_notificacion(user, title, message, link='', notification_type='system'):
    """
    Crea una notificación en BD y la envía en tiempo real vía WebSocket.
    """
    notif = Notification.objects.create(
        user=user,
        title=title,
        message=message,
        link=link,
        notification_type=notification_type
    )
    
    channel_layer = get_channel_layer()
    if channel_layer:
        # Preparamos los datos para enviar al frontend
        data = {
            'id': notif.id,
            'title': notif.title,
            'message': notif.message,
            'link': notif.link,
            'type': notif.notification_type,
            # Se usa un formato genérico que el JS o la plantilla puedan usar
            'created_at': 'Justo ahora',
        }
        
        async_to_sync(channel_layer.group_send)(
            f"user_{user.id}_notifications",
            {
                "type": "notification_message", # Must match consumer method name replacing underscores if needed (actually it maps exactly)
                "data": data
            }
        )
    return notif
