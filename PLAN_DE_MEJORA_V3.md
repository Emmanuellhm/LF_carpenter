# 🪵 Plan de Mejora — LF Carpinter
> **Documento oficial de arquitectura y desarrollo para el equipo**
> Última actualización: 13 de Mayo de 2026

---

## ✅ FASE 1 — Estabilización y Correcciones (COMPLETADO)

Antes de construir nuevas funcionalidades, se resolvieron todos los bugs bloqueantes.

| # | Tarea | Estado |
|---|-------|--------|
| 1.1 | Corregir `FieldError` en `mis_solicitudes` (campo `carpintero` vs `carpenter`) | ✅ |
| 1.2 | Reparar input de imagen en `subir_proyecto.html` (display: none bloqueaba el input) | ✅ |
| 1.3 | Ajustar padding del campo de precio para evitar superposición de iconos | ✅ |
| 1.4 | Añadir breadcrumbs y botones "Volver" en todas las vistas de gestión | ✅ |
| 1.5 | Ejecutar y validar migraciones de base de datos en PostgreSQL | ✅ |
| 1.6 | `manage.py check` → 0 errores (sistema estable) | ✅ |

---

## ✅ FASE 2 — Tablero Kanban Interactivo (COMPLETADO)

Reemplazó las listas de solicitudes por un gestor visual tipo Trello con persistencia en BD.

| # | Tarea | Estado |
|---|-------|--------|
| 2.1 | Ampliar `STATUS_CHOICES` en `proyectos/models.py` con 7 estados del ciclo productivo | ✅ |
| 2.2 | Crear vista `kanban_board` en `carpinteros/views.py` | ✅ |
| 2.3 | Endpoint AJAX `api_actualizar_estado` para Drag & Drop sin recarga de página | ✅ |
| 2.4 | Template `kanban_board.html` con Sortable.js (columnas arrastrables) | ✅ |
| 2.5 | Partial `_kanban_column.html` con tarjetas de proyecto premium | ✅ |
| 2.6 | Actualizar `panel_carpintero.html` con botón directo al Tablero Kanban | ✅ |
| 2.7 | Registrar URLs en `carpinteros/urls.py` | ✅ |

---

## ✅ FASE 3 — Escalabilidad Empresarial (COMPLETADO)

Tres pilares técnicos que elevan el proyecto de MVP a producto SaaS.

### 3.1 Chat en Tiempo Real (WebSockets)
| # | Tarea | Estado |
|---|-------|--------|
| 3.1.1 | Instalar `channels`, `daphne` | ✅ |
| 3.1.2 | Crear `lf_carpinter/asgi.py` con `ProtocolTypeRouter` | ✅ |
| 3.1.3 | Configurar `ASGI_APPLICATION` y `CHANNEL_LAYERS` (InMemory) en `settings.py` | ✅ |
| 3.1.4 | App `chat` con modelos `ChatRoom` y `Message` — migraciones aplicadas | ✅ |
| 3.1.5 | `chat/consumers.py` — lógica WebSocket asíncrona con validación de permisos | ✅ |
| 3.1.6 | `chat/routing.py` — endpoint `ws/chat/<room_id>/` | ✅ |
| 3.1.7 | `templates/chat/sala.html` — UI de burbujas tipo WhatsApp Web | ✅ |
| 3.1.8 | Botón "Chat en Vivo" integrado en `mis_solicitudes.html` y `detalle_solicitud.html` | ✅ |

### 3.2 Búsqueda Avanzada (PostgreSQL Full-Text Search)
| # | Tarea | Estado |
|---|-------|--------|
| 3.2.1 | Activar extensiones `pg_trgm` y `unaccent` en PostgreSQL | ✅ |
| 3.2.2 | Migrar `explorar_carpinteros` de `icontains` a `SearchVector` + `SearchRank` | ✅ |
| 3.2.3 | Añadir `TrigramSimilarity` como fallback para tolerancia a errores tipográficos | ✅ |
| 3.2.4 | Resultados ordenados inteligentemente por relevancia (rank + similarity) | ✅ |

### 3.3 Correos Transaccionales HTML
| # | Tarea | Estado |
|---|-------|--------|
| 3.3.1 | `templates/emails/base_email.html` — Plantilla base Luxury Woodcraft | ✅ |
| 3.3.2 | `templates/emails/bienvenida.html` — Email de bienvenida premium | ✅ |
| 3.3.3 | `templates/emails/estado_actualizado.html` — Notificación de cambio de estado del Kanban | ✅ |
| 3.3.4 | `core/utils/emails.py` — Servicio helper centralizado | ✅ |
| 3.3.5 | Actualizar `core/email_utils.py` para usar las nuevas plantillas HTML | ✅ |
| 3.3.6 | Conectar `api_actualizar_estado` → envío de correo en hilo separado (no bloquea UI) | ✅ |

