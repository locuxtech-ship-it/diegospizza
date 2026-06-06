<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $negocio = App\Models\NegocioSetting::first() ?? new App\Models\NegocioSetting();
    $rendered = view('filament.pages.configuracion', ['negocio' => $negocio])->render();
    echo 'RENDERED OK: ' . strlen($rendered) . ' bytes' . PHP_EOL;
    // Check if it contains the sidebar component
    if (str_contains($rendered, 'filament-sidebar')) {
        echo 'SIDEBAR: PRESENT' . PHP_EOL;
    } else {
        echo 'SIDEBAR: NOT FOUND' . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
