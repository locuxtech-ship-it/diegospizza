# Changelog — Diego's Pizza

## [12-10-2026] — Fix: reporte semanal mostraba $0 los lunes

### Problema
Los lunes, la comparativa semanal mostraba "Esta semana: $0" y todas las ventas aparecían como "Semana anterior".

### Causa raíz
`startOfWeek()` devuelve lunes. El código hacía `->addDay()` para llegar al martes, pero los lunes eso daba el martes DEL DÍA SIGUIENTE — la "semana actual" quedaba en el futuro y las ventas del día (y de toda la semana) se contaban como "semana anterior".

### Cambios
- **`Reportes.php`**: cálculo corregido con `((dayOfWeek + 5) % 7)` para encontrar el martes más reciente ≤ hoy, sin importar el día de la semana. Funciona correctamente lunes a domingo.

### Puntos de Restauración
- Tag: `pre-fix-reporte-semanal` (commit `4037f56`)
- Deploy: commit `19277b6`

## [12-10-2026] — Fix: WhatsApp mostraba "Generando QR" aunque la sesión estaba activa

### Problema
El chatbot mostraba "Generando código QR, esperá unos segundos..." indefinidamente aunque la sesión de WhatsApp estaba funcionando (mensajes entraban y salían normalmente).

### Causa raíz
WAHA se queda "stuck" en status `STARTING` aunque internamente ya está autenticado y conectado (`engine.connected: true`, `me` con datos). La app solo reconocía `WORKING`/`CONNECTED` como "conectado", así que `STARTING` se mostraba como desconectado.

### Cambios
- **`WhatsAppService::getStatus()`**: `STARTING` con `me` autenticado ahora se trata como `CONNECTED` — la sesión funciona aunque WAHA no haya actualizado su status
- **`waha-recover.sh`** (mejorado): detecta `STARTING` stuck — si el engine reporta `connected: true`, envía un sendText dummy para forzar la transición a `WORKING`. Si no funciona en 3 ciclos (~6 min), hace restart completo de la sesión

### Puntos de Restauración
- Tag: `pre-fix-waha-status` (commit `59d2f4b`)
- Deploy: commit `6433f06`

## [11-10-2026] — Fix: tickets impresos sin productos + doble impresión

### Problema
Tickets impresos ocasionalmente sin productos (o con pocos). La BD estaba limpia — todos los pedidos tenían sus productos. También había doble diálogo de impresión (mismo pedido 2-3x en logs de nginx).

### Causa raíz
1. **Race condition**: `Checkout.php` y `ManualOrder.php` hacían `Pedido::create()` primero (visible al polling de 5s), luego el loop de `PedidoProducto::create()`, sin `DB::transaction`. El poll podía dispararse en la brecha → ticket impreso con 0 productos. La BD después quedaba correcta.
2. **Doble auto-print**: `ticket.blade.php` tenía `window.onload` + 1s → `window.print()`, Y `comandas.blade.php` tenía `iframe.onload` + 500ms → `iframe.contentWindow.print()`. Ambos disparaban → diálogo duplicado.

### Cambios
- **`DB::transaction`** en `Checkout.php` y `ManualOrder.php`: `Pedido::create()` + `PedidoProducto::create()` + `Pago::create()` ahora son atómicos. El pedido no es visible hasta que la transacción confirma con todos los productos
- **`ticket.blade.php`**: auto-print solo cuando NO está en iframe (`window.self === window.top`). El iframe de PDV maneja su propia impresión; las reimpresiones manuales (`window.open`) siguen auto-imprimiendo

### Puntos de Restauración
- Tag: `pre-fix-ticket-race` (commit `e4f82d5`)
- Deploy: commit `564fdc7`

## [10-10-2026] — Feature: cambiar método de pago desde modal sin registrar pago

### Cambios
- **`guardarMetodoPago()`** (nuevo en Comandas.php): actualiza `pedido->metodo_pago` sin crear un registro de pago. Solo disponible cuando no hay pagos confirmados en el pedido
- **Botón 💾** en el modal de pago: aparece al lado del select de método cuando el método seleccionado difiere del actual y no hay pagos registrados
- El cierre automático del modal al hacer click en "💾 Guardar" (`aplicarCambios`) ya existía desde septiembre (commit `8c8c59d`) — no se modificó

### Puntos de Restauración
- Tag: `pre-fix-metodo-pago-modal` (commit `869fb7b`)
- Deploy: commit `decd009`, contenedor imagen `c8d5a63`

