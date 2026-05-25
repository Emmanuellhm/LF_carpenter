#!/usr/bin/env bash
# exit on error
set -o errexit

pip install -r requirements.txt

python manage.py collectstatic --no-input
python manage.py migrate

# Crear superusuario automáticamente si se configuran las variables
python manage.py shell -c "
import os
from django.contrib.auth import get_user_model
User = get_user_model()
email = os.environ.get('SUPERUSER_EMAIL')
password = os.environ.get('SUPERUSER_PASSWORD')
if email and password and not User.objects.filter(email=email).exists():
    user = User.objects.create_superuser(email=email, username=email, password=password, full_name='Administrador', role='admin')
    print('Superusuario creado exitosamente.')
"
