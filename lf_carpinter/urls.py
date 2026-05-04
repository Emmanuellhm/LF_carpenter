"""
URL configuration for lf_carpinter project.
"""
from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('core.urls')),  # Home, Login, registro, etc.
    path('usuarios/', include('usuarios.urls')),
    path('carpinteros/', include('carpinteros.urls')),
    path('proyectos/', include('proyectos.urls')),
    path('contrataciones/', include('contrataciones.urls')),
]

if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
    urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
