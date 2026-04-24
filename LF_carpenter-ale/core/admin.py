from django.contrib import admin
from django.contrib.auth.admin import UserAdmin as BaseUserAdmin
from .models import User


@admin.register(User)
class UserAdmin(BaseUserAdmin):
    list_display = ('username', 'email', 'role', 'is_active', 'created_at')
    list_filter = ('role', 'is_active', 'created_at')
    search_fields = ('username', 'email', 'full_name')
    fieldsets = BaseUserAdmin.fieldsets + (
        ('Información adicional', {'fields': ('phone', 'city', 'role')}),
    )
    add_fieldsets = BaseUserAdmin.add_fieldsets + (
        ('Información adicional', {'fields': ('phone', 'city', 'role')}),
    )
