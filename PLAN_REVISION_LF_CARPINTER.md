# PLAN DE REVISIÓN — Proyecto Lf_Carpinter
> Archivo generado para análisis automatizado por IA. Lee las instrucciones al final antes de comenzar.

---

## CONTEXTO DEL PROYECTO

- **Nombre:** Lf_Carpinter
- **Descripción:** Plataforma web que conecta clientes con carpinteros. Los clientes pueden buscar carpinteros, ver perfiles, portafolios y dejar reseñas. Los carpinteros pueden registrarse, gestionar su perfil y recibir solicitudes de servicio. Los administradores gestionan y aprueban carpinteros.
- **Framework:** Django (Python)
- **Gestión:** Scrum con Taiga (2 sprints completados + backlog pendiente)
- **Roles del sistema:** Cliente, Carpintero, Administrador

---

## INSTRUCCIONES PARA LA IA QUE LEA ESTE ARCHIVO

Eres un desarrollador senior especializado en Django. Tu tarea es analizar el código fuente de este proyecto y verificar si cada requerimiento listado está implementado correctamente.

### Cómo proceder:

1. Lee este archivo completo primero para entender el alcance del proyecto.
2. Explora la estructura de carpetas del proyecto Django.
3. Para cada requerimiento, busca evidencia en el código: modelos, vistas, URLs, templates, serializers o tests.
4. Marca cada ítem con uno de estos estados:
   - ✅ COMPLETADO — implementado y parece funcionar correctamente
   - ⚠️ INCOMPLETO — existe pero le falta algo (indica qué falta)
   - ❌ NO IMPLEMENTADO — no existe ninguna evidencia en el código
   - 🔒 BLOQUEADO — depende de otro RF que no está completo
5. Al final, genera un resumen con conteo de cada estado y las recomendaciones más urgentes.

### Dónde buscar en un proyecto Django típico:

```
proyecto/
├── apps/ o [nombre_app]/
│   ├── models.py          → Modelos de base de datos
│   ├── views.py           → Lógica de vistas
│   ├── urls.py            → Rutas
│   ├── forms.py           → Formularios
│   ├── serializers.py     → Serializers DRF (si usa API REST)
│   ├── admin.py           → Panel de administración
│   └── templates/         → Archivos HTML
├── config/ o [proyecto]/
│   ├── settings.py        → Configuración general
│   └── urls.py            → URLs raíz
└── requirements.txt       → Dependencias instaladas
```

---

## SPRINT 1 — Autenticación y Usuarios
**Periodo:** 11 de marzo al 18 de marzo de 2026
**Objetivo:** Módulo completo de registro, login, roles y gestión de acceso.

---

### RF01 — Registro de clientes (3 pts)
**Descripción:** Como cliente, quiero registrarme mediante un formulario básico para crear mi cuenta en la plataforma.

**Qué buscar en el código:**
- Modelo `User` personalizado o uso de `AbstractUser`/`AbstractBaseUser` con campo `role` o similar
- Vista de registro de clientes (`RegisterClientView`, `registro_cliente`, o similar)
- Formulario de registro (`ClienteRegistroForm` o similar) con validación
- URL mapeada (ej. `/registro/`, `/accounts/register/`)
- Template HTML con el formulario
- Redirección o mensaje de éxito tras registro

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF01.1 — Registro de carpinteros (3 pts)
**Descripción:** Permitir el registro de carpinteros mediante un formulario de solicitud que incluye datos de contacto, experiencia y tipo de trabajos.

**Qué buscar en el código:**
- Formulario de solicitud de carpintero (campos: contacto, experiencia, tipo de trabajo)
- Modelo `SolicitudCarpintero` o similar que almacene la solicitud en estado "pendiente"
- Vista y URL para este registro
- Lógica que impide acceso hasta ser aprobado

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF02 — Inicio de sesión multi-rol (3 pts)
**Descripción:** Permitir inicio de sesión para todos los roles (cliente, carpintero, administrador) con redirección al panel correspondiente según el rol.

**Qué buscar en el código:**
- Vista de login (puede ser `LoginView` de Django o personalizada)
- Lógica post-login que detecta el rol y redirige al panel correcto
- Paneles separados por rol: `/dashboard/cliente/`, `/dashboard/carpintero/`, `/admin/`
- Decoradores o mixins de control de acceso por rol (`@login_required`, `UserPassesTestMixin`, etc.)

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF02.1 — Acceso restringido por rol (3 pts)
**Descripción:** Cada rol debe tener acceso restringido únicamente a su panel y funcionalidades correspondientes.

