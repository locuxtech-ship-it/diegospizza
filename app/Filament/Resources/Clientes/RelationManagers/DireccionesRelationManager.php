<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use App\Models\ClienteDireccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DireccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'direcciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('alias')
                    ->label('Alias (ej: Casa, Trabajo)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('conjunto')
                    ->label('Conjunto')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('torre')
                    ->label('Torre')
                    ->maxLength(50),
                Forms\Components\TextInput::make('apto')
                    ->label('Apto')
                    ->maxLength(50),
                Forms\Components\Toggle::make('es_principal')
                    ->label('Dirección principal'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alias')
            ->columns([
                Tables\Columns\TextColumn::make('alias')
                    ->label('Alias')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('conjunto')
                    ->label('Conjunto'),
                Tables\Columns\TextColumn::make('torre')
                    ->label('Torre')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('apto')
                    ->label('Apto')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('es_principal')
                    ->label('Principal')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
