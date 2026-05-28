from django.test import TestCase
from core.models import User
from carpinteros.models import Carpintero
from contrataciones.models import Solicitud
from chat.models import ChatRoom, Message

class ChatModelsTestCase(TestCase):
    def setUp(self):
        self.client_user = User.objects.create_user(
            email='client@example.com', password='testpassword123', full_name='Test Client', role='client'
        )
        self.carpenter_user = User.objects.create_user(
            email='carpintero@example.com', password='testpassword123', full_name='Test Carpenter', role='carpenter'
        )
        self.carpenter = Carpintero.objects.create(
            user=self.carpenter_user, years_experience=5, is_approved=True
        )
        
        self.solicitud = Solicitud.objects.create(
            cliente=self.client_user,
            carpintero=self.carpenter,
            title='Mesa de centro',
            description='Mesa de roble 120x60cm'
        )

    def test_chat_room_creation(self):
        room = ChatRoom.objects.create(solicitud=self.solicitud)
        self.assertEqual(room.solicitud.title, 'Mesa de centro')
        self.assertEqual(str(room), f"Chat para {self.solicitud.title}")

    def test_message_creation(self):
        room = ChatRoom.objects.create(solicitud=self.solicitud)
        msg = Message.objects.create(
            room=room,
            sender=self.client_user,
            content='Hola, ¿cuándo podrías empezar?'
        )
        self.assertEqual(msg.content, 'Hola, ¿cuándo podrías empezar?')
        self.assertEqual(msg.sender, self.client_user)
