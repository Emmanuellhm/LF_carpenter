# LF CARPINTER - Product Backlog
## Plataforma de Contratación de Carpinteros

---

## SPRINT 1: Autenticación y Gestión de Usuarios

### Historia de Usuario 1.1: Registro de Cliente
- **ID:** RF01.1
- **Título:** Registro de clientes
- **Descripción:** Como cliente, quiero registrarme mediante un formulario básico para acceder a la plataforma.
- **Criterios de aceptación:**
  - Formulario con: nombre, correo, teléfono, contraseña
  - Validación de datos
  - Almacenamiento en BD
- **Prioridad:** Must Have
- **Estimación:** 3 puntos

### Historia de Usuario 1.2: Registro de Carpintero
- **ID:** RF01.2, RF05, RF05.1
- **Título:** Solicitud de registro de carpintero
- **Descripción:** Como carpintero, quiero enviar una solicitud de registro con mis datos profesionales.
- **Criterios de aceptación:**
  - Formulario con: nombre, oficio, experiencia, teléfono, especialidad
  - Adjuntar CV
  - Envío de solicitud al admin
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

### Historia de Usuario 1.3: Inicio de Sesión
- **ID:** RF02, RF02.1
- **Título:** Inicio de sesión multi-rol
- **Descripción:** Como usuario, quiero iniciar sesión para acceder a mi panel correspondiente.
- **Criterios de aceptación:**
  - Login con email y contraseña
  - Redirección según rol (cliente/carpintero/admin)
  - Sesión segura
- **Prioridad:** Must Have
- **Estimación:** 3 puntos

### Historia de Usuario 1.4: Recuperación de Contraseña
- **ID:** RF03
- **Título:** Recuperar contraseña
- **Descripción:** Como usuario, quiero recuperar mi contraseña por correo electrónico.
- **Criterios de aceptación:**
  - Formulario de recuperación
  - Envío de enlace por correo
  - Cambio de contraseña seguro
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

---

## SPRINT 2: Gestión de Perfiles

### Historia de Usuario 2.1: Perfil de Carpintero
- **ID:** RF09, RF09.1
- **Título:** Edición de perfil de carpintero
- **Descripción:** Como carpintero, quiero editar mi perfil con ubicación, servicios y experiencia.
- **Criterios de aceptación:**
  - Edición de datos: ubicación, tipo de servicio, experiencia
  - Galería de trabajos
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

### Historia de Usuario 2.2: Portafolio de Trabajos
- **ID:** RF11, RF11.1
- **Título:** Gestión de portafolio
- **Descripción:** Como carpintero, quiero mostrar mi galería de trabajos anteriores.
- **Criterios de aceptación:**
  - Subir imágenes con título, descripción, fecha
  - Editar y eliminar proyectos
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

### Historia de Usuario 2.3: Actualización de Perfil de Cliente
- **ID:** RF21, RF22
- **Título:** Editar perfil de cliente
- **Descripción:** Como cliente, quiero actualizar mi información personal.
- **Criterios de aceptación:**
  - Editar nombre, teléfono, ciudad
  - Cambiar contraseña
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

---

## SPRINT 3: Búsqueda y Contacto

### Historia de Usuario 3.1: Ver Perfiles de Carpinteros
- **ID:** RF10
- **Título:** Directorio de carpinteros
- **Descripción:** Como cliente, quiero ver los perfiles de carpinteros aprobados.
- **Criterios de acceso:**
  - Listado de carpinteros verificados
  - Información de contacto y especialización
- **Prioridad:** Must Have
- **Estimación:** 3 puntos

### Historia de Usuario 3.2: Solicitud de Servicio
- **ID:** RF13, RF14, RF15
- **Título:** Solicitar servicio a carpintero
- **Descripción:** Como cliente, quiero enviar una solicitud de servicio a un carpintero.
- **Criterios de aceptación:**
  - Formulario con descripción del proyecto
  - Registro de visualización/contacto
  - Notificación al carpintero
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

### Historia de Usuario 3.3: Gestión de Solicitudes (Carpintero)
- **ID:** RF25
- **Título:** Responder solicitudes
- **Descripción:** Como carpintero, quiero aceptar, rechazar o completar solicitudes.
- **Criterios de aceptación:**
  - Ver solicitudes recibidas
  - Cambiar estado (pendiente/aceptada/rechazada/completada)
- **Prioridad:** Must Have
- **Estimación:** 3 puntos

---

## SPRINT 4: Sistema de Reviews

### Historia de Usuario 4.1: Reseñas y Calificaciones
- **ID:** RF12, RF12.1
- **Título:** Calificar servicios
- **Descripción:** Como cliente, quiero calificar y comentar el trabajo del carpintero.
- **Criterios de aceptación:**
  - Calificación de 1 a 5 estrellas
  - Comentario opcional
  - Mostrar promedio en perfil
- **Prioridad:** Should Have
- **Estimación:** 3 puntos

---

## SPRINT 5: Notificaciones y Historial

