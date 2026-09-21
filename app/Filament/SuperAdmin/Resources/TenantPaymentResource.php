<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Models\TenantPayment;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TenantPaymentResource extends Resource
{
    protected static ?string $model = TenantPayment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Pagos de planes';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pagos de planes';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('tenant_id')
                    ->label('Negocio')
                    ->relationship('tenant', 'nombre_negocio')
                    ->preload()
                    ->searchable()
                    ->required(),
                \Filament\Forms\Components\Select::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'nombre')
                    ->preload(),
                \Filament\Forms\Components\TextInput::make('monto')
                    ->numeric()
                    ->required(),
                \Filament\Forms\Components\Select::make('metodo')
                    ->options([
                        'transferencia' => 'Transferencia',
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('referencia')
                    ->label('Referencia'),
                \Filament\Forms\Components\Select::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('periodo')
                    ->label('Período (ej: 2026-09)'),
                \Filament\Forms\Components\Textarea::make('notas')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tenant.nombre_negocio')
                    ->label('Negocio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.nombre')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (?\App\Models\Plan $state): string => match ($state?->slug) {
                        'pro' => 'primary',
                        'negocio' => 'warning',
                        'arranque' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('COP'),
                Tables\Columns\TextColumn::make('metodo')
                    ->label('Método'),
                Tables\Columns\TextColumn::make('periodo')
                    ->label('Período'),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aprobado' => 'success',
                        'pendiente' => 'warning',
                        'rechazado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pagado_en')
                    ->label('Pagado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TenantPayment $record): bool => $record->estado !== 'aprobado')
                    ->action(fn (TenantPayment $record) => $record->aprobar()),
                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (TenantPayment $record): bool => $record->estado === 'pendiente')
                    ->action(fn (TenantPayment $record) => $record->rechazar()),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\SuperAdmin\Resources\TenantPaymentResource\Pages\ListTenantPayments::route('/'),
            'create' => \App\Filament\SuperAdmin\Resources\TenantPaymentResource\Pages\CreateTenantPayment::route('/create'),
            'edit' => \App\Filament\SuperAdmin\Resources\TenantPaymentResource\Pages\EditTenantPayment::route('/{record}/edit'),
        ];
    }
}
