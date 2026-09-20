<x-filament-panels::page>
    @if($cliente)
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        {{-- Datos --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
            <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #111827;">👤 Datos del Cliente</h3>
            <div style="display: grid; gap: 8px;">
                <div><span style="color: #6b7280; font-size: 13px;">Nombre:</span> <strong>{{ $cliente->nombre }}</strong></div>
                <div><span style="color: #6b7280; font-size: 13px;">Teléfono:</span> {{ $cliente->telefono }}</div>
                <div><span style="color: #6b7280; font-size: 13px;">Email:</span> {{ $cliente->email ?? '-' }}</div>
                <div><span style="color: #6b7280; font-size: 13px;">Clasificación:</span>
                    <span style="padding: 2px 8px; border-radius: 9999px; font-size: 12px; font-weight: 600;
                        background: {{ $cliente->clasificacion === 'elite' ? '#fef3c7' : ($cliente->clasificacion === 'frecuente' ? '#ffedd5' : '#f3f4f6') }};
                        color: {{ $cliente->clasificacion === 'elite' ? '#92400e' : ($cliente->clasificacion === 'frecuente' ? '#9a3412' : '#6b7280') }};">
                        {{ $cliente->clasificacion_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Dirección --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
            <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #111827;">📍 Dirección</h3>
            <div style="display: grid; gap: 8px;">
                <div><span style="color: #6b7280; font-size: 13px;">Conjunto:</span> {{ $cliente->conjunto }}</div>
                <div><span style="color: #6b7280; font-size: 13px;">Torre:</span> {{ $cliente->torre ?? '-' }}</div>
                <div><span style="color: #6b7280; font-size: 13px;">Apto:</span> {{ $cliente->apto ?? '-' }}</div>
                <div><span style="color: #6b7280; font-size: 13px;">Dirección completa:</span> <strong>{{ $cliente->direccion_completa ?: '-' }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #16a34a;">{{ $totalPedidos }}</div>
            <div style="font-size: 12px; color: #16a34a; text-transform: uppercase;">Total Pedidos</div>
        </div>
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #2563eb;">${{ number_format($montoTotal, 0, ',', '.') }}</div>
            <div style="font-size: 12px; color: #2563eb; text-transform: uppercase;">Monto Total</div>
        </div>
        <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #d97706;">{{ $puntosGanados }}</div>
            <div style="font-size: 12px; color: #92400e; text-transform: uppercase;">Puntos Ganados</div>
        </div>
        <div style="background: #fce7f3; border: 1px solid #fbcfe8; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #db2777;">{{ $puntosCanjeados }}</div>
            <div style="font-size: 12px; color: #9d174d; text-transform: uppercase;">Puntos Canjeados</div>
        </div>
    </div>

    {{-- Notas --}}
    @if(!empty($cliente->notas))
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px; font-size: 14px; font-weight: 700; color: #92400e;">📝 Notas</h3>
        <p style="margin: 0; color: #78350f;">{{ $cliente->notas }}</p>
    </div>
    @endif

    {{-- Historial de Pedidos --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">📋 Historial de Pedidos ({{ $totalPedidos }})</h3>
        </div>
        @if(count($pedidos) > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f9fafb;">
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">#</th>
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Fecha</th>
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Estado</th>
                        <th style="padding: 10px 16px; text-align: right; font-weight: 600; color: #374151;">Total</th>
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Método</th>
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Origen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $p)
                    @php
                        $totalReal = (float)$p['total'];
                        $estadoColors = [
                            'pendiente_pago' => ['#fef3c7', '#92400e', '⏳ Pend. Pago'],
                            'en_proceso' => ['#ffedd5', '#9a3412', '👨‍🍳 Preparación'],
                            'en_camino' => ['#dbeafe', '#1e40af', '🚗 En Camino'],
                            'entregado' => ['#f3e8ff', '#6b21a8', '📍 Entregado'],
                            'finalizado' => ['#dcfce7', '#166534', '✅ Finalizado'],
                            'cancelado' => ['#fef2f2', '#991b1b', '❌ Cancelado'],
                        ];
                        $ec = $estadoColors[$p['estado']] ?? ['#f3f4f6', '#374151', $p['estado']];
                    @endphp
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 10px 16px; font-weight: 600;">#{{ $p['numero_pedido'] }}</td>
                        <td style="padding: 10px 16px; color: #6b7280;">{{ \Carbon\Carbon::parse($p['created_at'])->setTimezone('America/Bogota')->format('d/m/y H:i') }}</td>
                        <td style="padding: 10px 16px;">
                            <span style="padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; background: {{ $ec[0] }}; color: {{ $ec[1] }};">{{ $ec[2] }}</span>
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700;">${{ number_format($totalReal, 0, ',', '.') }}</td>
                        <td style="padding: 10px 16px; color: #6b7280;">{{ $p['metodo_pago'] ?? '-' }}</td>
                        <td style="padding: 10px 16px;">
                            <span style="padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 600; {{ ($p['origen'] ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;' }}">
                                {{ strtoupper($p['origen'] ?? 'PDV') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 32px; color: #6b7280;">Sin pedidos registrados</div>
        @endif
    </div>

    {{-- Historial de Puntos --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">⭐ Historial de Puntos</h3>
        </div>
        @if(count($puntos) > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f9fafb;">
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Fecha</th>
                        <th style="padding: 10px 16px; text-align: right; font-weight: 600; color: #374151;">Puntos</th>
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Concepto</th>
                        <th style="padding: 10px 16px; text-align: left; font-weight: 600; color: #374151;">Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($puntos as $pu)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 10px 16px; color: #6b7280;">{{ \Carbon\Carbon::parse($pu['created_at'])->setTimezone('America/Bogota')->format('d/m/y H:i') }}</td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: {{ $pu['puntos'] > 0 ? '#16a34a' : '#dc2626' }};">
                            {{ $pu['puntos'] > 0 ? '+' : '' }}{{ $pu['puntos'] }} pts
                        </td>
                        <td style="padding: 10px 16px;">{{ $pu['concepto'] ?? '-' }}</td>
                        <td style="padding: 10px 16px; color: #6b7280;">{{ $pu['pedido'] ? '#' . $pu['pedido']['numero_pedido'] : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 32px; color: #6b7280;">Sin historial de puntos</div>
        @endif
    </div>
    @endif
</x-filament-panels::page>
