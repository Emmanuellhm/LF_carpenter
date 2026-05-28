# pyrefly: ignore [missing-import]
from django import forms
from core.models import User

class UserProfileForm(forms.ModelForm):
    class Meta:
        model = User
        fields = ['full_name', 'phone', 'city', 'avatar']
        widgets = {
            'full_name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Nombre completo'}),
            'phone': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Teléfono'}),
            'city': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Ciudad'}),
            'avatar': forms.ClearableFileInput(attrs={'class': 'form-control', 'accept': 'image/*'}),
        }
