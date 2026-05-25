# PROMPT DE CONTINUIDAD - LF CARPINTER

## USAR AL INICIO DE UNA NUEVA SESIÓN:

```
Estoy trabajando en un proyecto Django llamado LF Carpinter (sistema de gestión de carpinteros). El proyecto ya está migrado de PHP a Django y funcionando. Necesito que me ayudes con mejoras y nuevas funcionalidades.

## ESTADO ACTUAL DEL PROYECTO

### Tecnologías:
- Django 6.0.3
- PostgreSQL (base de datos: lf_carpinter, usuario: lf_user)
- Python 3.12
- Tailwind CSS (CDN)
- SQLite existe pero NO se usa (la BD activa es PostgreSQL)

### Ubicación del proyecto:
/home/miguel/Documentos/LF_carpenter-main

### Estructura de Apps:
- core/ - Autenticación, home, registro
- usuarios/ - Panel de usuarios clientes
- carpinteros/ - Panel de carpinteros, portafolio
- proyectos/ - Solicitudes de proyectos
- contrataciones/ - Notificaciones e interacciones

### Base de Datos (PostgreSQL):
- 131 usuarios (2 admin, 48 carpinteros, 81 usuarios normales)
- 44 carpinteros (3 aprobados, 41 pendientes de aprobación)
- 18 portafolios con imágenes
- 5 solicitudes (2 pendientes, 1 aceptada, 1 rechazada, 1 completada)
- 14 notificaciones

### Archivos Estáticos:
- static/img/ - Imágenes del sitio (logo SVG, placeholders creados)
- media/portafolio/ - Imágenes de portafolio (18 imágenes creadas)
- media/cvs/ - Hojas de vida (vacío)
- media/solicitudes/ - Imágenes de referencia (vacío)

### Modelos Principales:
- User (core) - Usuario personalizado con roles: user, carpenter, admin
- Carpintero - Perfil de carpintero con especialidad, experiencia, aprobación
- Portafolio - Proyectos de carpinteros con imagen
- SolicitudProyecto - Solicitudes de clientes (pending, accepted, rejected, completed)
- Notificacion - Sistema de notificaciones

### URLs Principales:
- / - Home
- /login/ - Inicio de sesión
- /registro-usuario/ - Registro clientes
- /registro-carpintero/ - Registro carpinteros
- /usuarios/panel/ - Panel usuario
- /usuarios/carpinteros/ - Lista carpinteros
- /carpinteros/panel/ - Panel carpintero
- /carpinteros/solicitudes/ - Ver solicitudes recibidas
- /proyectos/solicitar/<id>/ - Enviar solicitud

### Funcionalidades Implementadas:
✅ Sistema de autenticación (login con email)
✅ Registro de usuarios y carpinteros
✅ Aprobación de carpinteros desde admin
✅ Panel de usuario (ver carpinteros, enviar solicitudes)
✅ Panel de carpintero (ver solicitudes, aceptar/rechazar, subir proyectos)
✅ Sistema de notificaciones básico
✅ Panel de administración Django
✅ Todas las imágenes placeholder creadas

### Configuración Actual (settings.py):
- MEDIA_ROOT: /home/miguel/Documentos/LF_carpenter-main/media
- MEDIA_URL: /media/
- STATIC_URL: /static/
- AUTH_USER_MODEL: core.User
- LOGIN_URL: 'login'

## PLAN DE MEJORA PROPUESTO

### FASE 1: SEGURIDAD Y CONFIGURACIÓN
1. Configurar ALLOWED_HOSTS para producción
2. Generar SECRET_KEY segura
3. Mover credenciales a .env
4. Desactivar DEBUG en producción
5. Configurar CSRF_TRUSTED_ORIGINS

### FASE 2: FUNCIONALIDADES CORE
1. Sistema de emails (confirmación, recuperación)
2. Paginación en listados
3. Búsqueda avanzada de carpinteros
4. Sistema de calificaciones funcional

### FASE 3: EXPERIENCIA DE USUARIO
1. Dashboard admin con estadísticas
2. Perfil de carpintero editable
3. Mensajes entre usuarios
4. Historial de proyectos

### FASE 4: BACKEND Y APIs
1. API REST para app móvil
2. Optimización de imágenes
3. Logs de actividad
4. Backup automático

### FASE 5: MEJORAS VISUALES
1. Página "Sobre nosotros"
2. FAQ/Ayuda
3. Términos y condiciones
4. Animaciones

### ACCIONES INMEDIATAS:
- 41 carpinteros pendientes de aprobación
- Configurar SMTP para emails
- Crear más datos de demo

## COMANDOS ÚTILES

# Activar entorno
source venv/bin/activate

# Ejecutar servidor
python manage.py runserver

# Verificar base de datos
python -c "import django; import os; os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'lf_carpinter.settings'); django.setup(); from core.models import User; print(User.objects.count())"

# Crear superusuario
python manage.py createsuperuser

## NOTAS IMPORTANTES
- El login usa EMAIL como campo principal (no username)
- Los carpinteros requieren aprobación del admin
- Las imágenes de portafolio están en media/portafolio/
- Los templates usan Tailwind CSS via CDN
- El proyecto está listo para desarrollo local

¿Qué mejora o funcionalidad quieres que implemente primero?
```

---

## ARCHIVOS CLAVE DEL PROYECTO

### settings.py (configuración):
```python
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': 'lf_carpinter',
        'USER': 'lf_user',
        'PASSWORD': 'lf_carpinter_2025',
        'HOST': 'localhost',
        'PORT': '5432',
    }
}

AUTH_USER_MODEL = 'core.User'
LOGIN_URL = 'login'
```

### Modelos principales:
```python
# core/models.py - User
roles: 'user', 'carpenter', 'admin'
USERNAME_FIELD = 'email'

# carpinteros/models.py - Carpintero
fields: specialties, experience_years, description, hoja_vida, is_verified, approved, rating_avg

# proyectos/models.py - SolicitudProyecto
status: 'pending', 'accepted', 'rejected', 'completed'

# contrataciones/models.py - Notificacion
fields: user, message, is_read
```

### Templates importantes:
- templates/base.html - Template base con navegación
- templates/core/home.html - Página principal
- templates/carpinteros/panel_carpintero.html - Dashboard carpintero
- templates/usuarios/ver_carpintero_detalle.html - Perfil carpintero

---

## DATOS DE ACCESO (desarrollo)

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@lfcarpinter.com | admin123 |
| Cliente | cliente@test.com | cliente123 |
| Carpintero | carpintero@test.com | carpintero123 |

---

## CAMBIOS REALIZADOS EN LA ÚLTIMA SESIÓN

1. ✅ Creadas imágenes placeholder en static/img/ (10 imágenes)
2. ✅ Creadas imágenes de portafolio en media/portafolio/ (18 imágenes)
3. ✅ Corregido logo PNG → SVG en templates de autenticación
4. ✅ Verificada conexión PostgreSQL funcionando
5. ✅ Verificadas todas las URLs del proyecto

---

*Documento generado: 2026-04-21*
*Proyecto: LF Carpinter - Sistema de Gestión de Carpinteros*