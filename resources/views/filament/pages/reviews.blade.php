<x-filament-panels::page>
    <div x-data="{
            abrirResena(evento) {
                if (evento.target.closest('button, a')) return;
                var celda = evento.target.closest('[data-review-id]');
                if (!celda) {
                    var fila = evento.target.closest('tr');
                    celda = fila ? fila.querySelector('[data-review-id]') : null;
                }
                if (!celda) return;
                $wire.abrirDetalle(parseInt(celda.getAttribute('data-review-id'), 10));
            }
        }"
        x-on:dblclick="abrirResena($event)"
        style="position: relative;">

        {{ $this->table }}

        <p style="margin: 10px 0 0 0; font-size: 12px; color: #9ca3af; text-align: right;">💡 Doble click sobre una fila para ver el detalle completo</p>

        @if($resenaDetalle)
            {{-- Modal de detalle --}}
            <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 16px;" x-on:click.self="$wire.cerrarDetalle()" x-on:keydown.escape.window="$wire.cerrarDetalle()">
                <div style="background: white; border-radius: 16px; width: 100%; max-width: 560px; max-height: 85vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">

                    {{-- Header --}}
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 18px 20px; border-bottom: 1px solid #f3f4f6;">
                        <div>
                            <p style="margin: 0; font-size: 18px; font-weight: 700; color: #111827;">{{ str_repeat('⭐', $resenaDetalle['rating']) }} <span style="font-size: 13px; color: #6b7280; font-weight: 600;">({{ $resenaDetalle['rating'] }}/5)</span></p>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">
                                {{ $resenaDetalle['nombre'] }}@if($resenaDetalle['telefono']) · 📞 {{ $resenaDetalle['telefono'] }}@endif
                            </p>
                        </div>
                        <button wire:click="cerrarDetalle" title="Cerrar" style="border: none; background: #f3f4f6; color: #6b7280; width: 30px; height: 30px; border-radius: 8px; font-size: 15px; cursor: pointer; flex-shrink: 0;">✕</button>
                    </div>

                    {{-- Meta: pedido + estado + fecha + visibilidad --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; padding: 14px 20px 0;">
                        @if($resenaDetalle['pedido_numero'])
                            <span style="font-size: 12px; font-weight: 600; background: #111827; color: white; padding: 4px 10px; border-radius: 6px;">🧾 Pedido #{{ $resenaDetalle['pedido_numero'] }}</span>
                            <span style="font-size: 12px; font-weight: 600; background: #f3f4f6; color: #374151; padding: 4px 10px; border-radius: 6px;">${{ number_format((float) $resenaDetalle['pedido_total'], 0, ',', '.') }}</span>
                            @if($resenaDetalle['pedido_estado'])
                                @php
                                    $estados = ['pendiente_pago' => '⏳ Pend. Pago', 'en_proceso' => '👨‍🍳 Preparación', 'en_camino' => '🚗 En Ruta', 'entregado' => '📍 Entregado', 'finalizado' => '🎉 Finalizado', 'cancelado' => '❌ Cancelado'];
                                @endphp
                                <span style="font-size: 12px; font-weight: 600; background: #eff6ff; color: #2563eb; padding: 4px 10px; border-radius: 6px;">{{ $estados[$resenaDetalle['pedido_estado']] ?? $resenaDetalle['pedido_estado'] }}</span>
                            @endif
                        @endif
                        <span style="font-size: 12px; font-weight: 600; background: #f9fafb; color: #6b7280; padding: 4px 10px; border-radius: 6px; border: 1px solid #e5e7eb;">🕒 {{ $resenaDetalle['fecha'] }}</span>
                        <span style="font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; {{ $resenaDetalle['visible'] ? 'background: #dcfce7; color: #16a34a;' : 'background: #fee2e2; color: #dc2626;' }}">{{ $resenaDetalle['visible'] ? '👁 Visible' : '🙈 Oculta' }}</span>
                    </div>

                    {{-- Comentario completo --}}
                    <div style="padding: 14px 20px 18px;">
                        <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af;">Comentario</p>
                        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; font-size: 15px; line-height: 1.6; color: #111827; white-space: pre-wrap; word-break: break-word; min-height: 56px;">
                            @if($resenaDetalle['comentario'] !== '')
                                {{ $resenaDetalle['comentario'] }}
                            @else
                                <span style="color: #9ca3af; font-style: italic;">Sin comentario — solo dejó la puntuación</span>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="display: flex; gap: 8px; justify-content: flex-end; padding: 14px 20px; border-top: 1px solid #f3f4f6;">
                        <button wire:click="alternarVisibilidadDetalle" style="border: 1px solid #d1d5db; background: white; color: #374151; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            {{ $resenaDetalle['visible'] ? '🙈 Ocultar reseña' : '👁️ Mostrar reseña' }}
                        </button>
                        <button wire:click="cerrarDetalle" style="border: none; background: #111827; color: white; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">Cerrar</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
