# HungerClick — Documentación del Sistema

## Stack Tecnológico
- **Laravel 13** + **FilamentPHP 5** + **Livewire 4** + **Tailwind CSS 4**
- **Base de datos**: SQLite (`database/database.sqlite`)
- **Node.js / Vite**: Compilación de assets frontend (`npm run build`)

---

## Credenciales Admin
- URL: `/admin`
- Email: `admin@diegospizza.com`
- Password: `admin123`

---

## Roles de Usuario

| Rol | Descripción |
|-----|-------------|
| `admin` | Acceso completo: PDV, Historial, Reportes, Configuración, Categorías, Usuarios |
| `cajero` | Solo PDV, Orden Manual, Productos (toggle disponible), Historial (hoy, sin stats monetarios) |

Los roles se almacenan en `users.role` (varchar). No se usa Spatie.

### Lógica de acceso
- `Configuracion::canAccess()` → solo admin
- `CategoriaResource`, `UserResource` → solo admin
- `Reportes` → solo admin
- Dashboard → solo admin (visible como "Escritorio")
- Cajero no puede aplicar descuentos (puntos ni manuales) en formularios — campos deshabilitados/ocultos
- Login redirige a `/admin/comandas` vía `LoginResponse` custom registrado en `AppServiceProvider`

---

## Estructura del Admin Panel

### Grupos de Navegación
| Grupo | Páginas |
|-------|---------|
| **Punto de Venta** | PDV (Comandas) |
| **Ventas** | Historial de Pedidos, Reportes |
| **Menú** | Productos, Categorías |
| **Configuración** | Configuración, Usuarios |

### Páginas Custom (Fuera de Filament Resources)

| Página | PHP | Blade |
|--------|-----|-------|
| PDV (Comandas) | `app/Filament/Pages/Comandas.php` | `resources/views/filament/pages/comandas.blade.php` |
| Historial | `app/Filament/Pages/HistorialPedidos.php` | `resources/views/filament/pages/historial-pedidos.blade.php` |
| Reportes | `app/Filament/Pages/Reportes.php` | `resources/views/filament/pages/reportes.blade.php` |
| Configuración | `app/Filament/Pages/Configuracion.php` | `resources/views/filament/pages/configuracion.blade.php` |
| Orden Manual | `app/Filament/Pages/ManualOrder.php` | `resources/views/filament/pages/manual-order.blade.php` |
| Editar Pedido | `app/Filament/Resources/Pedidos/Pages/EditPedido.php` | `resources/views/filament/resources/pedidos/edit-pedido.blade.php` |

### Recursos Filament
| Recurso | Modelo | Grupo |
|---------|--------|-------|
| `ProductoResource` | Producto | Menú |
| `CategoriaResource` | Categoria | Menú |
| `PedidoResource` | Pedido | Oculta (uso interno) |
| `ClienteResource` | Cliente | Oculta |

---

## Páginas Públicas

| Ruta | Componente Livewire | Descripción |
|------|-------------------|-------------|
| `/` | `Menu` | Menú público con categorías, productos, variantes, modal Mitad y Mitad |
| `/checkout` | `Checkout` | Checkout: formulario + carrito + WhatsApp + puntos |

### Diseño del Menú Público
- Fondo blanco, acento naranja `#FF8D08`
- Header oscuro con logo, nombre del negocio, indicador abierto/cerrado
- Categorías como pills deslizables horizontales
- Tarjetas de producto con imagen, precio, ingredientes, botón agregar
- Carrito lateral (slideover) con FAB naranja
- Selector de variantes (tamaños) en modal
- Footer oscuro con datos de contacto

---

## Estilos en el Admin Panel
Las vistas custom del admin usan **inline styles** (no Tailwind classes) para evitar conflictos con CSS de Filament. Solo se usan componentes Filament (`<x-filament::button>`, `<x-filament-panels::page>`) para mantener consistencia visual.

---

## Modelo de Datos (10 tablas)

