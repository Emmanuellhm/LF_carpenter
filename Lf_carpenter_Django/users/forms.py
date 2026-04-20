from django import forms
from django.contrib.auth.forms import UserCreationForm
from .models import User, CarpenterProfile

class ClientRegistrationForm(UserCreationForm):
    full_name = forms.CharField(max_length=255, required=True, label="Nombre Completo")
    phone = forms.CharField(max_length=15, required=False, label="Teléfono")
    city = forms.CharField(max_length=100, required=False, label="Ciudad")

    class Meta(UserCreationForm.Meta):
        model = User
        fields = UserCreationForm.Meta.fields + ('full_name', 'email', 'phone', 'city')

    def save(self, commit=True):
        user = super().save(commit=False)
        user.role = 'client'
        user.first_name = self.cleaned_data['full_name']
        if commit:
            user.save()
        return user

class CarpenterRegistrationForm(UserCreationForm):
    full_name = forms.CharField(max_length=255, required=True, label="Nombre Completo")
    phone = forms.CharField(max_length=15, required=True, label="Teléfono")
    city = forms.CharField(max_length=100, required=True, label="Ciudad")
    
    # Campos de CarpenterProfile
    specialties = forms.CharField(widget=forms.Textarea, required=False, label="Especialidades")
    experience = forms.IntegerField(min_value=0, required=False, label="Años de experiencia")
    cv_file = forms.FileField(required=False, label="Hoja de vida (PDF)")

    class Meta(UserCreationForm.Meta):
        model = User
        fields = UserCreationForm.Meta.fields + ('full_name', 'email', 'phone', 'city')

    def save(self, commit=True):
        user = super().save(commit=False)
        user.role = 'carpenter'
        user.first_name = self.cleaned_data['full_name']
        if commit:
            user.save()
            CarpenterProfile.objects.create(
                user=user,
                specialties=self.cleaned_data.get('specialties'),
                years_of_experience=self.cleaned_data.get('experience', 0),
                cv_file=self.cleaned_data.get('cv_file')
            )
        return user
