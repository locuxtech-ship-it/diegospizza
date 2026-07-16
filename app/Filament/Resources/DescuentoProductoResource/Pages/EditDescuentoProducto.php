<?php

namespace App\Filament\Resources\DescuentoProductoResource\Pages;

use App\Filament\Resources\DescuentoProductoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDescuentoProducto extends EditRecord
{
    protected static string $resource = DescuentoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
