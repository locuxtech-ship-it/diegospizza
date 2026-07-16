<?php

namespace App\Filament\Resources\CuponDescuentoResource\Pages;

use App\Filament\Resources\CuponDescuentoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCuponDescuento extends EditRecord
{
    protected static string $resource = CuponDescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
