<?php

namespace App\Filament\Resources\DescuentoProductoResource\Pages;

use App\Filament\Resources\DescuentoProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDescuentoProductos extends ListRecords
{
    protected static string $resource = DescuentoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
