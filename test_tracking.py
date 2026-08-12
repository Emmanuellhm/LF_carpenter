import os
import django

os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'lf_carpinter.settings')
django.setup()

from proyectos.models import SolicitudProyecto

solicitudes = SolicitudProyecto.objects.all()
if solicitudes.exists():
    s = solicitudes.first()
    print(f"Solicitud ID: {s.id}")
    print(f"User username: {s.user.username}")
else:
    print("No solicitudes found")
