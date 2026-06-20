<?php

namespace App\Filament\Resources\Productos\Pages;

use App\Filament\Resources\Productos\ProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;
    protected string $view = 'filament.resources.productos.pages.list-productos';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
