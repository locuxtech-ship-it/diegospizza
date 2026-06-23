<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CierreCaja\Pages\ListCierreCajas;
use App\Filament\Resources\CierreCaja\Pages\ViewCierreCaja;
use App\Models\CierreCaja;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class CierreCajaResource extends Resource
{
    protected static ?string $model = CierreCaja::class;

    protected static ?string $navigationLabel = 'Historial Cierres';
    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public static function getNavigationGroup(): ?string
    {
        return 'Punto de Venta';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Fecha'),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable(),
                TextColumn::make('total_ventas')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 0, ',', '.'))
                    ->label('Total Ventas')
                    ->sortable(),
                TextColumn::make('total_efectivo')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 0, ',', '.'))
                    ->label('Efectivo')
                    ->sortable(),
                TextColumn::make('total_gastos')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 0, ',', '.'))
                    ->label('Gastos')
                    ->sortable(),
                TextColumn::make('efectivo_esperado')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 0, ',', '.'))
                    ->label('Esperado'),
                TextColumn::make('efectivo_real')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format((float) $state, 0, ',', '.') : '-')
                    ->label('Real'),
                TextColumn::make('diferencia')
                    ->formatStateUsing(fn ($state) => ($state >= 0 ? '+' : '') . '$' . number_format((float) $state, 0, ',', '.'))
                    ->label('Diferencia'),
                BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'abierto',
                        'success' => 'cuadrado',
                    ])
                    ->label('Estado'),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        $fmt = fn ($v) => $v !== null ? '$' . number_format((float) $v, 0, ',', '.') : '-';

        return $schema
            ->schema([
                Placeholder::make('fecha')
                    ->label('Fecha')
                    ->content(fn ($record) => $record?->fecha?->format('d/m/Y') ?? '-'),
                Placeholder::make('user.name')
                    ->label('Usuario')
                    ->content(fn ($record) => $record?->user?->name ?? '-'),
                Placeholder::make('total_ventas')
                    ->label('Total Ventas')
                    ->content(fn ($record) => $fmt($record?->total_ventas)),
                Placeholder::make('total_efectivo')
                    ->label('Total Efectivo')
                    ->content(fn ($record) => $fmt($record?->total_efectivo)),
                Placeholder::make('total_transferencias')
                    ->label('Total Transferencias')
                    ->content(fn ($record) => $fmt($record?->total_transferencias)),
                Placeholder::make('total_tarjeta')
                    ->label('Total Tarjeta')
                    ->content(fn ($record) => $fmt($record?->total_tarjeta)),
                Placeholder::make('total_gastos')
                    ->label('Total Gastos')
                    ->content(fn ($record) => $fmt($record?->total_gastos)),
                Placeholder::make('efectivo_esperado')
                    ->label('Efectivo Esperado')
                    ->content(fn ($record) => $fmt($record?->efectivo_esperado)),
                Placeholder::make('efectivo_real')
                    ->label('Efectivo Real')
                    ->content(fn ($record) => $fmt($record?->efectivo_real)),
                Placeholder::make('diferencia')
                    ->label('Diferencia')
                    ->content(fn ($record) => ($record?->diferencia !== null ? (($record->diferencia >= 0 ? '+' : '') . '$' . number_format((float) $record->diferencia, 0, ',', '.')) : '-')),
                Placeholder::make('estado')
                    ->label('Estado')
                    ->content(fn ($record) => ucfirst($record?->estado ?? '-')),
                Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->disabled(),
            ])
            ->columns(2);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCierreCajas::route('/'),
            'view' => ViewCierreCaja::route('/{record}'),
        ];
    }
}
