<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use App\Models\ClienteDireccion;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class DireccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'direcciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('alias')
                    ->label('Alias (ej: Casa, Trabajo)')
                    ->maxLength(255),
                TextInput::make('conjunto')
                    ->label('Conjunto')
                    ->required()
                    ->maxLength(255),
                TextInput::make('torre')
                    ->label('Torre')
                    ->maxLength(50),
                TextInput::make('apto')
                    ->label('Apto')
                    ->maxLength(50),
                Toggle::make('es_principal')
                    ->label('Dirección principal'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alias')
            ->columns([
                TextColumn::make('alias')
                    ->label('Alias')
                    ->placeholder('—'),
                TextColumn::make('conjunto')
                    ->label('Conjunto'),
                TextColumn::make('torre')
                    ->label('Torre')
                    ->placeholder('—'),
                TextColumn::make('apto')
                    ->label('Apto')
                    ->placeholder('—'),
                IconColumn::make('es_principal')
                    ->label('Principal')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
