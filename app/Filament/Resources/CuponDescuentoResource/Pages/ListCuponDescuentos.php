<?php

namespace App\Filament\Resources\CuponDescuentoResource\Pages;

use App\Filament\Resources\CuponDescuentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCuponDescuentos extends ListRecords
{
    protected static string $resource = CuponDescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
