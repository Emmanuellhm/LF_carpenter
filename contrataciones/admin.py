from django.contrib import admin
from .models import Notificacion, Interaccion


@admin.register(Notificacion)
class NotificacionAdmin(admin.ModelAdmin):
    list_display = ('user', 'message', 'is_read', 'created_at')
    list_filter = ('is_read', 'created_at')
    search_fields = ('user__username', 'message')


@admin.register(Interaccion)
class InteraccionAdmin(admin.ModelAdmin):
    list_display = ('user', 'carpenter', 'action', 'created_at')
    list_filter = ('action', 'created_at')
    search_fields = ('user__username', 'carpenter__user__username')
