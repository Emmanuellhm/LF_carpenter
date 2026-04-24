from django import forms
from .models import Carpintero

class CarpinteroProfileForm(forms.ModelForm):
    """Formulario para que el carpintero edite su propio perfil"""
    class Meta:
        model = Carpintero
        fields = ['specialties', 'experience_years', 'description', 'portfolio_url', 'hoja_vida']
        widgets = {
            'specialties': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition',
                'placeholder': 'Ej. Muebles de cocina, Restauración...'
            }),
            'experience_years': forms.NumberInput(attrs={
                'class': 'w-full px-4 py-3 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition',
                'min': '0'
            }),
            'description': forms.Textarea(attrs={
                'class': 'w-full px-4 py-3 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition',
                'rows': 5,
                'placeholder': 'Cuéntale a los clientes sobre ti, tus métodos de trabajo...'
            }),
            'portfolio_url': forms.URLInput(attrs={
                'class': 'w-full px-4 py-3 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition',
                'placeholder': 'https://...'
            }),
            'hoja_vida': forms.FileInput(attrs={
                'class': 'w-full px-4 py-3 rounded-xl border border-stone-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition cursor-pointer'
            })
        }
