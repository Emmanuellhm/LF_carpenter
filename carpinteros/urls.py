from django.urls import path
from . import views

app_name = 'carpinteros'

urlpatterns = [
    path('panel/', views.panel_carpintero, name='panel'),
    path('proyectos/', views.mis_proyectos, name='mis_proyectos'),
    path('proyectos/subir/', views.subir_proyecto, name='subir_proyecto'),
    path('proyectos/<int:proyecto_id>/editar/', views.editar_proyecto, name='editar_proyecto'),
    path('proyectos/<int:proyecto_id>/eliminar/', views.eliminar_proyecto, name='eliminar_proyecto'),
    path('solicitudes/', views.ver_solicitudes, name='ver_solicitudes'),
    path('solicitudes/<int:solicitud_id>/', views.detalle_solicitud, name='detalle_solicitud'),
    path('notificaciones/', views.notificaciones, name='notificaciones'),
    path('editar-perfil/', views.editar_perfil, name='editar_perfil'),
]