**Qué buscar en el código:**
- Decoradores o mixins aplicados en cada vista (`@login_required`, `PermissionRequiredMixin`, `UserPassesTestMixin`)
- Verificación de rol dentro de las vistas (ej. `if request.user.role != 'carpintero': redirect(...)`)
- Tests de autorización (intentar acceder a un panel sin el rol correcto devuelve 403 o redirección)

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF03 — Recuperar contraseña (2 pts)
**Descripción:** Permitir recuperación de contraseña mediante envío de correo electrónico.

**Qué buscar en el código:**
- Uso de `django.contrib.auth.views` (PasswordResetView, PasswordResetConfirmView, etc.) o implementación custom
- URLs configuradas para el flujo de reset
- Configuración de email en `settings.py` (`EMAIL_BACKEND`, `EMAIL_HOST`, etc.)
- Templates de email de recuperación

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF04 — Creación de administradores (3 pts)
**Descripción:** Permitir la creación de usuarios administradores.

**Qué buscar en el código:**
- Comando `createsuperuser` funcional o vista restringida para crear admins
- El rol `admin` está correctamente definido en el modelo de usuario
- Acceso al panel de administración de Django o panel custom

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF05 — Solicitud de registro de carpintero (3 pts)
**Descripción:** Carpinteros pueden enviar una solicitud de registro al sistema.

**Qué buscar en el código:**
- Modelo `SolicitudRegistro` o similar con estado inicial "pendiente"
- Vista y formulario para que un carpintero envíe su solicitud
- La solicitud queda almacenada en base de datos para revisión del admin

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF05.1 — Formulario completo de solicitud (2 pts)
**Descripción:** El formulario de solicitud de carpintero debe incluir datos de contacto, experiencia y tipo de trabajos.

**Qué buscar en el código:**
- Campos del formulario: nombre, teléfono, correo, experiencia (años o descripción), tipo de trabajos (muebles, pisos, puertas, etc.)
- Validación de campos obligatorios

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF05.2 — Notificación de estado de solicitud al carpintero (2 pts)
**Descripción:** El sistema debe notificar al carpintero sobre el estado de su solicitud (aprobada o rechazada).

**Qué buscar en el código:**
- Envío de email al carpintero cuando el admin aprueba o rechaza (signal de Django, método en vista, o Celery task)
- Configuración de email activa
- Template del email de notificación

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF06 / RF06.1 — Solicitud de ingreso de carpintero (3 pts)
**Descripción:** El sistema debe permitir a carpinteros enviar una solicitud de ingreso a la plataforma.

**Nota:** RF06 y RF06.1 son equivalentes en el backlog. Verificar como un solo flujo.

**Qué buscar en el código:**
- Flujo completo desde que el carpintero envía la solicitud hasta que queda registrada en el sistema
- Estado inicial de la solicitud: "pendiente de aprobación"

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF07 — Aprobar o rechazar solicitudes de carpinteros (5 pts)
**Descripción:** El administrador puede revisar, aprobar o rechazar solicitudes de registro de carpinteros.

**Qué buscar en el código:**
- Vista del panel de admin que muestra solicitudes pendientes
- Acción de aprobar: cambia el estado de la solicitud y activa la cuenta del carpintero
- Acción de rechazar: cambia el estado y opcionalmente envía notificación
- Solo el rol admin puede acceder a esta funcionalidad

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

## SPRINT 2 — Gestión de Perfiles y Administración
**Periodo:** 8 de abril al 15 de abril de 2026
**Objetivo:** Perfiles de carpinteros, galería de trabajos, sistema de reseñas y herramientas de administración.

---

### RF08 — Listado de carpinteros para el admin (5 pts)
**Descripción:** El administrador puede consultar el listado completo de carpinteros registrados con sus perfiles.

**Qué buscar en el código:**
- Vista del admin que lista todos los carpinteros (`CarpinteroListView` o similar)
- Filtros por estado (aprobado, pendiente, bloqueado)
- Paginación si hay muchos registros
- Acceso restringido al rol admin

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF08.1 — Perfil detallado de carpintero para admin (3 pts)
**Descripción:** El administrador puede ver el perfil completo de un carpintero específico.

