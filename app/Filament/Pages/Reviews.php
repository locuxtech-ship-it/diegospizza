<?php

namespace App\Filament\Pages;

use App\Models\Review;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Action;
use UnitEnum;

class Reviews extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $slug = 'reviews';
    protected static ?string $title = 'Reseñas';
    protected static ?string $navigationLabel = 'Reseñas';
    protected static UnitEnum|string|null $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.reviews';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Review::query()->with('pedido'))
            ->columns([
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y g:i A')->sortable(),
                TextColumn::make('nombre')->label('Cliente')->searchable(),
                TextColumn::make('rating')->label('Puntaje')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)),
                TextColumn::make('comentario')->label('Comentario')->limit(60)->searchable(),
                TextColumn::make('pedido.numero_pedido')->label('Pedido #'),
                TextColumn::make('visible')->label('Visible')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('toggle_visibility')
                    ->label(fn (Review $record): string => $record->visible ? 'Ocultar' : 'Mostrar')
                    ->action(fn (Review $record) => $record->update(['visible' => !$record->visible])),
            ])
            ->bulkActions([]);
    }
}
