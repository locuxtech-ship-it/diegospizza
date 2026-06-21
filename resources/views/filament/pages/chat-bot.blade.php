@php use function \Filament\Support\get_color_css_variables; @endphp
<x-filament-panels::page>
    <div style="max-width: 768px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
        @if($mensaje)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.duration.300ms
                 style="background: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; padding: 16px 20px; border-radius: 12px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <span>{{ $mensaje }}</span>
            </div>
        @endif

        {{-- Connection Status --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="background: linear-gradient(to right, #f0f9ff, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">📡 Estado de Conexión</h2>
                <button type="button" wire:click="checkStatus" style="background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 14px; font-size: 13px; cursor: pointer; color: #374151;">
                    ⟳ Refrescar
                </button>
            </div>
            <div @if($status === 'WAITING_QR') wire:poll.3s="pollQR" @endif style="padding: 24px; display: flex; flex-direction: column; align-items: center; gap: 16px;">
                @if($status === 'CONNECTED')
                    <div style="display: flex; align-items: center; gap: 10px; padding: 12px 24px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 9999px;">
                        <span style="width: 12px; height: 12px; background: #22c55e; border-radius: 50%; display: inline-block;"></span>
                        <span style="font-weight: 700; color: #166534; font-size: 15px;">CONECTADO</span>
                        <span style="color: #6b7280; font-size: 14px;">— {{ $pushName }}</span>
                    </div>
                    <button type="button" wire:click="logout" style="background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; border-radius: 8px; padding: 8px 18px; font-size: 13px; font-weight: 600; cursor: pointer;">
                        ❌ Cerrar Sesión
                    </button>
                @elseif($status === 'SCAN_QR_CODE' && $qrCode)
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                        <span style="font-size: 14px; color: #6b7280;">Escaneá este código QR con tu WhatsApp:</span>
                        <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px;">
                            <img src="{{ $qrCode }}" alt="QR Code" style="width: 220px; height: 220px; display: block;">
                        </div>
                        <span style="font-size: 12px; color: #9ca3af;">Abrí WhatsApp → Menú → Vincular dispositivo</span>
                        <button type="button" wire:click="checkStatus" style="background: #3b82f6; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer;">
                            ✅ Ya escaneé
                        </button>
                    </div>
                @elseif($status === 'WAITING_QR')
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                        <span style="font-size: 14px; color: #6b7280;">Generando código QR, esperá unos segundos...</span>
                        <div style="width: 48px; height: 48px; border: 4px solid #e5e7eb; border-top-color: #22c55e; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <button type="button" wire:click="checkStatus" style="background: #3b82f6; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer;">
                            ⟳ Verificar QR
                        </button>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px; padding: 12px 24px; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 9999px;">
                            <span style="width: 12px; height: 12px; background: #ef4444; border-radius: 50%; display: inline-block;"></span>
                            <span style="font-weight: 700; color: #991b1b; font-size: 15px;">DESCONECTADO</span>
                        </div>
                        <button type="button" wire:click="showQR" style="background: #22c55e; color: white; border: none; border-radius: 8px; padding: 12px 24px; font-size: 15px; font-weight: 700; cursor: pointer;">
                            📱 Mostrar QR para conectar
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="save" style="display: flex; flex-direction: column; gap: 20px;">
            {{-- Auto-response settings --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #fefce8, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">🤖 Auto-Respuesta</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: #374151; cursor: pointer;">
                        <input type="checkbox" wire:model="enabled" style="width: 18px; height: 18px; accent-color: #22c55e;">
                        Activar bot de auto-respuesta
                    </label>

                    <div style="display: flex; flex-direction: column; gap: 16px; {{ $enabled ? '' : 'opacity: 50%; pointer-events: none;' }}">
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: #374151; cursor: pointer;">
                            <input type="checkbox" wire:model="welcome_enabled" style="width: 18px; height: 18px; accent-color: #22c55e;">
                            Enviar mensaje de bienvenida
                        </label>

                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <label style="font-size: 13px; font-weight: 600; color: #6b7280;">Mensaje de bienvenida</label>
                            <span style="font-size: 11px; color: #9ca3af;">Usá {nombre} y {empresa} como placeholders</span>
                            <textarea wire:model="welcome_message" rows="4" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; font-size: 14px; resize: vertical;">{{ $welcome_message }}</textarea>
                            @error('welcome_message') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-size: 13px; font-weight: 600; color: #6b7280;">Opciones del menú</label>
                            @foreach($menu_options as $i => $opt)
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="text" wire:model="menu_options.{{ $i }}.key" placeholder="Letra" maxlength="2" style="width: 60px; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 14px; text-align: center; text-transform: uppercase;">
                                    <input type="text" wire:model="menu_options.{{ $i }}.label" placeholder="Ej: Realizar un pedido 🍽️" style="flex: 1; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;">
                                    <button type="button" wire:click="removeMenuOption({{ $i }})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px;">×</button>
                                </div>
                                @error("menu_options.{$i}.key") <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                                @error("menu_options.{$i}.label") <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                            @endforeach
                            <button type="button" wire:click="addMenuOption" style="background: none; border: 1px dashed #d1d5db; border-radius: 8px; padding: 8px; font-size: 13px; color: #6b7280; cursor: pointer; margin-top: 4px;">
                                + Agregar opción
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order notifications --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #f0fdf4, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">📬 Notificaciones de Pedidos</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: #374151; cursor: pointer;">
                        <input type="checkbox" wire:model="order_notifications" style="width: 18px; height: 18px; accent-color: #22c55e;">
                        Enviar notificaciones automáticas al cambiar estado del pedido
                    </label>

                    <div style="{{ $order_notifications ? '' : 'opacity: 50%; pointer-events: none;' }}">
                        @php
                            $estados = [
                                'pendiente_pago' => '📦 Pedido Recibido',
                                'en_proceso' => '👨‍🍳 En Preparación',
                                'en_camino' => '🛵 En Camino',
                                'entregado' => '✅ Entregado',
                                'finalizado' => '✅ Finalizado',
                                'cancelado' => '❌ Cancelado',
                            ];
                        @endphp
                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151; width: 130px;">Estado</th>
                                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Mensaje <span style="font-weight: 400; color: #9ca3af;">({numero}, {nombre})</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estados as $key => $label)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 6px 12px; font-weight: 500; color: #374151;">{{ $label }}</td>
                                            <td style="padding: 6px 12px;">
                                                <input type="text" wire:model="notifications.{{ $key }}" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 10px; font-size: 13px;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Review request --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #fef2f2, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">⭐ Solicitar Reseña</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: #374151; cursor: pointer;">
                        <input type="checkbox" wire:model="review_enabled" style="width: 18px; height: 18px; accent-color: #f59e0b;">
                        Enviar enlace de reseña al finalizar el pedido
                    </label>

                    <div style="{{ $review_enabled ? '' : 'opacity: 50%; pointer-events: none;' }}">
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <label style="font-size: 13px; font-weight: 600; color: #6b7280;">Mensaje con enlace de reseña</label>
                            <span style="font-size: 11px; color: #9ca3af;">Usá {nombre}, {numero} y {link} como placeholders</span>
                            <textarea wire:model="review_message" rows="3" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; font-size: 14px; resize: vertical;">{{ $review_message }}</textarea>
                            @error('review_message') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(to right, #dc2626, #ef4444); color: white; padding: 14px 24px; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer;">
                💾 Guardar Cambios
            </button>
        </form>
    </div>
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
</x-filament-panels::page>
