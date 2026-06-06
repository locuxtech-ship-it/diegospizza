<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('telefono')
                    ->tel()
                    ->required(),
                TextInput::make('conjunto')
                    ->label('Conjunto')
                    ->required(),
                TextInput::make('torre')
                    ->label('Torre'),
                TextInput::make('apto')
                    ->label('Apto'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Textarea::make('notas')
                    ->columnSpanFull(),
                TextInput::make('puntos_acumulados')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
