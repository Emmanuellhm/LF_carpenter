"""
URL configuration for lf_carpinter project.
"""
from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static
from django.views.generic import TemplateView
from django.contrib.sitemaps.views import sitemap
from core.sitemaps import sitemaps

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('core.urls')),  # Home, Login, registro, etc.
    path('usuarios/', include('usuarios.urls', namespace='usuarios')),
    path('carpinteros/', include('carpinteros.urls', namespace='carpinteros')),
    path('proyectos/', include('proyectos.urls')),
    path('contrataciones/', include('contrataciones.urls')),
    path('chat/', include('chat.urls')),
    path('oauth/', include('social_django.urls', namespace='social')),
    
    # SEO
    path('sitemap.xml', sitemap, {'sitemaps': sitemaps}, name='django.contrib.sitemaps.views.sitemap'),
    path('robots.txt', TemplateView.as_view(template_name="robots.txt", content_type="text/plain")),
]

if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
    urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
