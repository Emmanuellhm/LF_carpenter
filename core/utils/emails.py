"""
core/utils/emails.py
--------------------
Servicio centralizado de correos transaccionales para LF Carpinter.
Todos los correos se envían en formato HTML con la plantilla Luxury Woodcraft.
"""
from django.core.mail import EmailMultiAlternatives
from django.template.loader import render_to_string
from django.utils.html import strip_tags
from django.conf import settings
import logging

logger = logging.getLogger(__name__)


def _get_site_url():
    return getattr(settings, 'SITE_URL', 'http://localhost:8000')


def send_html_email(subject, template_html, context, recipient_email):
    """
    Función base para enviar correos HTML+TXT.
    Siempre incluye un fallback de texto plano.
    """
    context['site_url'] = _get_site_url()
    
    try:
        html_content = render_to_string(template_html, context)
        text_content = strip_tags(html_content)

        msg = EmailMultiAlternatives(
            subject=subject,
            body=text_content,
            from_email=settings.DEFAULT_FROM_EMAIL,
            to=[recipient_email],
        )
        msg.attach_alternative(html_content, "text/html")
        msg.send()
        logger.info(f"Email enviado a {recipient_email} — Asunto: {subject}")
        return True
    except Exception as e:
        logger.error(f"Error enviando email a {recipient_email}: {e}")
        return False


# ──────────────────────────────────────────────────
# Funciones de alto nivel (1 por evento del sistema)
# ──────────────────────────────────────────────────

def send_welcome_email(user):
    """Envía el correo de bienvenida a un nuevo usuario registrado."""
    return send_html_email(
        subject=f"¡Bienvenido a LF Carpinter, {user.full_name}! 🪵",
        template_html='emails/bienvenida.html',
        context={'user': user},
        recipient_email=user.email,
    )


def send_status_update_email(solicitud):
    """
    Notifica al cliente cuando su solicitud cambia de estado.
    Se llama desde la vista api_actualizar_estado del Kanban.
    """
    user = solicitud.user
    status_labels = {
        'pending': 'Nueva Solicitud',
        'budgeting': 'En Cotización',
        'accepted': 'Aceptado',
        'in_progress': 'En Taller 🔨',
        'finishing': 'En Acabados',
        'ready': '¡Listo para Entrega! 📦',
        'completed': '¡Completado! 🎉',
    }
    status_label = status_labels.get(solicitud.status, solicitud.get_status_display())
    
    return send_html_email(
        subject=f"Actualización de tu proyecto: {status_label}",
        template_html='emails/estado_actualizado.html',
        context={'user': user, 'solicitud': solicitud},
        recipient_email=user.email,
    )


def send_new_solicitud_email(carpintero, solicitud):
    """Notifica al carpintero cuando recibe una nueva solicitud de proyecto."""
    return send_html_email(
        subject=f"Nueva solicitud recibida: {solicitud.title}",
        template_html='emails/nueva_solicitud.html',
        context={
            'carpintero': carpintero,
            'solicitud': solicitud,
        },
        recipient_email=carpintero.user.email,
    )
