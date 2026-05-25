from django.urls import path
from . import views

app_name = 'chat'

urlpatterns = [
    path('iniciar/<int:solicitud_id>/', views.abrir_chat, name='abrir_chat'),
    path('sala/<int:room_id>/', views.sala_chat, name='sala_chat'),
]
