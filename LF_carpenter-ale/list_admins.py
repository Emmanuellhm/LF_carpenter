import os
import django

os.environ.setdefault("DJANGO_SETTINGS_MODULE", "lf_carpinter.settings")
django.setup()

from core.models import User

print("Superusers/Admins:")
for u in User.objects.filter(role='admin') | User.objects.filter(is_superuser=True):
    print(f"ID: {u.id}, Email: {u.email}, Full Name: {u.full_name}, Role: {u.role}, is_superuser: {u.is_superuser}")

print("\nUsers matching 'miguel':")
for u in User.objects.filter(email__icontains='miguel'):
    print(f"ID: {u.id}, Email: {u.email}, Full Name: {u.full_name}, Role: {u.role}")
