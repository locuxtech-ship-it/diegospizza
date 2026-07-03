<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @if($pedidoCreado)
            <div style="text-align: center; padding: 48px 24px; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px;">
                <p style="font-size: 48px; margin: 0 0 8px 0;">✅</p>
                <h2 style="font-size: 24px; font-weight: 700; color: #166534; margin: 0 0 8px 0;">Pedido #{{ $pedidoId }} creado</h2>
                <p style="color: #16a34a; margin: 0 0 20px 0;">El pedido ya está en Recepción</p>
                <div style="display: flex; gap: 8px; justify-content: center;">
                    <x-filament::button wire:click="reiniciar" color="gray">Nuevo Pedido</x-filament::button>
                    <x-filament::button onclick="window.open('{{ route('admin.ticket', $pedidoId) }}', '_blank', 'width=400,height=600')" color="warning">
                        🖨️ Imprimir Ticket
                    </x-filament::button>
                </div>
            </div>
        @else
        <div style="display: grid; grid-template-columns: 1fr 360px; gap: 20px; align-items: start;">
            {{-- Products --}}
            <div>
                {{-- Categories --}}
                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px;">
                    <button wire:click="$set('categoriaActiva', null)" style="padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; {{ is_null($categoriaActiva) ? 'background: #111827; color: white;' : 'background: #f3f4f6; color: #374151;' }}">
                        Todas
                    </button>
                    @foreach($categorias as $cat)
                    <button wire:click="$set('categoriaActiva', {{ $cat->id }})" style="padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; {{ $categoriaActiva == $cat->id ? 'background: #111827; color: white;' : 'background: #f3f4f6; color: #374151;' }}">
                        {{ $cat->nombre }}
                    </button>
                    @endforeach
                </div>

                {{-- Product grid --}}
                @foreach($categoriasFiltradas as $categoria)
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 12px 0;">{{ $categoria->nombre }}</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                        @foreach($categoria->productosDisponibles as $producto)
                        @php
                            $precioBase = (float) $producto->precio;
                            $hasVariants = $producto->variants && $producto->variants->isNotEmpty();
                        @endphp
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px;">
                            <div style="display: flex; align-items: start; gap: 10px;">
                                @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                                @endif
                                <div style="flex: 1; min-width: 0;">
                                    <p style="margin: 0; font-weight: 600; font-size: 13px; color: #111827;">{{ $producto->nombre }}</p>
                                    <p style="margin: 2px 0 0 0; font-size: 11px; color: #9ca3af; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $producto->descripcion ?? '' }}</p>
                                </div>
                            </div>

                            @if($producto->es_personalizable)
                            <div style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-weight: 700; font-size: 13px; color: #ea580c;">Mitad y Mitad</span>
                                <button wire:click="agregarAlCarrito({{ $producto->id }})" style="padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; background: #111827; color: white; cursor: pointer;">
                                    + Agregar
                                </button>
                            </div>
                            @elseif($hasVariants)
                            <div style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($producto->variants as $variant)
                                <button wire:click="agregarAlCarrito({{ $producto->id }}, {{ $variant->id }})" style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 500; border: 1px solid #e5e7eb; background: white; color: #374151; cursor: pointer;">
                                    {{ $variant->tamanio }} <strong>${{ number_format($variant->precio, 0, ',', '.') }}</strong>
                                </button>
                                @endforeach
                            </div>
                            @else
                            <div style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-weight: 700; font-size: 15px; color: #111827;">${{ number_format($precioBase, 0, ',', '.') }}</span>
                                <button wire:click="agregarAlCarrito({{ $producto->id }})" style="padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; background: #111827; color: white; cursor: pointer;">
                                    + Agregar
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Cart + Checkout --}}
            <div style="position: sticky; top: 16px;">
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h3 style="margin: 0; font-size: 16px; font-weight: 700;">🛒 Carrito</h3>
                        @if(count($carrito) > 0)
                        <button wire:click="limpiarCarrito" style="background: none; border: none; color: #ef4444; font-size: 12px; cursor: pointer;">Vaciar</button>
                        @endif
                    </div>

                    @forelse($carrito as $key => $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px;">
                        <div style="flex: 1; min-width: 0;">
                            <p style="margin: 0; font-weight: 500;">{{ $item['nombre'] }}@if($item['variant_tamanio']) <span style="color: #ea580c;">({{ $item['variant_tamanio'] }})</span>@endif</p>
                            @if($item['mitades'] ?? null)
                            <p style="margin: 0; font-size: 10px; color: #ea580c;">{{ collect($item['mitades'])->pluck('nombre')->implode(' / ') }}</p>
                            @endif
                            <p style="margin: 0; color: #6b7280;">${{ number_format($item['precio'], 0, ',', '.') }} c/u</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <button wire:click="cambiarCantidad('{{ $key }}', -1)" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #d1d5db; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;">−</button>
                            <span style="font-weight: 600; min-width: 20px; text-align: center;">{{ $item['cantidad'] }}</span>
                            <button wire:click="cambiarCantidad('{{ $key }}', 1)" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #d1d5db; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;">+</button>
                            <button wire:click="quitarDelCarrito('{{ $key }}')" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px;">×</button>
                        </div>
                    </div>
                    @empty
                    <p style="text-align: center; color: #9ca3af; padding: 24px 0; margin: 0; font-size: 13px;">
                        Agrega productos desde la lista
                    </p>
                    @endforelse

                    @if(count($carrito) > 0)
                    <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 16px; padding: 12px 0 8px 0; border-top: 2px solid #111827; margin-top: 8px;">
                        <span>Total</span>
                        <span>${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Customer form --}}
                @if(count($carrito) > 0)
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-top: 12px;">
                    <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700;">👤 Datos del cliente</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: #374151;">Nombre</label>
                            <input type="text" wire:model.live="nombre" placeholder="Nombre del cliente" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                            @error('nombre') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: #374151;">Teléfono</label>
                            <input type="text" wire:model.live="telefono" placeholder="3001234567" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                            @error('telefono') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                            @if($clienteInfo)
                            <div style="margin-top: 6px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px 10px; font-size: 11px;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="padding: 2px 8px; border-radius: 6px; font-weight: 600; font-size: 10px; background: {{ $clienteInfo['clasificacion'] === 'elite' ? '#fef3c7; color: #92400e' : ($clienteInfo['clasificacion'] === 'frecuente' ? '#ffedd5; color: #9a3412' : '#f3f4f6; color: #6b7280') }};">
                                        {{ $clienteInfo['clasificacion_label'] }}
                                    </span>
                                    <span style="color: #6b7280;">📦 {{ $clienteInfo['total_pedidos'] }}</span>
                                    <span style="color: #d97706;">⭐ {{ $clienteInfo['puntos'] }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        @if(count($direccionesDisponibles) > 0)
                        <div style="margin-bottom: 8px;">
                            <label style="font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">📍 Dirección guardada</label>
                            @foreach($direccionesDisponibles as $dir)
                            <label style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 8px; cursor: pointer; font-size: 13px; margin-bottom: 4px; border: 1px solid {{ $direccionSeleccionadaId === $dir['id'] ? '#ef4444' : '#e5e7eb' }}; background: {{ $direccionSeleccionadaId === $dir['id'] ? '#fef2f2' : '#f9fafb' }};">
                                <input type="radio" wire:click="seleccionarDireccion({{ $dir['id'] }})" {{ $direccionSeleccionadaId === $dir['id'] ? 'checked' : '' }} style="accent-color: #dc2626;">
                                <span style="font-weight: 600;">{{ $dir['alias'] ?? 'Dirección' }}:</span>
                                <span style="color: #6b7280;">{{ $dir['conjunto'] }}{{ $dir['torre'] ? ', Torre ' . $dir['torre'] : '' }}{{ $dir['apto'] ? ', Apto ' . $dir['apto'] : '' }}</span>
                            </label>
                            @endforeach
                            <label style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 8px; cursor: pointer; font-size: 13px; border: 1px solid {{ $direccionSeleccionadaId === null ? '#ef4444' : '#e5e7eb' }}; background: {{ $direccionSeleccionadaId === null ? '#fef2f2' : '#f9fafb' }};">
                                <input type="radio" wire:click="seleccionarDireccion(null)" {{ $direccionSeleccionadaId === null ? 'checked' : '' }} style="accent-color: #dc2626;">
                                <span>✏️ Otra dirección</span>
                            </label>
                        </div>
                        @endif
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #374151;">Conjunto *</label>
                                <input type="text" wire:model.live="conjunto" placeholder="Nombre del conjunto" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                                @error('conjunto') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #374151;">Torre</label>
                                <input type="text" wire:model.live="torre" placeholder="N°" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #374151;">Apto</label>
                                <input type="text" wire:model.live="apto" placeholder="N°" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                            </div>
                        </div>
                        @if(count($direccionesDisponibles) > 0)
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; color: #6b7280;">
                            <input type="checkbox" wire:model.live="guardarDireccion" style="accent-color: #dc2626;">
                            Guardar dirección para próximos pedidos
                        </label>
                        @endif
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: #374151;">Notas <span style="color: #9ca3af; font-weight: 400;">(opcional)</span></label>
                            <textarea wire:model.live="notas" placeholder="Instrucciones especiales" rows="2" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px; resize: none;"></textarea>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: #374151;">Método de pago</label>
                            <select wire:model.live="metodoPago" style="margin-top: 3px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                                @foreach(\App\Models\NegocioSetting::getActivePaymentMethods() as $valor => $info)
                                    <option value="{{ $valor }}">{{ $info['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-filament::button wire:click="procesarPedido" color="success" style="width: 100%; justify-content: center;">
                            ✅ Crear Pedido (${{ number_format($total, 0, ',', '.') }})
                        </x-filament::button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Mitad y Mitad Modal --}}
        @if($productoMitad)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 999;">
            <div style="background: white; border-radius: 16px; padding: 24px; width: 380px; max-width: 90vw;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700;">{{ $productoMitad->nombre }}</h3>
                    <button wire:click="cerrarMitadYMitad" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">×</button>
                </div>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px 0;">Selecciona 2 sabores</p>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #374151;">Primer sabor</label>
                        <select wire:model.live="mitad1" style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($saboresPizza as $sabor)
                            <option value="{{ $sabor->id }}">{{ $sabor->nombre }} - ${{ number_format($sabor->precio, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #374151;">Segundo sabor</label>
                        <select wire:model.live="mitad2" style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($saboresPizza as $sabor)
                            <option value="{{ $sabor->id }}">{{ $sabor->nombre }} - ${{ number_format($sabor->precio, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($mitad1 && $mitad2)
                        @php
                            $p1 = \App\Models\Producto::find($mitad1);
                            $p2 = \App\Models\Producto::find($mitad2);
                            $v1 = $p1 ? \App\Models\ProductoVariant::where('producto_id', $p1->id)->get() : collect();
                            $v2 = $p2 ? \App\Models\ProductoVariant::where('producto_id', $p2->id)->get() : collect();
                            $m1 = $v1->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
                            $m2 = $v2->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
                            $pr1 = (float) ($m1?->precio ?? $v1->first()?->precio ?? $p1?->precio ?? 0);
                            $pr2 = (float) ($m2?->precio ?? $v2->first()?->precio ?? $p2?->precio ?? 0);
                            $precioFinal = max($pr1, $pr2);
                        @endphp
                        <div style="text-align: center; padding: 8px 0;">
                            <span style="font-size: 14px; color: #6b7280;">Precio: </span>
                            <span style="font-size: 18px; font-weight: 700; color: #ea580c;">${{ number_format($precioFinal, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <button wire:click="agregarMitadYMitad" style="padding: 10px; border-radius: 8px; font-size: 14px; font-weight: 600; border: none; background: #111827; color: white; cursor: pointer; {{ !$mitad1 || !$mitad2 ? 'opacity: 0.5;' : '' }}" @if(!$mitad1 || !$mitad2) disabled @endif>
                        + Agregar al carrito
                    </button>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</x-filament-panels::page>
