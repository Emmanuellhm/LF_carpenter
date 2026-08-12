# Documentación del Proyecto LF Carpinter - Migración a Django

## Descripción del Proyecto

Este proyecto es una migración de un sistema web de carpintería desarrollado originalmente en PHP/MySQL a Django/PostgreSQL. El sistema permite gestionar carpinteros, usuarios, solicitudes de proyectos y portafolios.

---

## Tecnologías Utilizadas

### Backend
- **Django 6.0.3** - Framework web Python
- **SQLite** - Base de datos (desarrollo) / PostgreSQL (producción)
- **Python 3.12** - Lenguaje de programación

### Frontend
- **Tailwind CSS** - Framework CSS (CDN)
- **Font Awesome 6.0** - Iconos
- **HTML5 Templates** - Jinja2 templating

### Estructura del Proyecto
```
LF_carpenter-main/
├── core/                   # App principal (autenticación, home)
├── usuarios/               # App de usuarios clientes
├── carpinteros/            # App de carpinteros
├── proyectos/              # App de solicitudes de proyectos
├── contrataciones/         # App de notificaciones e interacciones
├── lf_carpinter/           # Configuración Django
├── templates/              # Templates base
│   ├── base.html
│   ├── core/
│   ├── usuarios/
│   ├── carpinteros/
│   └── proyectos/
├── static/                 # Archivos estáticos
│   └── img/               # Imágenes SVG
├── media/                  # Archivos subidos
│   ├── portafolio/
│   ├── cvs/
│   └── solicitudes/
├── venv/                   # Entorno virtual
├── db.sqlite3             # Base de datos SQLiteo
└── manage.py
```

---

## Modelos Implementados

### Core - User (Usuario personalizado)
```python
# core/models.py
class User(AbstractUser):
    email = EmailField(unique=True)
    full_name = CharField(max_length=255)
    phone = CharField(max_length=20, blank=True)
    city = CharField(max_length=100, blank=True)
    role = CharField(choices=['user', 'carpenter', 'admin'])
    USERNAME_FIELD = 'email'
```

### Carpinteros
```python
# carpinteros/models.py
class Carpintero:
    user = OneToOneField(User)
    specialties = CharField()
    experience_years = IntegerField()
    description = TextField()
    portfolio_url = URLField()
    hoja_vida = FileField()
    is_verified = BooleanField()
    approved = BooleanField()

class Portafolio:
    carpenter = ForeignKey(Carpintero)
    title = CharField()
    description = TextField()
    image = ImageField()
    price = DecimalField()

class Comentario:
    proyecto = ForeignKey(Portafolio)
    user = ForeignKey(User)
    comment = TextField()
    rating = IntegerField()
```

### Proyectos
```python
# proyectos/models.py
class SolicitudProyecto:
    user = ForeignKey(User)
    carpenter = ForeignKey(Carpintero)
    title = CharField()
    description = TextField()
    budget = DecimalField()
    deadline = DateField()
    status = CharField(choices=['pending', 'accepted', 'rejected', 'completed'])
```

### Contrataciones
```python
# contrataciones/models.py
class Notificacion:
    user = ForeignKey(User)
    message = TextField()
    is_read = BooleanField()

class Interaccion:
    user = ForeignKey(User)
    carpenter = ForeignKey(Carpintero)
    action = CharField(choices=['viewed', 'contacted', 'saved'])
```

---

## Funcionalidades Implementadas

### 1. Sistema de Autenticación
- Login con email
- Registro de usuarios (clientes)
- Registro de carpinteros (con aprobación de admin)
- Recuperación de contraseña
- Cierre de sesión

### 2. Panel de Usuario (Cliente)
- Ver carpinteros disponibles
- Ver detalle de carpintero con portafolio
- Solicitar proyectos personalizados
- Ver historial de solicitudes
- Editar perfil

### 3. Panel de Carpintero
- Dashboard con estadísticas
- Gestión de portafolio (subir, editar, eliminar proyectos)
- Ver y gestionar solicitudes recibidas
- Aceptar/Rechazar solicitudes
- Ver notificaciones

### 4. Panel de Administración
- Gestión de usuarios
- Aprobar/Rechazar carpinteros
- Ver todas las solicitudes
- Gestionar notificaciones