**Qué buscar en el código:**
- Vista de detalle de carpintero accesible desde el listado
- Muestra información personal, experiencia, estado y estadísticas

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF08.2 — Aprobar o rechazar solicitudes de registro (5 pts)
**Descripción:** El administrador puede aprobar o rechazar solicitudes de registro de carpinteros desde el panel.

**Nota:** Relacionado con RF07. Verificar que la acción también esté disponible desde el detalle de carpintero.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF08.3 — Modificar información de carpintero (3 pts)
**Descripción:** El administrador puede editar la información de un carpintero cuando sea necesario.

**Qué buscar en el código:**
- Vista de edición del perfil del carpintero accesible para el admin
- Formulario con los campos del perfil del carpintero
- Solo el admin puede editar perfiles de otros usuarios

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF08.4 — Bloquear o desbloquear carpintero (3 pts)
**Descripción:** El administrador puede bloquear o desbloquear el acceso de un carpintero a la plataforma.

**Qué buscar en el código:**
- Campo `is_active` o campo `bloqueado` en el modelo del carpintero
- Vista/acción para cambiar este estado
- Carpintero bloqueado no puede iniciar sesión o ve mensaje de cuenta suspendida

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF08.5 — Historial de trabajos y calificaciones del carpintero (admin) (3 pts)
**Descripción:** El administrador puede ver el historial de trabajos, calificaciones y comentarios de cada carpintero.

**Qué buscar en el código:**
- Sección en el detalle del carpintero que muestra sus reseñas y calificaciones
- Posiblemente también el historial de solicitudes completadas

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF09 — Carpintero edita su perfil (5 pts)
**Descripción:** Los carpinteros pueden editar su propio perfil una vez registrados.

**Qué buscar en el código:**
- Vista de edición de perfil accesible para el carpintero autenticado
- Solo puede editar su propio perfil (no el de otros)
- Formulario de edición con validación

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF09.1 — Perfil incluye ubicación, servicio, experiencia y galería (3 pts)
**Descripción:** El perfil del carpintero incluye: ubicación, tipo de servicio, experiencia y galería de trabajos.

**Qué buscar en el código:**
- Modelo `PerfilCarpintero` con campos: `ubicacion`, `tipo_servicio`, `experiencia`, y relación con imágenes de galería
- Estos campos se muestran en la vista del perfil público

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF10 — Visualizar perfiles de carpinteros aprobados (3 pts)
**Descripción:** Los usuarios (incluso sin login) pueden visualizar los perfiles de carpinteros que han sido aprobados.

**Qué buscar en el código:**
- Vista pública de listado y detalle de carpinteros aprobados
- Filtro en la query que solo muestra carpinteros con estado "aprobado"
- No requiere autenticación para acceder

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF11 — Galería de trabajos en el perfil (5 pts)
**Descripción:** El perfil del carpintero muestra una galería de trabajos anteriores.

**Qué buscar en el código:**
- Modelo `ImagenGaleria` o similar con FK a `PerfilCarpintero`
- Vista o sección que muestra las imágenes en el perfil
- Funcionalidad de subida de imágenes (campo `ImageField` y configuración de `MEDIA_ROOT`)

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF11.1 — Imágenes con título, descripción y fecha (2 pts)
**Descripción:** Cada imagen de la galería debe tener título, descripción y fecha.

**Qué buscar en el código:**
- Modelo de imagen con campos: `titulo`, `descripcion`, `fecha` (o `created_at`)
- Estos datos se muestran junto a cada imagen en el perfil

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF12 — Sistema de reseñas y calificaciones (3 pts)
**Descripción:** Los usuarios registrados pueden dejar reseñas con calificación y comentario. El sistema muestra el promedio de calificaciones en el perfil del carpintero. El administrador puede consultar todas las reseñas.

**Qué buscar en el código:**
- Modelo `Resena` con campos: `calificacion` (1-5), `comentario` (opcional), FK a `User` y a `PerfilCarpintero`
- Vista para crear reseña (solo usuarios autenticados)
- Cálculo del promedio de calificaciones (puede ser con `annotate(avg=Avg('resenas__calificacion'))`)
- Promedio visible en el perfil público del carpintero
- Vista del admin para ver todas las reseñas

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF12.1 — Reseña con calificación 1-5 y comentario opcional (2 pts)
**Descripción:** Cada reseña incluye una calificación de 1 a 5 estrellas y un comentario opcional.

**Qué buscar en el código:**
- Campo `calificacion` con validación de rango (1-5), ej. `IntegerField(validators=[MinValueValidator(1), MaxValueValidator(5)])`
- Campo `comentario` como `TextField(blank=True, null=True)`

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

