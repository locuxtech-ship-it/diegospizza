<?php

namespace App\Filament\Resources\Productos\Tables;

use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Imagen')
                    ->square()
                    ->size(50)
                    ->toggleable(),
                TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categoria.nombre')
                    ->label('Categoria')
                    ->searchable(),
                TextColumn::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->sortable()
                    ->prefix('$'),
                IconColumn::make('disponible')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle'),
            ])
            ->groups([
                Group::make('categoria.nombre')
                    ->label('Categoría')
                    ->collapsible(),
            ])
            ->defaultGroup('categoria.nombre')
            ->paginated(false)
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('toggleDisponible')
                    ->label(fn (Producto $record): string => $record->disponible ? 'Ocultar' : 'Mostrar')
                    ->icon(fn (Producto $record): string => $record->disponible ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->color(fn (Producto $record): string => $record->disponible ? 'warning' : 'success')
                    ->action(fn (Producto $record) => $record->update(['disponible' => !$record->disponible])),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}