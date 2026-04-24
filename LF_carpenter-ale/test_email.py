import os
import django
from dotenv import load_dotenv

load_dotenv()

os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'lf_carpinter.settings')
django.setup()

from django.core.mail import send_mail

try:
    send_mail(
        'Test Subject',
        'Test Message',
        os.environ.get('DEFAULT_FROM_EMAIL', 'test@test.com'),
        ['test@example.com'],
        fail_silently=False,
    )
    print("Email sent successfully")
except Exception as e:
    print("Error sending email:")
    print(e)
