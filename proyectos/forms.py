from django import forms
from .models import SolicitudProyecto

class SolicitudProyectoForm(forms.ModelForm):
    class Meta:
        model = SolicitudProyecto
        fields = ['title', 'description', 'budget', 'deadline', 'dimensions', 'materials', 'reference_image', 'contact_info']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300', 'placeholder': 'Ej. Comedor rústico de 6 puestos'}),
            'description': forms.Textarea(attrs={'class': 'form-textarea w-full rounded-md border-gray-300', 'rows': 4, 'placeholder': 'Describe detalladamente lo que necesitas...'}),
            'budget': forms.NumberInput(attrs={'class': 'form-input w-full rounded-md border-gray-300', 'placeholder': 'Presupuesto estimado'}),
            'deadline': forms.DateInput(attrs={'class': 'form-input w-full rounded-md border-gray-300', 'type': 'date'}),
            'dimensions': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300', 'placeholder': 'Ej. 2m x 1m x 0.8m'}),
            'materials': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300', 'placeholder': 'Ej. Madera de roble, acero inoxidable'}),
            'contact_info': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300', 'placeholder': 'Teléfono o email adicional'}),
        }
