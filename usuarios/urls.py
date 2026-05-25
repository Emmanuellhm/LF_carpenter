from django.urls import path
from . import views

app_name = 'usuarios'

urlpatterns = [
    path('panel/', views.panel_usuario, name='panel'),
    path('carpinteros/', views.ver_carpinteros, name='ver_carpinteros'),
    path('carpintero/<int:carpintero_id>/', views.ver_carpintero_detalle, name='ver_carpintero_detalle'),
    path('carpintero/<int:carpintero_id>/resena/', views.dejar_resena, name='dejar_resena'),
    path('mis-solicitudes/', views.mis_solicitudes, name='mis_solicitudes'),
    path('actualizar-perfil/', views.update_profile, name='update_profile'),
    path('historial/', views.historial, name='historial'),
    path('solicitud/<int:solicitud_id>/tracking/', views.tracking_proyecto, name='tracking_proyecto'),
]
