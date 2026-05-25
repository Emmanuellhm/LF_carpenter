from django.shortcuts import redirect
from social_core.pipeline.partial import partial

@partial
def require_extra_data(strategy, details, user=None, is_new=False, *args, **kwargs):
    """
    Pausa el pipeline de Social Auth para usuarios nuevos y los redirige
    a una vista donde pueden completar su rol (Cliente/Carpintero), teléfono y ciudad.
    """
    if is_new:
        # Si ya hemos guardado los datos en la sesión en el paso anterior, continuamos
        saved_role = strategy.session_get('saved_role')
        if saved_role:
            # Los datos ya fueron capturados, los aplicamos al usuario
            user.role = saved_role
            user.phone = strategy.session_get('saved_phone', '')
            user.city = strategy.session_get('saved_city', '')
            user.save()
            
            # Limpiamos la sesión
            strategy.session_pop('saved_role')
            strategy.session_pop('saved_phone')
            strategy.session_pop('saved_city')
            
            # Si eligió ser carpintero, creamos su perfil (inactivo por defecto)
            if saved_role == 'carpenter':
                from carpinteros.models import Carpintero
                Carpintero.objects.get_or_create(
                    user=user,
                    defaults={'is_verified': False, 'approved': False}
                )
            return

        # Si no tenemos los datos en sesión, pausamos y redirigimos
        return redirect('completar_registro_social')
