<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;" wire:poll.keep-alive.5s="cargarPedidos">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #fee2e2; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏳</div>
                    <div>
                        <p style="margin: 0; color: #ef4444; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Pend. Pago</p>
                        <p style="margin: 0; color: #991b1b; font-size: 24px; font-weight: 700;">{{ count($pendientePago) }}</p>
                    </div>
                </div>
            </div>
            <div style="background: #f3e8ff; border: 1px solid #d8b4fe; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #ede9fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📍</div>
                    <div>
                        <p style="margin: 0; color: #9333ea; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Ha Llegado</p>
                        <p style="margin: 0; color: #6b21a8; font-size: 24px; font-weight: 700;">{{ count($haLlegado) }}</p>
                    </div>
                </div>
            </div>
            <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #ffedd5; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">👨‍🍳</div>
                    <div>
                        <p style="margin: 0; color: #ea580c; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Preparacion</p>
                        <p style="margin: 0; color: #9a3412; font-size: 24px; font-weight: 700;">{{ count($enProceso) }}</p>
                    </div>
                </div>
            </div>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">🚗</div>
                    <div>
                        <p style="margin: 0; color: #2563eb; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">En Camino</p>
                        <p style="margin: 0; color: #1e3a5f; font-size: 24px; font-weight: 700;">{{ count($enCamino) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-bottom: -16px;">
            <button wire:click="$set('vistaLista', false)" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; {{ !$vistaLista ? 'background: #111827; color: white; border-color: #111827;' : 'background: white; color: #374151;' }}">
                📋 Kanban
            </button>
            <button wire:click="$set('vistaLista', true)" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; {{ $vistaLista ? 'background: #111827; color: white; border-color: #111827;' : 'background: white; color: #374151;' }}">
                📄 Lista
            </button>
            <button onclick="window.probarNotificacion()" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; background: #fef3c7; color: #92400e;">
                🔔 Probar
            </button>
            @can('applyDiscount', auth()->user())
            <button wire:click="togglePausar" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; {{ $pedidos_pausados ? 'background: #f59e0b; color: white; border-color: #f59e0b;' : 'background: #e5e7eb; color: #374151;' }}" title="{{ $pedidos_pausados ? 'Pedidos pausados — haz clic para reactivar' : 'Pausar recepción de pedidos web' }}">
                ⏸️ {{ $pedidos_pausados ? 'Pausado' : 'Pausar' }}
            </button>
            @endcan
        </div>

        @if($vistaLista)
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="display: grid; grid-template-columns: 0.7fr 0.8fr 0.7fr 1.4fr auto; gap: 0; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;">
                <div style="padding: 8px; text-align: left;">Fecha</div>
                <div style="padding: 8px; text-align: left;">Estado</div>
                <div style="padding: 8px; text-align: left;">Total</div>
                <div style="padding: 8px; text-align: left;">Cliente</div>
                <div style="padding: 8px; text-align: left;">Acción</div>
            </div>
            @forelse($todos as $pedido)
            @php
                $productos = $this->getProductosPedido($pedido['id']);
                $pagado = $this->pagoCompleto($pedido['id']);
                $dir = collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', ');
                $resumen = collect($productos)->map(fn($p) => $p['cantidad'] . 'x ' . (!empty($p['mitades']) ? 'Pizza Mediana Mitad y Mitad [' . collect($p['mitades'])->pluck('nombre')->implode('/') . ']' : $p['producto']['nombre'] . (!empty($p['variant_tamanio']) ? ' (' . $p['variant_tamanio'] . ')' : '')))->implode(', ');
                $iconoEstado = match($pedido['estado']) { 'pendiente_pago' => '⏳', 'en_proceso' => '👨‍🍳', 'en_camino' => '🚗', 'entregado' => '📍', default => '' };
                $siguiente = match($pedido['estado']) { 'pendiente_pago' => 'en_proceso', 'en_proceso' => 'en_camino', 'en_camino' => 'entregado', 'entregado' => 'finalizado', default => null };
                $pedidoMinutos = \Carbon\Carbon::parse($pedido['created_at'])->diffInMinutes(now());
                $colorTiempo = $pedidoMinutos > 60 ? '#dc2626' : ($pedidoMinutos > 30 ? '#d97706' : '#6b7280');
                $bgTiempo = $pedidoMinutos > 60 ? '#fef2f2' : ($pedidoMinutos > 30 ? '#fef3c7' : 'transparent');
                $colorEstado = match($pedido['estado']) { 'pendiente_pago' => '#ef4444', 'en_proceso' => '#ea580c', 'en_camino' => '#2563eb', 'entregado' => '#9333ea', default => '#6b7280' };
                $bgEstado = match($pedido['estado']) { 'pendiente_pago' => '#fee2e2', 'en_proceso' => '#ffedd5', 'en_camino' => '#dbeafe', 'entregado' => '#f3e8ff', default => '#f3f4f6' };
            @endphp
            <div wire:click="editarPedido({{ $pedido['id'] }})" style="display: grid; grid-template-columns: 0.7fr 0.8fr 0.7fr 1.4fr auto; gap: 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; cursor: pointer; transition: background 0.15s;"
                 onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                {{-- Fecha: # + tiempo + fecha --}}
                <div style="padding: 8px; text-align: center;">
                    <div style="font-weight: 700; color: #111827; font-size: 14px;">#{{ $pedido['numero_pedido'] }}</div>
                    <div style="font-size: 10px; color: {{ $colorTiempo }}; background: {{ $bgTiempo }}; padding: 1px 4px; border-radius: 4px; display: inline-block; margin-top: 2px;">
                        ⏱ {{ $this->tiempoTranscurrido($pedido['created_at']) }}
                    </div>
                    <div style="font-size: 10px; color: #6b7280; margin-top: 1px;">{{ \Carbon\Carbon::parse($pedido['created_at'], 'UTC')->setTimezone('America/Bogota')->format('d/m H:i') }}</div>
                </div>
                {{-- Estado: badge estado + origen --}}
                <div style="padding: 8px; display: flex; flex-direction: column; align-items: flex-start; gap: 4px; justify-content: center;">
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 600; background: {{ $bgEstado }}; color: {{ $colorEstado }};">{{ $iconoEstado }} {{ match($pedido['estado']) { 'pendiente_pago' => 'Pend. Pago', 'en_proceso' => 'Preparación', 'en_camino' => 'En Camino', 'entregado' => 'Ha Llegado', default => '' } }}</span>
                    <span style="font-size: 10px; padding: 1px 6px; border-radius: 4px; font-weight: 500; {{ ($pedido['origen'] ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;' }}">
                        {{ strtoupper($pedido['origen'] ?? 'PDV') }}
                    </span>
                </div>
                {{-- Total: monto + pago --}}
                <div style="padding: 8px; text-align: center;">
                    <div style="font-weight: 700; font-size: 14px; color: #111827;">${{ number_format($pedido['total'], 0, ',', '.') }}</div>
                    <div style="margin-top: 2px;">
                        @if(!empty($pedido['metodo_pago']))
                        <span style="font-size: 9px; padding: 1px 4px; border-radius: 4px; font-weight: 500;
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
                        @endif
                        <span style="font-size: 9px; padding: 1px 4px; border-radius: 4px; font-weight: 500; {{ $pagado ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;' }}">
                            {{ $pagado ? 'Pagado' : 'Pend.' }}
                        </span>
                    </div>
                </div>
                {{-- Cliente: nombre + dirección + teléfono --}}
                <div style="padding: 8px; text-align: center;">
                    <div style="display: flex; align-items: center; gap: 4px; justify-content: center;">
                        <span style="font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $pedido['cliente']['nombre'] }}</span>
                        @if(isset($pedido['cliente']['clasificacion']))
                            <span style="flex-shrink: 0; font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 600;
                                background: {{ $pedido['cliente']['clasificacion'] === 'elite' ? '#fef3c7' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#ffedd5' : '#f3f4f6') }};
                                color: {{ $pedido['cliente']['clasificacion'] === 'elite' ? '#92400e' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#9a3412' : '#6b7280') }};">
                                {{ $pedido['cliente']['clasificacion'] === 'elite' ? '⭐' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '🔥' : '🆕') }}
                            </span>
                        @endif
                    </div>
                    <div style="font-size: 11px; color: #4b5563; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 1px;" title="{{ $dir }}">{{ $dir }}</div>
                    <div style="font-size: 11px; color: #6b7280; margin-top: 1px;">📞 {{ $pedido['cliente']['telefono'] ?? '' }}</div>
                </div>
                <div style="padding: 6px 8px; text-align: center; display: flex; gap: 3px; align-items: center; justify-content: center; flex-wrap: wrap;">
                    @if(!in_array($pedido['estado'], ['finalizado', 'cancelado']))
                    <a href="/admin/pedidos/{{ $pedido['id'] }}/edit" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 6px; font-size: 16px; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Editar pedido">✏️</a>
                    @endif
                    <a href="#" onclick="event.stopPropagation(); printPedido({{ $pedido['id'] }}); return false;" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 6px; font-size: 16px; font-weight: 500; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Reimprimir ticket">🖨️</a>
                    @if(!empty($pedido['cliente']['telefono']))
                        @php $waTel = preg_replace('/[^0-9]/', '', $pedido['cliente']['telefono']); @endphp
                        <a href="https://wa.me/{{ $waTel }}?text={{ urlencode('Hola! Te escribimos de Diego\'s Pizza por tu pedido #' . $pedido['numero_pedido']) }}" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 6px; font-size: 16px; font-weight: 500; text-decoration: none; background: #dcfce7; color: #25D366; border: 1px solid #bbf7d0;" title="WhatsApp cliente">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="#25D366" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    @endif
                    @can('applyDiscount', auth()->user())
                        <button wire:click.stop="eliminarPedido({{ $pedido['id'] }})" onclick="return confirm('¿Eliminar pedido #{{ $pedido['numero_pedido'] }}? Esta acción no se puede deshacer.')" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 6px; font-size: 16px; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; cursor: pointer;" title="Eliminar pedido">🗑️</button>
                    @endcan
                    @if($siguiente === 'en_proceso')
                        <x-filament::button wire:click.stop="cambiarEstado({{ $pedido['id'] }}, 'en_proceso')" size="sm" color="success">
                            ✅ Acep.
                        </x-filament::button>
                        <x-filament::button wire:click.stop="rechazarPedido({{ $pedido['id'] }})" size="sm" color="danger">
                            ❌ Rech.
                        </x-filament::button>
                    @elseif($siguiente === 'finalizado')
                        @php $pagoOk = $this->pagoCompleto($pedido['id']); @endphp
                        @if($pagoOk)
                            <x-filament::button wire:click.stop="finalizarPedido({{ $pedido['id'] }})" size="sm" color="success">
                                ✅ Final.
                            </x-filament::button>
                        @else
                            <x-filament::button wire:click.stop="finalizarPedido({{ $pedido['id'] }})" size="sm" color="warning">
                                💳 Pagar
                            </x-filament::button>
                        @endif
                    @else
                        <x-filament::button wire:click.stop="cambiarEstado({{ $pedido['id'] }}, '{{ $siguiente }}')" size="sm" color="gray">
                            {{ $siguiente == 'en_camino' ? '🚗 Enviar' : '📍 Llegó' }}
                        </x-filament::button>
                    @endif
                    @if(!in_array($pedido['estado'], ['pendiente_pago', 'finalizado', 'cancelado']))
                    <x-filament::button x-data x-on:click="event.stopPropagation(); Swal.fire({title:'🛑 Cancelar Pedido',input:'select',inputOptions:{rechazado:'Rechazado',doble:'Se hizo doble',tiempo:'Por tiempo de espera',ya_no_quiere:'Ya no lo quiere'},inputPlaceholder:'Selecciona un motivo',showCancelButton:true,confirmButtonText:'Cancelar pedido',cancelButtonText:'Volver',confirmButtonColor:'#dc2626',preConfirm:(m)=>{if(!m){Swal.showValidationMessage('Debes seleccionar un motivo');return false}return m}}).then((r)=>{if(r.isConfirmed){$wire.cancelarPedido({{ $pedido['id'] }},r.value)}})" size="sm" color="danger">
                        🛑
                    </x-filament::button>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 32px 0; color: #6b7280;">📋 Sin pedidos activos</div>
            @endforelse
        </div>
        @else
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            @php
                $columnas = [
                    ['nombre' => 'Pendiente de Pago', 'color' => '#ef4444', 'bg' => '#fee2e2', 'icon' => '⏳', 'datos' => $pendientePago, 'siguiente' => 'en_proceso', 'accion' => 'Aceptar'],
                    ['nombre' => 'En Preparación', 'color' => '#ea580c', 'bg' => '#ffedd5', 'icon' => '👨‍🍳', 'datos' => $enProceso, 'siguiente' => 'en_camino', 'accion' => 'Enviar'],
                    ['nombre' => 'En Camino', 'color' => '#2563eb', 'bg' => '#dbeafe', 'icon' => '🚗', 'datos' => $enCamino, 'siguiente' => 'entregado', 'accion' => 'Llegó'],
                    ['nombre' => 'Ha Llegado', 'color' => '#9333ea', 'bg' => '#f3e8ff', 'icon' => '📍', 'datos' => $haLlegado, 'siguiente' => 'finalizado', 'accion' => 'Finalizar'],
                ];
            @endphp

            @foreach($columnas as $col)
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <div style="background: {{ $col['color'] }}; padding: 12px 16px; display: flex; align-items: center; gap: 8px;">
                        <span>{{ $col['icon'] }}</span>
                        <span style="color: white; font-weight: 600;">{{ $col['nombre'] }} ({{ count($col['datos']) }})</span>
                    </div>
                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 12px; max-height: 500px; overflow-y: auto;">
                        @forelse($col['datos'] as $pedido)
                            @php $dir = collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', '); @endphp
                            <div wire:click="editarPedido({{ $pedido['id'] }})" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="background: #111827; color: white; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 4px;">#{{ $pedido['numero_pedido'] }}</span>
                                        <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; {{ ($pedido['origen'] ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;' }}">
                                            {{ strtoupper($pedido['origen'] ?? 'PDV') }}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @php $pedidoMinutos = \Carbon\Carbon::parse($pedido['created_at'])->diffInMinutes(now()); @endphp
                                        @php $colorTiempo = $pedidoMinutos > 60 ? '#dc2626' : ($pedidoMinutos > 30 ? '#d97706' : '#6b7280'); @endphp
                                        @php $bgTiempo = $pedidoMinutos > 60 ? '#fef2f2' : ($pedidoMinutos > 30 ? '#fef3c7' : '#f3f4f6'); @endphp
                                        <span style="font-size: 12px; background: {{ $bgTiempo }}; color: {{ $colorTiempo }}; padding: 2px 8px; border-radius: 10px;">{{ $this->tiempoTranscurrido($pedido['created_at']) }}</span>
                                        @if(!empty($pedido['metodo_pago']))
                                            <span style="font-size: 12px; padding: 2px 8px; border-radius: 10px; font-weight: 500;
                                                 @if($pedido['metodo_pago'] == 'efectivo') background: #dcfce7; color: #16a34a;
                                                @elseif($pedido['metodo_pago'] == 'tarjeta') background: #dbeafe; color: #2563eb;
                                                @elseif($pedido['metodo_pago'] == 'mixto') background: #fef3c7; color: #d97706;
                                                @else background: #f3e8ff; color: #9333ea; @endif">
                                                {{ $pedido['metodo_pago'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <p style="margin: 0; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 4px;">
                                    {{ $pedido['cliente']['nombre'] }}
                                    @if(isset($pedido['cliente']['clasificacion']))
                                        <span style="font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 600;
                                            background: {{ $pedido['cliente']['clasificacion'] === 'elite' ? '#fef3c7' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#ffedd5' : '#f3f4f6') }};
                                color: {{ $pedido['cliente']['clasificacion'] === 'elite' ? '#92400e' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#9a3412' : '#4b5563') }};">
                                            {{ $pedido['cliente']['clasificacion'] === 'elite' ? '⭐' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '🔥' : '🆕') }}
                                        </span>
                                    @endif
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #6b7280;" title="{{ $dir }}">{{ $dir }}</p>
                                @if(!empty($pedido['cliente']['telefono']))
                                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #6b7280;">📞 {{ $pedido['cliente']['telefono'] }}</p>
                                @endif

                                @php $productos = $this->getProductosPedido($pedido['id']); @endphp
                                <div style="margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 8px;">
                                    @foreach($productos as $pp)
                                        <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 2px 0;">
                                            <span style="color: #4b5563;"><strong>{{ $pp['cantidad'] }}x</strong> @if(!empty($pp['mitades']))Pizza Mediana Mitad y Mitad<span style="color: #ea580c; font-size: 10px;"> [{{ collect($pp['mitades'])->pluck('nombre')->implode(' / ') }}]</span>@else{{ $pp['producto']['nombre'] }}@if(!empty($pp['variant_tamanio'])) <span style="color: #ea580c;">({{ $pp['variant_tamanio'] }})</span>@endif @endif</span>
                                            <span style="font-weight: 500; color: #374151;">${{ number_format($pp['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <span style="font-weight: 700; font-size: 16px;">${{ number_format($pedido['total'], 0, ',', '.') }}</span>
                                        @php $pedidoPagoCompleto = $pedido['pago_completo'] ?? $this->pagoCompleto($pedido['id']); @endphp
                                        <span style="font-size: 11px; padding: 2px 6px; border-radius: 10px; font-weight: 500; {{ $pedidoPagoCompleto ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;' }}">
                                            {{ $pedidoPagoCompleto ? '💳 Pagado' : '⏳ Pendiente' }}
                                        </span>
                                        <a href="#" onclick="event.stopPropagation(); printPedido({{ $pedido['id'] }}); return false;" style="display: inline-flex; align-items: center; gap: 3px; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;">
                                            🖨️
                                        </a>
                                        @if(!empty($pedido['cliente']['telefono']))
                                            @php $waTelKanban = preg_replace('/[^0-9]/', '', $pedido['cliente']['telefono']); @endphp
                                            <a href="https://wa.me/{{ $waTelKanban }}?text={{ urlencode('Hola! Te escribimos de Diego\'s Pizza por tu pedido #' . $pedido['numero_pedido']) }}" target="_blank" onclick="event.stopPropagation()" style="display: inline-flex; align-items: center; gap: 3px; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #dcfce7; color: #25D366; border: 1px solid #bbf7d0;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="#25D366" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        @if(!in_array($pedido['estado'], ['finalizado', 'cancelado']))
                                        <a href="/admin/pedidos/{{ $pedido['id'] }}/edit" onclick="event.stopPropagation()" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-size: 12px; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Editar pedido">✏️</a>
                                        @endif
                                        @can('applyDiscount', auth()->user())
                                        <button wire:click.stop="eliminarPedido({{ $pedido['id'] }})" onclick="return confirm('¿Eliminar pedido #{{ $pedido['numero_pedido'] }}? Esta acción no se puede deshacer.')" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-size: 12px; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; cursor: pointer;" title="Eliminar pedido">🗑️</button>
                                        @endcan
                                        <x-filament::button wire:click.stop="abrirModalPago({{ $pedido['id'] }})" size="sm" color="gray">
                                            💳
                                        </x-filament::button>
                                        @if($col['siguiente'] === 'en_proceso')
                                            <x-filament::button wire:click.stop="cambiarEstado({{ $pedido['id'] }}, 'en_proceso')" size="sm" color="success">
                                                ✅ {{ $col['accion'] }}
                                            </x-filament::button>
                                            <x-filament::button wire:click.stop="rechazarPedido({{ $pedido['id'] }})" size="sm" color="danger">
                                                ❌ Rechazar
                                            </x-filament::button>
                                        @elseif($col['siguiente'] !== 'finalizado')
                                            <x-filament::button wire:click.stop="cambiarEstado({{ $pedido['id'] }}, '{{ $col['siguiente'] }}')" size="sm" color="gray">
                                                {{ $col['accion'] }}
                                            </x-filament::button>
                                        @elseif($col['siguiente'] === 'finalizado' && ($pedido['pago_completo'] ?? false))
                                            <x-filament::button wire:click.stop="finalizarPedido({{ $pedido['id'] }})" size="sm" color="success">
                                                ✅ Finalizar
                                            </x-filament::button>
                                        @elseif($col['siguiente'] === 'finalizado')
                                            <x-filament::button wire:click.stop="finalizarPedido({{ $pedido['id'] }})" size="sm" color="warning">
                                                💳 Pagar
                                            </x-filament::button>
                                        @endif
                                        @if(!in_array($pedido['estado'], ['pendiente_pago', 'finalizado', 'cancelado']))
                                        <x-filament::button x-data x-on:click="event.stopPropagation(); Swal.fire({title:'🛑 Cancelar Pedido',input:'select',inputOptions:{rechazado:'Rechazado',doble:'Se hizo doble',tiempo:'Por tiempo de espera',ya_no_quiere:'Ya no lo quiere'},inputPlaceholder:'Selecciona un motivo',showCancelButton:true,confirmButtonText:'Cancelar pedido',cancelButtonText:'Volver',confirmButtonColor:'#dc2626',preConfirm:(m)=>{if(!m){Swal.showValidationMessage('Debes seleccionar un motivo');return false}return m}}).then((r)=>{if(r.isConfirmed){$wire.cancelarPedido({{ $pedido['id'] }},r.value)}})" size="sm" color="danger">
                                            🛑
                                        </x-filament::button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 32px 0;">
                                <p style="font-size: 36px; margin: 0 0 8px 0;">📋</p>
                                <p style="color: #6b7280; margin: 0;">Sin pedidos</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @if($modalPago)
    @php $restante = max(0, $totalConDescuento - $totalPagado); @endphp
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 999;">
        <div style="background: white; border-radius: 16px; padding: 0; width: 650px; max-width: 95vw; max-height: 95vh; overflow-y: auto;">
            <div style="background: linear-gradient(to right, #f9fafb, white); padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="margin: 0; font-size: 20px; font-weight: 800; color: #111827;">Pedido #{{ $pedidoNumero }}</h3>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 700; background: #fef3c7; color: #92400e;">{{ $pedidoEstado === 'pendiente_pago' ? 'Pendiente Pago' : $pedidoEstado }}</span>
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; background: {{ $pedidoOrigen === 'web' ? '#dbeafe' : '#f3e8ff' }}; color: {{ $pedidoOrigen === 'web' ? '#1e40af' : '#6b21a8' }};">{{ strtoupper($pedidoOrigen) }}</span>
                        <span style="font-size: 12px; color: #6b7280;">{{ $pedidoFecha }}</span>
                    </div>
                </div>
                <button wire:click="cerrarModalPago" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280; padding: 0 4px;">×</button>
            </div>
            <div style="padding: 16px 24px;">

            {{-- Cliente --}}
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 4px; font-size: 14px; font-weight: 700; color: #111827;">Cliente: {{ $clienteNombre }}</p>
                <p style="margin: 0 0 2px; font-size: 13px; color: #6b7280;">Teléfono: +57 {{ $clienteTelefono }}</p>
                <p style="margin: 0; font-size: 13px; color: #6b7280;">{{ $clienteDireccion }}</p>
                @if(count($direccionesDisponibles) > 1)
                <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 4px;">
                    @foreach($direccionesDisponibles as $dir)
                    <button type="button" wire:click="seleccionarDireccionModal({{ $dir['id'] }})" style="padding: 4px 10px; border-radius: 6px; font-size: 11px; cursor: pointer; border: 1px solid {{ $direccionSeleccionadaId === $dir['id'] ? '#ef4444' : '#d1d5db' }}; background: {{ $direccionSeleccionadaId === $dir['id'] ? '#fef2f2' : '#f9fafb' }}; color: {{ $direccionSeleccionadaId === $dir['id'] ? '#dc2626' : '#374151' }};">
                        {{ $dir['alias'] ?? 'Dir' }}: {{ $dir['conjunto'] }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Products ordered --}}
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 13px; font-weight: 700; color: #374151;">Productos: {{ count($productosPedido) }}</p>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; max-height: 140px; overflow-y: auto;">
                    @foreach($productosPedido as $pp)
                    <div style="display: flex; justify-content: space-between; padding: 6px 12px; font-size: 13px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }}">
                        <span style="color: #374151;">
                            <strong>{{ $pp['cantidad'] }}</strong>
                            @if(!empty($pp['mitades']))
                                Pizza Mediana Mitad y Mitad <span style="color: #ea580c;">[{{ collect($pp['mitades'])->pluck('nombre')->implode(' / ') }}]</span>
                            @else
                                {{ $pp['producto']['nombre'] }}@if(!empty($pp['variant_tamanio'])) <span style="color: #6b7280;">- {{ $pp['variant_tamanio'] }}</span>@endif
                            @endif
                        </span>
                        <span style="font-weight: 600;">${{ number_format($pp['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Subtotal + Total --}}
            <div style="background: #f9fafb; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; margin-bottom: 4px;">
                    <span>Subtotal Productos ({{ count($productosPedido) }})</span>
                    <span>${{ number_format($pedidoSubtotal, 0, ',', '.') }}</span>
                </div>
                @if($descuentoAplicado > 0)
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #dc2626;">
                    <span>Descuento</span>
                    <span>-${{ number_format($descuentoAplicado, 0, ',', '.') }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280;">
                    <span>Estado</span>
                    <span style="font-weight: 600; color: {{ $restante > 0 ? '#f59e0b' : '#16a34a' }};">{{ $restante > 0 ? 'Pendiente' : 'Pagado' }}</span>
                </div>
                @if($restante > 0)
                <div style="display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; color: #dc2626; border-top: 1px dashed #e5e7eb; padding-top: 6px; margin-top: 6px;">
                    <span>Saldo pendiente</span>
                    <span>${{ number_format($restante, 0, ',', '.') }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: #111827; border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                    <span>Total</span>
                    <span>${{ number_format($totalConDescuento, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Payment form --}}
            @if($pedidoEstado === 'cancelado')
            <div style="text-align: center; padding: 12px; background: #fef2f2; border-radius: 8px; color: #dc2626; font-weight: 600; margin-bottom: 16px;">
                ❌ Pedido cancelado
            </div>
            @elseif($restante > 0)
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
                @if($pagoError)
                <div style="margin-top: 8px; padding: 10px 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #dc2626; font-size: 13px; font-weight: 500; text-align: center;">
                    ⚠️ {{ $pagoError }}
                </div>
                @endif
                {{-- Descuento --}}
                @can('applyDiscount', auth()->user())
                <div style="margin-top: 8px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <span style="font-size: 12px; font-weight: 600; color: #6b7280;">🏷️ Descuento:</span>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="radio" wire:model.live="descuentoTipo" value="fijo"> en pesos
                    </label>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="radio" wire:model.live="descuentoTipo" value="porcentaje"> Porcentaje
                    </label>
                    <input type="number" step="1" wire:model="descuentoValor" min="0" placeholder="0" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px; width: 80px;">
                    <x-filament::button wire:click="aplicarDescuento" color="gray" size="sm">
                        Aplicar
                    </x-filament::button>
                </div>
                @endcan
            </div>
            @endif

            {{-- Payment history --}}
            @if(count($pagosRegistrados) > 0)
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Historial de pagos</p>
                <div style="max-height: 120px; overflow-y: auto;">
                    @foreach($pagosRegistrados as $pago)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 4px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                            <span style="font-weight: 700; color: #16a34a;">${{ number_format($pago['monto'], 0, ',', '.') }}</span>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;
                                @if($pago['metodo'] == 'efectivo') background: #dcfce7; color: #16a34a;
                                @elseif($pago['metodo'] == 'tarjeta') background: #dbeafe; color: #2563eb;
                                @else background: #f3e8ff; color: #9333ea; @endif">
                                {{ $pago['metodo'] }}
                            </span>
                            <span style="color: #6b7280; font-size: 12px;">{{ \Carbon\Carbon::parse($pago['created_at'], 'UTC')->setTimezone('America/Bogota')->format('d/m/y H:i') }}</span>
                        </div>
                        <button wire:click="eliminarPago({{ $pago['id'] }})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; padding: 0 4px;" title="Eliminar">×</button>
                    </div>
                    @endforeach
                </div>
                @if($restante > 0)
                <div style="text-align: center; margin-top: 8px;">
                    <span style="font-size: 13px; color: #6b7280; font-weight: 500;">¿Agregar otro pago?</span>
                </div>
                @endif
            </div>
            @elseif($restante <= 0)
            <div style="text-align: center; padding: 12px; background: #f0fdf4; border-radius: 8px; color: #16a34a; font-weight: 600; margin-bottom: 12px;">
                ✅ Pago completo
            </div>
            <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 16px;">
                @if($pedidoEstado === 'pendiente_pago')
                    <x-filament::button wire:click="aceptarDesdeModal" color="success">
                        ✅ Aceptar Pedido
                    </x-filament::button>
                @elseif($pedidoEstado === 'entregado')
                    <x-filament::button wire:click="finalizarDesdeModal" color="success">
                        🎉 Finalizar Pedido
                    </x-filament::button>
                @endif
            </div>
            @endif

            {{-- Edit Client --}}
            @if(!in_array($pedidoEstado, ['finalizado', 'cancelado']))
            <div style="border-top: 1px solid #e5e7eb; padding-top: 12px;">
                <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">✏️ Editar Cliente</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <input type="text" wire:model.live="clienteNombre" placeholder="Nombre" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    <input type="text" wire:model.live="clienteTelefono" placeholder="Teléfono" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    <input type="text" wire:model.live="clienteConjunto" placeholder="Conjunto" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    <div style="display: flex; gap: 8px;">
                        <input type="text" wire:model.live="clienteTorre" placeholder="Torre" style="flex: 1; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                        <input type="text" wire:model.live="clienteApto" placeholder="Apto" style="flex: 1; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    </div>
                </div>
            </div>
            @endif
            <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: flex-end;">
                <x-filament::button wire:click="guardarCambios" color="gray" size="sm">
                    💾 Guardar cambios
                </x-filament::button>
            </div>
        </div>
    </div>
    @endif

    <script src="https://unpkg.com/sweetalert2@11"></script>
    <script>
        console.log('PDV: script loaded');
        var pdvCtx = null;
        var pdvAlarmInterval = null;
        var pdvIdsAlertando = {};
        var pdvMostrandoSwal = false;

        function pdvInitAudio() {
            if (pdvCtx) return;
            try {
                pdvCtx = new (window.AudioContext || window.webkitAudioContext)();
                pdvCtx.resume();
            } catch(e) {}
        }

        var pdvKeepAliveInterval = null;
        function pdvKeepAlive() {
            if (!pdvCtx) return;
            try {
                if (pdvCtx.state === 'suspended') pdvCtx.resume();
                var now = pdvCtx.currentTime;
                var g = pdvCtx.createGain();
                g.gain.value = 0.001;
                g.connect(pdvCtx.destination);
                var o = pdvCtx.createOscillator();
                o.type = 'sine'; o.frequency.value = 20;
                o.connect(g); o.start(now); o.stop(now + 0.05);
            } catch(e){}
        }
        async function pdvTocarBeep() {
            if (!pdvCtx) return;
            try {
                if (pdvCtx.state === 'suspended') await pdvCtx.resume();
                var now = pdvCtx.currentTime;
                var g = pdvCtx.createGain();
                g.gain.setValueAtTime(0.5, now);
                g.gain.setValueAtTime(0.5, now + 0.25);
                g.gain.setValueAtTime(0, now + 0.5);
                g.connect(pdvCtx.destination);
                var o1 = pdvCtx.createOscillator();
                o1.type = 'square'; o1.frequency.value = 880;
                o1.connect(g); o1.start(now); o1.stop(now + 0.5);
                var o2 = pdvCtx.createOscillator();
                o2.type = 'square'; o2.frequency.value = 660;
                o2.connect(g); o2.start(now + 0.25); o2.stop(now + 0.5);
            } catch(e){}
        }

        function pdvIniciarAlarma(pedido) {
            if (pdvIdsAlertando[pedido.id]) return;
            pdvIdsAlertando[pedido.id] = true;
            if (!pdvCtx) pdvInitAudio();
            pdvTocarBeep();
            if (!pdvKeepAliveInterval) {
                pdvKeepAliveInterval = setInterval(pdvKeepAlive, 30000);
            }
            if (!pdvAlarmInterval) {
                pdvAlarmInterval = setInterval(function(){
                    if (Object.keys(pdvIdsAlertando).length > 0) pdvTocarBeep();
                }, 2000);
            }
            if (!pdvMostrandoSwal && typeof Swal !== 'undefined') {
                pdvMostrandoSwal = true;
                pdvIniciarAudioLoop();
                var nom = (pedido.cliente && pedido.cliente.nombre) ? pedido.cliente.nombre : '';
                var totalStr = Number(pedido.total).toLocaleString('es-CO');
                Swal.fire({
                    title: '🔔 NUEVO PEDIDO',
                    html: '<div style="font-size:20px;font-weight:700;margin-bottom:4px;">#'+(pedido.numero_pedido||pedido.id)+'</div><div style="font-size:16px;">'+nom+'</div><div style="font-size:14px;color:#6b7280;margin-top:4px;">$'+totalStr+'</div>',
                    icon: 'warning',
                    confirmButtonText: '✅ Ver Pedido',
                    confirmButtonColor: '#22c55e',
                    allowOutsideClick: false,
                    didClose: function() {
                        pdvMostrandoSwal = false;
                    }
                });
            }
        }

        function pdvDetenerAlarma(pedidoId) {
            delete pdvIdsAlertando[pedidoId];
            if (Object.keys(pdvIdsAlertando).length === 0) {
                if (pdvAlarmInterval) { clearInterval(pdvAlarmInterval); pdvAlarmInterval = null; }
                if (pdvKeepAliveInterval) { clearInterval(pdvKeepAliveInterval); pdvKeepAliveInterval = null; }
            }
        }

        document.addEventListener('click', pdvInitAudio, { once: true });

        function pdvToast(p) {
            var c = document.getElementById('pdv-toast-container');
            if (!c) { c = document.createElement('div'); c.id = 'pdv-toast-container'; c.style.cssText='position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;'; document.body.appendChild(c); }
            var n = document.createElement('div');
            n.style.cssText = 'background:#1e293b;color:white;border-radius:12px;padding:16px 20px;box-shadow:0 10px 40px rgba(0,0,0,0.3);max-width:380px;font-family:-apple-system,sans-serif;display:flex;align-items:center;gap:12px;cursor:pointer;';
            n.innerHTML = '<div style="background:#22c55e;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">🍕</div><div><div style="font-size:14px;font-weight:700;">Nuevo Pedido #'+(p.numero_pedido||p.id)+'</div><div style="font-size:12px;color:#94a3b8;">'+(p.cliente?.nombre||'')+'</div></div>';
            n.onclick = function(){ n.remove(); };
            c.appendChild(n);
            setTimeout(function(){ if(n.parentNode) n.remove(); }, 10000);
        }
        function pdvSystemNotif(p) {
            if (!('Notification' in window) || Notification.permission !== 'granted') return;
            try {
                var n = new Notification('🍕 Pedido #'+(p.numero_pedido||p.id), { body: (p.cliente?.nombre||'')+' — $'+Number(p.total).toLocaleString('es-CO'), tag: 'pdv-'+p.id, requireInteraction: true });
                setTimeout(function(){ n.close(); }, 10000);
                n.onclick = function(){ window.focus(); this.close(); };
            } catch(e) {}
        }
        function pdvFlash(p) {
            var orig = document.title, count = 0, t = setInterval(function(){
                document.title = (count%2===0) ? '🆕 Pedido #'+(p.numero_pedido||p.id)+' — '+orig : orig;
                count++;
                if (count >= 8) { clearInterval(t); document.title = orig; }
            }, 800);
            document.addEventListener('visibilitychange', function vis(){
                if (!document.hidden) { clearInterval(t); document.title = orig; document.removeEventListener('visibilitychange', vis); }
            });
        }

        var pdvUltimosIds = [];
        var pdvInicializado = false;
        function pdvBuscarNuevos() {
            console.log('PDV: checking for new pedidos...');
            fetch('/api/pedidos/pendientes', { credentials: 'same-origin' }).then(function(r){
                console.log('PDV: fetch status', r.status);
                return r.json();
            }).then(function(data){
                console.log('PDV: data received', data);
                var pedidos = data.pedidos || [];
                var ids = pedidos.map(function(p){ return p.id; });
                console.log('PDV: ids', ids, 'ultimosIds', pdvUltimosIds, 'inicializado', pdvInicializado);
                Object.keys(pdvIdsAlertando).forEach(function(id) {
                    if (ids.indexOf(parseInt(id)) === -1) {
                        pdvDetenerAlarma(parseInt(id));
                    }
                });
                if (pdvInicializado) {
                    var nuevos = pedidos.filter(function(p){ return pdvUltimosIds.indexOf(p.id) === -1; });
                    console.log('PDV: nuevos pedidos', nuevos);
                    nuevos.forEach(function(p){
                        pdvIniciarAlarma(p);
                        pdvToast(p);
                        pdvSystemNotif(p);
                        pdvFlash(p);
                        try { navigator.vibrate && navigator.vibrate([200,100,200]); } catch(e){}
                        printPedido(p.id);
                    });
                }
                pdvUltimosIds = ids;
                pdvInicializado = true;
            }).catch(function(err){
                console.error('PDV: fetch error', err);
            });
        }

        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(function(){ Notification.requestPermission(); }, 3000);
        }

        setTimeout(pdvBuscarNuevos, 2000);
        setInterval(pdvBuscarNuevos, 5000);

        function printPedido(id) {
            var iframe = document.getElementById('pdv-print-frame');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'pdv-print-frame';
                iframe.style = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;';
                document.body.appendChild(iframe);
            }
            iframe.src = '/admin/ticket/' + id;
            iframe.onload = function() {
                setTimeout(function() {
                    try { iframe.contentWindow.print(); } catch(e) {}
                }, 500);
            };
        }

        window.probarNotificacion = function() {
            pdvInitAudio();
            var p = { id: 999999, numero_pedido: 'TEST', cliente: { nombre: 'Prueba' }, total: 50000, origen: 'web' };
            pdvIniciarAlarma(p);
            pdvToast(p);
            pdvSystemNotif(p);
            pdvFlash(p);
            try { navigator.vibrate && navigator.vibrate([200,100,200]); } catch(e){}
            setTimeout(function() { pdvDetenerAlarma(999999); }, 10000);
        };
    </script>
</x-filament-panels::page>