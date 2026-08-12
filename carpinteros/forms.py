from django import forms
from .models import Carpintero, Portafolio


INPUT_CSS = 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition bg-white'


class CarpinteroPerfilForm(forms.ModelForm):
    """Formulario para que el carpintero edite su propio perfil"""

    class Meta:
        model = Carpintero
        fields = ['specialties', 'experience_years', 'description', 'portfolio_url', 'budget_range', 'hoja_vida']
        labels = {
            'specialties': 'Especialidades',
            'experience_years': 'Años de experiencia',
            'description': 'Descripción profesional',
            'portfolio_url': 'URL Portafolio externo (opcional)',
            'budget_range': 'Presupuesto base (COP)',
            'hoja_vida': 'Hoja de Vida (PDF)',
        }
        widgets = {
            'specialties': forms.TextInput(attrs={
                'class': INPUT_CSS,
                'placeholder': 'Ej: Muebles a medida, puertas, escaleras...'
            }),
            'experience_years': forms.NumberInput(attrs={
                'class': INPUT_CSS,
                'placeholder': '0',
                'min': 0,
                'max': 60,
            }),
            'description': forms.Textarea(attrs={
                'class': INPUT_CSS,
                'placeholder': 'Cuéntales a tus clientes sobre tu experiencia, tu proceso de trabajo y tu pasión por la madera...',
                'rows': 5,
            }),
            'portfolio_url': forms.URLInput(attrs={
                'class': INPUT_CSS,
                'placeholder': 'https://tu-portafolio.com'
            }),
            'budget_range': forms.NumberInput(attrs={
                'class': INPUT_CSS,
                'placeholder': 'Ej: 500000',
                'min': 0,
            }),
            'hoja_vida': forms.FileInput(attrs={
                'class': 'hidden',
                'accept': '.pdf,.doc,.docx',
                'id': 'hoja_vida_input',
            }),
        }


class PortafolioForm(forms.ModelForm):
    """Formulario para subir/editar proyectos al portafolio"""

    class Meta:
        model = Portafolio
        fields = ['title', 'description', 'image', 'price']
        labels = {
            'title': 'Nombre del proyecto',
            'description': 'Descripción del trabajo',
            'image': 'Fotografía del proyecto',
            'price': 'Precio aproximado (COP)',
        }
        widgets = {
            'title': forms.TextInput(attrs={
                'class': INPUT_CSS,
                'placeholder': 'Ej: Comedor familiar en roble macizo'
            }),
            'description': forms.Textarea(attrs={
                'class': INPUT_CSS,
                'placeholder': 'Describe los materiales, técnicas y tiempo invertido...',
                'rows': 4,
            }),
            'price': forms.NumberInput(attrs={
                'class': INPUT_CSS + ' pl-16',
                'placeholder': 'Ej: 2500000',
                'min': 0,
            }),
            'image': forms.FileInput(attrs={
                'class': 'absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50',
                'accept': 'image/*',
                'id': 'proyecto_imagen_input',
            }),
        }
