<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 20px;" wire:poll.keep-alive.10s="cargarPedidos">

        {{-- Resumen --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
            <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 12px; text-align: center;">
                <p style="margin: 0; color: #ea580c; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">👨‍🍳 Preparación</p>
                <p style="margin: 4px 0 0 0; color: #9a3412; font-size: 24px; font-weight: 700;">{{ count($enPreparacion) }}</p>
            </div>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 12px; text-align: center;">
                <p style="margin: 0; color: #2563eb; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">🚗 En Ruta</p>
                <p style="margin: 4px 0 0 0; color: #1e3a5f; font-size: 24px; font-weight: 700;">{{ count($enReparto) }}</p>
            </div>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px; text-align: center;">
                <p style="margin: 0; color: #16a34a; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">✅ Entregados Hoy</p>
                <p style="margin: 4px 0 0 0; color: #14532d; font-size: 24px; font-weight: 700;">{{ count($entregadosHoy) }}</p>
            </div>
        </div>

        @php
            $secciones = [
                ['titulo' => '🚗 En Ruta', 'pedidos' => $enReparto, 'color' => '#2563eb', 'bg' => '#eff6ff', 'borde' => '#bfdbfe'],
                ['titulo' => '👨‍🍳 En Preparación', 'pedidos' => $enPreparacion, 'color' => '#ea580c', 'bg' => '#fff7ed', 'borde' => '#fed7aa'],
                ['titulo' => '✅ Entregados Hoy', 'pedidos' => $entregadosHoy, 'color' => '#16a34a', 'bg' => '#f0fdf4', 'borde' => '#bbf7d0'],
                ['titulo' => '📚 Entregados (días anteriores)', 'pedidos' => $entregadosAnteriores, 'color' => '#6b7280', 'bg' => '#f9fafb', 'borde' => '#e5e7eb', 'historico' => true],
            ];
        @endphp

        @foreach($secciones as $seccion)
            <div>
                <h3 style="margin: 0 0 8px 0; font-size: 13px; font-weight: 700; color: {{ $seccion['color'] }}; text-transform: uppercase; letter-spacing: 0.05em;">
                    {{ $seccion['titulo'] }} ({{ count($seccion['pedidos']) }})
                </h3>

                @forelse($seccion['pedidos'] as $pedido)
                    @php
                        $cliente = $pedido['cliente'] ?? [];
                        $dir = collect([$cliente['conjunto'] ?? '', $cliente['torre'] ?? '', $cliente['apto'] ?? ''])->filter()->implode(', ');
                        $totalReal = max(0, (float) $pedido['total']);
                        $marcadoLlegada = !empty($pedido['llegada_at']);
                    @endphp
                    <div style="background: white; border: 1px solid {{ $seccion['borde'] }}; border-radius: 12px; padding: 14px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                            <div style="flex: 1; min-width: 0;">
                                <p style="margin: 0; font-size: 15px; font-weight: 700; color: #111827;">#{{ $pedido['numero_pedido'] }} · ${{ number_format($totalReal, 0, ',', '.') }}</p>
                                <p style="margin: 3px 0 0 0; font-size: 14px; font-weight: 600; color: #374151;">{{ $cliente['nombre'] ?? 'Cliente' }}</p>
                                @if($dir)
                                    <p style="margin: 2px 0 0 0; font-size: 13px; color: #6b7280;">🏠 {{ $dir }}</p>
                                @endif
                                @if(!empty($cliente['telefono']))
                                    <p style="margin: 2px 0 0 0; font-size: 13px; color: #6b7280;">📞 {{ $cliente['telefono'] }}</p>
                                @endif
                                @if($marcadoLlegada)
                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #16a34a; font-weight: 600;">📍 Llegaste: {{ \Carbon\Carbon::parse($pedido['llegada_at'])->format('H:i') }}</p>
                                @endif
                            </div>
                            <span style="font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: 600; white-space: nowrap; background: {{ $seccion['bg'] }}; color: {{ $seccion['color'] }};">
                                {{ match($pedido['estado']) { 'pendiente_pago' => '⏳ Pend. Pago', 'en_proceso' => '👨‍🍳 Preparación', 'en_camino' => '🚗 En Ruta', 'entregado' => '📍 Entregado', default => $pedido['estado'] } }}
                            </span>
                        </div>

                        @if($pedido['estado'] !== 'entregado')
                            <div style="display: flex; gap: 8px; margin-top: 12px;">
                                @unless($marcadoLlegada)
                                    <button wire:click="llegue({{ $pedido['id'] }})" style="flex: 1; padding: 11px 12px; border-radius: 8px; font-size: 14px; font-weight: 700; border: 1px solid #2563eb; background: #eff6ff; color: #1e3a5f; cursor: pointer;">
                                        📍 Ya llegué
                                    </button>
                                @endunless
                                <button wire:click="entregue({{ $pedido['id'] }})" wire:confirm="¿Confirmas que entregaste este pedido?" style="flex: 1; padding: 11px 12px; border-radius: 8px; font-size: 14px; font-weight: 700; border: 1px solid #16a34a; background: #16a34a; color: white; cursor: pointer;">
                                    ✅ Entregado
                                </button>
                            </div>
                        @elseif(!empty($seccion['historico']))
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #6b7280; font-weight: 600;">
                                ✅ Entregado el {{ !empty($pedido['entregado_at']) ? \Carbon\Carbon::parse($pedido['entregado_at'])->format('d/m/Y H:i') : '—' }}@if($marcadoLlegada) · 📍 Llegada {{ \Carbon\Carbon::parse($pedido['llegada_at'])->format('H:i') }}@endif
                            </div>
                        @else
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #16a34a; font-weight: 600;">
                                ✅ Entregado a las {{ !empty($pedido['entregado_at']) ? \Carbon\Carbon::parse($pedido['entregado_at'])->format('H:i') : '—' }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 12px; padding: 20px; text-align: center; color: #9ca3af; font-size: 13px; margin-bottom: 10px;">
                        {{ !empty($seccion['historico']) ? 'Sin entregas anteriores todavía 📚' : 'Sin pedidos aquí 🍕' }}
                    </div>
                @endforelse

                @if(!empty($seccion['historico']) && count($seccion['pedidos']) >= $historialLimite)
                    <button wire:click="cargarMasHistorial" style="width: 100%; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px dashed #9ca3af; background: white; color: #6b7280; cursor: pointer;">
                        📚 Ver más entregas (ya ves {{ count($seccion['pedidos']) }})
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
