import os
import django
from django.test import Client

os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'lf_carpinter.settings')
django.setup()

from core.models import User
user = User.objects.get(username='miguel@gmail.com')

client = Client()
client.force_login(user)

try:
    response = client.get('/usuarios/solicitud/13/tracking/')
except Exception as e:
    import traceback
    traceback.print_exc()
