<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

    protected static ?string $title = 'Detalle del Cliente';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Datos del Cliente')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('telefono')
                            ->label('Teléfono')
                            ->icon('heroicon-o-phone'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope'),
                        TextEntry::make('clasificacion_label')
                            ->label('Clasificación')
                            ->badge(),
                    ])->columns(2),

                Section::make('Dirección')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextEntry::make('conjunto')
                            ->label('Conjunto'),
                        TextEntry::make('torre')
                            ->label('Torre'),
                        TextEntry::make('apto')
                            ->label('Apto'),
                        TextEntry::make('direccion_completa')
                            ->label('Dirección Completa')
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Puntos y Fidelidad')
                    ->icon('heroicon-o-star')
                    ->schema([
                        TextEntry::make('puntos_acumulados')
                            ->label('Puntos Acumulados')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('pedidos_count')
                            ->label('Total Pedidos')
                            ->state(fn ($record) => $record->pedidos()->count()),
                        TextEntry::make('monto_total')
                            ->label('Monto Total Gastado')
                            ->state(fn ($record) => '$' . number_format($record->pedidos()->where('estado', 'finalizado')->sum('total'), 0, ',', '.'))
                            ->weight('bold'),
                    ])->columns(3),

                Section::make('Notas')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notas')
                            ->label('Notas')
                            ->placeholder('Sin notas registradas')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->notas)),
            ]);
    }
}
