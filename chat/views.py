from django.shortcuts import render, get_object_or_404, redirect
from django.contrib.auth.decorators import login_required
from django.core.exceptions import PermissionDenied
from .models import ChatRoom
from proyectos.models import SolicitudProyecto

@login_required
def abrir_chat(request, solicitud_id):
    """Crea o recupera la sala de chat para una solicitud específica."""
    solicitud = get_object_or_404(SolicitudProyecto, id=solicitud_id)
    
    # Verificar acceso: solo el cliente creador o el carpintero asignado pueden entrar
    is_client = request.user == solicitud.user
    is_carpenter = request.user == solicitud.carpenter.user
    
    if not (is_client or is_carpenter):
        raise PermissionDenied("No tienes acceso a esta sala de chat.")
    
    # Obtener o crear la sala de chat asociada a la solicitud
    chat_room, created = ChatRoom.objects.get_or_create(solicitud=solicitud)
    
    return redirect('chat:sala_chat', room_id=chat_room.id)

@login_required
def sala_chat(request, room_id):
    """Renderiza la interfaz de la sala de chat."""
    chat_room = get_object_or_404(ChatRoom, id=room_id)
    
    # Verificar acceso
    is_client = request.user == chat_room.solicitud.user
    is_carpenter = request.user == chat_room.solicitud.carpenter.user
    
    if not (is_client or is_carpenter):
        raise PermissionDenied("No tienes acceso a esta sala de chat.")
        
    # Identificar la "otra persona" para la interfaz
    if is_client:
        other_user = chat_room.solicitud.carpenter.user
    else:
        other_user = chat_room.solicitud.user
        
    mensajes = chat_room.messages.select_related('sender').all()
    
    context = {
        'room_id': room_id,
        'chat_room': chat_room,
        'other_user': other_user,
        'mensajes': mensajes,
    }
    return render(request, 'chat/sala.html', context)
