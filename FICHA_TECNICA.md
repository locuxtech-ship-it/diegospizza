# 📋 Ficha Técnica — Diego's Pizza

## Información General
| Campo | Detalle |
|-------|---------|
| **Proyecto** | Sistema de pedidos de pizza con delivery |
| **Cliente** | Diego's Pizza Alameda |
| **Dominio** | diegospizzabq.click |
| **Ubicación** | Alameda Del Rio, Bucaramanga, Colombia |
| **Teléfono** | +57 3106444759 |
| **Zona horaria** | America/Bogota (COT, UTC-5) |

## Stack Tecnológico
| Componente | Tecnología | Versión |
|------------|-----------|---------|
| Backend | Laravel | 13 |
| Admin Panel | FilamentPHP | 5 |
| Frontend Livewire | Livewire | 4 |
| CSS | Tailwind CSS | 4 |
| Base de datos | MariaDB | 11 |
| Servidor web | Nginx | Alpine |
| PHP Runtime | PHP-FPM | 8.x |
| Queue Worker | Supervisor | — |
| Contenedores | Docker Compose | — |
| Tunnel | Cloudflared | — |
| VPN | Tailscale | — |

## Arquitectura

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│  Cloudflared │────▶│    Nginx     │────▶│  PHP-FPM    │
│   (tunnel)   │     │  (alpine)    │     │  (app)      │
└─────────────┘     └──────────────┘     └──────┬──────┘
                                                │
                    ┌──────────────┐     ┌──────▼──────┐
                    │   WAHA       │     │  MariaDB 11 │
                    │  (WhatsApp)  │     │  (database) │
                    └──────────────┘     └─────────────┘
