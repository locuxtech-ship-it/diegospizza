<?php

namespace App\Filament\Auth;

use App\Models\NegocioSetting;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Hash;

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
                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Grid::make()
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('avatar')
                                    ->label('')
                                    ->content(fn () => view('filament.components.user-avatar', ['user' => $user])),
                            ])
                            ->columns(1),
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('nombre')
                                    ->label('Nombre')
                                    ->content($user->name),
                                \Filament\Forms\Components\Placeholder::make('email')
                                    ->label('Correo electrónico')
                                    ->content($user->email),
                                \Filament\Forms\Components\Placeholder::make('rol')
                                    ->label('Rol')
                                    ->content(fn () => view('filament.components.role-badge', ['role' => $user->role])),
                                \Filament\Forms\Components\Placeholder::make('miembro_desde')
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

    protected function getCurrentPasswordFormComponent(): \Filament\Forms\Components\Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->label('Contraseña actual');
    }

    protected function getPasswordFormComponent(): \Filament\Forms\Components\Component
    {
        return parent::getPasswordFormComponent()
            ->label('Nueva contraseña');
    }

    protected function getPasswordConfirmationFormComponent(): \Filament\Forms\Components\Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->label('Confirmar nueva contraseña');
    }
}
