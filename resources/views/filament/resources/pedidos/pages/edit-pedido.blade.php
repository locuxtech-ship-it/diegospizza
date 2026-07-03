<x-filament-panels::page>
    @php $record = $this->getRecord(); @endphp

    {{-- Status banners --}}
    @if($readOnly)
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">🔒</span>
        <span style="font-size: 14px; font-weight: 600; color: #991b1b;">
            Pedido cancelado — solo lectura
        </span>
    </div>
    @elseif($productosReadOnly)
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">💳</span>
        <span style="font-size: 14px; font-weight: 600; color: #16a34a;">
            Pedido finalizado — solo se permite gestionar pagos
        </span>
    </div>
    @endif

    {{-- Order Header --}}
    @if($record)
    <div style="display: flex; justify-content: space-between; align-items: center; background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="font-size: 22px; font-weight: 800; color: #111827;">#{{ $record->numero_pedido }}</span>
            <span style="font-size: 12px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                @if($record->estado == 'finalizado') background: #dcfce7; color: #16a34a;
                @elseif($record->estado == 'cancelado') background: #fee2e2; color: #dc2626;
                @elseif($record->estado == 'en_camino') background: #dbeafe; color: #2563eb;
                @elseif($record->estado == 'en_proceso') background: #ffedd5; color: #ea580c;
                @elseif($record->estado == 'pendiente_pago') background: #fee2e2; color: #dc2626;
                @else background: #f3f4f6; color: #6b7280; @endif">
                @if($record->estado == 'finalizado') ✅
                @elseif($record->estado == 'cancelado') ❌
                @elseif($record->estado == 'en_camino') 🚗
                @elseif($record->estado == 'en_proceso') 👨‍🍳
                @elseif($record->estado == 'pendiente_pago') ⏳
                @else 📥 @endif
                {{ $record->estado }}
            </span>
            <span style="font-size: 12px; padding: 4px 10px; border-radius: 20px; font-weight: 600; {{ ($record->origen ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;' }}">
                {{ strtoupper($record->origen ?? 'PDV') }}
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="font-size: 13px; color: #6b7280;">{{ $record->created_at->setTimezone('America/Bogota')->format('d/m/Y H:i') }}</span>
            <span style="font-size: 20px; font-weight: 800; color: #111827;">${{ number_format($record->total, 0, ',', '.') }}</span>
        </div>
    </div>
    @endif

    {{-- Product Management --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <div style="background: #f9fafb; padding: 12px 16px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 15px; font-weight: 700;">🛒 Productos</h3>
        </div>

        @if(count($productosPedido) > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f3f4f6; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase;">
                        <th style="padding: 8px 12px; text-align: left;">Producto</th>
                        <th style="padding: 8px 12px; text-align: left;">Detalle</th>
                        <th style="padding: 8px 12px; text-align: center;">Precio</th>
                        <th style="padding: 8px 12px; text-align: center;">Cantidad</th>
                        <th style="padding: 8px 12px; text-align: right;">Subtotal</th>
                        @if(!$productosReadOnly)
                        <th style="padding: 8px 12px; text-align: center;"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($productosPedido as $index => $item)
                    <tr style="border-top: 1px solid #f3f4f6;">
                        <td style="padding: 8px 12px; font-weight: 600;">{{ $item['nombre'] ?? $item['producto']['nombre'] ?? '-' }}</td>
                        <td style="padding: 8px 12px; color: #ea580c; font-size: 12px;">
                            @if($item['mitades'] ?? null)
                                {{ collect($item['mitades'])->pluck('nombre')->implode(' / ') }}
                            @else
                                {{ $item['variant_tamanio'] ?? '-' }}
                            @endif
                        </td>
                        <td style="padding: 8px 12px; text-align: center;">${{ number_format($item['precio_unitario'] ?? 0, 0, ',', '.') }}</td>
                        <td style="padding: 4px 12px; text-align: center;">
                            @if(!$readOnly)
                            <div style="display: inline-flex; align-items: center; gap: 4px;">
                                <button wire:click="cambiarCantidad({{ $index }}, -1)" style="width: 26px; height: 26px; border-radius: 6px; border: 1px solid #d1d5db; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; line-height: 1;">−</button>
                                <span style="font-weight: 700; min-width: 24px; text-align: center; font-size: 14px;">{{ $item['cantidad'] ?? 1 }}</span>
                                <button wire:click="cambiarCantidad({{ $index }}, 1)" style="width: 26px; height: 26px; border-radius: 6px; border: 1px solid #d1d5db; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; line-height: 1;">+</button>
                            </div>
                            @else
                            <span style="font-weight: 700; font-size: 14px;">{{ $item['cantidad'] ?? 1 }}</span>
                            @endif
                        </td>
                        <td style="padding: 8px 12px; text-align: right; font-weight: 700;">${{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</td>
                        @if(!$readOnly)
                        <td style="padding: 8px 12px; text-align: center;">
                            <button wire:click="quitarProducto({{ $index }})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px;" title="Quitar">×</button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 32px 0; color: #9ca3af; font-size: 13px;">No hay productos en este pedido.</div>
        @endif

        {{-- Add product --}}
        @if(!$readOnly)
        <div style="border-top: 1px solid #e5e7eb; padding: 16px; background: #fafafa;">
            <p style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Agregar producto</p>
            <div style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 200px;">
                    <label style="font-size: 11px; font-weight: 600; color: #374151;">Producto</label>
                    <select wire:model.live="nuevoProductoId" style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                        <option value="">-- Seleccionar --</option>
                        @foreach($categorias as $cat)
                        <optgroup label="{{ $cat->nombre }}">
                            @foreach($cat->productosDisponibles as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nombre }} - ${{ number_format($prod->precio, 0, ',', '.') }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; min-width: 140px; {{ $saboresPizza ? 'display: none;' : '' }}">
                    <label style="font-size: 11px; font-weight: 600; color: #374151;">Variante</label>
                    <select wire:model.live="nuevoVariantId" style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                        <option value="">-- Sin variante --</option>
                        @if($nuevoProductoId && !$saboresPizza)
                            @php $prodSel = \App\Models\Producto::with('variants')->find($nuevoProductoId); @endphp
                            @if($prodSel && $prodSel->variants->isNotEmpty())
                                @foreach($prodSel->variants as $v)
                                <option value="{{ $v->id }}">{{ $v->tamanio }} - ${{ number_format($v->precio, 0, ',', '.') }}</option>
                                @endforeach
                            @endif
                        @endif
                    </select>
                </div>
                @if($saboresPizza)
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 11px; font-weight: 600; color: #374151;">Primer sabor</label>
                    <select wire:model.live="nuevoMitad1" style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                        <option value="">-- Seleccionar --</option>
                        @foreach($saboresPizza as $sabor)
                        <option value="{{ $sabor->id }}">{{ $sabor->nombre }} - ${{ number_format($sabor->precio, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 11px; font-weight: 600; color: #374151;">Segundo sabor</label>
                    <select wire:model.live="nuevoMitad2" style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                        <option value="">-- Seleccionar --</option>
                        @foreach($saboresPizza as $sabor)
                        <option value="{{ $sabor->id }}">{{ $sabor->nombre }} - ${{ number_format($sabor->precio, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <x-filament::button wire:click="agregarProducto" color="success" size="sm">+ Agregar</x-filament::button>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Payment Section (new design) --}}
    @php $restante = max(0, $totalConDescuento - $totalPagado); @endphp
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <div style="background: linear-gradient(to right, #f9fafb, white); padding: 14px 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 700;">💳 Pago - Pedido #{{ $record->numero_pedido }}</h3>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 700; background: #fef3c7; color: #92400e;">{{ $record->estado === 'pendiente_pago' ? 'Pendiente Pago' : $record->estado }}</span>
                    <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; background: {{ ($record->origen ?? 'pdv') === 'web' ? '#dbeafe' : '#f3e8ff' }}; color: {{ ($record->origen ?? 'pdv') === 'web' ? '#1e40af' : '#6b21a8' }};">{{ strtoupper($record->origen ?? 'PDV') }}</span>
                    <span style="font-size: 12px; color: #6b7280;">{{ $record->created_at->setTimezone('America/Bogota')->format('d/m/y H:i') }}</span>
                </div>
            </div>
        </div>

        <div style="padding: 16px;">
            {{-- Client info --}}
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 4px; font-size: 14px; font-weight: 700; color: #111827;">Cliente: {{ $record->cliente->nombre ?? '' }}</p>
                <p style="margin: 0 0 2px; font-size: 13px; color: #6b7280;">Teléfono: +57 {{ $record->cliente->telefono ?? '' }}</p>
                @php $dirCliente = $record->direccion_completa; @endphp
                <p style="margin: 0; font-size: 13px; color: #6b7280;">{{ $dirCliente }}</p>
            </div>

            {{-- Products summary --}}
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 13px; font-weight: 700; color: #374151;">Productos: {{ count($productosPedido) }}</p>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; max-height: 140px; overflow-y: auto;">
                    @foreach($productosPedido as $pp)
                    <div style="display: flex; justify-content: space-between; padding: 6px 12px; font-size: 13px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }}">
                        <span style="color: #374151;">
                            <strong>{{ $pp['cantidad'] ?? 1 }}</strong>
                            @if(!empty($pp['mitades']))
                                Pizza Mediana Mitad y Mitad <span style="color: #ea580c;">[{{ collect($pp['mitades'])->pluck('nombre')->implode(' / ') }}]</span>
                            @else
                                {{ $pp['producto']['nombre'] ?? $pp['nombre'] ?? '-' }}@if(!empty($pp['variant_tamanio'])) <span style="color: #6b7280;">- {{ $pp['variant_tamanio'] }}</span>@endif
                            @endif
                        </span>
                        <span style="font-weight: 600;">${{ number_format($pp['subtotal'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @php $subtotalActual = array_sum(array_map(fn($i) => (float) ($i['subtotal'] ?? 0), $productosPedido)); @endphp
            {{-- Subtotal + Total --}}
            <div style="background: #f9fafb; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280;">
                    <span>Subtotal Productos ({{ count($productosPedido) }})</span>
                    <span>${{ number_format($subtotalActual, 0, ',', '.') }}</span>
                </div>
                @if($descuentoAplicado > 0)
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #dc2626;">
                    <span>Descuento</span>
                    <span>-${{ number_format($descuentoAplicado, 0, ',', '.') }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: #111827; border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                    <span>Total</span>
                    <span>${{ number_format($totalConDescuento, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #16a34a; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #e5e7eb;">
                    <span>✅ Pagado</span>
                    <span style="font-weight: 700;">${{ number_format($totalPagado, 0, ',', '.') }}</span>
                </div>
                @if($restante > 0)
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #dc2626; margin-top: 2px;">
                    <span>⏳ Falta por pagar</span>
                    <span style="font-weight: 700;">${{ number_format($restante, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            {{-- Payment form --}}
            @if(!$readOnly && $restante > 0)
            <div style="margin-bottom: 16px;">
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <select wire:model.live="pagoMetodo" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; background: white; flex: 1; min-width: 140px;">
                        @foreach(\App\Models\NegocioSetting::getActivePaymentMethods() as $valor => $info)
                            <option value="{{ $valor }}">{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                    <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; flex: 1; min-width: 120px;">
                        <span style="background: #f3f4f6; padding: 10px 10px; font-size: 14px; color: #6b7280; border-right: 1px solid #d1d5db;">$</span>
                        <input type="number" step="1" wire:model.live="pagoMonto" max="{{ $restante }}" placeholder="0" style="width: 100%; border: none; padding: 10px 12px; font-size: 14px; outline: none;">
                    </div>
                    <input type="text" wire:model.live="pagoReferencia" placeholder="Ref (opcional)" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 13px; flex: 1; min-width: 120px;">
                    <x-filament::button wire:click="registrarPago" color="success" style="height: 42px; white-space: nowrap;">
                        💳 Registrar pago
                    </x-filament::button>
                </div>
                {{-- Descuento --}}
                @can('applyDiscount', auth()->user())
                <div style="margin-top: 8px; display: flex; gap: 8px; align-items: center;">
                    <span style="font-size: 12px; font-weight: 600; color: #6b7280;">🏷️ Descuento:</span>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="radio" wire:model.live="descuentoTipo" value="fijo"> Fijo
                    </label>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="radio" wire:model.live="descuentoTipo" value="porcentaje"> %
                    </label>
                    <input type="number" step="1" wire:model.live="descuentoValor" min="0" placeholder="0" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px; width: 80px;">
                </div>
                @endcan
                @if($pagoError)
                <div style="margin-top: 8px; padding: 10px 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #dc2626; font-size: 13px; font-weight: 500; text-align: center;">
                    ⚠️ {{ $pagoError }}
                </div>
                @endif
            </div>
            @endif

            {{-- Payment history --}}
            @if(count($pagosRegistrados) > 0)
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Historial de pagos</p>
                <div style="max-height: 120px; overflow-y: auto;">
                    @foreach($pagosRegistrados as $pago)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 4px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; flex-wrap: wrap;">
                            <span style="font-weight: 700; color: #16a34a;">${{ number_format($pago['monto'], 0, ',', '.') }}</span>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;
                                @if($pago['metodo'] == 'efectivo') background: #dcfce7; color: #16a34a;
                                @elseif($pago['metodo'] == 'tarjeta') background: #dbeafe; color: #2563eb;
                                @else background: #f3e8ff; color: #9333ea; @endif">
                                {{ $pago['metodo'] }}
                            </span>
                            @if(!empty($pago['referencia']))
                                <span style="color: #6b7280; font-size: 12px;">Ref: {{ $pago['referencia'] }}</span>
                            @endif
                            <span style="color: #9ca3af; font-size: 12px;">{{ \Carbon\Carbon::parse($pago['created_at'], 'UTC')->setTimezone('America/Bogota')->format('d/m/y H:i') }}</span>
                        </div>
                        @if(!$readOnly)
                        <button wire:click="eliminarPago({{ $pago['id'] }})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; padding: 0 4px;" title="Eliminar">×</button>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if(!$readOnly && $restante > 0)
                <div style="text-align: center; margin-top: 8px;">
                    <span style="font-size: 13px; color: #6b7280; font-weight: 500;">¿Agregar otro pago?</span>
                </div>
                @endif
            </div>
            @elseif(!$readOnly && $restante <= 0)
            <div style="text-align: center; padding: 12px; background: #f0fdf4; border-radius: 8px; color: #16a34a; font-weight: 600; margin-bottom: 16px;">
                ✅ Pago completo
            </div>
            @endif
        </div>
    </div>

    {{-- Form actions --}}
    @php $pagoCompleto = $totalPagado >= $totalConDescuento && $totalConDescuento > 0; @endphp
    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px;">
        @if($readOnly)
        <x-filament::button wire:click="redirectToComandas" color="gray">
            ← Volver a PDV
        </x-filament::button>
        @elseif($pagoCompleto)
        <x-filament::button wire:click="finalizarPedido" color="success">
            🎉 Finalizar Pedido
        </x-filament::button>
        @else
        <x-filament::button wire:click="saveAndRedirect" color="primary">
            💾 Guardar
        </x-filament::button>
        @endif
    </div>
</x-filament-panels::page>