```

### Contenedores Docker
| Contenedor | Imagen | Función |
|-----------|--------|---------|
| `diegospizza-app` | Custom PHP-FPM | Backend Laravel + Queue Worker |
| `diegospizza-nginx` | nginx:alpine | Servidor web + proxy reverso |
| `diegospizza-db` | mariadb:11 | Base de datos |
| `diegospizza-waha` | devlikeapro/waha:latest | API WhatsApp (WAHA) |

### Volúmenes persistentes
- `dbdata` → datos MariaDB
- `storage_data` → archivos Laravel storage/public

## Base de Datos

### Tablas principales
| Tabla | Descripción |
|-------|-------------|
| `pedidos` | Órdenes con total, estado, origen, descuentos |
| `pedido_productos` | Items por pedido (con mitades y variantes) |
| `pagos` | Pagos confirmados/pendientes por pedido |
| `clientes` | Clientes con teléfono, dirección, puntos |
| `productos` | Menú con categorías y variantes |
| `categorias` | Categorías del menú |
| `puntos` | Historial de fidelidad (ganados/canjeados) |
| `cierres_caja` | Cierres de caja diarios |
| `gastos_cierre` | Gastos del cierre |
| `negocio_settings` | Configuración global (horarios, métodos pago, chatbot) |
| `users` | Usuarios con rol admin/cajero |

### Campos clave de `pedidos`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `total` | decimal | **= subtotal - descuento_puntos - descuento_manual** (ya incluye descuentos) |
| `estado` | enum | pendiente_pago → en_proceso → en_camino → entregado → finalizado/cancelado |
| `origen` | enum | `web` (checkout público) / `pdv` (punto de venta) |
| `numero_pedido` | int | **Se reinicia cada día** — usar `id` para enlaces |
| `metodo_pago` | string | efectivo/tarjeta/transferencia/mixto |

## Funcionalidades

### 1. Punto de Venta (PDV)
- Vista Kanban (4 columnas) + vista lista
- Timer por pedido (MM:SS, congela a 60:00)
- Notificaciones JS con sonido + toast + impresión iframe
- Modal de pago completo (productos, totales, descuentos, historial)
- Botón WhatsApp directo al cliente
- Reimpresión de tickets
- Cambiar método de pago sin registrar pago

### 2. Checkout Público (`/checkout`)
- Pedido como cliente con datos de contacto
- Carrito de compras con mitad y mitad
- Sistema de puntos de fidelidad
- Selección de recompensa manual
- Confirmación WhatsApp automática
- `DB::transaction` en creación (evita race condition de tickets)

### 3. Fidelidad
- Puntos por compra (configurable: $X → Y puntos)
- Canje de recompensas (% o $ de descuento)
- Cliente elige recompensa en checkout
- Historial de puntos visible en perfil

### 4. Reportes
- Semanal (martes → lunes), mensual, anual
- Comparativa con periodo anterior (+/- %)
- Tablas: sabores más vendidos, tamaños, mitades
- Exportación CSV (BOM, separador `;`)

### 5. Cierre de Caja
- Totales por método de pago
- Gastos registrados
- Cuadre de efectivo con diferencia
- Ticket térmico de cierre

### 6. WhatsApp Bot (WAHA)
- Auto-respuesta con menú configurable
- Mensaje de bienvenida con placeholders ({nombre}, {empresa})
- Notificaciones de estado de pedido
- Sistema de reseñas post-entrega (link usa `id`, no `numero_pedido`)
- QR de conexión con auto-recuperación (watchdog cada 2 min)
- Auto-actualización de imagen (domingos 4AM)

### 7. Impresión
- Ticket 57mm/80mm vía `window.print()`
- Auto-impresión desde PDV (iframe, sin duplicado)
- El ticket solo auto-imprime fuera de iframe (`window.self === window.top`)
- Tamaño y formato configurables desde admin

### 8. Gestión de Menú
- CRUD completo de categorías, productos, variantes
- Mitad y mitad personalizable
- Imágenes de producto
- Agrupación por categoría en admin

### 9. Chat Bot Panel
- Estado de conexión con QR
- Configuración de auto-respuesta, bienvenida, menú
- Notificaciones de pedidos
- Reseñas habilitadas/deshabilitadas

## Seguridad

| Aspecto | Implementación |
|---------|---------------|
| Autenticación | Laravel Auth + sesión |
| Roles | `admin` (total) / `cajero` (restringido) |
| Cajero ve | PDV, Orden Manual, Productos toggle, Historial (solo hoy, sin stats, sin descuentos) |
| Gate | `applyDiscount` → solo admin |
| Eliminar pedidos | Solo admin |
| API Keys | WhatsApp API key, print agent key |
| `.env` | Fuera de repo, inyectado via docker-compose |
| Contraseñas | Hash bcrypt (Laravel) |

## Infraestructura

| Aspecto | Detalle |
|---------|---------|
| **Servidor** | Ubuntu + Docker |
| **Acceso SSH** | Tailscale `100.122.80.102`, user `oliver` |
| **Ruta deploy** | `/backups/diegospizza` |
| **Repositorio** | GitHub `locuxtech-ship-it/diegospizza` |
| **Rama** | `master` (deploy directo) |
| **Backup DB** | Cron diario `mysqldump` (retención 7 días) |
| **WAHA Watchdog** | Cada 2 min, recupera sesión caída |
| **WAHA Auto-update** | Domingos 4AM, actualiza imagen si hay nueva |
| **SSL/TLS** | Via Cloudflared tunnel |
| **Restore points** | Tags git antes de cada cambio (`pre-fix-*`, `v*`) |

## Endpoints Principales

| Ruta | Descripción |
|------|-------------|
| `/` | Checkout público |
| `/checkout` | Carrito y confirmación |
| `/admin` | Panel Filament (login) |
| `/admin/comandas` | Punto de Venta |
| `/admin/nuevo-pedido` | Orden Manual |
| `/admin/ventas/historial` | Historial de pedidos |
| `/admin/ventas/reportes` | Reportes y comparativas |
| `/admin/ticket/{id}` | Ticket térmico |
| `/admin/ticket/cierre/{id}` | Ticket de cierre de caja |
| `/admin/clientes/{id}/ver` | Vista de cliente |
| `/admin/chat-bot` | Configuración del chatbot |
| `/api/pedidos/pendientes` | Polling de nuevos pedidos |
| `/api/agent/ticket/{id}` | Ticket para agente de impresión |

## Proceso de Deploy

1. Crear tag de restauración: `git tag pre-fix-*`
2. Commit + push a `master`
3. En servidor: `git pull origin master`
4. `docker compose build app`
5. `docker compose up -d --force-recreate app`
6. Verificar: HTTP 200 + logs sin errores
7. Tag de versión: `git tag v*`

## Historial de Versiones

| Versión | Fecha | Cambio |
|---------|-------|--------|
| v1.9/v1.9.1 | — | Fix descuento doble resta en total |
| v2.1/v2.1.1 | — | Reporte comparativo semanal/mensual |
| v2.2 | — | Fix links de reseña (usar `id`, no `numero_pedido`) |
| v2.3.1 | — | Historial de cierres de caja |
| v2.4/v2.4.1 | — | Fix `round()` 4 argumentos (agregar productos) |
| v2.5 | — | Fix WAHA QR (imagen desactualizada + auto-update) |
| v2.6 | 10 oct 2026 | Cambiar método de pago desde modal |
| v2.7 | 11 oct 2026 | Fix race condition ticket + doble impresión |
| v2.8 | 12 oct 2026 | Fix WhatsApp status stuck en STARTING |
| v2.9 | 12 oct 2026 | Fix reporte semanal $0 los lunes |
| v3.0 | 12 oct 2026 | Horario AM/PM + próxima apertura + iconos móvil |
