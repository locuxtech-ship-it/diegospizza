<?php

namespace App\Filament\Resources\Usuarios;

use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\CreateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Usuarios';

    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 99;

    // --- ACCESS CONTROL ---
    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->hiddenOn('edit')
                    ->required(fn ($context) => $context === 'create'),
                TextInput::make('password_confirmation')
                    ->label('Confirmar contraseña')
                    ->password()
                    ->hiddenOn('edit')
                    ->same('password'),
                Select::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'cajero' => 'Cajero',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn ($state) => $state === 'admin' ? 'danger' : 'warning'),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Usuarios\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\Usuarios\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\Usuarios\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
