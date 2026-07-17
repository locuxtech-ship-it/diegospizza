<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'Mi Perfil';

    public static function getLabel(): string
    {
        return 'Mi Perfil';
    }

    public function form(Schema $schema): Schema
    {
        $user = auth()->user();

        return $schema
            ->components([
                Section::make('Información del usuario')
                    ->description('Datos de tu cuenta')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Placeholder::make('avatar')
                                    ->label('')
                                    ->content(fn () => view('filament.components.user-avatar', ['user' => $user])),
                            ])
                            ->columns(1),
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('nombre')
                                    ->label('Nombre')
                                    ->content($user->name),
                                Placeholder::make('email')
                                    ->label('Correo electrónico')
                                    ->content($user->email),
                                Placeholder::make('rol')
                                    ->label('Rol')
                                    ->content(fn () => view('filament.components.role-badge', ['role' => $user->role])),
                                Placeholder::make('miembro_desde')
                                    ->label('Miembro desde')
                                    ->content($user->created_at->isoFormat('D [de] MMMM [del] YYYY')),
                            ]),
                    ])
                    ->heading('Información del usuario')
                    ->description('Datos de tu cuenta'),
                Section::make('Cambiar contraseña')
                    ->description('Actualiza tu contraseña de acceso')
                    ->schema([
                        $this->getCurrentPasswordFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->label('Contraseña actual');
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label('Nueva contraseña');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->label('Confirmar nueva contraseña');
    }
}
