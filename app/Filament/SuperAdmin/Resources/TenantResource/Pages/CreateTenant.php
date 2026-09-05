<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use App\Services\TenantService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Config;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function afterCreate(): void
    {
        $tenant = $this->record;
        $originalDefault = config('database.default');

        try {
            $service = app(TenantService::class);
            $service->runMigrations($tenant);
            $service->seedDefaults($tenant->nombre_negocio);
        } finally {
            Config::set('database.default', $originalDefault);
        }
    }
}
