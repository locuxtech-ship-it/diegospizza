<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CierreCaja\Pages\ListCierreCajas;
use App\Filament\Resources\CierreCaja\Pages\ViewCierreCaja;
use App\Models\CierreCaja;
use BackedEnum;
use Filament\Forms\Components\TextInput;
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
        return $schema
            ->schema([
                TextInput::make('fecha')->label('Fecha')->disabled(),
                TextInput::make('user.name')->label('Usuario')->disabled(),
                TextInput::make('total_ventas')->label('Total Ventas')->disabled(),
                TextInput::make('total_efectivo')->label('Total Efectivo')->disabled(),
                TextInput::make('total_transferencias')->label('Total Transferencias')->disabled(),
                TextInput::make('total_tarjeta')->label('Total Tarjeta')->disabled(),
                TextInput::make('total_gastos')->label('Total Gastos')->disabled(),
                TextInput::make('efectivo_esperado')->label('Efectivo Esperado')->disabled(),
                TextInput::make('efectivo_real')->label('Efectivo Real')->disabled(),
                TextInput::make('diferencia')->label('Diferencia')->disabled(),
                TextInput::make('estado')->label('Estado')->disabled(),
                Textarea::make('observaciones')->label('Observaciones')->disabled(),
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
