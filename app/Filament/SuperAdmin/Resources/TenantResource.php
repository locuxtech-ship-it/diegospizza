<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Models\Tenant;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|UnitEnum|null $navigationGroup = 'Administración';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('dominio')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Dominio'),
                TextInput::make('nombre_negocio')
                    ->required()
                    ->label('Nombre del negocio'),
                TextInput::make('db_name')
                    ->required()
                    ->label('Nombre DB'),
                Select::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->required(),
                FileUpload::make('logo')
                    ->image()
                    ->directory('tenant-logos'),
                KeyValue::make('colores')
                    ->label('Colores de marca'),
                KeyValue::make('config')
                    ->label('Configuración extra'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dominio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre_negocio')
                    ->searchable()
                    ->sortable()
                    ->label('Negocio'),
                Tables\Columns\TextColumn::make('db_name')
                    ->label('DB'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\SuperAdmin\Resources\TenantResource\Pages\ListTenants::route('/'),
            'create' => \App\Filament\SuperAdmin\Resources\TenantResource\Pages\CreateTenant::route('/create'),
            'edit' => \App\Filament\SuperAdmin\Resources\TenantResource\Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