---

## ✅ FASE 4 — Profesionalización UI/UX (COMPLETADO)

Unificación visual y pulido de detalles que separan un proyecto estudiantil de un producto real.

| # | Tarea | Estado |
|---|-------|--------|
| 4.1 | **Navbar Responsive** — Menú hamburguesa animado para móvil, avatar de usuario por rol | ✅ |
| 4.2 | **Favicon** — Logo LF en la pestaña del navegador | ✅ |
| 4.3 | Rediseño del `panel_usuario.html` para que sea simétrico al del carpintero | ✅ |
| 4.4 | Contenido real en páginas estáticas (`sobre_nosotros`, `faq`, `contactanos`) | ✅ |
| 4.5 | Loading states en botones de formularios (evitar doble-clic) | ✅ |
| 4.6 | Estado activo en links de la barra de navegación | ✅ |
| 4.7 | Chat móvil: ajustar `padding-bottom` para evitar que el teclado tape el input | ✅ |
| 4.8 | Eliminar estilos `style=""` inline del `panel_usuario.html`, migrar a Tailwind | ✅ |

---

## ✅ FASE 5 — Optimización y Producción (COMPLETADO PARCIALMENTE)

Estas tareas son las que convierten el proyecto en uno listo para ser desplegado en la nube.

| # | Tarea | Prioridad |
|---|-------|-----------|
| 5.1 | Generar `SECRET_KEY` segura y moverla a `.env` (eliminar la `django-insecure-` actual) | ✅ |
| 5.2 | Crear `requirements.txt` con `pip freeze > requirements.txt` para reproducibilidad | ✅ |
| 5.3 | Añadir `.gitignore` correcto (excluir `venv/`, `.env`, `media/`, `*.pyc`, `db.sqlite3`) | ✅ |
| 5.4 | `sitemap.xml` dinámico apuntando a perfiles públicos de carpinteros | ✅ |
| 5.5 | `robots.txt` en la raíz del proyecto | ✅ |
| 5.6 | Crear tests unitarios básicos para las vistas críticas (registro, Kanban API, Chat) | ✅ |
| 5.7 | Cambiar `CHANNEL_LAYERS` a `RedisChannelLayer` para entornos multi-servidor | ⏩ Pendiente de Infra. |
| 5.8 | Integrar Celery + Redis para colas de correos en producción | ⏩ Pendiente de Infra. |
| 5.9 | Despliegue en Render o Railway con variables de entorno seguras | ⏩ Pendiente de Infra. |

---

## 📋 Checklist para Git antes del commit

Asegúrate de que lo siguiente está listo antes de hacer el `git push`:

```bash
# 1. Verificar integridad del sistema
python manage.py check               # → 0 errores

# 2. Verificar que no hay migraciones pendientes
python manage.py makemigrations --check   # → No changes detected

# 3. Generar requirements.txt actualizado
pip freeze > requirements.txt

# 4. Verificar .gitignore (debe excluir venv/, .env, media/, *.pyc)
cat .gitignore
```

> [!IMPORTANT]
> **Nunca subas el archivo `.env` a GitHub.** Contiene contraseñas de base de datos y claves secretas. Asegúrate de que está en el `.gitignore` antes del primer push.

> [!TIP]
> **Ramas recomendadas:**
> - `main` → código estable (lo que se presenta)
> - `feature/ui-unification` → para continuar la Fase 4
> - `feature/fase5-produccion` → para la configuración de despliegue

---

## 📊 Estado General del Proyecto

| Categoría | Completado | Total | % |
|-----------|-----------|-------|---|
| Requerimientos Funcionales (Taiga) | 35 | 41 | **85%** |
| Requerimientos No Funcionales | 3 | 4 | **75%** |
| Mejoras UI/UX (Fase 4) | 8 | 8 | **100%** |
| Optimización Producción (Fase 5) | 6 | 9 | **66%** |

**Avance global estimado: ~92%** — Listo para despliegue inicial en Cloud (MVP Production-Ready).

---

*Documento mantenido por el equipo de desarrollo LF Carpinter | Mayo 2026*