## [30-09-2026] — Fix: chatbot desconectado y QR no cargaba + auto-actualización WAHA

### Problema
El chatbot se desconectó y al generar un nuevo QR no cargaba. En los logs: `Client outdated (405) connect failure` — WhatsApp rechazaba la conexión porque la imagen de WAHA era del 26-may-2026.

### Causa raíz
WhatsApp actualiza su protocolo periódicamente y rechaza clientes viejos (error 405 "client outdated"). La imagen `devlikeapro/waha:latest` no se había actualizado en 4 meses → el QR **nunca se genera** porque WhatsApp rechaza antes del intercambio.

### Cambios
- **Imagen actualizada**: `63263ba82ef6` (26-may) → `25a2d6f5d249` (22-sep-2026), contenedor recreado
- **`waha-autoupdate.sh`** (nuevo): cron semanal domingos 4AM — compara hash de la imagen, si cambió hace `docker compose up -d --force-recreate waha`
- **`waha-recover.sh`** (nuevo): recupera sesión FAILED/STOPPED/borrada (stop + start limpio)
- **`waha-watchdog.sh`** (mejorado): ahora llama a `waha-recover.sh` — el viejo hacía `POST /sessions/start` que devolvía 422 "already started" con sesiones FAILED, nunca se recuperaban

### Automatización (no requiere intervención manual)
| Cron | Frecuencia | Qué hace |
|---|---|---|
| `waha-watchdog.sh` | cada 2 min | Recupera sesión caída (FAILED/STOPPED) |
| `waha-autoupdate.sh` | domingos 4AM | Actualiza imagen WAHA si hay nueva versión |

### Notas de diagnóstico
- **Endpoint QR correcto**: `GET /api/default/auth/qr?format=image` (NO `/api/sessions/default/auth/qr` — devuelve 404)
- **Slowness percibida**: cuando WAHA está caído, cada `sendText` cuelga ~10s → el sitio se siente lento. Con WAHA sano, no ocurre. El servidor en sí estaba sano (CPU 2%, RAM libre)
- **Pull de imagen lento**: pueden atascarse pulls concurrentes de Docker; matar y reintentar con un solo pull
- **WAHA_API_KEY**: `diegospizza-waha-2026` (env en docker-compose)
- **Sesión QR expira** (~2-3 min): si expira, watchdog la reinicia sola

### Puntos de Restauración
- `v2.4` — fix round() EditPedido
- Imagen anterior WAHA: `63263ba82ef6` (se puede volver con `docker compose up -d --force-recreate waha` tras editar tag)

## [23-09-2026] — Fix: no se podían agregar productos a pedidos en preparación (v2.4)

### Problema
Al intentar sumar cantidad (+) o agregar un producto ya existente en un pedido "en preparación", salía **"error al cargar la página"**.

### Causa raíz
`round()` en PHP acepta máximo 3 argumentos (valor, precisión, modo). El código pasaba 4: `round($x, 0, ',', '.')`. PHP lanzaba `ArgumentCountError: round() expects at most 3 arguments, 4 given` en cada clic de cantidad o agregar producto.

### Cambios
- **EditPedido.php** — 4 ocurrencias corregidas (`round($x, 0)`):
  - `agregarProducto()` línea 180 — sumar producto duplicado
  - `cambiarCantidad()` línea 251 — botones +/− de cantidad
  - `recalcularSubtotalForm()` línea 281 — descuento porcentaje
  - línea 339 — descuento porcentaje en save

### Verificación
- `php -l` OK; contenedor Docker reconstruido con imagen nueva (`6aa69ef`); sitio 200; logs sin errores

### Puntos de Restauración
- `v2.4` — fix aplicado y desplegado
- `v2.4-pre-fix-round` — estado previo
- `v2.4-deployed` — tag post-deploy

## [20-09-2026] — Fix Reseñas + Fix Descuento Doble + Reporte Comparativo + WAHA Persistencia

### Problema
El campo `total` en BD ya incluye descuentos (`subtotal - descuento_puntos - descuento_manual`), pero las vistas lo estaban restando doble, mostrando valores incorrectos en PDV, historial, kanban, estadísticas y modal de pago.

