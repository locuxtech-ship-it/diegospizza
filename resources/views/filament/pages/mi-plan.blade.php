<x-filament-panels::page>
    <div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

        {{-- Banner estado --}}
        <div style="background: linear-gradient(135deg, #F1590C, #FF8C42); border-radius: 16px; padding: 24px; color: white;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9;">Tu plan actual</p>
                    <h2 style="margin: 4px 0 0 0; font-size: 28px; font-weight: 800;">{{ $planActual?->nombre ?? 'Sin plan' }}</h2>
                    <p style="margin: 4px 0 0 0; font-size: 14px; opacity: 0.95;">
                        ${{ number_format($planActual?->precio_mensual ?? 0, 0) }}/mes
                    </p>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; background: rgba(255,255,255,0.25); padding: 6px 16px; border-radius: 999px; font-size: 13px; font-weight: 600;">
                        {{ $planStatus === 'trial' ? 'Prueba gratuita' : ucfirst($planStatus) }}
                    </span>
                    @if($planExpira)
                        <p style="margin: 8px 0 0 0; font-size: 13px; opacity: 0.9;">Expira: {{ $planExpira }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sugerencia upgrade para Gratis/Arranque --}}
        @if($planActual && in_array($planActual->slug, ['gratis', 'arranque']))
            <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 24px;">💡</span>
                <div style="font-size: 14px; color: #9a3412;">
                    <strong>¿Quieres crecer más?</strong> Con el plan <strong>Negocio</strong> o <strong>Pro</strong> activas el bot de WhatsApp, fidelidad, reseñas y reportes avanzados. Pasa a un plan superior y desbloquea todo.
                </div>
            </div>
        @endif

        {{-- Planes disponibles --}}
        <div>
            <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #374151;">Planes disponibles</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                @foreach($planes as $plan)
                    <div style="background: white; border: 1px solid {{ $plan['slug'] === $planActual?->slug ? '#F1590C' : '#e5e7eb' }}; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">{{ $plan['nombre'] }}</h4>
                            @if($plan['slug'] === $planActual?->slug)
                                <span style="background: #F1590C; color: white; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px;">Actual</span>
                            @endif
                        </div>
                        <p style="margin: 0; font-size: 24px; font-weight: 800; color: #111827;">
                            ${{ number_format($plan['precio_mensual'], 0) }}<span style="font-size: 12px; font-weight: 400; color: #6b7280;">/mes</span>
                        </p>
                        <p style="margin: 0; font-size: 13px; color: #6b7280;">{{ $plan['descripcion'] }}</p>
                        @if($plan['slug'] !== $planActual?->slug)
                            <button wire:click="pagar('{{ $plan['slug'] }}')"
                                style="margin-top: auto; background: #F1590C; color: white; border: none; border-radius: 10px; padding: 10px; font-size: 14px; font-weight: 600; cursor: pointer;">
                                Pagar y cambiar
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Formulario de pago --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #374151;">Realizar un pago</h3>
            <p style="margin: 0 0 16px 0; font-size: 13px; color: #6b7280;">Transfiere al número que te indicamos y sube el comprobante. Validamos y activamos tu plan.</p>
            <form wire:submit.prevent="pagar('negocio')" style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; color: #6b7280; font-weight: 500;">Método</label>
                        <select wire:model="metodo" style="width: 100%; margin-top: 4px; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;">
                            <option value="transferencia">Transferencia</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #6b7280; font-weight: 500;">Referencia (opcional)</label>
                        <input type="text" wire:model="referencia" placeholder="N° de transacción" style="width: 100%; margin-top: 4px; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;">
                    </div>
                </div>
                <div>
                    <label style="font-size: 12px; color: #6b7280; font-weight: 500;">Comprobante (opcional)</label>
                    <input type="file" wire:model="comprobante" style="width: 100%; margin-top: 4px; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px; font-size: 14px;">
                </div>
                <button type="submit" style="background: #F1590C; color: white; border: none; border-radius: 10px; padding: 12px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    Enviar pago para validación
                </button>
            </form>
        </div>

        {{-- Historial de pagos --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #374151;">Historial de pagos</h3>
            </div>
            @if(count($pagos) === 0)
                <div style="padding: 24px; text-align: center; color: #9ca3af; font-size: 14px;">Aún no hay pagos registrados.</div>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f9fafb; text-align: left; color: #6b7280;">
                            <th style="padding: 12px 24px;">Plan</th>
                            <th style="padding: 12px 24px;">Monto</th>
                            <th style="padding: 12px 24px;">Período</th>
                            <th style="padding: 12px 24px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagos as $pago)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 12px 24px;">{{ $pago['plan_id'] ? (\App\Models\Plan::find($pago['plan_id'])?->nombre ?? '—') : '—' }}</td>
                                <td style="padding: 12px 24px;">${{ number_format($pago['monto'], 0) }}</td>
                                <td style="padding: 12px 24px;">{{ $pago['periodo'] }}</td>
                                <td style="padding: 12px 24px;">
                                    <span style="padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; background: {{ $pago['estado'] === 'aprobado' ? '#d1fae5' : ($pago['estado'] === 'pendiente' ? '#fef3c7' : '#fee2e2') }}; color: {{ $pago['estado'] === 'aprobado' ? '#065f46' : ($pago['estado'] === 'pendiente' ? '#92400e' : '#991b1b') }};">
                                        {{ ucfirst($pago['estado']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</x-filament-panels::page>
