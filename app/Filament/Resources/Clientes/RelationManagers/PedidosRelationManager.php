<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class PedidosRelationManager extends RelationManager
{
    protected static string $relationship = 'pedidos';

    protected static ?string $title = 'Historial de Pedidos';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_pedido')
                    ->label('#')
                    ->badge()
                    ->color('gray')
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente_pago' => '⏳ Pend. Pago',
                        'en_proceso' => '👨‍🍳 Preparación',
                        'en_camino' => '🚗 En Camino',
                        'entregado' => '📍 Entregado',
                        'finalizado' => '✅ Finalizado',
                        'cancelado' => '❌ Cancelado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente_pago' => 'warning',
                        'en_proceso' => 'info',
                        'en_camino' => 'primary',
                        'entregado' => 'success',
                        'finalizado' => 'gray',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->weight('bold'),
                TextColumn::make('descuento_puntos')
                    ->label('Dcto. Puntos')
                    ->money('COP')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('descuento_manual')
                    ->label('Dcto. Manual')
                    ->money('COP')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'tarjeta' => 'info',
                        'mixto' => 'warning',
                        'transferencia' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('origen')
                    ->label('Origen')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'web' => 'info',
                        'pdv' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s');
    }
}