## REQUERIMIENTOS FUNCIONALES FUERA DE SPRINTS (Backlog)
Estos RF existen en el backlog pero no fueron asignados a ningún sprint. Verificar si ya están implementados.

---

### RF13 — Carpintero consulta solicitudes recibidas (5 pts)
**Descripción:** Los carpinteros pueden ver las solicitudes de servicio que han recibido con el detalle del cliente.

**Qué buscar:** Modelo `SolicitudServicio`, vista de listado para carpintero, detalle con información del cliente.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF14 — Registro de visualizaciones de perfil (2 pts)
**Descripción:** El sistema registra cada vez que un usuario visualiza o contacta a un carpintero.

**Qué buscar:** Modelo `Visita` o campo de contador, signal o lógica en la vista de detalle del carpintero.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF15 — Notificaciones al carpintero por nueva solicitud (5 pts)
**Descripción:** El sistema notifica al carpintero cuando recibe una nueva solicitud de servicio.

**Qué buscar:** Signal `post_save` en `SolicitudServicio`, envío de email, o tarea Celery, o notificación en el sistema.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF16 — Estado de solicitud en tiempo real (3 pts)
**Descripción:** El usuario puede consultar el estado actual de su solicitud.

**Qué buscar:** Campo `estado` en `SolicitudServicio` (pendiente, aceptada, rechazada, completada), vista de seguimiento para el cliente.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF17 — Página de inicio (2 pts)
**Descripción:** El sistema muestra en la página de inicio la información general del proyecto.

**Qué buscar:** Vista `HomeView` o función `home`, template `index.html` o `home.html`, URL raíz `/` mapeada.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF18 — Página Sobre Nosotros (2 pts)
**Descripción:** Sección con el propósito del proyecto y el equipo creador.

**Qué buscar:** Vista y template para `/about/` o `/sobre-nosotros/`.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF19 — Página de FAQ (2 pts)
**Descripción:** Sección de preguntas frecuentes organizada, accesible para cualquier usuario.

**Qué buscar:** Vista y template para `/faq/`, puede ser estática o con modelo `Pregunta`.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF20 — Página de Contacto (2 pts)
**Descripción:** Sección con información de contacto y medios oficiales.

**Qué buscar:** Vista y template para `/contacto/`, formulario de contacto opcional.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF21 / RF22 — Perfil y actualización de datos del cliente (2 pts)
**Descripción:** Los usuarios pueden actualizar su información de perfil (nombre, teléfono, ciudad).

**Qué buscar:** Vista de perfil del cliente, formulario de edición, campos nombre/teléfono/ciudad en el modelo.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF23 — Carpintero edita proyectos del portafolio (3 pts)
**Descripción:** Los carpinteros pueden editar proyectos de su portafolio (galería).

**Qué buscar:** Vista de edición de imagen/proyecto de galería, solo el dueño puede editar.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF24 — Carpintero elimina proyectos del portafolio (2 pts)
**Descripción:** Los carpinteros pueden eliminar proyectos de su portafolio.

**Qué buscar:** Vista de eliminación con confirmación, solo el dueño puede eliminar.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF25 — Carpintero actualiza estado de solicitudes (3 pts)
**Descripción:** El carpintero puede cambiar el estado de una solicitud a: aceptada, rechazada o completada.

**Qué buscar:** Vista para actualizar el campo `estado` de `SolicitudServicio`, accesible solo para el carpintero receptor.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF26 — Notificación al cliente cuando cambia estado de solicitud (3 pts)
**Descripción:** El sistema envía una notificación al cliente cuando el estado de su solicitud cambia.

**Qué buscar:** Signal `post_save` o lógica en la vista de actualización de estado, envío de email o notificación interna.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF27 — Marcar notificaciones como leídas (2 pts)
**Descripción:** Los usuarios pueden marcar sus notificaciones como leídas.

**Qué buscar:** Modelo `Notificacion` con campo `leida` (booleano), vista o endpoint para marcar como leída.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RF28 — Historial de actividades del usuario (3 pts)
**Descripción:** El sistema muestra un historial de actividades relevantes del usuario.

**Qué buscar:** Vista de historial para el cliente o carpintero, puede ser una lista de solicitudes, notificaciones o eventos.

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

## REQUERIMIENTOS NO FUNCIONALES

