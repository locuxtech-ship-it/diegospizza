<?php

namespace App\Filament\Resources\CierreCaja\Pages;

use App\Filament\Resources\CierreCajaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewCierreCaja extends ViewRecord
{
    protected static string $resource = CierreCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reimprimir')
                ->label('Reimprimir Ticket')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('ticket.cierre', ['cierre' => $this->getRecord()->id]), shouldOpenInNewTab: true),
        ];
    }
}
