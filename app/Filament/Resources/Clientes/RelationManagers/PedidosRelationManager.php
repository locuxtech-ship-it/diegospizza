<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use App\Models\Pedido;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PedidosRelationManager extends RelationManager
{
    protected static string $relationship = 'pedidos';

    protected static ?string $title = 'Historial de Pedidos';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_pedido')
            ->columns([
                TextColumn::make('numero_pedido')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y g:i A')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente_pago' => 'warning',
                        'en_proceso' => 'info',
                        'en_camino' => 'info',
                        'entregado' => 'success',
                        'ha_llegado' => 'success',
                        'finalizado' => 'gray',
                        'cancelado' => 'danger',
                    }),
                TextColumn::make('origen')
                    ->label('Origen')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'web' ? 'primary' : 'gray'),
                TextColumn::make('total')
                    ->label('Total ($)')
                    ->sortable(),
                TextColumn::make('puntos_ganados')
                    ->label('Puntos')
                    ->getStateUsing(fn (Pedido $record): int =>
                        max(0, \App\Models\Punto::where('pedido_id', $record->id)->sum('puntos'))
                    ),
                TextColumn::make('metodo_pago')
                    ->label('Pago'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
