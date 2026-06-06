<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        {{-- Filters --}}
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

        {{-- Period label --}}
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 13px; color: #6b7280;">Período:</span>
            <span style="font-size: 14px; font-weight: 600; color: #374151; background: #f3f4f6; padding: 4px 12px; border-radius: 6px;">{{ $this->etiquetaPeriodo() }}</span>
        </div>

        {{-- Stats --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
            <div style="background: linear-gradient(to bottom right, #fef2f2, #fee2e2); border: 1px solid #fecaca; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #ef4444; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Venta Total</p>
                <p style="margin: 4px 0 0 0; color: #991b1b; font-size: 24px; font-weight: 700;">${{ number_format($totalVentas, 0, ',', '.') }}</p>
                <p style="margin: 2px 0 0 0; color: #ef4444; font-size: 12px;">{{ $this->etiquetaPeriodo() }}</p>
            </div>
            <div style="background: linear-gradient(to bottom right, #f9fafb, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Pedidos</p>
                <p style="margin: 4px 0 0 0; color: #374151; font-size: 24px; font-weight: 700;">{{ $totalPedidos }}</p>
                <p style="margin: 2px 0 0 0; color: #9ca3af; font-size: 12px;">pedidos realizados</p>
            </div>
            <div style="background: linear-gradient(to bottom right, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px;">
                <p style="margin: 0; color: #3b82f6; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Precio Promedio</p>
                <p style="margin: 4px 0 0 0; color: #1e3a5f; font-size: 24px; font-weight: 700;">${{ number_format($promedioPedido, 0, ',', '.') }}</p>
                <p style="margin: 2px 0 0 0; color: #3b82f6; font-size: 12px;">por pedido</p>
            </div>
        </div>

        {{-- Detailed tables --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            {{-- Dia que mas vendes --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">📅</span>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Día que más vendes</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Día</th>
                                <th style="text-align: center; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Pedidos</th>
                                <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diaMasVendido as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 10px 16px; font-weight: 500; color: #374151;">{{ $item['dia'] }}</td>
                                    <td style="padding: 10px 16px; text-align: center; color: #6b7280;">{{ $item['pedidos'] }}</td>
                                    <td style="padding: 10px 16px; text-align: right; font-weight: 600; color: #059669;">${{ number_format($item['ventas'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 32px 16px; text-align: center; color: #9ca3af;">Sin datos en este período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Horas mas vendidas --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">⏰</span>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Horas más vendidas</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Horario</th>
                                <th style="text-align: center; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Pedidos</th>
                                <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($horasMasVendidas as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 10px 16px; font-weight: 500; color: #374151;">{{ $item['hora'] }}</td>
                                    <td style="padding: 10px 16px; text-align: center; color: #6b7280;">{{ $item['pedidos'] }}</td>
                                    <td style="padding: 10px 16px; text-align: right; font-weight: 600; color: #059669;">${{ number_format($item['ventas'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 32px 16px; text-align: center; color: #9ca3af;">Sin datos en este período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sabores y Tamaños --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
            {{-- Sabores mas vendidos --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">🍕</span>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Sabores más vendidos</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Sabor</th>
                                <th style="text-align: center; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Cantidad</th>
                                <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($saboresMasVendidos as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 10px 16px; font-weight: 500; color: #374151;">{{ $item['nombre'] }}</td>
                                    <td style="padding: 10px 16px; text-align: center; color: #6b7280;">{{ $item['cantidad'] }}</td>
                                    <td style="padding: 10px 16px; text-align: right; font-weight: 600; color: #059669;">${{ number_format($item['ventas'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 32px 16px; text-align: center; color: #9ca3af;">Sin datos en este período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tamaños mas vendidos --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">📏</span>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Tamaños más vendidos</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Tamaño</th>
                                <th style="text-align: center; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Cantidad</th>
                                <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tamaniosMasVendidos as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 10px 16px; font-weight: 500; color: #374151;">{{ $item['tamanio'] }}</td>
                                    <td style="padding: 10px 16px; text-align: center; color: #6b7280;">{{ $item['cantidad'] }}</td>
                                    <td style="padding: 10px 16px; text-align: right; font-weight: 600; color: #059669;">${{ number_format($item['ventas'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 32px 16px; text-align: center; color: #9ca3af;">Sin datos en este período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mitades mas populares --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">✂️</span>
                    <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Mitades más pedidas</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Sabor</th>
                                <th style="text-align: center; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Veces</th>
                                <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mitadesMasVendidas as $sabor => $cantidad)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 10px 16px; font-weight: 500; color: #374151;">{{ $sabor }}</td>
                                    <td style="padding: 10px 16px; text-align: center; color: #6b7280;">{{ $cantidad }}</td>
                                    <td style="padding: 10px 16px; text-align: right; color: #9ca3af; font-size: 12px;">mitad</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 32px 16px; text-align: center; color: #9ca3af;">Sin datos en este período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
