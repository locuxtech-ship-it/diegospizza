<div style="display: flex; flex-direction: column; gap: 16px;">

    {{-- Métricas del mes --}}
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;">
        {{-- Total clientes --}}
        <div style="background: linear-gradient(to bottom right, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px;">
            <p style="margin: 0; color: #3b82f6; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Clientes totales</p>
            <p style="margin: 4px 0 0 0; color: #1e3a5f; font-size: 28px; font-weight: 700;">{{ number_format($totalClientes) }}</p>
            <p style="margin: 2px 0 0 0; color: #3b82f6; font-size: 12px;">registrados en el sistema</p>
        </div>

        {{-- Nuevos del mes --}}
        <div style="background: linear-gradient(to bottom right, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px;">
            <p style="margin: 0; color: #059669; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Nuevos en {{ $mesLabel }}</p>
            <p style="margin: 4px 0 0 0; color: #065f46; font-size: 28px; font-weight: 700;">{{ number_format($nuevosMes) }}</p>
            <p style="margin: 2px 0 0 0; color: #059669; font-size: 12px;">
                Mes anterior completo: {{ number_format($nuevosMesAnterior) }}
            </p>
        </div>

        {{-- Top clientes --}}
        <div style="background: linear-gradient(to bottom right, #fffbeb, #fef3c7); border: 1px solid #fde68a; border-radius: 12px; padding: 16px;">
            <p style="margin: 0; color: #d97706; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Top clientes del mes</p>
            <p style="margin: 4px 0 0 0; color: #92400e; font-size: 28px; font-weight: 700;">${{ number_format($topVentas, 0, ',', '.') }}</p>
            <p style="margin: 2px 0 0 0; color: #b45309; font-size: 12px;">
                {{ number_format($topPorcentaje, 1, ',', '.') }}% de las ventas del mes
            </p>
        </div>
    </div>

    {{-- Ranking --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 16px;">🏆</span>
            <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Clientes que más pidieron — {{ $mesLabel }}</h3>
            <span style="color: #9ca3af; font-size: 12px; margin-left: auto;">{{ number_format($ranking->sum('pedidos')) }} pedidos · ${{ number_format($topVentas, 0, ',', '.') }}</span>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; min-width: 620px;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: center; padding: 10px 12px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase; width: 48px;">#</th>
                        <th style="text-align: left; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Cliente</th>
                        <th style="text-align: center; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Pedidos</th>
                        <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">Dinero</th>
                        <th style="text-align: right; padding: 10px 16px; font-weight: 600; color: #6b7280; font-size: 11px; text-transform: uppercase;">% del mes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ranking as $i => $item)
                        @php
                            $porcentaje = $totalMes > 0 ? ($item->ventas / $totalMes) * 100 : 0;
                            $medalla = match ($i) {
                                0 => '🥇',
                                1 => '🥈',
                                2 => '🥉',
                                default => $i + 1,
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 10px 12px; text-align: center; font-weight: 700; color: #6b7280;">{{ $medalla }}</td>
                            <td style="padding: 10px 16px;">
                                <a href="{{ \App\Filament\Resources\Clientes\ClienteResource::getUrl('view', ['record' => $item->cliente_id]) }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                    {{ $item->nombre }}
                                </a>
                                @if($item->telefono)
                                    <div style="color: #9ca3af; font-size: 11px;">{{ $item->telefono }}</div>
                                @endif
                            </td>
                            <td style="padding: 10px 16px; text-align: center; color: #374151; font-weight: 600;">{{ number_format($item->pedidos) }}</td>
                            <td style="padding: 10px 16px; text-align: right;">
                                <span style="font-weight: 600; color: #059669;">${{ number_format($item->ventas, 0, ',', '.') }}</span>
                                <div style="margin-top: 4px; height: 4px; border-radius: 9999px; background: #f3f4f6; overflow: hidden;">
                                    <div style="height: 4px; border-radius: 9999px; background: #10b981; width: {{ max(2, (int) round($porcentaje)) }}%;"></div>
                                </div>
                            </td>
                            <td style="padding: 10px 16px; text-align: right; color: #6b7280;">{{ number_format($porcentaje, 1, ',', '.') }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 32px 16px; text-align: center; color: #9ca3af;">Aún no hay pedidos en {{ $mesLabel }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($ranking->isNotEmpty())
                    <tfoot>
                        <tr style="background: #f9fafb; border-top: 2px solid #e5e7eb;">
                            <td colspan="2" style="padding: 10px 16px; font-weight: 700; color: #374151;">Total top {{ $ranking->count() }}</td>
                            <td style="padding: 10px 16px; text-align: center; font-weight: 700; color: #374151;">{{ number_format($ranking->sum('pedidos')) }}</td>
                            <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #059669;">${{ number_format($topVentas, 0, ',', '.') }}</td>
                            <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #374151;">{{ number_format($topPorcentaje, 1, ',', '.') }}%</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
