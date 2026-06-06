<?php

namespace App\Filament\Resources\Pedidos\Tables;

use App\Models\Pedido;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.id')
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('descuento_puntos')
                    ->label('Desc. puntos')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('descuento_manual')
                    ->label('Desc. manual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('origen')
                    ->label('Origen')
                    ->searchable(),
                TextColumn::make('estado')
                    ->searchable(),
                TextColumn::make('metodo_pago')
                    ->searchable(),
                TextColumn::make('fecha_entrega')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge)
                    ->form([
                        Select::make('cliente_id')
                            ->relationship('cliente', 'id')
                            ->required(),
                        TextInput::make('subtotal')
                            ->required()
                            ->numeric(),
                        TextInput::make('descuento_puntos')
                            ->label('Desc. puntos')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->disabled(fn () => auth()->user()?->isCajero()),
                        Select::make('descuento_manual_tipo')
                            ->label('Descuento manual')
                            ->options([
                                '' => 'Sin descuento',
                                'monto' => 'Monto fijo ($)',
                                'porcentaje' => 'Porcentaje (%)',
                            ])
                            ->hidden(fn () => auth()->user()?->isCajero()),
                        TextInput::make('descuento_manual_valor')
                            ->label('Valor descuento')
                            ->numeric()
                            ->default(0)
                            ->hidden(fn () => auth()->user()?->isCajero()),
                        TextInput::make('descuento_manual')
                            ->label('Desc. manual aplicado')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('total')
                            ->required()
                            ->numeric(),
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
                                'finalizado' => 'Finalizado',
                                'cancelado' => 'Cancelado',
                            ]),
                        Select::make('metodo_pago')
                            ->required()
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                                'mixto' => 'Mixto',
                            ]),
                        Textarea::make('notas')
                            ->columnSpanFull(),
                        Textarea::make('motivo_cancelacion')
                            ->columnSpanFull(),
                        DateTimePicker::make('fecha_entrega'),
                    ])
                    ->using(function (Pedido $record, array $data): void {
                        if (auth()->user()?->isCajero()) {
                            $record->update(collect($data)->only([
                                'cliente_id', 'origen', 'estado', 'metodo_pago',
                                'notas', 'motivo_cancelacion', 'fecha_entrega',
                            ])->toArray());
                            return;
                        }
                        $subtotal = (float) ($data['subtotal'] ?? 0);
                        $descuentoPuntos = (float) ($data['descuento_puntos'] ?? 0);
                        $tipo = $data['descuento_manual_tipo'] ?? null;
                        $valor = (float) ($data['descuento_manual_valor'] ?? 0);
                        if ($tipo === 'porcentaje') {
                            $data['descuento_manual'] = round($subtotal * $valor / 100, 2);
                        } elseif ($tipo === 'monto') {
                            $data['descuento_manual'] = $valor;
                        } else {
                            $data['descuento_manual'] = 0;
                        }
                        $data['total'] = max(0, $subtotal - $descuentoPuntos - $data['descuento_manual']);
                        $record->update($data);
                    })
                    ->visible(fn (): bool => true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