### Cambios - Fix Reseñas (v2.2)
- **Ruta corregida**: `/review/{numero}` → `/review/{pedido}` (usa ID único del pedido)
- **ReviewController**: busca por `id` en vez de `numero_pedido` (que se resetea diariamente)
- **Pedido.php**: genera link WhatsApp con `$this->id` en vez de `$this->numero_pedido`
- **Causa raíz**: `numero_pedido` no es único — el #1 ha aparecido 83 veces. Al buscar `/review/17`, el sistema encontraba el pedido más viejo, no el del cliente

### Cambios - Reporte Comparativo (v2.1)
- **Comparativa semanal**: Esta semana vs semana anterior (martes-lunes) con porcentaje y flecha ↑↓
- **Comparativa mensual**: Este mes vs mes anterior con porcentaje y flecha ↑↓
- **Acumulado anual**: Total desde enero + promedio mensual + total pedidos + meses con datos
- **Exportar CSV**: Botón verde con todos los pedidos del período + resumen

### Cambios - Fix Descuento Doble (v1.9)
- **Comandas.php**: `totalPedido` ahora usa `pedido->subtotal` (base sin descuentos) en vez de `pedido->total`
- **Comandas.php**: `pagoCompleto()`, `pagoParcial()`, `finalizarDesdeModal()` usan `pedido->total` directo
- **Comandas.php**: `guardarDescuento()` ahora actualiza `total` en BD al aplicar/quitar descuento manual
- **Comandas.php**: `quitarDescuento()` ahora actualiza `total` en BD
- **HistorialPedidos.php**: `detalleTotal` y stats usan `pedido->total` directo
- **Blades** (comandas, kanban, historial, vista cliente): todos usan `pedido->total` directo sin recalcular
- **Pedido #5**: corregido manualmente en BD (total ajustado a 35400)

### Cambios - WAHA WhatsApp (v1.7-v1.8)
- **Volumen corregido**: mount de `/app/sessions` a `/app/.sessions` (con punto) para persistir sesiones
- **Watchdog cron**: script cada 2 minutos auto-inicia sesión si se cae
- **Healthcheck**: docker-compose con healthcheck para WAHA
- **API correcta**: `POST /api/sessions/start` (deprecated upsert+start) en vez de crear + iniciar por separado

### Puntos de Restauración
- `v1.9.1` — Fix guardarDescuento actualiza total en BD
- `v1.9` — Fix descuento doble en todas las vistas
- `v1.8` — Pre-fix descuento doble
- `v1.7` — Vista de cliente

---

### Resumen
Se restauró la sección de **detalle de cliente** en admin, que había sido eliminada por un fix anterior. La página muestra datos personales, dirección, estadísticas de fidelidad y el historial completo de pedidos y puntos. Se documentó el proceso completo de fixes de Filament v5.

### Cambios

- **Botón "Ver" en tabla de clientes** (`ClientesTable.php`): acción `ViewAction` con icono 👁 y etiqueta "Ver" en la columna de acciones
- **Nueva página ViewCliente** (`ViewCliente.php`): extiende `Filament\Resources\Pages\Page`, carga datos del cliente, estadísticas y relaciones en `mount()`
- **Blade propio** (`view-cliente.blade.php`): diseño manual con inline styles, sin depender de infolists de Filament
  - Sección datos: nombre, teléfono, email, clasificación (badge coloreado)
  - Sección dirección: conjunto, torre, apto, dirección completa
  - Estadísticas: total pedidos, monto total, puntos ganados, puntos canjeados
  - Historial de pedidos: tabla con #, fecha, estado (badge), total real (con descuentos), método pago, origen
  - Historial de puntos: tabla con fecha, puntos (+/-), concepto, pedido asociado

### Fixes Técnicos Filament v5
- `infolist()` espera `Filament\Schemas\Schema` (no `Filament\Infolists\Infolist`) — corregido 2 veces
- `$view` en `Filament\Resources\Pages\Page` es **non-static** — declararlo como `static` causa fatal error
- ViewRecord con infolists nativos causaba errores de compatibilidad → se optó por página Livewire con blade propio

### Puntos de Restauración
- `v1.7` — ClienteView con secciones completas
- `v1.6` — Pre-revisión sección clientes
- `v1.5` — Pre-detalle-whatsapp

---

## [05-06-2026] — Descuentos Reflejados en Toda la App + Mejoras Modal Pago

### Resumen
Se garantizó que los descuentos aplicados se reflejen correctamente en **todas las vistas** de la app: lista del historial, modal de detalle, estadísticas, PDV (comandas), tablero de pedidos y editar pedido. También se mejoró el flujo del modal de pago.

