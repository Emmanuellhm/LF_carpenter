from django import forms
from core.models import User


class UserProfileForm(forms.ModelForm):
    """Formulario para actualizar perfil de usuario"""

    class Meta:
        model = User
        fields = ['full_name', 'phone', 'city']
        widgets = {
            'full_name': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': 'Nombre completo'
            }),
            'phone': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': '+57 300 123 4567'
            }),
            'city': forms.TextInput(attrs={
                'class': 'w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition',
                'placeholder': 'Ciudad'
            }),
        }


class ReviewForm(forms.Form):
    """Formulario para crear una calificación/reseña de carpintero"""

    rating = forms.IntegerField(
        label='Calificación',
        min_value=1,
        max_value=5,
        error_messages={
            'required': 'Por favor selecciona una calificación.',
            'min_value': 'La calificación mínima es 1 estrella.',
            'max_value': 'La calificación máxima es 5 estrellas.'
        },
        widget=forms.HiddenInput(attrs={
            'id': 'rating-input'
        })
    )

    comment = forms.CharField(
        label='Tu comentario',
        required=False,
        max_length=1000,
        widget=forms.Textarea(attrs={
            'class': 'w-full px-4 py-3 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition resize-none',
            'rows': '4',
            'placeholder': 'Cuéntanos tu experiencia con este carpintero...',
            'maxlength': '1000',
            'id': 'review-comment'
        })
    )

    def clean_rating(self):
        rating = self.cleaned_data.get('rating')
        if not rating or rating < 1 or rating > 5:
            raise forms.ValidationError('La calificación debe estar entre 1 y 5.')
        return rating