### Tablas principales
| Tabla | Modelo | Propósito |
|-------|--------|-----------|
| `categorias` | `Categoria` | Categorías de productos con orden, activo, es_pizza |
| `productos` | `Producto` | Productos con precio, disponible, es_personalizable |
| `producto_variants` | `ProductoVariant` | Variantes por producto (tamaño, precio) |
| `clientes` | `Cliente` | Clientes con conjunto/torre/apto, puntos_acumulados |
| `pedidos` | `Pedido` | Pedidos con estado, total, origen, metodo_pago |
| `pedido_productos` | `PedidoProducto` | Items del pedido con cantidad, precio, mitades |
| `pagos` | `Pago` | Pagos con monto, método, confirmado |
| `puntos` | `Punto` | Historial de puntos ganados/canjeados |
| `negocio_settings` | `NegocioSetting` | Configuración del negocio (fila única) |
| `users` | `User` | Usuarios con rol (admin/cajero) |

### Columnas clave
- `pedidos.origen`: `'web'` (tienda pública) o `'pdv'` (orden manual)
- `pedidos.estado`: `pendiente_pago`, `pendiente`, `en_proceso`, `en_camino`, `finalizado`, `cancelado`
- `pedidos.numero_pedido`: entero auto-asignado (se reinicia diario al máximo del día + 1)
- `pedido_productos.mitades`: JSON array con `[{producto_id, nombre, precio}, ...]` para Mitad y Mitad
- `clientes.conjunto`, `clientes.torre`, `clientes.apto`: dirección dividida (conjunto requerido)
- `categorias.es_pizza`: boolean que identifica categorías de pizza para el selector de Mitad y Mitad
- `productos.es_personalizable`: boolean que activa el modal de Mitad y Mitad

---

## Flujo del Pedido

### Estados
```
pendiente_pago → pendiente → en_proceso → en_camino → finalizado
                                                          ↕
                                                      cancelado
```

### Flujo completo
1. **Cliente** ordena desde la web (origen=web) o **Cajero** crea orden manual (origen=pdv)
2. **Siempre** empieza como `pendiente_pago` — el cajero debe confirmar el pago primero
3. **Confirmar pago**: Registrar pago desde el modal en PDV o desde EditPedido
4. Si pago completo → avanzar a `pendiente` manualmente desde PDV (Kanban)
5. **Estados**: Pendiente → Preparación → En Camino → Finalizado
6. **Finalizado** → solo lectura (no se puede editar, botón "Volver a PDV")
7. **Cancelado** se puede marcar desde cualquier estado

### Métodos de pago
- `efectivo`, `tarjeta`, `transferencia`, `mixto` (múltiples métodos)
- `metodo_pago` se establece como `'mixto'` automáticamente si hay pagos de diferentes métodos

---

## Pizza Mitad y Mitad

### Activación
- Un producto con `es_personalizable = true` activa el modal de Mitad y Mitad
- Las categorías con `es_pizza = true` proveen los sabores disponibles
- Los sabores deben tener `es_personalizable = false` y `disponible = true`

### Flujo
1. Usuario selecciona producto personalizable → modal con 2 selects de sabores
2. Selecciona 2 sabores → se muestra el precio calculado
3. Al agregar, se crea un item con `mitades` JSON y nombre `"[Producto] (Mitad y Mitad)"`

### Cálculo de precio
- Para cada sabor, se busca un variant con `tamanio` que contenga "Mediana" o "Mediano" (case-insensitive)
- Si existe, se usa el precio de ese variant
- Si no, se usa el primer variant disponible
- Si no hay variants, se usa `productos.precio` (precio base)
- El precio final = `max(precioSabor1, precioSabor2)`

### Archivos que implementan Mitad y Mitad
| Archivo | Método/Función |
|---------|----------------|
| `app/Livewire/Menu.php` | `agregarMitadYMitad()` — tienda pública |
| `app/Filament/Pages/ManualOrder.php` | `agregarMitadYMitad()` — orden manual |
| `app/Filament/Resources/Pedidos/Pages/EditPedido.php` | `agregarMitadYMitadProducto()` — editar pedido |
| `app/Livewire/Cart.php` | `agregarProducto()` — clave única por combinación de sabores |
| `resources/views/livewire/menu.blade.php` | Modal y preview de precio |
| `resources/views/filament/pages/manual-order.blade.php` | Modal y preview de precio |

