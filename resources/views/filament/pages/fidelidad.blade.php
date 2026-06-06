<x-filament-panels::page>
    <div style="max-width: 768px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
        @if($mensaje)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.300ms
                 style="background: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; padding: 16px 20px; border-radius: 12px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <span>{{ $mensaje }}</span>
            </div>
        @endif

        <form wire:submit.prevent="save" style="display: flex; flex-direction: column; gap: 20px;">
            {{-- Puntos de Lealtad --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #fefce8, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">⭐ Acumulación de Puntos</h2>
                </div>
                <div style="padding: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <span style="font-size: 14px; color: #374151; font-weight: 500;">Por cada</span>
                        <div style="width: 120px;">
                            <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                                <span style="background: #f3f4f6; padding: 10px 12px; font-size: 14px; color: #6b7280; border-right: 1px solid #d1d5db;">$</span>
                                <input type="number" wire:model="puntos_ganancia_monto" min="1" required style="width: 100%; border: none; padding: 10px 12px; font-size: 14px; outline: none;">
                            </div>
                        </div>
                        <span style="font-size: 14px; color: #374151; font-weight: 500;">que gaste el cliente acumula:</span>
                        <div style="width: 80px;">
                            <input type="number" wire:model="puntos_ganancia_valor" min="1" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; outline: none;">
                        </div>
                        <span style="font-size: 14px; color: #374151; font-weight: 500;">punto(s)</span>
                    </div>
                    @error('puntos_ganancia_monto') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                    @error('puntos_ganancia_valor') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Recompensas --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #f0fdf4, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">🏆 Niveles de Recompensa</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    @if(count($recompensas) > 0)
                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Puntos</th>
                                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Tipo</th>
                                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Valor</th>
                                        <th style="padding: 10px 16px; text-align: right; font-weight: 600; color: #374151;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recompensas as $i => $r)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 10px 16px; font-weight: 600;">{{ number_format($r['puntos']) }}</td>
                                            <td style="padding: 10px 16px;">
                                                @if($r['tipo'] === 'porcentaje')
                                                    <span style="background: #dbeafe; color: #1e40af; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Porcentaje</span>
                                                @else
                                                    <span style="background: #fef3c7; color: #92400e; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Fijo</span>
                                                @endif
                                            </td>
                                            <td style="padding: 10px 16px;">
                                                @if($r['tipo'] === 'porcentaje')
                                                    {{ $r['valor'] }}%
                                                @else
                                                    ${{ number_format($r['valor'], 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td style="padding: 10px 16px; text-align: right;">
                                                <button type="button" wire:click="editarRecompensa({{ $i }})" style="background: none; border: none; color: #3b82f6; cursor: pointer; font-size: 13px; margin-right: 12px;">✏️</button>
                                                <button type="button" wire:click="eliminarRecompensa({{ $i }})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 13px;">🗑️</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: #9ca3af; text-align: center; font-size: 14px; margin: 0;">No hay recompensas configuradas. Agrega la primera.</p>
                    @endif

                    {{-- Add/Edit form --}}
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                        <h3 style="margin: 0 0 12px; font-size: 14px; font-weight: 700; color: #374151;">
                            {{ $editIndex !== null ? '✏️ Editar Recompensa' : '➕ Agregar Recompensa' }}
                        </h3>
                        <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 120px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">Puntos requeridos</label>
                                <input type="number" wire:model="form_puntos" min="1" placeholder="Ej: 3000" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                                @error('form_puntos') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                            </div>
                            <div style="min-width: 140px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">Tipo</label>
                                <select wire:model="form_tipo" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; background: white;">
                                    <option value="porcentaje">Porcentaje (%)</option>
                                    <option value="fijo">Fijo ($)</option>
                                </select>
                            </div>
                            <div style="flex: 1; min-width: 120px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">Valor</label>
                                <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                                    <span style="background: #f3f4f6; padding: 10px 10px; font-size: 14px; color: #6b7280; border-right: 1px solid #d1d5db;">{{ $form_tipo === 'porcentaje' ? '%' : '$' }}</span>
                                    <input type="number" wire:model="form_valor" min="0" step="0.01" placeholder="Ej: 15" style="width: 100%; border: none; padding: 10px 12px; font-size: 14px; outline: none;">
                                </div>
                                @error('form_valor') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" wire:click="agregarRecompensa" style="background: #22c55e; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer;">
                                    {{ $editIndex !== null ? 'Guardar' : 'Agregar' }}
                                </button>
                                @if($editIndex !== null)
                                    <button type="button" wire:click="resetForm" style="background: #6b7280; color: white; border: none; border-radius: 8px; padding: 10px 16px; font-size: 14px; cursor: pointer;">Cancelar</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Save --}}
            <button type="submit" style="width: 100%; background: linear-gradient(to right, #dc2626, #ef4444); color: white; padding: 14px 24px; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer;">
                💾 Guardar Cambios
            </button>
        </form>
    </div>
</x-filament-panels::page>