### Historia de Usuario 5.1: Notificaciones
- **ID:** RF15, RF26, RF27
- **Título:** Sistema de notificaciones
- **Descripción:** Como usuario, quiero recibir notificaciones sobre el estado de mis solicitudes.
- **Criterios de aceptación:**
  - Notificaciones por nuevas solicitudes
  - Notificaciones por cambio de estado
  - Marcar como leídas
- **Prioridad:** Should Have
- **Estimación:** 5 puntos

### Historia de Usuario 5.2: Historial de Actividad
- **ID:** RF16, RF28
- **Título:** Historial de solicitudes
- **Descripción:** Como usuario, quiero consultar el estado de mis solicitudes y actividades.
- **Criterios de aceptación:**
  - Ver estado en tiempo real
  - Historial de actividades
- **Prioridad:** Should Have
- **Estimación:** 3 puntos

---

## SPRINT 6: Administración

### Historia de Usuario 6.1: Aprobación de Carpinteros
- **ID:** RF07, RF08.2
- **Título:** Revisar solicitudes de carpinteros
- **Descripción:** Como admin, quiero aprobar o rechazar solicitudes de carpinteros.
- **Criterios de aceptación:**
  - Ver listado de solicitudes pendientes
  - Aprobar o rechazar
  - Notificar al carpintero
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

### Historia de Usuario 6.2: Gestión de Carpinteros
- **ID:** RF08, RF08.1, RF08.3, RF08.4, RF08.5
- **Título:** Administración de carpinteros
- **Descripción:** Como admin, quiero gestionar la información de los carpinteros.
- **Criterios de aceptación:**
  - Consultar listado de carpinteros
  - Ver perfil individual
  - Modificar información
  - Bloquear/desbloquear acceso
  - Ver historial y calificaciones
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

---

## SPRINT 7: Páginas Públicas

### Historia de Usuario 7.1: Página de Inicio
- **ID:** RF17
- **Título:** Landing page
- **Descripción:** Como visitante, quiero ver información general del proyecto.
- **Criterios de aceptación:**
  - Información del servicio
  - Llamados a acción (registro)
- **Prioridad:** Must Have
- **Estimación:** 2 puntos

### Historia de Usuario 7.2: Sobre Nosotros
- **ID:** RF18
- **Título:** Página Sobre Nosotros
- **Descripción:** Como visitante, quiero conocer el propósito del proyecto y el equipo.
- **Criterios de aceptación:**
  - Historia de la empresa
  - Misión y visión
  - Equipo de trabajo
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

### Historia de Usuario 7.3: Preguntas Frecuentes
- **ID:** RF19
- **Título:** Sección FAQ
- **Descripción:** Como visitante, quiero encontrar respuestas a preguntas comunes.
- **Criterios de aceptación:**
  - Preguntas organizadas por categoría
  - Accordion o lista desplegable
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

### Historia de Usuario 7.4: Página de Contacto
- **ID:** RF20
- **Título:** Página de contacto
- **Descripción:** Como visitante, quiero encontrar información de contacto.
- **Criterios de aceptación:**
  - Formulario de contacto
  - Datos de contacto (email, teléfono, dirección)
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

---

## SPRINT 8: Requisitos No Funcionales

### Historia de Usuario 8.1: Diseño Responsivo
- **ID:** RNF1
- **Título:** Compatibilidad multi-dispositivo
- **Descripción:** La plataforma debe funcionar en cualquier navegador y dispositivo móvil.
- **Criterios de aceptación:**
  - Prueba en Chrome, Firefox, Safari, Edge
  - Prueba en móvil y tablet
- **Prioridad:** Must Have
- **Estimación:** 3 puntos

### Historia de Usuario 8.2: Seguridad
- **ID:** RNF3, RNF4
- **Título:** Seguridad de datos
- **Descripción:** La información debe almacenarse de forma segura.
- **Criterios de aceptación:**
  - Contraseñas hasheadas
  - Sesiones seguras
  - Restricción de acceso admin
- **Prioridad:** Must Have
- **Estimación:** 5 puntos

### Historia de Usuario 8.3: Usabilidad
- **ID:** RNF6
- **Título:** Interfaz intuitiva
- **Descripción:** La interfaz debe ser fácil de usar.
- **Criterios de aceptación:**
  - Navegación clara
  - Para usuarios con conocimientos básicos
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

### Historia de Usuario 8.4: SEO
- **ID:** RNF7
- **Título:** Optimización para motores de búsqueda
- **Descripción:** Los contenidos públicos deben estar optimizados para SEO.
- **Criterios de aceptación:**
  - Meta tags
  - Títulos y descripciones
  - Estructura semántica
- **Prioridad:** Should Have
- **Estimación:** 2 puntos

---

## Resumen por Sprint

| Sprint | Módulo | Puntos |
|--------|--------|--------|
| 1 | Autenticación y Usuarios | 13 |
| 2 | Gestión de Perfiles | 12 |
| 3 | Búsqueda y Contacto | 11 |
| 4 | Reviews y Calificaciones | 3 |
| 5 | Notificaciones y Historial | 8 |
| 6 | Administración | 10 |
| 7 | Páginas Públicas | 8 |
| 8 | Requisitos No Funcionales | 12 |
| **TOTAL** | | **77** |