### Visualización en ticket/PDV
- En el nombre del producto se incluye `[Sabor1 / Sabor2]`
- La clave del carrito usa `md5(json_encode($mitades))` para que combinaciones diferentes tengan entradas separadas

---

## Sistema de Puntos

- **Ganancia**: 1 punto por cada $1 (configurable en admin)
- **Canje**: Cada punto vale $0.02 de descuento (configurable)
- Se muestran puntos acumulados al buscar cliente por teléfono
- En checkout se ofrecen puntos disponibles para canjear
- Se registran en tabla `puntos` con concepto y pedido_id

---

## Descuentos

### Manual (admin/cajero)
- `descuento_manual_tipo`: `'fijo'` (monto en COP) o `'porcentaje'` (% del subtotal)
- `descuento_manual_valor`: número
- Se calcula en `EditPedido::mutateFormDataBeforeSave()`
- Se muestra en el ticket como "Desc. manual"

### Por Puntos (cliente)
- `descuento_puntos`: monto en COP
- Se descuenta del total

### Restricción de roles
- **Cajero**: No puede aplicar ningún descuento (campos deshabilitados/ocultos)
- `PedidoForm`, `PedidosTable` → descuentos ocultos para cajero
- `recalcularSubtotalForm()`, `mutateFormDataBeforeSave()` preservan descuentos existentes

---

## Ticket / Comanda

### Visualización
- Ruta: `/admin/ticket/{pedido}`
- Controlador: `app/Http/Controllers/TicketController.php`
- Vista: `resources/views/ticket.blade.php`
- Formato COP con separador de miles (`.`), sin decimales
- Muestra: encabezado, #pedido, fecha, teléfono, dirección (conjunto/torre/apto), productos con variantes/sabores, totales, descuentos, método de pago

### Diseño (configurable desde admin)
- **Tamaño**: 57mm o 80mm
- **Escala de fuente**: 100%–200%
- **Fuente**: Courier New (mono) o Arial (sans-serif)
- **Interlineado**: Compacto, Normal, Espaciado
- **Espaciado productos**: Compacto, Normal, Espaciado
- **Márgenes**: 0–5mm (valor numérico)
- **Logo**: Mostrar/ocultar
- **Negritas**: Activar/desactivar
- **Color**: 100% negro (sin escala de grises)

### Impresión

