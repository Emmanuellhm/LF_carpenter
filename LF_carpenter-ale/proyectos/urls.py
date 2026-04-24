from django.urls import path
from . import views

app_name = 'proyectos'

urlpatterns = [
    path('solicitar/<int:carpintero_id>/', views.solicitar_proyecto, name='solicitar'),
]