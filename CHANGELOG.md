# Changelog — Diego's Pizza

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
