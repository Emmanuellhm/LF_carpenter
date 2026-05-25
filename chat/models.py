from django.db import models
from django.conf import settings
from proyectos.models import SolicitudProyecto

class ChatRoom(models.Model):
    solicitud = models.OneToOneField(SolicitudProyecto, on_delete=models.CASCADE, related_name='chat_room')
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Chat Sala - Solicitud #{self.solicitud.id}"

class Message(models.Model):
    room = models.ForeignKey(ChatRoom, on_delete=models.CASCADE, related_name='messages')
    sender = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)
    content = models.TextField()
    timestamp = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ['timestamp']

    def __str__(self):
        return f"De {self.sender.full_name} en {self.room}: {self.content[:20]}"
