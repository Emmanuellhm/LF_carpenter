from django import forms
from django.contrib.auth.forms import SetPasswordForm, PasswordResetForm
from django.contrib.auth.tokens import default_token_generator
from django.utils.http import urlsafe_base64_encode
from django.utils.encoding import force_str
from django.core.exceptions import ValidationError
from django.core.validators import RegexValidator
from .models import User


class LoginForm(forms.Form):
    """Formulario de login con email"""
    email = forms.EmailField(
        label='Correo electrónico',
        widget=forms.EmailInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'tu@email.com',
            'autocomplete': 'email'
        })
    )
    password = forms.CharField(
        label='Contraseña',
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': '••••••••',
            'autocomplete': 'current-password'
        })
    )
    remember_me = forms.BooleanField(
        required=False,
        label='Recordarme',
        widget=forms.CheckboxInput(attrs={'class': 'w-4 h-4 text-amber-600 border-stone-300 rounded focus:ring-amber-500'})
    )


class UserRegistroForm(forms.ModelForm):
    """Formulario de registro para usuarios normales"""
    password = forms.CharField(
        label='Contraseña',
        min_length=8,
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Mínimo 8 caracteres'
        })
    )
    password2 = forms.CharField(
        label='Confirmar contraseña',
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Repite tu contraseña'
        })
    )
    accept_terms = forms.BooleanField(
        required=True,
        label='Acepto los términos y condiciones',
        widget=forms.CheckboxInput(attrs={'class': 'w-4 h-4 text-amber-600 border-stone-300 rounded focus:ring-amber-500'})
    )

    class Meta:
        model = User
        fields = ['full_name', 'email', 'phone', 'city', 'password']
        widgets = {
            'full_name': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': 'Nombre completo'
            }),
            'email': forms.EmailInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': 'tu@email.com'
            }),
            'phone': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': '+57 300 123 4567',
                'pattern': r'^\+?1?\d{9,15}$',
                'title': 'Formato válido: +573001234567 o 3001234567'
            }),
            'city': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': 'Ciudad'
            }),
        }

    def clean_email(self):
        email = self.cleaned_data.get('email')
        if User.objects.filter(email=email).exists():
            raise ValidationError('Ya existe un usuario con este correo electrónico.')
        return email

    def clean_phone(self):
        phone = self.cleaned_data.get('phone')
        import re
        if phone and not re.match(r'^\+?1?\d{9,15}$', phone):
            raise ValidationError('Formato de teléfono inválido.')
        return phone

    def clean_password2(self):
        password = self.cleaned_data.get('password')
        password2 = self.cleaned_data.get('password2')
        if password and password2 and password != password2:
            raise forms.ValidationError('Las contraseñas no coinciden.')
        return password2


class CarpinteroRegistroForm(forms.Form):
    """Formulario de registro para carpinteros"""
    nombre = forms.CharField(
        label='Nombre completo',
        max_length=255,
        validators=[RegexValidator(regex=r'^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$', message='El nombre solo puede contener letras y espacios.')],
        widget=forms.TextInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Nombre completo'
        })
    )
    email = forms.EmailField(
        label='Correo electrónico',
        widget=forms.EmailInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'tu@email.com'
        })
    )
    telefono = forms.CharField(
        label='Teléfono',
        max_length=20,
        validators=[RegexValidator(regex=r'^\+?1?\d{9,15}$', message='Formato de teléfono inválido.')],
        widget=forms.TextInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': '+57 300 123 4567'
        })
    )
    ciudad = forms.CharField(
        label='Ciudad',
        max_length=100,
        widget=forms.TextInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Ciudad'
        })
    )
    especialidad = forms.CharField(
        label='Especialidad',
        max_length=255,
        validators=[RegexValidator(regex=r'^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\,\.\-]+$', message='La especialidad no debe contener caracteres especiales.')],
        widget=forms.TextInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Ej: Muebles, Restauración, Cocinas...'
        })
    )
    experiencia = forms.IntegerField(
        label='Años de experiencia',
        min_value=1,
        max_value=50,
        widget=forms.NumberInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'min': '1',
            'max': '50'
        })
    )
    descripcion = forms.CharField(
        label='Descripción',
        required=False,
        widget=forms.Textarea(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition resize-none',
            'rows': '4',
            'placeholder': 'Cuéntanos sobre tu experiencia y especialidades...'
        })
    )
    portafolio = forms.URLField(
        label='URL de portafolio (opcional)',
        required=False,
        widget=forms.URLInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'https://tusitio.com'
        })
    )
    hoja_vida = forms.FileField(
        label='Hoja de vida (PDF)',
        required=False,
        widget=forms.FileInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'accept': '.pdf,.doc,.docx'
        })
    )
    password = forms.CharField(
        label='Contraseña',
        min_length=8,
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Mínimo 8 caracteres'
        })
    )
    password2 = forms.CharField(
        label='Confirmar contraseña',
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Repite tu contraseña'
        })
    )
    accept_terms = forms.BooleanField(
        required=True,
        label='Acepto los términos y condiciones',
        widget=forms.CheckboxInput(attrs={'class': 'w-4 h-4 text-amber-600 border-stone-300 rounded focus:ring-amber-500'})
    )

    def clean_email(self):
        email = self.cleaned_data.get('email')
        if User.objects.filter(email=email).exists():
            raise ValidationError('Ya existe un usuario con este correo electrónico.')
        return email

    def clean_password2(self):
        password = self.cleaned_data.get('password')
        password2 = self.cleaned_data.get('password2')
        if password and password2 and password != password2:
            raise forms.ValidationError('Las contraseñas no coinciden.')
        return password2


class PasswordRecoveryForm(forms.Form):
    """Formulario de recuperación de contraseña"""
    email = forms.EmailField(
        label='Correo electrónico',
        widget=forms.EmailInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'tu@email.com',
            'autocomplete': 'email'
        })
    )


class SetNewPasswordForm(SetPasswordForm):
    """Formulario para establecer nueva contraseña"""
    new_password1 = forms.CharField(
        label='Nueva contraseña',
        min_length=8,
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Mínimo 8 caracteres'
        })
    )
    new_password2 = forms.CharField(
        label='Confirmar nueva contraseña',
        widget=forms.PasswordInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Repite tu nueva contraseña'
        })
    )

    def clean_new_password1(self):
        password = self.cleaned_data.get('new_password1')
        if self.user.check_password(password):
            raise ValidationError("La nueva contraseña no puede ser igual a la anterior.")
        return password


class ContactForm(forms.Form):
    """Formulario de contacto"""
    nombre = forms.CharField(
        label='Nombre completo',
        max_length=255,
        widget=forms.TextInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Tu nombre'
        })
    )
    correo = forms.EmailField(
        label='Correo electrónico',
        widget=forms.EmailInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'tu@email.com'
        })
    )
    asunto = forms.CharField(
        label='Asunto',
        max_length=200,
        required=False,
        widget=forms.TextInput(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
            'placeholder': 'Asunto del mensaje'
        })
    )
    mensaje = forms.CharField(
        label='Mensaje',
        widget=forms.Textarea(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition resize-none',
            'rows': '5',
            'placeholder': 'Escribe tu mensaje aquí...'
        })
    )