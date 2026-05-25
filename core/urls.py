#
# pyrefly: ignore [missing-import]
from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name='home'),
    path('login/', views.login_view, name='login'),
    path('logout/', views.logout_view, name='logout'),
    path('registro/', views.registro_usuario, name='registro_usuario'),
    path('registro/carpintero/', views.registro_carpintero, name='registro_carpintero'),
    path('registro/enviado/', views.registro_enviado, name='registro_enviado'),
    path('recuperar-contrasena/', views.recuperar_contrasena, name='recuperar_contrasena'),
    path('restablecer-contrasena/<uidb64>/<token>/', views.restablecer_contrasena, name='restablecer_contrasena'),
    path('confirmar-email/<uidb64>/<token>/', views.confirmar_email, name='confirmar_email'),
    path('contactanos/', views.contactanos, name='contactanos'),
    path('admin-dashboard/', views.admin_dashboard, name='admin_dashboard'),
    path('sobre-nosotros/', views.sobre_nosotros, name='sobre_nosotros'),
    path('faq/', views.faq, name='faq'),
    path('terminos/', views.terminos, name='terminos'),
    # Catálogo público (RF10)
    path('explorar/', views.explorar_carpinteros, name='explorar_carpinteros'),
    path('carpintero/<int:carpintero_id>/', views.perfil_publico_carpintero, name='perfil_publico_carpintero'),
    # Onboarding Social
    path('completar-registro-social/', views.completar_registro_social, name='completar_registro_social'),
    path('api/notificaciones/marcar-leida/<int:notif_id>/', views.marcar_notificacion_leida, name='marcar_notificacion_leida'),
]
