<?php

namespace App\Filament\SuperAdmin\Resources\TenantPaymentResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListTenantPayments extends ListRecords
{
    protected static string $resource = TenantPaymentResource::class;
}
