from django.contrib import admin
from .models import Carpintero, Portafolio, Comentario


@admin.register(Carpintero)
class CarpinteroAdmin(admin.ModelAdmin):
    list_display = ('user', 'specialties', 'experience_years', 'is_verified', 'approved', 'created_at')
    list_filter = ('approved', 'is_verified', 'created_at')
    search_fields = ('user__username', 'user__email', 'specialties')
    readonly_fields = ('created_at', 'updated_at')
    actions = ['aprobar_carpinteros', 'rechazar_carpinteros']

    def aprobar_carpinteros(self, request, queryset):
        queryset.update(approved=True, is_verified=True)
        self.message_user(request, f'{queryset.count()} carpinteros aprobados.')
    aprobar_carpinteros.short_description = 'Aprobar carpinteros seleccionados'

    def rechazar_carpinteros(self, request, queryset):
        queryset.update(approved=False)
        self.message_user(request, f'{queryset.count()} carpinteros rechazados.')
    rechazar_carpinteros.short_description = 'Rechazar carpinteros seleccionados'


@admin.register(Portafolio)
class PortafolioAdmin(admin.ModelAdmin):
    list_display = ('title', 'carpenter', 'price', 'created_at')
    list_filter = ('created_at',)
    search_fields = ('title', 'description', 'carpenter__user__username')


@admin.register(Comentario)
class ComentarioAdmin(admin.ModelAdmin):
    list_display = ('proyecto', 'user', 'rating', 'created_at')
    list_filter = ('rating', 'created_at')
    search_fields = ('comment', 'user__username')