### Cambios en Modal de Pago (PDV — Comandas)
- **Botón "Guardar" compacto**: ya no ocupa todo el ancho del modal, está centrado con tamaño normal
- **Botón × para quitar descuento**: cuando hay un descuento aplicado, aparece una fila estilo "pago" con fondo rojo mostrando el monto, tipo y botón × para eliminarlo
- **Cierre automático**: al hacer clic en "Guardar", el modal se cierra automáticamente y se actualiza la lista del PDV
- **Evento `pedidoActualizado`**: se dispara al quitar descuento para refrescar la lista

### Cambios en Editar Pedido (`EditPedido.php`)
- **`cargarDescuentoExistente()`**: al abrir "Editar Pedido" ahora carga el descuento existente desde la BD (antes siempre mostraba 0)
- **Botón × para quitar descuento**: igual que en el PDV, muestra el descuento aplicado con opción de eliminarlo
- **`quitarDescuento()`**: método que resetea el descuento y actualiza la BD

### Cambios en Historial de Pedidos (`HistorialPedidos.php`)
- **Modal de detalle**: ahora calcula `Total = total_original - descuento_puntos - descuento_manual`
- **Estadísticas** (Total Ventas, Efectivo, Tarjeta, Transferencia): ahora suman el valor real de cada pedido después de descuentos
- **Lista de pedidos**: la columna TOTAL muestra el valor real con descuentos aplicados

### Cambios en PDV Comandas (`comandas.blade.php`)
- **Lista y Kanban**: muestran el total real restando `descuento_puntos` y `descuento_manual`

### Cambios en Tablero de Pedidos (`pedidos-board.blade.php`)
- **Las 3 columnas** (Recibidos, Preparación, En Camino): muestran el total real con descuentos

### Puntos de Restauración
- `restore-point-pre-quitar-descuento`
- `restore-point-pre-cerrar-modal-guardar`

---

## [02-06-2026] — Fix Seguridad Finalizar Pedido (Admin + Cajero)

### Regla Única
Ni admin ni cajero pueden finalizar un pedido desde el PDV si no tiene pago registrado. Solo se permite finalizar desde el estado "Ha Llegado" (entregado) y con pago completo.

### Capas de protección (7 niveles)

| # | Capa | Archivo | Qué hace |
|---|------|---------|----------|
| 1 | Kanban blade | `comandas.blade.php` | Botón "✅ Finalizar" solo visible si `$pedido['pago_completo']` es true |
| 2 | Vista Lista blade | `comandas.blade.php` | Botón con `wire:click.stop` (evita doble disparo con `editarPedido`) |
| 3 | `finalizarPedido()` | `Comandas.php` | Verifica estado = entregado + pago completo antes de actualizar |
| 4 | `finalizarDesdeModal()` | `Comandas.php` | Ahora también verifica `pagoCompleto()` antes de actualizar (antes solo confiaba en el modal) |
| 5 | `pagoCompleto()` | `Comandas.php` | Retorna solo si `$total > 0 && $totalPagado >= $total` |
| 6 | `Pedido::saving()` hook | `Pedido.php` | Bloquea cualquier save a `finalizado` si estado anterior ≠ entregado o pago incompleto |
| 7 | Dropdown estado | `PedidoForm.php` | Se eliminó `finalizado` de las opciones del Select |

### Bypass corregido
El bypass más crítico era en **vista Lista**: los botones usaban `wire:click` sin `.stop`, y el row tenía `wire:click="editarPedido()"`. Al hacer clic en un botón se disparaban **dos** Livewire requests simultáneos — la acción del botón (ej: `cambiarEstado`) y `editarPedido()` que abría el modal con "🎉 Finalizar Pedido". Se corrigió agregando `.stop` a todos los botones y eliminando el `onclick="event.stopPropagation()"` que no funcionaba con Livewire.

### Diferencia clave encontrada
`finalizarDesdeModal()` no verificaba pago — solo confiaba en que el modal mostraba el botón. Si por algún motivo `$restante <= 0` (total = 0, descuento 100%), el botón aparecía y finalizaba sin verificar nada. Ahora llama a `pagoCompleto()` antes de actualizar.

---

## [18-05-2026] — Diseño OlaClick + Fixes Admin

### Mejoras Visuales
- Menú público: diseño claro OlaClick (fondo blanco, acento naranja `#FF8D08`, Poppins)
- Carrito rediseñado con tema claro y acento naranja
- PDV: tarjetas kanban más legibles con inline styles
- Historial: tabla con badges, filtros, stats con degradados
- Ticket 57mm: clipping corregido (`@page margin: 0 4.5mm`, body 48mm)

