<?php

namespace App\Filament\Resources;

use App\Models\DescuentoProducto;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DescuentoProductoResource extends Resource
{
    protected static ?string $model = DescuentoProducto::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | UnitEnum | null $navigationGroup = 'Promociones';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tipo')
                ->label('Tipo')
                ->options(['porcentaje' => 'Porcentaje (%)', 'monto' => 'Monto fijo ($)'])
                ->required(),
            TextInput::make('valor')
                ->label('Valor')
                ->numeric()
                ->required()
                ->minValue(0),
            Select::make('producto_id')
                ->label('Producto')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->nullable()
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('categoria_id', null)),
            Select::make('categoria_id')
                ->label('Categoría')
                ->relationship('categoria', 'nombre')
                ->searchable()
                ->nullable()
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('producto_id', null)),
            DateTimePicker::make('fecha_inicio')
                ->label('Fecha de inicio')
                ->required(),
            DateTimePicker::make('fecha_expiracion')
                ->label('Fecha de expiración')
                ->required()
                ->afterOrEqual('fecha_inicio'),
            Toggle::make('activo')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'porcentaje' ? '%' : '$'),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->formatStateUsing(fn ($state, $record) => $record->tipo === 'porcentaje' ? "{$state}%" : '$' . number_format((float) $state, 0, ',', '.')),
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->placeholder('—'),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->placeholder('—'),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->dateTime('d/m/Y g:i A')
                    ->sortable(),
                TextColumn::make('fecha_expiracion')
                    ->label('Expira')
                    ->dateTime('d/m/Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DescuentoProductoResource\Pages\ListDescuentoProductos::route('/'),
            'create' => \App\Filament\Resources\DescuentoProductoResource\Pages\CreateDescuentoProducto::route('/create'),
            'edit' => \App\Filament\Resources\DescuentoProductoResource\Pages\EditDescuentoProducto::route('/{record}/edit'),
        ];
    }
}
