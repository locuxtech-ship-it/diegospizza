<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #111827;">Cierre del Día</h2>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">{{ now()->format('d/m/Y') }} — {{ $cierre ? ucfirst($cierre->estado) : 'Nuevo' }}</p>
            </div>
            @if($cierre)
                <div style="display: flex; gap: 8px;">
                    @if($cierre->estado === 'cuadrado')
                        <x-filament::button wire:click="reabrirCierre" color="warning">
                            🔓 Reabrir cierre
                        </x-filament::button>
                    @endif
                    <x-filament::button wire:click="guardarCierre" color="primary">
                        💾 Guardar cambios
                    </x-filament::button>
                    <x-filament::button onclick="window.open('{{ route('ticket.cierre', ['cierre' => $cierre->id]) }}', '_blank', 'width=400,height=600')" color="gray">
                        🖨️ Imprimir
                    </x-filament::button>
                </div>
            @endif
        </div>

        {{-- Totales de Ventas --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #111827;">💰 Ventas del Día</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px; text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #16a34a; font-weight: 600;">EFECTIVO</p>
                    <p style="margin: 4px 0 0; font-size: 22px; font-weight: 800; color: #166534;">${{ number_format($totalEfectivo, 0, ',', '.') }}</p>
                </div>
                @if($esAdmin)
                <div style="background: #f3e8ff; border: 1px solid #d8b4fe; border-radius: 10px; padding: 16px; text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #9333ea; font-weight: 600;">TRANSFERENCIA</p>
                    <p style="margin: 4px 0 0; font-size: 22px; font-weight: 800; color: #6b21a8;">${{ number_format($totalTransferencias, 0, ',', '.') }}</p>
                </div>
                <div style="background: #dbeafe; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px; text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #2563eb; font-weight: 600;">TARJETA</p>
                    <p style="margin: 4px 0 0; font-size: 22px; font-weight: 800; color: #1e40af;">${{ number_format($totalTarjeta, 0, ',', '.') }}</p>
                </div>
                @endif
                @if($esAdmin)
                <div style="background: #111827; border-radius: 10px; padding: 16px; text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #9ca3af; font-weight: 600;">TOTAL VENTAS</p>
                    <p style="margin: 4px 0 0; font-size: 22px; font-weight: 800; color: white;">${{ number_format($totalVentas, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Gastos --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #111827;">📋 Gastos del Día</h3>

            <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px;">
                <input type="text" wire:model.live="nuevoGastoDesc" placeholder="Descripción del gasto" style="flex: 2; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;">
                <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                    <span style="background: #f3f4f6; padding: 8px 10px; font-size: 14px; color: #6b7280; border-right: 1px solid #d1d5db;">$</span>
                    <input type="number" step="1" wire:model.live="nuevoGastoMonto" min="1" placeholder="0" style="width: 100px; border: none; padding: 8px 10px; font-size: 14px; outline: none;">
                </div>
                <x-filament::button wire:click="agregarGasto" color="danger" size="sm">
                    + Agregar gasto
                </x-filament::button>
            </div>

            @if(count($gastos) > 0)
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    @foreach($gastos as $index => $gasto)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }}">
                            <span style="font-size: 14px; color: #374151;">{{ $gasto['descripcion'] }}</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-weight: 600; color: #dc2626; font-size: 14px;">-${{ number_format($gasto['monto'], 0, ',', '.') }}</span>
                                <button wire:click="quitarGasto({{ $index }})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px;">×</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p style="margin: 8px 0 0; font-size: 14px; font-weight: 700; color: #dc2626; text-align: right;">Total Gastos: -${{ number_format($totalGastos, 0, ',', '.') }}</p>
            @else
                <p style="color: #9ca3af; font-size: 13px; text-align: center; padding: 16px 0;">Sin gastos registrados</p>
            @endif
        </div>

        {{-- Cuadre --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #111827;">🧮 Cuadre de Efectivo</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px;">
                <div style="background: #f9fafb; border-radius: 8px; padding: 12px; text-align: center;">
                    <p style="margin: 0; font-size: 11px; color: #6b7280; font-weight: 600;">VENTAS EFECTIVO</p>
                    <p style="margin: 4px 0 0; font-size: 20px; font-weight: 800; color: #16a34a;">${{ number_format($totalEfectivo, 0, ',', '.') }}</p>
                </div>
                <div style="background: #f9fafb; border-radius: 8px; padding: 12px; text-align: center;">
                    <p style="margin: 0; font-size: 11px; color: #6b7280; font-weight: 600;">GASTOS</p>
                    <p style="margin: 4px 0 0; font-size: 20px; font-weight: 800; color: #dc2626;">-${{ number_format($totalGastos, 0, ',', '.') }}</p>
                </div>
                <div style="background: #f9fafb; border-radius: 8px; padding: 12px; text-align: center;">
                    <p style="margin: 0; font-size: 11px; color: #6b7280; font-weight: 600;">EFECTIVO ESPERADO</p>
                    <p style="margin: 4px 0 0; font-size: 20px; font-weight: 800; color: #111827;">${{ number_format($efectivoEsperado, 0, ',', '.') }}</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 12px;">
                <div style="display: flex; align-items: center;">
                    <span style="font-size: 14px; font-weight: 600; color: #374151; margin-right: 8px;">Efectivo Real (contado):</span>
                    <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                        <span style="background: #f3f4f6; padding: 8px 10px; font-size: 14px; color: #6b7280; border-right: 1px solid #d1d5db;">$</span>
                        <input type="number" step="1" wire:model.live="efectivoReal" min="0" placeholder="0" style="width: 130px; border: none; padding: 8px 10px; font-size: 14px; outline: none;">
                    </div>
                </div>
            </div>

            @if($efectivoReal !== null)
                @php $diferencia = $efectivoReal - $efectivoEsperado; @endphp
                <div style="background: {{ abs($diferencia) < 1000 ? '#f0fdf4' : '#fef2f2' }}; border: 1px solid {{ abs($diferencia) < 1000 ? '#bbf7d0' : '#fecaca' }}; border-radius: 8px; padding: 12px 16px; text-align: center;">
                    <p style="margin: 0; font-size: 13px; color: #6b7280; font-weight: 600;">DIFERENCIA</p>
                    <p style="margin: 4px 0 0; font-size: 24px; font-weight: 800; color: {{ $diferencia >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ $diferencia >= 0 ? '+' : '' }}${{ number_format($diferencia, 0, ',', '.') }}
                    </p>
                </div>
            @endif

            <div style="margin-top: 16px;">
                <label style="font-size: 13px; font-weight: 600; color: #374151;">Observaciones</label>
                <textarea wire:model.live="observaciones" rows="2" placeholder="Notas sobre el cierre..." style="margin-top: 4px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; resize: vertical;"></textarea>
            </div>

            <div style="margin-top: 16px; display: flex; gap: 8px; justify-content: center;">
                <x-filament::button wire:click="guardarCierre" color="success" size="lg">
                    💾 Guardar Cierre
                </x-filament::button>
            </div>
        </div>

        {{-- Historial de Cierres --}}
        @if(count($historial) > 0)
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #111827;">📅 Historial de Cierres</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Fecha</th>
                            <th style="text-align: left; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Cajero</th>
                            @if($esAdmin)
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Total Ventas</th>
                            @endif
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Efectivo</th>
                            @if($esAdmin)
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Transferencia</th>
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Tarjeta</th>
                            @endif
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Gastos</th>
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Efectivo Real</th>
                            <th style="text-align: right; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Diferencia</th>
                            <th style="text-align: center; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Estado</th>
                            <th style="text-align: center; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historial as $c)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 10px 12px; font-weight: 500; color: #374151;">{{ \Carbon\Carbon::parse($c['fecha'])->format('d/m/Y') }}</td>
                                <td style="padding: 10px 12px; color: #6b7280;">{{ $c['user']['name'] ?? 'N/A' }}</td>
                                @if($esAdmin)
                                <td style="padding: 10px 12px; text-align: right; font-weight: 600; color: #374151;">${{ number_format($c['total_ventas'], 0, ',', '.') }}</td>
                                @endif
                                <td style="padding: 10px 12px; text-align: right; color: #16a34a;">${{ number_format($c['total_efectivo'], 0, ',', '.') }}</td>
                                @if($esAdmin)
                                <td style="padding: 10px 12px; text-align: right; color: #9333ea;">${{ number_format($c['total_transferencias'], 0, ',', '.') }}</td>
                                <td style="padding: 10px 12px; text-align: right; color: #2563eb;">${{ number_format($c['total_tarjeta'], 0, ',', '.') }}</td>
                                @endif
                                <td style="padding: 10px 12px; text-align: right; color: #dc2626;">-${{ number_format($c['total_gastos'], 0, ',', '.') }}</td>
                                <td style="padding: 10px 12px; text-align: right; color: #374151;">{{ $c['efectivo_real'] !== null ? '$' . number_format($c['efectivo_real'], 0, ',', '.') : '—' }}</td>
                                <td style="padding: 10px 12px; text-align: right; font-weight: 600; color: {{ ($c['diferencia'] ?? 0) == 0 ? '#16a34a' : '#dc2626' }};">
                                    {{ ($c['diferencia'] ?? 0) == 0 ? '$0' : (($c['diferencia'] ?? 0) > 0 ? '+' : '') . '$' . number_format($c['diferencia'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td style="padding: 10px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; {{ ($c['estado'] ?? '') === 'cuadrado' ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;' }}">
                                        {{ ($c['estado'] ?? '') === 'cuadrado' ? 'Cuadrado' : 'Abierto' }}
                                    </span>
                                </td>
                                <td style="padding: 10px 12px; text-align: center;">
                                    <a href="#" onclick="window.open('/admin/ticket/cierre/{{ $c['id'] }}', '_blank', 'width=400,height=600'); return false;" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;">
                                        🖨️ Imprimir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <script>
        function printCierre(id) {
            var w = window.open('/admin/ticket/cierre/' + id, '_blank', 'width=400,height=600,menubar=no,toolbar=no,location=no');
            if (w) w.focus();
        }
    </script>
    @include('partials.global-notifications')
</x-filament-panels::page>