---

## Vistas y URLs

### Core (/)
- `/` - Home (home)
- `/login/` - Inicio de sesión (login_view)
- `/logout/` - Cerrar sesión (logout_view)
- `/registro-usuario/` - Registro de usuarios (registro_usuario)
- `/registro-carpintero/` - Registro de carpinteros (registro_carpintero)
- `/registro-enviado/` - Confirmación de registro (registro_enviado)
- `/recuperar-contrasena/` - Recuperar contraseña (recuperar_contrasena)
- `/contactanos/` - Página de contacto (contactanos)

### Usuarios (/usuarios/)
- `/panel/` - Panel principal (panel_usuario)
- `/carpinteros/` - Ver carpinteros (ver_carpinteros)
- `/carpintero/<id>/` - Ver detalle carpintero (ver_carpintero_detalle)
- `/mis-solicitudes/` - Historial de solicitudes (mis_solicitudes)
- `/actualizar-perfil/` - Editar perfil (update_profile)
- `/historial/` - Historial de actividad (historial)

### Carpinteros (/carpinteros/)
- `/panel/` - Panel principal (panel_carpintero)
- `/proyectos/` - Mis proyectos (mis_proyectos)
- `/proyectos/subir/` - Subir proyecto (subir_proyecto)
- `/proyectos/<id>/editar/` - Editar proyecto (editar_proyecto)
- `/proyectos/<id>/eliminar/` - Eliminar proyecto (eliminar_proyecto)
- `/solicitudes/` - Ver solicitudes (ver_solicitudes)
- `/solicitudes/<id>/` - Detalle de solicitud (detalle_solicitud)
- `/notificaciones/` - Ver notificaciones (notificaciones)

### Proyectos (/proyectos/)
- `/solicitar/<carpintero_id>/` - Solicitar proyecto (solicitar_proyecto)

---

## Usuarios de Prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@lfcarpinter.com | admin123 |
| Cliente | cliente@test.com | cliente123 |
| Carpintero | carpintero@test.com | carpintero123 |

---

## Comandos Útiles

### Activar entorno virtual
```bash
cd /home/miguel/Documentos/LF_carpenter-main
source venv/bin/activate
```

### Ejecutar servidor de desarrollo
```bash
python manage.py runserver
```

### Crear migraciones
```bash
python manage.py makemigrations
python manage.py migrate
```

### Crear superusuario
```bash
python manage.py createsuperuser
```

### Recolectar archivos estáticos
```bash
python manage.py collectstatic
```

---

## Configuración de Base de Datos

### Desarrollo (SQLite)
```python
# settings.py
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.sqlite3',
        'NAME': BASE_DIR / 'db.sqlite3',
    }
}
```

### Producción (PostgreSQL)
```python
# settings.py
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': 'lf_carpinter',
        'USER': 'postgres',
        'PASSWORD': 'tu_contraseña',
        'HOST': 'localhost',
        'PORT': '5432',
    }
}
```

```sql
-- Crear base de datos
CREATE DATABASE lf_carpinter;
```

---

## Notas Importantes

1. **Autenticación**: El sistema usa email como campo de login principal, no username.

2. **Roles de usuario**:
   - `user` - Cliente normal
   - `carpenter` - Carpintero (requiere aprobación)
   - `admin` - Administrador

3. **Flujo de registro de carpintero**:
   - El carpintero se registra
   - Su cuenta queda como `approved=False`
   - Un administrador debe aprobarlo desde el admin
   - Después puede acceder a su panel

4. **Archivos de medios**:
   - Las imágenes de portafolio se guardan en `/media/portafolio/`
   - Las hojas de vida se guardan en `/media/cvs/`
   - Las imágenes de referencia en `/media/solicitudes/`

---

## Estado del Proyecto

**COMPLETADO**: Migración de PHP a Django finalizada.

- [x] Modelos creados y migraciones aplicadas
- [x] Vistas y URLs funcionales
- [x] Templates completos
- [x] Admin configurado
- [x] Usuarios de prueba creados
- [x] Carpetas PHP eliminadas
- [x] Archivos estáticos creados

---

## Contacto

Proyecto desarrollado para SENA - Análisis y Desarrollo de Software.