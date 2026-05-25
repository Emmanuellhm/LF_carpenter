from django.contrib import admin
from .models import SolicitudProyecto


@admin.register(SolicitudProyecto)
class SolicitudProyectoAdmin(admin.ModelAdmin):
    list_display = ('title', 'user', 'carpenter', 'status', 'budget', 'created_at')
    list_filter = ('status', 'created_at')
    search_fields = ('title', 'description', 'user__username', 'carpenter__user__username')
    readonly_fields = ('created_at', 'updated_at')
    actions = ['marcar_como_aceptada', 'marcar_como_rechazada', 'marcar_como_completada']

    def marcar_como_aceptada(self, request, queryset):
        queryset.update(status='accepted')
        self.message_user(request, 'Solicitudes marcadas como aceptadas.')
    marcar_como_aceptada.short_description = 'Marcar como aceptada'

    def marcar_como_rechazada(self, request, queryset):
        queryset.update(status='rejected')
        self.message_user(request, 'Solicitudes marcadas como rechazadas.')
    marcar_como_rechazada.short_description = 'Marcar como rechazada'

    def marcar_como_completada(self, request, queryset):
        queryset.update(status='completed')
        self.message_user(request, 'Solicitudes marcadas como completadas.')
    marcar_como_completada.short_description = 'Marcar como completada'
