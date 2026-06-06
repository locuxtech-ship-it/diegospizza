<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use App\Models\Cliente;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PedidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Información del Pedido')
                    ->columns(2)
                    ->schema([

                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->options(Cliente::all()->mapWithKeys(fn ($c) => [$c->id => "#{$c->id} - {$c->nombre} ({$c->telefono})"]))
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('origen')
                            ->label('Origen')
                            ->options([
                                'pdv' => 'PDV',
                                'web' => 'WEB',
                            ]),

                        Select::make('estado')
                            ->required()
                            ->options([
                                'pendiente_pago' => 'Pendiente de Pago',
                                'en_proceso' => 'En Proceso',
                                'en_camino' => 'En Camino',
                                'entregado' => 'Ha Llegado',
                                'cancelado' => 'Cancelado',
                            ]),

                        Select::make('metodo_pago')
                            ->label('Método de pago')
                            ->required()
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                                'mixto' => 'Mixto',
                            ]),

                        Textarea::make('notas')
                            ->columnSpanFull(),

                    ]),

                Section::make('Totales y Descuentos')
                    ->columns(2)
                    ->schema([

                        TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),

                        TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),

                        TextInput::make('descuento_puntos')
                            ->label('Descuento puntos ($)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->disabled(fn () => auth()->user()?->isCajero())
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                        TextInput::make('descuento_manual')
                            ->label('Descuento aplicado ($)')
                            ->numeric()
                            ->disabled(),

                        Select::make('descuento_manual_tipo')
                            ->label('Tipo descuento manual')
                            ->options([
                                '' => 'Sin descuento',
                                'monto' => 'Monto fijo ($)',
                                'porcentaje' => 'Porcentaje (%)',
                            ])
                            ->live()
                            ->hidden(fn () => auth()->user()?->isCajero())
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                        TextInput::make('descuento_manual_valor')
                            ->label('Valor')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->hidden(fn () => auth()->user()?->isCajero())
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    ]),

            ]);
    }

    private static function recalcularTotal(callable $set, callable $get): void
    {
        $subtotal = (float) ($get('subtotal') ?? 0);
        $descuentoPuntos = (float) ($get('descuento_puntos') ?? 0);
        $tipo = $get('descuento_manual_tipo');
        $valor = (float) ($get('descuento_manual_valor') ?? 0);

        if ($tipo === 'porcentaje') {
            $descuentoManual = round($subtotal * $valor / 100, 2);
        } elseif ($tipo === 'monto') {
            $descuentoManual = $valor;
        } else {
            $descuentoManual = 0;
        }

        $set('descuento_manual', $descuentoManual);
        $set('total', max(0, $subtotal - $descuentoPuntos - $descuentoManual));
    }
}