### Correcciones
- Horario: `format('l')` devolvía inglés → `now()->locale('es')->dayName`
- Imagen producto: FileUpload guardaba en disco `local` → `public`
- Configuración: envuelta en `<x-filament-panels::page>`, corregido `$navigationGroup`
- CSS: eliminado `viteTheme`, vistas migradas a inline styles

### Técnico
- `.env`: `APP_ENV=production`
- Assets compilados con `npm run build`

## [27-05-2026] — Loyalty Program, Pago Transferencia, Roles, Refactor PDV

### Flujo de Estados (Pedidos)
- Nuevo flujo: `pendiente_pago → en_proceso → en_camino → entregado (ha_llegado) → finalizado`
- Eliminado estado `pendiente` (recepción)
- Aceptar va directo a "En Preparación"; nuevo "Ha Llegado" antes de Finalizar
- PDV Kanban: columnas En Preparación, Ha Llegado

### Impresión
- Restaurado `window.open('/admin/ticket/' + id)` — impresión vía navegador
- Eliminado agente Node/ESC/POS
- Ticket: auto-print + auto-close

### Roles y Acceso
- Roles: `admin` / `cajero` en columna `role` de users
- Cajero: acceso a PDV, Orden Manual, Productos, Historial (hoy, sin stats)
- Cajero no puede aplicar descuentos (ni puntos ni manual)
- Password reset habilitado: `/admin/password-reset/request`, `/admin/password-reset/reset`
- Profile page personalizada: solo cambio de clave ("Cambiar Clave")

### Mitad y Mitad
- Label: "* Solo aplica para pizza de tamaño Mediana" en selector de sabores
- PDV display: "Pizza Mediana Mitad y Mitad [sabor1 / sabor2]"
- Precio = max(Mediana/Mediano de ambos sabores)

### Notificaciones
- Fix: reemplazado `$this->dispatch()` + `window.addEventListener` por `$this->js()` llamando `window.procesarNuevosPedidos()` (bypass bug eventos Livewire 4)
- Eliminado `pdvInitAudio()` (causaba ReferenceError que rompía scripts)
- Sonido, toast y flash en JS global

### Modal de Pago (Comandas)
- Rediseñado: 600px, muestra items del pedido
- Descuento manual: tipo fijo o porcentaje, auto-recalcula vía `updatedDescuentoTipo/Valor`
- Edición de datos del cliente (nombre, teléfono, conjunto, torre, apto)
- Método de pago default desde `pedido.metodo_pago`
- Sin cambio de estado dentro del modal

### Programa de Fidelidad
- **Nueva página admin**: `/admin/fidelidad`
  - Tasa de acumulación configurable: "Por cada $___ que gaste → ___ punto(s)"
  - CRUD de niveles de recompensa (puntos → % o $ descuento)
  - Almacenado como JSON en `negocio_settings`
- **Checkout**: earn rate usa fórmula configurable
- **Checkout**: cliente elige recompensa manualmente (radio buttons)
- **Canje**: deduce puntos exactos del tier seleccionado
- **ManualOrder**: gana puntos usando fórmula configurable
- Configuración: sección de puntos reemplazada por link a Fidelidad

### Métodos de Pago Activables
- Nuevo campo `metodos_pago_activos` (JSON) en `negocio_settings`
- Helper `NegocioSetting::getActivePaymentMethods()`
- Configuración: toggles switch para activar/desactivar (mínimo 1)
- Checkout, Orden Manual, Comandas, EditPedido: filtran métodos activos
- Validaciones dinámicas en todos los forms

### Transferencia — Datos en Confirmación
- Checkout al seleccionar Transferencia: al confirmar pedido muestra cuentas (Llave, Nequi, Daviplata)
- Botón "Enviar comprobante" → WhatsApp con mensaje del pedido
- Efectivo/Tarjeta: botón "Enviar mensaje al Restaurante" con resumen completo

### Origen (WEB vs PDV)
- `origen` columna en pedidos: `'web'` (online) / `'pdv'` (manual)
- Estado inicial de todos los pedidos: `pendiente_pago`

### Varios
- Checkout: teléfono antes que nombre
- Notas opcionales mejoradas
- Docker + Nginx config ready
- NegocioSetting: casts para JSON arrays y booleanos
