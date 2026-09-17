<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class PuntosRelationManager extends RelationManager
{
    protected static string $relationship = 'puntos';

    protected static ?string $title = 'Historial de Puntos';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('puntos')
                    ->label('Puntos')
                    ->weight('bold')
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '') . $state),
                TextColumn::make('concepto')
                    ->label('Concepto')
                    ->limit(50),
                TextColumn::make('pedido.numero_pedido')
                    ->label('Pedido')
                    ->formatStateUsing(fn ($state): string => $state ? "#{$state}" : '-')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
