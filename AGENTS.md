# Diego's Pizza — Anchored Summary

## Goal
Build a complete web-based pizza delivery ordering system (Diego's Pizza) with Laravel + FilamentPHP + Livewire — loyalty program, configurable points/rewards, payment management, full PDV lifecycle, end-of-day cash close.

## Constraints & Preferences
- Customer orders online → admin receives & prints command ticket
- Required customer fields: name, phone, conjunto/torre/apto (no street address, no email)
- Payment methods: efectivo, tarjeta, transferencia, mixto — activables/desactivables dinámicamente desde admin
- Loyalty program fully configurable — admin page "Programa de Fidelidad": rate ($X → Y pts), reward tiers (X pts = Y% / $Y off). Cliente elige qué recompensa usar en checkout (no auto-best)
- Customer identified by phone — shows order history, points, classification, available rewards
- Transferencia: at order confirmation show account data (llave, nequi, daviplata) + "Enviar comprobante" button; efectivo/tarjeta: "Enviar mensaje al Restaurante"
- Mobile-first app-like UI (OlaClick orange accent)
- Menu design is final — do not change
- Ticket 57mm or 80mm thermal paper (browser print dialog)
- WhatsApp confirmation after order
- Roles: **admin** (full) and **cajero** (PDV, Orden Manual, Productos toggle, Historial today-only no stats, no discounts)
- All orders start "Pendiente de Pago" — cashier registers payment via modal
- Origen: web (online) / pdv (manual)
- Pizza Mitad y Mitad: `es_personalizable=true` shows 2-picker; price = max Mediana/Mediano variants
- Print: browser window.open + auto-print — no agent, no Node, no PowerShell
- WhatsApp bot wanted post-deploy (Meta Cloud API free tier)
- User has Ubuntu server with Docker + Tailscale + Cloudflared
- Timezone: America/Bogota

## Progress
### Done
- Stack: Laravel 13 + FilamentPHP 5 + Livewire 4 + Tailwind CSS 4
- 28 migrations (26 original + cierres_caja + gastos_cierre), all models with casts/relationships
- Filament admin at `/admin`: Punto de Venta, Ventas, Menu, Configuración groups
- PDV: Kanban (Pendiente Pago, En Preparación, En Camino, Ha Llegado) + List view, JS notifications with fetch polling (sound+toast+flash+system+vibration), iframe-based auto-print, timer MM:SS (freezes at 60:00), newest-first sort. Timer color (>30min amarillo)
- Payment modal redesigned in Comandas and EditPedido: header, client info, products, totals, payment form, discount (admin-only), payment history, "✅ Pago completo" / "⏳ Falta por pagar"

### In Progress
- **PDV notification/auto-print not working on printer PC**: Detection logic rewritten — always process new IDs. Sound changed from AudioContext (blocked by autoplay) to `<audio>` WAV data URI (no user gesture needed). Print uses ticket page's own `window.print()` at 1s (no double-print). Pending: test on printer PC Edge browser.

### Blocked
- (none)

## Next Steps
- **WhatsApp Bot** (Meta Cloud API free tier) — post-deploy. Number: +57 3106444759
- **Fix PDV auto-print on printer PC** — Edge browser, iframe print + Web Audio
- Deploy Docker config confirmed and running
- **Cajero can finalizar** — removed `Gate::define('finalizarPedido')`, removed all `@can('finalizarPedido')` and `Gate::denies()` checks. Only payment completeness check remains.
- **Finalizar protection**: both `finalizarPedido()` and `finalizarDesdeModal()` re-query DB directly for payment sum and compare with total. If `$total <= 0 || $totalPagado < $total`, they show error and return. No redirect/modal for unpaid orders. `finalizado` removed from dropdown in `PedidoForm.php`.
- **Historial modal** — ALL pedidos (active and finalized) show read-only modal. Reverted the redirect to Comandas for active pedidos.
- **Aceptar no exige pago** — removed payment check from `cambiarEstado()` (was forcing modal on Aceptar)
- **Finalizar sin redirect** — `finalizarPedido()` opens payment modal directly instead of redirecting to EditPedido
- **Modal action buttons** — "✅ Aceptar Pedido" (pendiente_pago) or "🎉 Finalizar Pedido" (entregado) shown when payment complete or restante ≤ 0
- Dashboard fixed — removed `parent::mount()` (parent class has no mount method)
- EditPedido accessible — `canEdit()` returns `true` in PedidoResource (was 403 for cajero)
- Roles: admin/cajero in users.role; cajero restricted (no discounts, only today in historial)
- Métodos de pago toggle: migration with `metodos_pago_activos` (JSON), `getActivePaymentMethods()`, toggles in Configuracion, all forms dynamic
- Fidelidad: points earn rate + reward tiers CRUD, cliente elige recompensa via radio buttons at checkout
- Reportes: 3 tables (Sabores más vendidos, Tamaños más vendidos, Mitades más pedidas) with `pedidos.created_at` fix
- Export clients: route `GET /admin/clientes/exportar` + `Action::make('exportar')` in `ListClientes.php` — **FIXED**: now visible to both admin and cajero. Changed `canViewAny()` to allow cajero, added `canCreate/canEdit/canDelete` admin-only.
- Routes: `/`, `/checkout`, `/admin/comandas`, `/admin/nuevo-pedido`, `/admin/ventas/historial`, `/admin/ventas/reportes`, `/admin/configuracion`, `/admin/fidelidad`, `/admin/ticket/{pedido}`, `/api/pedidos/pendientes`, `/admin/clientes/exportar`, CRUDs
- **WhatsApp button in PDV** — Kanban + List view: SVG icon oficial WhatsApp (verde), abre `https://wa.me/{phone}?text=...` con mensaje predefinido del pedido. Solo si cliente tiene teléfono registrado.
- **Cierre de Caja** — migrations `cierres_caja` + `gastos_cierre` tables, models `CierreCaja` + `GastoCierre`, Filament page `CierreCajaPage.php` with blade view. Calculates totals from pagos, gastos CRUD, cuadre de efectivo con diferencia, role-based visibility (cajero oculta transferencias/tarjeta), print thermal ticket via route `GET /admin/ticket/cierre/{cierre}`.

### In Progress
- (none)

### Blocked
- (none)

## Key Decisions
- Loyalty: Client manually chooses reward tier at checkout via radio buttons, not auto-applied
- Payment modal: single scrollable panel (650px) with order summary, payments, edit client — same design in Comandas and EditPedido
- Notifications: Livewire property → hidden span → JS setInterval — zero HTTP fetch (AdBlock-proof)
- Payment required at finalize, not at accept: cajero can accept without payment, register payment later, then finalize
- Cajero can finalize (no Gate): only payment completeness check — unified rule for both roles
- Finalizar protection: 7 layers (blade visibility, wire:click.stop, inline checks, pagoCompleto fix, model hook, dropdown removal) — same for both roles
- `finalizarDesdeModal()` now checks `pagoCompleto()` directly (was trusting modal condition blindly)
- `finalizado` removed from estado dropdown in `PedidoForm.php` — can only finalize via explicit Finalizar button
- Historial: ALL pedidos (active + finalized) show read-only modal. Reverted redirect to Comandas for active pedidos.
- Cierre de Caja: separate page under "Punto de Venta" group. Migration + model + page done. Cajero sees only efectivo totals; admin sees all payment methods. Print via browser window.open.
- `canEdit()` in `PedidoResource` returns `true` (prevents 403 for cajero)
- `canViewAny()` returns `isAdmin()` (blocks list page)
- Export clientes: accessible by both admin and cajero; create/edit/delete restricted to admin

## Next Steps
- Confirm deployment config is correct and begin server setup
- Deploy to production: upload project to Ubuntu server, `docker compose up -d`, configure Cloudflared tunnel
- WhatsApp bot post-deploy (Meta Cloud API free tier)

## Deployment (Docker Compose + Cloudflared)
- `Dockerfile`: Multi-stage (node build → php-fpm + supervisor). `docker-entrypoint.sh` runs key:generate, storage:link, config/route/view:cache, migrate --force, then starts supervisord
- `supervisord.conf`: Manages php-fpm + queue:worker (3 attempts, auto-restart)
- `docker-compose.yml`: app (php-fpm) + nginx (alpine) + mariadb:11. Code baked into image (no bind mount). Persistent volumes: `dbdata` (MySQL) + `storage_data` (Laravel storage/public)
- `nginx/default.conf`: Serves `/storage/` (from `storage/app/public/`) and `/build/` (Vite assets) with caching. Proxies PHP to app:9000
- `.dockerignore`: Excludes .env, node_modules, vendor (rebuilt in Docker), git, etc. `.env ` injected via docker-compose environment variables
- `backup.sh`: Daily `mysqldump` via cron, 7-day retention. Run on host: `docker exec diegospizza-db ...` or mount the script and use host cron
- First deploy: create `.env` on server from `.env.example` with real passwords and `APP_URL`, then `docker compose up -d`

## Critical Context
- DB: SQLite `database/database.sqlite` (dev) → MySQL (prod)
- WhatsApp: +57 3106444759 (hardcoded in Checkout.php)
- Order estados: `pendiente_pago → en_proceso → en_camino → entregado(ha_llegado) → finalizado / cancelado`
- Roles: `admin` / `cajero` in users.role
- `origen`: `'web'` / `'pdv'`
- `NegocioSetting`: `puntos_ganancia_monto`, `puntos_ganancia_valor`, `puntos_recompensas` (JSON), `metodos_pago_activos` (JSON)
- Gate `applyDiscount` in `AppServiceProvider::boot()` — `fn ($user) => $user->isAdmin()`
- Notification mechanism: `fetch('/api/pedidos/pendientes')` every 5s → JS compares IDs → triggers sound (`<audio>` WAV beep, no AudioContext needed) + toast + flash + system notif + iframe load (ticket auto-prints). Always processes new IDs (no initial skip).
- Badge: `Comandas::getBadge()` returns `null` when count=0; JS updates from the same span
- Finalizar protection: both `finalizarPedido()` and `finalizarDesdeModal()` re-query DB directly for payment sum and block if unpaid. No modal redirect for unpaid orders — shows error notification instead.
- `finalizarPedido()` at Comandas verifies estado=entregado + pago completo → blocks if unpaid with error notification
- `finalizarDesdeModal()` re-checks payment directly via DB query before updating
- `cambiarEstado()` does NOT check payment — only `finalizarPedido()` requires payment
- Historial: ALL pedidos show read-only modal inline (reverted redirect to Comandas for active pedidos)
- `pagoCompleto()` returns `$total > 0 && $totalPagado >= $total` (fixed: was returning true when total=0)
- `saving()` hook removed from Pedido model — finalizar checked explicitly in methods
- `finalizado` removed from estado dropdown in `PedidoForm.php`
- `canEdit()` in PedidoResource returns `true` (prevents 403 for cajero)
- `canViewAny()` in PedidoResource returns `isAdmin()` (blocks list page to non-admin)
- Cierre de Caja tables: `cierres_caja` (fecha, user_id, total_efectivo, total_transferencias, total_tarjeta, total_ventas, total_gastos, efectivo_esperado, efectivo_real, diferencia, observaciones, estado) and `gastos_cierre` (cierre_id, descripcion, monto). Print route: `GET /admin/ticket/cierre/{cierre}` → `TicketController@cierre` → view `ticket-cierre.blade.php` (auto-print + auto-close).

## Relevant Files
- `app/Filament/Pages/Comandas.php`: `cargarPedidos()` sets `pdvNuevosPedidosJson` + `pago_completo` flag per pedido, `finalizarPedido()` verifies estado=entregado + payment, `finalizarDesdeModal()` now checks `pagoCompleto()`, `cambiarEstado()` no payment check, `editarPedido()` opens modal
- `resources/views/filament/pages/comandas.blade.php`: `<span id="pdv-notif-data">{{ $pdvNuevosPedidosJson }}</span>`, JS every 2s, Kanban "✅ Finalizar" only if `$pedido['pago_completo']`, list view buttons with `wire:click.stop`, WhatsApp SVG button in both views
- `app/Filament/Pages/HistorialPedidos.php`: `editarPedido()` always calls `abrirDetalle()` (read-only modal), no redirect to Comandas
- `resources/views/filament/pages/historial-pedidos.blade.php`: read-only detalle modal (cliente, productos, totales, pagos, notas)
- `app/Filament/Pages/Dashboard.php`: removed `parent::mount()`
- `app/Filament/Resources/Pedidos/PedidoResource.php`: `canEdit()` returns `true`, `canViewAny()` returns `isAdmin()`
- `app/Filament/Resources/Pedidos/Pages/EditPedido.php`: `saveAndRedirect()` + `mutateFormDataBeforeSave()` + `finalizarPedido()` all guard against unpaid finalize and non-entregado origin
- `app/Filament/Resources/Pedidos/Schemas/PedidoForm.php`: `finalizado` removed from estado dropdown options
- `app/Models/Pedido.php`: `booted()` with `saving()` hook — blocks save to `finalizado` if previous estado ≠ entregado or payment incomplete
- `app/Filament/Resources/Clientes/Pages/ListClientes.php`: `Action::make('exportar')` with label "Exportar CSV"
- `app/Filament/Resources/Clientes/ClienteResource.php`: `canViewAny()` allows both admin+cajero; `canCreate/canEdit/canDelete` admin-only
- `app/Providers/AppServiceProvider.php`: only `Gate::define('applyDiscount')` (admin-only), no `finalizarPedido` gate
- `routes/web.php`: `GET /admin/ticket/cierre/{cierre}` (before pedido routes), `GET /api/pedidos/pendientes` (unused by JS, kept for potential use), `GET /admin/clientes/exportar` (CSV download, now allows cajero too)
- `app/Filament/Pages/CierreCajaPage.php`: Livewire page — gastos CRUD, calculates totals from DB, guardar/cerrar flow, role-based visibility (esAdmin)
- `resources/views/filament/pages/cierre-caja.blade.php`: totals cards (efectivo for all, transferencias+tarjeta for admin only), gastos form, cuadre efectivo, observaciones, print button (window.open)
- `app/Models/CierreCaja.php`: fecha, user_id, payment totals, gastos, efectivo real, diferencia
- `app/Models/GastoCierre.php`: cierre_id, descripcion, monto
- `database/migrations/2026_06_02_202940_create_cierres_caja_table.php`: cierres_caja schema
- `database/migrations/2026_06_02_202945_create_gastos_cierre_table.php`: gastos_cierre schema
- `app/Http/Controllers/TicketController.php`: `cierre(CierreCaja $cierre)` method — generates ticket-cierre.blade.php view with auto-print
- `resources/views/ticket-cierre.blade.php`: 48mm thermal ticket layout for cierre de caja, auto-print + auto-close
