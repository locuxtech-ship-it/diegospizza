<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        {{-- Filters (admin only) --}}
        @if($isAdmin)
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <x-filament::button wire:click="aplicarPeriodo('hoy')" :color="$periodo === 'hoy' ? 'primary' : 'gray'" size="sm">Hoy</x-filament::button>
                <x-filament::button wire:click="aplicarPeriodo('ayer')" :color="$periodo === 'ayer' ? 'primary' : 'gray'" size="sm">Ayer</x-filament::button>
                <x-filament::button wire:click="aplicarPeriodo('7d')" :color="$periodo === '7d' ? 'primary' : 'gray'" size="sm">7 dias</x-filament::button>
                <x-filament::button wire:click="aplicarPeriodo('30d')" :color="$periodo === '30d' ? 'primary' : 'gray'" size="sm">30 dias</x-filament::button>
                <x-filament::button wire:click="aplicarPeriodo('personalizado')" :color="$periodo === 'personalizado' ? 'primary' : 'gray'" size="sm">Fechas</x-filament::button>
            </div>
            @if($periodo === 'personalizado')
            <div style="display: flex; gap: 16px; align-items: end; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <div>
                    <label style="font-size: 12px; color: #6b7280; font-weight: 500;">Desde</label>
                    <input type="date" wire:model.live="fechaInicio" style="margin-top: 4px; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; font-size: 14px;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #6b7280; font-weight: 500;">Hasta</label>
                    <input type="date" wire:model.live="fechaFin" style="margin-top: 4px; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; font-size: 14px;">
                </div>
                <x-filament::button wire:click="filtrar" size="sm">Buscar</x-filament::button>
            </div>
            @endif
        </div>
        @else
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 13px; font-weight: 600; color: #374151;">📋 Pedidos de hoy</span>
        </div>
        @endif

        {{-- Stats (admin only) --}}
        @if($isAdmin)
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div style="background: linear-gradient(to bottom right, #fef2f2, #fee2e2); border: 1px solid #fecaca; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #ef4444; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Total Ventas</p>
                <p style="margin: 4px 0 0 0; color: #991b1b; font-size: 24px; font-weight: 700;">${{ number_format($totalVentas, 0, ',', '.') }}</p>
            </div>
            <div style="background: linear-gradient(to bottom right, #f9fafb, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Pedidos</p>
                <p style="margin: 4px 0 0 0; color: #374151; font-size: 24px; font-weight: 700;">{{ $totalPedidos }}</p>
            </div>
            <div style="background: linear-gradient(to bottom right, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #22c55e; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Efectivo</p>
                <p style="margin: 4px 0 0 0; color: #166534; font-size: 18px; font-weight: 700;">${{ number_format($totalEfectivo, 0, ',', '.') }}</p>
            </div>
            <div style="background: linear-gradient(to bottom right, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #3b82f6; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Tarjeta / Transf.</p>
                <p style="margin: 4px 0 0 0; color: #1e3a5f; font-size: 18px; font-weight: 700;">${{ number_format($totalTarjeta + $totalTransferencia, 0, ',', '.') }}</p>
            </div>
        </div>
        @endif

        {{-- Table --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">#</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Cliente</th>
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Total</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Origen</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Pago</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Estado</th>
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Fecha</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase;">Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                         <tr wire:click="editarPedido({{ $pedido['id'] }})" style="border-bottom: 1px solid #f3f4f6; cursor: pointer;">
                             <td style="padding: 12px 16px;"><strong>#{{ $pedido['numero_pedido'] }}</strong></td>
                            <td style="padding: 12px 16px;">
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <span style="font-weight: 500;">{{ $pedido['cliente']['nombre'] }}</span>
                                    @if(isset($pedido['cliente']['clasificacion']))
                                        <span style="font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 600;
                                            background: {{ $pedido['cliente']['clasificacion'] === 'elite' ? '#fef3c7' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#ffedd5' : '#f3f4f6') }};
                                            color: {{ $pedido['cliente']['clasificacion'] === 'elite' ? '#92400e' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#9a3412' : '#6b7280') }};">
                                            {{ $pedido['cliente']['clasificacion'] === 'elite' ? '⭐' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '🔥' : '🆕') }}
                                        </span>
                                    @endif
                                </div>
                                <p style="margin: 2px 0 0 0; font-size: 12px; color: #9ca3af;">{{ $pedido['cliente']['telefono'] ?? '' }}</p>
                            </td>
                            <td style="padding: 12px 16px; text-align: right; font-weight: 700;">${{ number_format($pedido['total'], 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <span style="font-size: 11px; padding: 2px 6px; border-radius: 6px; font-weight: 600; {{ ($pedido['origen'] ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;' }}">
                                    {{ strtoupper($pedido['origen'] ?? 'PDV') }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;
                                    @if($pedido['metodo_pago'] == 'efectivo') background: #dcfce7; color: #16a34a;
                                    @elseif($pedido['metodo_pago'] == 'tarjeta') background: #dbeafe; color: #2563eb;
                                    @elseif($pedido['metodo_pago'] == 'mixto') background: #fef3c7; color: #d97706;
                                    @else background: #f3e8ff; color: #9333ea; @endif">
                                    @if($pedido['metodo_pago'] == 'efectivo') 💵
                                    @elseif($pedido['metodo_pago'] == 'tarjeta') 💳
                                    @elseif($pedido['metodo_pago'] == 'mixto') 🔀
                                    @else 🏦 @endif
                                    {{ $pedido['metodo_pago'] }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;
                                    @if($pedido['estado'] == 'finalizado' || $pedido['estado'] == 'entregado') background: #dcfce7; color: #16a34a;
                                    @elseif($pedido['estado'] == 'cancelado') background: #fee2e2; color: #dc2626;
                                    @elseif($pedido['estado'] == 'en_camino') background: #dbeafe; color: #2563eb;
                                    @elseif($pedido['estado'] == 'en_proceso') background: #ffedd5; color: #ea580c;
                                    @elseif($pedido['estado'] == 'pendiente_pago') background: #fee2e2; color: #dc2626;
                                    @else background: #f3f4f6; color: #6b7280; @endif">
                                    @if($pedido['estado'] == 'finalizado') ✅
                                    @elseif($pedido['estado'] == 'entregado') ✅
                                    @elseif($pedido['estado'] == 'cancelado') ❌
                                    @elseif($pedido['estado'] == 'en_camino') 🚗
                                    @elseif($pedido['estado'] == 'en_proceso') 👨‍🍳
                                    @elseif($pedido['estado'] == 'pendiente_pago') ⏳
                                    @else 📥 @endif
                                    {{ $this->etiquetaEstado($pedido['estado']) }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #6b7280; font-size: 12px;">
                                {{ \Carbon\Carbon::parse($pedido['created_at'], 'UTC')->setTimezone('America/Bogota')->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding: 12px 16px; text-align: center;" onclick="event.stopPropagation()">
                                <a href="#" onclick="printPedido({{ $pedido['id'] }}); return false;" style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; font-size: 13px; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Reimprimir ticket">🖨️</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="padding: 48px 16px; text-align: center;">
                                <p style="font-size: 36px; margin: 0 0 8px 0;">📋</p>
                                <p style="color: #9ca3af; margin: 0;">Sin pedidos en este período</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal detalle pedido --}}
    @if($modalDetalle && $detallePedido)
    @php $restanteD = max(0, $detalleTotal - $detalleTotalPagado); @endphp
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 999;">
        <div style="background: white; border-radius: 16px; padding: 0; width: 600px; max-width: 95vw; max-height: 90vh; overflow-y: auto;">
            <div style="background: linear-gradient(to right, #f9fafb, white); padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="margin: 0; font-size: 20px; font-weight: 800; color: #111827;">Pedido #{{ $detallePedido['numero_pedido'] }}</h3>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 700;
                            @if($detallePedido['estado'] == 'finalizado' || $detallePedido['estado'] == 'entregado') background:#dcfce7;color:#16a34a;
                            @elseif($detallePedido['estado'] == 'cancelado') background:#fee2e2;color:#dc2626;
                            @else background:#fef3c7;color:#92400e; @endif">
                            {{ $this->etiquetaEstado($detallePedido['estado']) }}
                        </span>
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; background: {{ ($detallePedido['origen'] ?? 'pdv') === 'web' ? '#dbeafe' : '#f3e8ff' }}; color: {{ ($detallePedido['origen'] ?? 'pdv') === 'web' ? '#1e40af' : '#6b21a8' }};">{{ strtoupper($detallePedido['origen'] ?? 'PDV') }}</span>
                        <span style="font-size: 12px; color: #6b7280;">{{ \Carbon\Carbon::parse($detallePedido['created_at'])->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <button wire:click="cerrarDetalle" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #9ca3af; padding: 0 4px;">×</button>
            </div>
            <div style="padding: 16px 24px;">

                {{-- Cliente --}}
                <div style="margin-bottom: 16px;">
                    <p style="margin: 0 0 4px; font-size: 14px; font-weight: 700; color: #111827;">{{ $detallePedido['cliente']['nombre'] ?? 'S/N' }}</p>
                    @if(!empty($detallePedido['cliente']['telefono']))
                    <p style="margin: 0 0 2px; font-size: 13px; color: #6b7280;">📞 {{ $detallePedido['cliente']['telefono'] }}</p>
                    @endif
                    @php $dir = collect([$detallePedido['cliente']['conjunto'] ?? '', $detallePedido['cliente']['torre'] ?? '', $detallePedido['cliente']['apto'] ?? ''])->filter()->implode(', '); @endphp
                    @if($dir)
                    <p style="margin: 0; font-size: 13px; color: #6b7280;">📍 {{ $dir }}</p>
                    @endif
                </div>

                {{-- Productos --}}
                <div style="margin-bottom: 16px;">
                    <p style="margin: 0 0 8px; font-size: 13px; font-weight: 700; color: #374151;">Productos</p>
                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; max-height: 200px; overflow-y: auto;">
                        @foreach($detalleProductos as $pp)
                        <div style="display: flex; justify-content: space-between; padding: 6px 12px; font-size: 13px; {{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
                            <span style="color: #374151;">
                                <strong>{{ $pp['cantidad'] }}x</strong>
                                @if(!empty($pp['mitades']))
                                    Pizza Mitad y Mitad <span style="color: #ea580c;">[{{ collect($pp['mitades'])->pluck('nombre')->implode('/') }}]</span>
                                @else
                                    {{ $pp['producto']['nombre'] ?? 'Producto' }}
                                    @if(!empty($pp['variant_tamanio'])) <span style="color: #6b7280;">({{ $pp['variant_tamanio'] }})</span>@endif
                                @endif
                            </span>
                            <span style="font-weight: 600;">${{ number_format($pp['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Totales --}}
                <div style="background: #f9fafb; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                    @if(($detallePedido['descuento_puntos'] ?? 0) > 0 || ($detallePedido['descuento_manual'] ?? 0) > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #dc2626; margin-bottom: 4px;">
                        <span>Descuentos</span>
                        <span>-${{ number_format(($detallePedido['descuento_puntos'] ?? 0) + ($detallePedido['descuento_manual'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: #111827;">
                        <span>Total</span>
                        <span>${{ number_format($detalleTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Pagos --}}
                @if(count($detallePagos) > 0)
                <div style="margin-bottom: 12px;">
                    <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Pagos</p>
                    @foreach($detallePagos as $pago)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 4px; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 700; color: #16a34a;">${{ number_format($pago['monto'], 0, ',', '.') }}</span>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;
                                @if($pago['metodo'] == 'efectivo') background:#dcfce7;color:#16a34a;
                                @elseif($pago['metodo'] == 'tarjeta') background:#dbeafe;color:#2563eb;
                                @else background:#f3e8ff;color:#9333ea; @endif">
                                {{ $pago['metodo'] }}
                            </span>
                            <span style="color: #9ca3af; font-size: 12px;">{{ \Carbon\Carbon::parse($pago['created_at'], 'UTC')->setTimezone('America/Bogota')->format('d/m/y H:i') }}</span>
                        </div>
                    </div>
                    @endforeach
                    @if($restanteD > 0)
                    <div style="text-align: center; margin-top: 8px;">
                        <span style="font-size: 13px; color: #f59e0b; font-weight: 600;">⏳ Falta por pagar: ${{ number_format($restanteD, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div style="text-align: center; margin-top: 8px;">
                        <span style="font-size: 13px; color: #16a34a; font-weight: 600;">✅ Pago completo</span>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Notas --}}
                @if(!empty($detallePedido['notas']))
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #92400e; margin-bottom: 12px;">
                    📝 {{ $detallePedido['notas'] }}
                </div>
                @endif

                {{-- Edit payment method --}}
                @php
                    $editUrl = \App\Filament\Resources\Pedidos\Pages\EditPedido::getUrl([$detallePedido['id']]);
                @endphp
                <div style="text-align: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <a href="{{ $editUrl }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; text-decoration: none;">
                        ✏️ Gestionar método de pago
                    </a>
                </div>

            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        function printPedido(id) {
            var w = window.open('/admin/ticket/' + id, '_blank', 'width=400,height=600,menubar=no,toolbar=no,location=no');
            if (w) w.focus();
        }
    </script>
    @endpush

    @include('partials.global-notifications')
</x-filament-panels::page>
