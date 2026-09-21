<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #111827;">📅 Historial de Cierres</h2>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">Últimos {{ count($cierres) }} cierres de caja</p>
        </div>

        {{-- Tabla --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Fecha</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Cajero</th>
                            @if($esAdmin)
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Total Ventas</th>
                            @endif
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Efectivo</th>
                            @if($esAdmin)
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Transferencia</th>
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Tarjeta</th>
                            @endif
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Gastos</th>
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Efectivo Real</th>
                            <th style="text-align: right; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Diferencia</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Estado</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cierres as $c)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 12px 16px; font-weight: 500; color: #374151;">{{ \Carbon\Carbon::parse($c['fecha'])->format('d/m/Y') }}</td>
                                <td style="padding: 12px 16px; color: #6b7280;">{{ $c['user']['name'] ?? 'N/A' }}</td>
                                @if($esAdmin)
                                <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">${{ number_format($c['total_ventas'], 0, ',', '.') }}</td>
                                @endif
                                <td style="padding: 12px 16px; text-align: right; color: #16a34a;">${{ number_format($c['total_efectivo'], 0, ',', '.') }}</td>
                                @if($esAdmin)
                                <td style="padding: 12px 16px; text-align: right; color: #9333ea;">${{ number_format($c['total_transferencias'], 0, ',', '.') }}</td>
                                <td style="padding: 12px 16px; text-align: right; color: #2563eb;">${{ number_format($c['total_tarjeta'], 0, ',', '.') }}</td>
                                @endif
                                <td style="padding: 12px 16px; text-align: right; color: #dc2626;">-${{ number_format($c['total_gastos'], 0, ',', '.') }}</td>
                                <td style="padding: 12px 16px; text-align: right; color: #374151;">{{ $c['efectivo_real'] !== null ? '$' . number_format($c['efectivo_real'], 0, ',', '.') : '—' }}</td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: {{ ($c['diferencia'] ?? 0) == 0 ? '#16a34a' : '#dc2626' }};">
                                    {{ ($c['diferencia'] ?? 0) == 0 ? '$0' : (($c['diferencia'] ?? 0) > 0 ? '+' : '') . '$' . number_format($c['diferencia'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <span style="display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; {{ ($c['estado'] ?? '') === 'cuadrado' ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;' }}">
                                        {{ ($c['estado'] ?? '') === 'cuadrado' ? 'Cuadrado' : 'Abierto' }}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <a href="#" onclick="window.open('/admin/ticket/cierre/{{ $c['id'] }}', '_blank', 'width=400,height=600'); return false;" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;">
                                        🖨️ Imprimir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $esAdmin ? 11 : 8 }}" style="padding: 40px 16px; text-align: center; color: #9ca3af;">No hay cierres registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
