import json
from channels.generic.websocket import AsyncWebsocketConsumer
from channels.db import database_sync_to_async
from .models import ChatRoom, Message
from django.contrib.auth import get_user_model

User = get_user_model()

class ChatConsumer(AsyncWebsocketConsumer):
    async def connect(self):
        self.room_name = self.scope['url_route']['kwargs']['room_name']
        self.room_group_name = f'chat_{self.room_name}'
        self.user = self.scope['user']

        if self.user.is_anonymous:
            await self.close()
            return

        # Verificar si la sala existe y el usuario tiene acceso
        has_access = await self.check_room_access(self.room_name, self.user)
        if not has_access:
            await self.close()
            return

        # Join room group
        await self.channel_layer.group_add(
            self.room_group_name,
            self.channel_name
        )

        await self.accept()

    async def disconnect(self, close_code):
        if hasattr(self, 'room_group_name'):
            # Leave room group
            await self.channel_layer.group_discard(
                self.room_group_name,
                self.channel_name
            )

    # Receive message from WebSocket
    async def receive(self, text_data):
        text_data_json = json.loads(text_data)
        message = text_data_json.get('message', '')

        if message.strip() == '':
            return

        # Save to database
        saved_msg = await self.save_message(self.room_name, self.user, message)

        # Send message to room group
        await self.channel_layer.group_send(
            self.room_group_name,
            {
                'type': 'chat_message',
                'message': message,
                'sender': self.user.full_name,
                'sender_id': self.user.id,
                'timestamp': saved_msg.timestamp.strftime('%H:%M')
            }
        )

        # Enviar notificación al destinatario (si no está en la sala, recibirá la alerta)
        await self.notify_recipient(self.room_name, self.user, message)

    # Receive message from room group
    async def chat_message(self, event):
        message = event['message']
        sender = event['sender']
        sender_id = event['sender_id']
        timestamp = event['timestamp']

        # Send message to WebSocket
        await self.send(text_data=json.dumps({
            'message': message,
            'sender': sender,
            'sender_id': sender_id,
            'timestamp': timestamp
        }))

    @database_sync_to_async
    def check_room_access(self, room_id, user):
        try:
            room = ChatRoom.objects.select_related('solicitud__user', 'solicitud__carpenter__user').get(id=room_id)
            # El usuario debe ser el cliente o el carpintero de la solicitud
            is_client = user == room.solicitud.user
            is_carpenter = user == room.solicitud.carpenter.user
            return is_client or is_carpenter
        except ChatRoom.DoesNotExist:
            return False

    @database_sync_to_async
    def save_message(self, room_id, user, content):
        room = ChatRoom.objects.get(id=room_id)
        return Message.objects.create(room=room, sender=user, content=content)

    @database_sync_to_async
    def notify_recipient(self, room_id, sender, message_text):
        """Crea una notificación en BD y empuja el evento al canal WS del destinatario."""
        from core.utils.notifications import crear_notificacion
        try:
            room = ChatRoom.objects.select_related(
                'solicitud__user', 'solicitud__carpenter__user'
            ).get(id=room_id)

            # Determinar quién es el destinatario (el que NO está enviando)
            solicitud = room.solicitud
            recipient = solicitud.user if sender == solicitud.carpenter.user else solicitud.carpenter.user

            preview = message_text[:60] + ('…' if len(message_text) > 60 else '')

            crear_notificacion(
                user=recipient,
                title=f"💬 Nuevo mensaje de {sender.full_name}",
                message=preview,
                link=f"/chat/sala/{room_id}/",
                notification_type='message'
            )
        except Exception:
            pass  # No bloquear el chat si la notificación falla