#### Print Agent (PowerShell)
- Script: `print-agent/agent.ps1` — servidor HTTP que recibe peticiones POST /print
- Helper: `print-agent/print-helper.ps1` — renderiza HTML en WebBrowser y envía a impresora
- Invocador: `print-agent/iniciar.bat` — intenta `agente.exe` (C#), fallback a agent.ps1
- No requiere Node.js

#### Mecanismo de impresión
1. `print-helper.ps1` usa Internet Explorer COM object (`InternetExplorer.Application`)
2. Navega a la URL del ticket con `?t=timestamp` (cache busting)
3. Espera 3s a que cargue
4. Llama `ExecWB(6, 2)` (OLECMDID_PRINT + OLECMDEXECOPT_DONTPROMPTUSER)
5. Si falla, fallback a WinForms WebBrowser con `Print()`
7. Márgenes de IE se guardan, ponen a 0 y restauran después

---

## WhatsApp

- Número hardcodeado: `+57 3106444759`
- Se envía confirmación al número del negocio después de cada pedido web
- Se construye mensaje con: items, variantes, sabores (Mitad y Mitad), total, método de pago, dirección y notas

---

## Migraciones Importantes

| Archivo | Columnas agregadas |
|---------|-------------------|
| `2026_05_24_000001_fix_foreign_keys` | FK fixes, origen, CHECK→varchar |
| `2026_05_18_000001_create_producto_variants_table` | producto_variants (tamanio, precio, orden) |
| `2026_05_18_000002_add_variant_id_to_pedido_productos` | variant_id, variant_tamanio en pedido_productos |
| `2026_05_25_155628_add_ticket_settings_to_negocio_settings` | ticket_mostrar_logo, ticket_escala, ticket_interlineado, ticket_espaciado, ticket_negritas, ticket_margen, ticket_fuente |

---

## Notificaciones de Nuevos Pedidos (PDV)

- Sonido de campana al llegar un pedido nuevo
- Toast deslizante
- Notification del sistema (funciona en otras pestañas)
- Título de pestaña parpadea con "🆕 Pedido #X"
- `visibilitychange` dispara refresco automático

---

## Slug de Productos
- Se genera automáticamente desde `Str::slug($data['nombre'])` en `CreateProducto`/`EditProducto`
- Campo `slug` oculto del formulario

---

## Eliminación de Productos
- `pedido_productos.producto_id` → `productos.id` con `ON DELETE NO ACTION`
- No se puede eliminar un producto que tenga pedidos asociados
- Alternativa: marcarlo como `disponible = false`

---

## Seguridad: Finalizar Pedido (PDV)

**Regla única**: Ni admin ni cajero pueden finalizar un pedido desde el PDV si no tiene pago registrado. Solo se permite finalizar desde el estado "Ha Llegado" (`entregado`) y con pago completo (`$totalPagado >= $total` y `$total > 0`).

### Capas de protección (de arriba a abajo)

| # | Capa | Archivo | Línea(s) | Qué hace |
|---|------|---------|----------|----------|
| 1 | **Blade Kanban** | `comandas.blade.php` | ~223 | Botón "✅ Finalizar" solo se renderiza si `$pedido['pago_completo']` es true |
| 2 | **Blade Lista** | `comandas.blade.php` | ~117 | Botón con `wire:click.stop` evita doble disparo con `editarPedido()` del row |
| 3 | **`finalizarPedido()`** | `Comandas.php` | 413 | Verifica `$pedido->estado === 'entregado'` + `pagoCompleto()` |
| 4 | **`finalizarDesdeModal()`** | `Comandas.php` | 301 | Ahora verifica `pagoCompleto()` antes de actualizar (antes solo confiaba en el modal) |
| 5 | **`pagoCompleto()`** | `Comandas.php` | 185 | Retorna `$total > 0 && $totalPagado >= $total` |
| 6 | **Model `saving` hook** | `Pedido.php` | 44 | Bloquea cualquier `save()` a `finalizado` si estado anterior ≠ entregado o pago incompleto |
| 7 | **Dropdown form** | `PedidoForm.php` | 37 | `finalizado` eliminado de opciones del Select — no se puede elegir desde el formulario |

### Bypass corregido — Vista Lista
Los botones de acción usaban `wire:click` sin `.stop`. El row tenía `wire:click="editarPedido()"`. Al hacer clic en un botón se disparaban **dos** acciones simultáneas — una del botón y otra del row que abría el modal con "🎉 Finalizar Pedido". Solución: cambiar todos a `wire:click.stop`.

### Bypass corregido — Modal
`finalizarDesdeModal()` no verificaba pago, solo confiaba en que el modal mostraba el botón (que a su vez dependía de `$restante <= 0`). Si el total era $0 por cualquier razón, el botón aparecía y finalizaba sin verificar nada. Solución: agregar `pagoCompleto()` dentro de `finalizarDesdeModal()`.

### Flujo correcto
```
1. Pedido llega → pendiente_pago
2. Cajero hace clic → modal de pago (opcional, puede pagar ahora o después)
3. ✅ Aceptar → en_proceso (sin exigir pago)
4. 👨‍🍳 Preparar → 🚗 Enviar → 📍 Llegó (entregado)
5. En "Ha Llegado" → click 🎉 Finalizar
6. Si no hay pago → se abre el modal de pago automáticamente
7. Registra pago → modal muestra "✅ Pago completo" + "🎉 Finalizar Pedido"
8. Click 🎉 Finalizar Pedido en el modal → pedido finalizado
```

---

## Comandos Útiles

```bash
php artisan serve                          # Iniciar servidor de desarrollo
npm run build                              # Compilar assets para producción
npm run dev                                # Servidor Vite con HMR
php artisan view:clear                     # Limpiar caché de vistas
php artisan cache:clear                    # Limpiar caché de aplicación
php artisan config:clear                   # Limpiar caché de configuración
php artisan migrate:fresh --seed           # Resetear BD + seeders
```
