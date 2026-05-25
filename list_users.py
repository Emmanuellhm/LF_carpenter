import os
import django

os.environ.setdefault("DJANGO_SETTINGS_MODULE", "lf_carpinter.settings")
django.setup()

from core.models import User
from carpinteros.models import Carpintero

print("All Users:")
for u in User.objects.all():
    print(f"ID: {u.id}, Email: {u.email}, Full Name: {u.full_name}, Role: {u.role}")

print("\nCarpinteros:")
for c in Carpintero.objects.all():
    print(f"ID: {c.id}, User ID: {c.user.id}, Name: {c.user.full_name}, Approved: {c.approved}")
