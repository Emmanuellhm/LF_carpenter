import os
import django

os.environ.setdefault("DJANGO_SETTINGS_MODULE", "lf_carpinter.settings")
django.setup()

from core.models import User
from carpinteros.models import Carpintero

ids_to_keep = [101, 103, 133]

users_to_delete = User.objects.exclude(id__in=ids_to_keep)
count, deleted_info = users_to_delete.delete()

print(f"Deleted {count} objects.")
print(deleted_info)

