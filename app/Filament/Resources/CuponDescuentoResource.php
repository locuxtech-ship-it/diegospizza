<?php

namespace App\Filament\Resources;

use App\Models\CuponDescuento;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CuponDescuentoResource extends Resource
{
    protected static ?string $model = CuponDescuento::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-ticket';
    protected static string | UnitEnum | null $navigationGroup = 'Promociones';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')
                ->label('Código')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),
            Select::make('tipo')
                ->label('Tipo')
                ->options(['porcentaje' => 'Porcentaje (%)', 'monto' => 'Monto fijo ($)'])
                ->required(),
            TextInput::make('valor')
                ->label('Valor')
                ->numeric()
                ->required()
                ->minValue(0),
            TextInput::make('monto_minimo')
                ->label('Monto mínimo del pedido ($)')
                ->numeric()
                ->minValue(0)
                ->nullable(),
            TextInput::make('usos_maximos')
                ->label('Usos máximos')
                ->numeric()
                ->minValue(0)
                ->nullable()
                ->placeholder('Sin límite'),
            Toggle::make('por_cliente')
                ->label('Un solo uso por cliente'),
            Toggle::make('activo')
                ->label('Activo')
                ->default(true),
            DateTimePicker::make('fecha_inicio')
                ->label('Fecha de inicio')
                ->nullable(),
            DateTimePicker::make('fecha_expiracion')
                ->label('Fecha de expiración')
                ->nullable()
                ->afterOrEqual('fecha_inicio'),
            Select::make('cliente_id')
                ->label('Cliente específico')
                ->relationship('cliente', 'nombre')
                ->searchable()
                ->nullable()
                ->placeholder('Todos los clientes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'porcentaje' ? '%' : '$'),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('COP', divideBy: 1)
                    ->formatStateUsing(fn ($state, $record) => $record->tipo === 'porcentaje' ? "{$state}%" : '$' . number_format((float) $state, 0, ',', '.')),
                TextColumn::make('usos_actuales')
                    ->label('Usos')
                    ->sortable(),
                TextColumn::make('usos_maximos')
                    ->label('Límite')
                    ->formatStateUsing(fn ($state) => $state ?? '∞'),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
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
            'index' => \App\Filament\Resources\CuponDescuentoResource\Pages\ListCuponDescuentos::route('/'),
            'create' => \App\Filament\Resources\CuponDescuentoResource\Pages\CreateCuponDescuento::route('/create'),
            'edit' => \App\Filament\Resources\CuponDescuentoResource\Pages\EditCuponDescuento::route('/{record}/edit'),
        ];
    }
}
