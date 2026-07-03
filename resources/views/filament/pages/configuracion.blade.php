<x-filament-panels::page>
    <div style="max-width: 768px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
        @if($mensaje)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.300ms
                 style="background: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; padding: 16px 20px; border-radius: 12px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <span>{{ $mensaje }}</span>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 16px 20px; border-radius: 12px; font-size: 14px;">
                ❌ Corrige los errores antes de guardar
            </div>
        @endif

        <form wire:submit.prevent="save" style="display: flex; flex-direction: column; gap: 20px;">
            {{-- Negocio --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #f9fafb, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">🏪 Información del Negocio</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nombre del Negocio</label>
                        <input type="text" wire:model="nombre_negocio" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        @error('nombre_negocio') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Teléfono</label>
                            <input type="tel" wire:model="telefono" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Dirección</label>
                            <input type="text" wire:model="direccion" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Logo</label>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <input type="file" wire:model="logo" accept="image/*" style="font-size: 14px;">
                            @if($logoPreview)
                                <img src="{{ $logoPreview }}" alt="Logo" style="height: 56px; width: 56px; border-radius: 12px; object-fit: cover; border: 2px solid #e5e7eb;">
                            @endif
                        </div>
                        @error('logo') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Horario --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #fffbeb, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">🕐 Horario de Atención</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Días Laborales</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                <label style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; border: 1px solid #e5e7eb; cursor: pointer; font-size: 14px;
                                    {{ in_array($dia, $dias_laborales ?? []) ? 'background: #fef2f2; border-color: #fecaca;' : 'background: #f9fafb;' }}">
                                    <input type="checkbox" wire:model="dias_laborales" value="{{ $dia }}">
                                    {{ $dia }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Horario General — Apertura</label>
                            <input type="time" wire:model="horario_apertura" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                            @error('horario_apertura') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Horario General — Cierre</label>
                            <input type="time" wire:model="horario_cierre" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                            @error('horario_cierre') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Horarios por Día <span style="font-weight: 400; color: #9ca3af;">(opcional — deja en blanco para usar el horario general)</span></label>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 12px; align-items: center; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; {{ in_array($dia, $dias_laborales ?? []) ? '' : 'opacity: 0.4;' }}">
                                    <span style="font-size: 14px; font-weight: 600; color: #374151;">{{ $dia }}</span>
                                    <div>
                                        <label style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 2px;">Apertura</label>
                                        <input type="time" wire:model="horarios_por_dia.{{ $dia }}.apertura" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 2px;">Cierre</label>
                                        <input type="time" wire:model="horarios_por_dia.{{ $dia }}.cierre" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Link a Fidelidad --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="padding: 24px; text-align: center;">
                    <p style="margin: 0 0 12px; font-size: 14px; color: #6b7280;">La configuración del programa de lealtad se maneja desde:</p>
                    <a href="{{ url('/admin/fidelidad') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #fefce8; color: #a16207; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: 1px solid #fde68a;">
                        ⭐ Ir a Programa de Fidelidad
                    </a>
                </div>
            </div>

            {{-- Pagos --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #f0fdf4, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">💳 Datos de Pago (WhatsApp)</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Llave</label>
                            <input type="text" wire:model="llave" placeholder="Ej: 3017226095" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Daviplata</label>
                            <input type="text" wire:model="daviplata" placeholder="Ej: 3007890081" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nequi</label>
                            <input type="text" wire:model="nequi" placeholder="Ej: 3007890081" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        </div>
                    </div>
                    <p style="margin: 0; font-size: 12px; color: #9ca3af;">Estos datos se usarán en el mensaje de confirmación por WhatsApp</p>
                </div>
            </div>

            {{-- Métodos de Pago --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #f0f9ff, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">💳 Métodos de Pago</h2>
                </div>
                <div style="padding: 24px;">
                    <p style="margin: 0 0 12px; font-size: 13px; color: #6b7280;">Activa o desactiva los métodos de pago disponibles para los clientes:</p>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach(['efectivo' => ['label' => 'Efectivo', 'icon' => '💵'], 'tarjeta' => ['label' => 'Tarjeta', 'icon' => '💳'], 'transferencia' => ['label' => 'Transferencia', 'icon' => '🏦']] as $valor => $info)
                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 10px; cursor: pointer; {{ in_array($valor, $metodos_pago_activos ?? []) ? 'background: #f0fdf4; border-color: #bbf7d0;' : 'background: #f9fafb; opacity: 0.6;' }}">
                                <span style="font-size: 14px; font-weight: 600; color: #374151;">
                                    <span style="margin-right: 8px;">{{ $info['icon'] }}</span>
                                    {{ $info['label'] }}
                                </span>
                                <button type="button" wire:click="toggleMetodoPago('{{ $valor }}')" style="position: relative; width: 44px; height: 24px; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s; {{ in_array($valor, $metodos_pago_activos ?? []) ? 'background: #22c55e;' : 'background: #d1d5db;' }}">
                                    <span style="position: absolute; top: 2px; width: 20px; height: 20px; border-radius: 50%; background: white; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); {{ in_array($valor, $metodos_pago_activos ?? []) ? 'right: 2px;' : 'left: 2px;' }}"></span>
                                </button>
                            </label>
                        @endforeach
                    </div>
                    @if(count($metodos_pago_activos ?? []) === 0)
                        <p style="color: #ef4444; font-size: 12px; margin-top: 8px;">Debes tener al menos un método de pago activo</p>
                    @endif
                </div>
            </div>

            {{-- Impresión --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #f9fafb, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">🖨️ Configuración de Impresión</h2>
                </div>
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" wire:model="imprimir_automaticamente">
                        <span style="font-size: 14px;">Imprimir comandas automáticamente</span>
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Tamaño del ticket</label>
                            <select wire:model="ticket_size" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px; background: white;">
                                <option value="80">80 mm</option>
                                <option value="57">57 mm (ticket térmico pequeño)</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nombre de la Impresora</label>
                            <input type="text" wire:model="impresora_nombre" placeholder="Ej: EPSON TM-T20" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                        </div>
                    </div>
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                        <h3 style="margin: 0 0 12px; font-size: 14px; font-weight: 700; color: #374151;">Diseño del Ticket</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-top: 12px;">
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Tamaño de letra</label>
                                <select wire:model="ticket_escala" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px; background: white;">
                                    <option value="100">100% — Normal</option>
                                    <option value="125">125% — Grande</option>
                                    <option value="150">150% — Muy grande</option>
                                    <option value="175">175% — Extra grande</option>
                                    <option value="200">200% — Doble</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Fuente</label>
                                <select wire:model="ticket_fuente" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px; background: white;">
                                    <option value="courier">Courier New (mono)</option>
                                    <option value="arial">Arial (sans-serif)</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Interlineado</label>
                                <select wire:model="ticket_interlineado" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px; background: white;">
                                    <option value="compacto">Compacto</option>
                                    <option value="normal">Normal</option>
                                    <option value="espaciado">Espaciado</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Espaciado productos</label>
                                <select wire:model="ticket_espaciado" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px; background: white;">
                                    <option value="compacto">Compacto</option>
                                    <option value="normal">Normal</option>
                                    <option value="espaciado">Espaciado</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-top: 12px;">
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Márgenes (mm)</label>
                                <input type="number" wire:model="ticket_margen" min="0" max="5" step="0.1" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; font-size: 14px;">
                            </div>
                            <div style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" wire:model="ticket_mostrar_logo">
                                    <span style="font-size: 14px;">Mostrar logo</span>
                                </label>
                            </div>
                            <div style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" wire:model="ticket_negritas">
                                    <span style="font-size: 14px;">Negritas</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pausar Pedidos --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: linear-gradient(to right, #fef2f2, white); padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">⏸️ Pausar Pedidos</h2>
                </div>
                <div style="padding: 24px;">
                    <p style="margin: 0 0 12px; font-size: 13px; color: #6b7280;">Activa esta opción para pausar la recepción de pedidos web (por alta demanda, cierre temporal, etc.). Los clientes verán un mensaje en el checkout y no podrán confirmar pedidos.</p>
                    <label style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 10px; cursor: pointer; {{ $pedidos_pausados ? 'background: #fef2f2; border-color: #fecaca;' : 'background: #f9fafb;' }}">
                        <span style="font-size: 14px; font-weight: 600; color: #374151;">
                            <span style="margin-right: 8px;">{{ $pedidos_pausados ? '🟡' : '🟢' }}</span>
                            Pedidos {{ $pedidos_pausados ? 'Pausados' : 'Activos' }}
                        </span>
                        <button type="button" wire:click="$set('pedidos_pausados', {{ $pedidos_pausados ? 'false' : 'true' }})" style="position: relative; width: 44px; height: 24px; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s; {{ $pedidos_pausados ? 'background: #f59e0b;' : 'background: #22c55e;' }}">
                            <span style="position: absolute; top: 2px; width: 20px; height: 20px; border-radius: 50%; background: white; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); {{ $pedidos_pausados ? 'right: 2px;' : 'left: 2px;' }}"></span>
                        </button>
                    </label>
                </div>
            </div>

            {{-- Save --}}
            <button type="submit" style="width: 100%; background: linear-gradient(to right, #dc2626, #ef4444); color: white; padding: 14px 24px; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer;">
                💾 Guardar Cambios
            </button>
        </form>
    </div>
</x-filament-panels::page>
