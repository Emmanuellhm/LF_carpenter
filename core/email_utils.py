"""
Utilidades de Email para LF Carpinter
Sistema profesional de envío de emails con templates HTML
"""

from django.core.mail import EmailMultiAlternatives
from django.template.loader import render_to_string
from django.utils.html import strip_tags
from django.conf import settings
from django.contrib.auth.tokens import default_token_generator
from django.utils.http import urlsafe_base64_encode
from django.utils.encoding import force_bytes
import logging

logger = logging.getLogger(__name__)


class LFEmailService:
    """Servicio profesional de envío de emails para LF Carpinter"""

    def __init__(self):
        self.from_email = settings.DEFAULT_FROM_EMAIL
        self.site_name = settings.SITE_NAME
        self.site_url = settings.SITE_URL

    def _send_email(self, to_email, subject, template_name, context):
        """
        Envía un email HTML con versión de texto plano alternativa.

        Args:
            to_email: Email del destinatario
            subject: Asunto del email
            template_name: Nombre del template (sin extensión)
            context: Diccionario con variables para el template
        """
        try:
            # Agregar variables globales al contexto
            context.update({
                'site_name': self.site_name,
                'site_url': self.site_url,
            })

            # Renderizar versión HTML
            html_content = render_to_string(f'emails/{template_name}.html', context)

            # Versión de texto plano
            text_content = strip_tags(html_content)

            # Crear email
            email = EmailMultiAlternatives(
                subject=f'{settings.EMAIL_SUBJECT_PREFIX}{subject}',
                body=text_content,
                from_email=self.from_email,
                to=[to_email],
            )
            email.attach_alternative(html_content, 'text/html')

            # Enviar
            email.send()
            logger.info(f'Email enviado exitosamente a {to_email}: {subject}')
            return True

        except Exception as e:
            logger.error(f'Error al enviar email a {to_email}: {str(e)}')
            return False

    def send_welcome_email(self, user):
        """Envía email de bienvenida premium a un nuevo usuario"""
        context = {
            'user': user,
        }
        return self._send_email(
            to_email=user.email,
            subject=f'¡Bienvenido a LF Carpinter, {user.full_name}! 🪵',
            template_name='bienvenida',
            context=context
        )

    def send_confirmation_email(self, user, request=None):
        """
        Envía email de confirmación de cuenta.
        Incluye enlace con token seguro para activar la cuenta.
        """
        token = default_token_generator.make_token(user)
        uid = urlsafe_base64_encode(force_bytes(user.pk))

        confirmation_url = f"{self.site_url}/confirmar-email/{uid}/{token}/"

        context = {
            'user': user,
            'title': 'Confirma tu cuenta',
            'confirmation_url': confirmation_url,
            'expiration_hours': 24,
        }
        return self._send_email(
            to_email=user.email,
            subject='Confirma tu cuenta en LF Carpinter',
            template_name='confirm_email',
            context=context
        )

    def send_password_reset_email(self, user, request=None):
        """
        Envía email para recuperación de contraseña.
        Incluye token seguro con expiración de 24 horas.
        """
        token = default_token_generator.make_token(user)
        uid = urlsafe_base64_encode(force_bytes(user.pk))

        reset_url = f"{self.site_url}/restablecer-contrasena/{uid}/{token}/"

        context = {
            'user': user,
            'title': 'Restablece tu contraseña',
            'reset_url': reset_url,
            'expiration_hours': 24,
        }
        return self._send_email(
            to_email=user.email,
            subject='Restablece tu contraseña - LF Carpinter',
            template_name='password_reset',
            context=context
        )

    def send_carpenter_approval_email(self, carpenter):
        """Envía email cuando un carpintero es aprobado"""
        context = {
            'user': carpenter.user,
            'carpenter': carpenter,
            'title': '¡Tu cuenta ha sido aprobada!',
            'login_url': f"{self.site_url}/login/",
            'panel_url': f"{self.site_url}/carpinteros/panel/",
        }
        return self._send_email(
            to_email=carpenter.user.email,
            subject='¡Tu cuenta ha sido aprobada! - LF Carpinter',
            template_name='carpenter_approved',
            context=context
        )

    def send_project_status_email(self, solicitud, new_status=None):
        """
        Envía email al cliente cuando cambia el estado de su solicitud.
        Usa la plantilla HTML premium que cubre todos los estados del Kanban.
        """
        context = {
            'user': solicitud.user,
            'solicitud': solicitud,
        }
        return self._send_email(
            to_email=solicitud.user.email,
            subject=f'Actualización de tu proyecto: {solicitud.get_status_display()}',
            template_name='estado_actualizado',
            context=context
        )

    def send_new_solicitud_email(self, solicitud):
        """Envía email al carpintero cuando recibe una nueva solicitud"""
        context = {
            'user': solicitud.carpenter.user,
            'solicitud': solicitud,
            'client': solicitud.user,
            'title': '¡Tienes una nueva solicitud!',
            'solicitudes_url': f"{self.site_url}/carpinteros/solicitudes/",
        }
        return self._send_email(
            to_email=solicitud.carpenter.user.email,
            subject=f'Nueva solicitud de proyecto - LF Carpinter',
            template_name='new_solicitud',
            context=context
        )

    def send_contact_form_email(self, nombre, email, mensaje):
        """Envía email de contacto desde el formulario público"""
        context = {
            'nombre': nombre,
            'email': email,
            'mensaje': mensaje,
            'title': 'Nuevo mensaje de contacto',
        }
        # Enviar al admin
        return self._send_email(
            to_email=settings.EMAIL_HOST_USER or 'admin@lfcarpinter.com',
            subject=f'Nuevo mensaje de contacto de {nombre}',
            template_name='contact_form',
            context=context
        )


# Instancia global del servicio
email_service = LFEmailService()