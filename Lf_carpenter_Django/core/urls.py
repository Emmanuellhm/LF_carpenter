from django.urls import path
from . import views

urlpatterns = [
    path('', views.index, name='index'),
    path('register/user/', views.register_user, name='register_user'),
    path('register/carpenter/', views.register_carpenter, name='register_carpenter'),
    path('panel/user/', views.user_panel, name='user_panel'),
    path('panel/carpenter/', views.carpenter_panel, name='carpenter_panel'),
]
