from django.test import TestCase
from django.urls import reverse
from core.models import User
from carpinteros.models import Carpintero

class CoreViewsTestCase(TestCase):
    def setUp(self):
        # Create a test user
        self.user = User.objects.create_user(
            email='test@example.com',
            password='testpassword123',
            full_name='Test User',
            role='client'
        )
        
        # Create a test carpenter
        self.carpenter_user = User.objects.create_user(
            email='carpintero@example.com',
            password='testpassword123',
            full_name='Test Carpenter',
            role='carpenter'
        )
        self.carpenter = Carpintero.objects.create(
            user=self.carpenter_user,
            years_experience=5,
            is_approved=True
        )

    def test_home_view_status_code(self):
        response = self.client.get(reverse('home'))
        self.assertEqual(response.status_code, 200)

    def test_explorar_view_status_code(self):
        response = self.client.get(reverse('explorar_carpinteros'))
        self.assertEqual(response.status_code, 200)

    def test_login_view_status_code(self):
        response = self.client.get(reverse('login'))
        self.assertEqual(response.status_code, 200)
        
    def test_perfil_publico_status_code(self):
        response = self.client.get(reverse('perfil_publico_carpintero', args=[self.carpenter.id]))
        self.assertEqual(response.status_code, 200)
