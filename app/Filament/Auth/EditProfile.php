<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'Cambiar Contraseña';

    public static function getLabel(): string
    {
        return 'Cambiar Contraseña';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCurrentPasswordFormComponent()->label('Contraseña actual'),
                $this->getPasswordFormComponent()->label('Nueva contraseña'),
                $this->getPasswordConfirmationFormComponent()->label('Confirmar nueva contraseña'),
            ]);
    }

    public function getMaxContentWidth(): string
    {
        return 'sm';
    }
}
