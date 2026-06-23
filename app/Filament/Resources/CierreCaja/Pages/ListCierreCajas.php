<?php

namespace App\Filament\Resources\CierreCaja\Pages;

use App\Filament\Resources\CierreCajaResource;
use Filament\Resources\Pages\ListRecords;

class ListCierreCajas extends ListRecords
{
    protected static string $resource = CierreCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
