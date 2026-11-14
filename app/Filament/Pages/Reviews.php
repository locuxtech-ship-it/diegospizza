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
    use InteractsWithTable {
        sortTable as protected originalSortTable;
    }
    use InteractsWithForms;

    /** Detalle de la reseña abierto con doble click (null = cerrado) */
    public ?array $resenaDetalle = null;

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

    /**
     * El primer clic en "Fecha" deja lo más reciente arriba.
     * Por defecto Filament ordena asc en el primer clic y las reseñas
     * más recientes se van a la última página (de ahí que no se vieran).
     */
    public function sortTable(?string $column = null, ?string $direction = null): void
    {
        if ($column === 'created_at' && $direction === null) {
            $direction = ($this->getTableSortColumn() === 'created_at' && $this->getTableSortDirection() === 'desc')
                ? 'asc'
                : 'desc';
        }

        $this->originalSortTable($column, $direction);
    }

    /** Abre el detalle completo de una reseña (doble click en la fila) */
    public function abrirDetalle(int $reviewId): void
    {
        $review = Review::with('pedido')->find($reviewId);
        if (!$review) {
            return;
        }

        $pedido = $review->pedido;
        $this->resenaDetalle = [
            'id' => $review->id,
            'nombre' => $review->nombre ?: 'Cliente',
            'rating' => (int) $review->rating,
            'comentario' => trim((string) $review->comentario),
            'telefono' => $review->telefono,
            'visible' => (bool) $review->visible,
            'fecha' => $review->created_at->format('d/m/Y H:i'),
            'pedido_numero' => $pedido?->numero_pedido,
            'pedido_total' => $pedido?->total,
            'pedido_estado' => $pedido?->estado,
        ];
    }

    public function cerrarDetalle(): void
    {
        $this->resenaDetalle = null;
    }

    /** Mostrar / Ocultar la reseña desde el modal de detalle */
    public function alternarVisibilidadDetalle(): void
    {
        if (!$this->resenaDetalle) {
            return;
        }

        $review = Review::find($this->resenaDetalle['id']);
        if (!$review) {
            return;
        }

        $review->update(['visible' => !$review->visible]);
        $this->resenaDetalle['visible'] = (bool) $review->visible;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Review::query()->with('pedido'))
            ->columns([
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y g:i A')->sortable()
                    ->extraAttributes(fn (Review $record): array => ['data-review-id' => $record->id]),
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