### RNF1 — Diseño Responsivo (3 pts)
**Descripción:** La interfaz debe funcionar correctamente en móvil, tablet y escritorio.

**Qué buscar:**
- Uso de Bootstrap, Tailwind u otro framework CSS con breakpoints
- Meta viewport en el template base: `<meta name="viewport" content="width=device-width, initial-scale=1">`
- Tablas y formularios con clases responsivas

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RNF3 — Seguridad (5 pts)
**Descripción:** La plataforma debe ser segura: autenticación robusta, autorización correcta y protección de datos.

**Qué buscar:**
- `SECRET_KEY` en variable de entorno (`.env`), no escrita en `settings.py`
- `DEBUG = False` o condicional en producción
- `ALLOWED_HOSTS` configurado (no `['*']` en producción)
- CSRF activo en todos los formularios POST
- Validación de inputs en forms y serializers
- `@login_required` o mixins de autenticación en todas las vistas protegidas
- Contraseñas nunca almacenadas en texto plano

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RNF6 — Usabilidad (2 pts)
**Descripción:** La interfaz debe ser intuitiva y accesible para usuarios sin conocimientos técnicos.

**Qué buscar:**
- Mensajes de error claros en formularios (uso de `messages` de Django o errores del form)
- Confirmación antes de acciones destructivas (eliminar, bloquear)
- Navegación consistente entre secciones

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

### RNF7 — SEO (2 pts)
**Descripción:** La plataforma debe estar optimizada para motores de búsqueda.

**Qué buscar:**
- Meta tags en el template base (`<title>`, `<meta name="description">`)
- URLs limpias y descriptivas (ej. `/carpinteros/juan-perez/` en lugar de `/carpintero/?id=5`)
- Uso de `django.contrib.sitemaps` para generar sitemap
- `robots.txt` disponible

**Estado:** [ ] COMPLETADO / [ ] INCOMPLETO / [ ] NO IMPLEMENTADO

**Observaciones de la IA:** _(completar aquí)_

---

## PLANTILLA DE REPORTE FINAL

> Instrucción para la IA: Una vez analizado todo el código, completa esta sección con los resultados reales.

## REPORTE DE REVISIÓN — Lf_Carpinter
Fecha de análisis: 13 de Mayo de 2026

### Resumen de estados:
- ✅ COMPLETADO:        35 / 41 RF  +  3 / 4 RNF
- ⚠️ INCOMPLETO:        3 / 41 RF  +  1 / 4 RNF (SEO básico, falta sitemap/robots.txt)
- ❌ NO IMPLEMENTADO:   3 / 41 RF (Página Sobre Nosotros, FAQ, Registro de Visualizaciones)
- 🔒 BLOQUEADO:         0 / 41 RF

### Porcentaje de avance estimado: 88%

### Prioridades urgentes (top 5 cosas a implementar ya):
1. **Unificación de Interfaz (UI/UX):** Los usuarios y carpinteros deben compartir paradigmas de navegación (breadcrumbs, botones de retroceso, modales) para reducir la curva de aprendizaje.
2. **Sistema de Notificaciones (RF15/RF26):** Implementar la capa de notificaciones asíncronas con Django Channels para alertar cambios en el Kanban en tiempo real.
3. **Páginas Estáticas (RF18, RF19, RF20):** Crear las vistas "Sobre Nosotros", "FAQ" y "Contacto" para dar profesionalismo corporativo a la web.
4. **Optimización SEO (RNF7):** Añadir `sitemap.xml`, `robots.txt` y meta tags dinámicos por cada perfil de carpintero.
5. **Métricas de Rendimiento (RF14):** Implementar modelo de analíticas ligeras para rastrear clics en perfiles de carpinteros.

### Riesgos detectados en el código:
- Faltan pruebas unitarias (tests) automatizados, lo que hace frágil la refactorización futura.
- Dependencia de recargas de página en algunas áreas antiguas, aunque mitigado por el nuevo Kanban con AJAX.

### Recomendaciones técnicas Django:
- Implementar Celery + Redis para la gestión de envíos masivos de correos y evitar bloquear el hilo principal.
- Utilizar `select_related` y `prefetch_related` exhaustivamente en las vistas de listados de portafolio para reducir N+1 queries.
- Abstraer componentes UI (botones, cards) en plantillas o custom template tags para mantener DRY el HTML.

---

*Archivo generado para análisis por IA — Proyecto Lf_Carpinter | Mayo 2026*
