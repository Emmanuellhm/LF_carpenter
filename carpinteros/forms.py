from django import forms
from .models import Portafolio, Carpintero
from core.models import User

class PortafolioForm(forms.ModelForm):
    class Meta:
        model = Portafolio
        fields = ['title', 'description', 'image', 'price']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
            'description': forms.Textarea(attrs={'class': 'form-textarea w-full rounded-md border-gray-300', 'rows': 4}),
            'price': forms.NumberInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
        }

class CarpinteroProfileForm(forms.ModelForm):
    # Campos del modelo User
    full_name = forms.CharField(max_length=255, required=True, widget=forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}))
    phone = forms.CharField(max_length=20, required=False, widget=forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}))
    city = forms.CharField(max_length=100, required=False, widget=forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}))

    class Meta:
        model = Carpintero
        fields = ['specialties', 'experience_years', 'description', 'portfolio_url', 'hoja_vida', 'budget_range']
        widgets = {
            'specialties': forms.TextInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
            'experience_years': forms.NumberInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
            'description': forms.Textarea(attrs={'class': 'form-textarea w-full rounded-md border-gray-300', 'rows': 4}),
            'portfolio_url': forms.URLInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
            'budget_range': forms.NumberInput(attrs={'class': 'form-input w-full rounded-md border-gray-300'}),
        }

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        if self.instance and hasattr(self.instance, 'user'):
            self.fields['full_name'].initial = self.instance.user.full_name
            self.fields['phone'].initial = self.instance.user.phone
            self.fields['city'].initial = self.instance.user.city

    def save(self, commit=True):
        carpintero = super().save(commit=False)
        user = carpintero.user
        user.full_name = self.cleaned_data['full_name']
        user.phone = self.cleaned_data['phone']
        user.city = self.cleaned_data['city']
        
        if commit:
            user.save()
            carpintero.save()
            
        return carpintero
