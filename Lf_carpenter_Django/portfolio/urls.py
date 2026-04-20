from django.urls import path
from . import views

urlpatterns = [
    path('carpenters/', views.carpenter_list, name='carpenter_list'),
    path('projects/', views.project_list, name='project_list'),
    path('request-project/', views.request_project, name='request_project'),
]
