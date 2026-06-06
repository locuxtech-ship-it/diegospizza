<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('categoria_id')
                    ->relationship('categoria', 'nombre')
                    ->label('Categoria')
                    ->required(),
                TextInput::make('nombre')
                    ->label('Nombre del producto')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->hidden(),
                Textarea::make('descripcion')
                    ->label('Descripcion')
                    ->columnSpanFull(),
                TextInput::make('precio')
                    ->label('Precio')
                    ->prefix('$')
                    ->required()
                    ->numeric(),
                FileUpload::make('imagen')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('productos')
                    ->columnSpanFull(),
                Textarea::make('ingredientes')
                    ->label('Ingredientes')
                    ->helperText('Lista de ingredientes separados por coma')
                    ->columnSpanFull(),
                Toggle::make('disponible')
                    ->label('Disponible para venta')
                    ->default(true),
                Toggle::make('es_personalizable')
                    ->label('Permite personalizacion'),
                Repeater::make('variants')
                    ->relationship()
                    ->label('Tamaños / Variantes')
                    ->helperText('Agrega diferentes tamaños con sus precios (ej: Pequeña, Mediana, Grande)')
                    ->schema([
                        TextInput::make('tamanio')
                            ->label('Tamaño')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('precio')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('orden')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ])
                    ->orderColumn('orden')
                    ->defaultItems(0)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['tamanio'] ?? null),
            ]);
    }
}