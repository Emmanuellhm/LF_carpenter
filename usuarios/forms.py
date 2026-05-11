from django import forms
from core.models import User

class UserProfileForm(forms.ModelForm):
    class Meta:
        model = User
        fields = ['full_name', 'phone', 'city']
        widgets = {
            'full_name': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
            'phone': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
            'city': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
        }
