<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WhatsAppController;
use App\Livewire\Checkout;
use App\Livewire\Menu;
use Illuminate\Support\Facades\Route;

Route::get('/', Menu::class)->name('menu');
Route::get('/checkout', Checkout::class)->name('checkout');

Route::post('/admin/configuracion', [AdminController::class, 'saveConfig'])->name('admin.configuracion.save');
Route::get('/admin/ticket/cierre/{cierre}', [TicketController::class, 'cierre'])->name('ticket.cierre');
Route::get('/admin/ticket/{pedido}', [TicketController::class, 'show'])->name('admin.ticket');
Route::get('/admin/ticket/{pedido}/raw', [TicketController::class, 'raw'])->name('admin.ticket.raw');
Route::get('/admin/print-config', function () {
    $s = App\Models\NegocioSetting::getSettings();
    return response()->json([
        'printer' => $s->impresora_nombre,
        'auto_print' => $s->imprimir_automaticamente ?? false,
    ]);
});

Route::match(['GET', 'POST'], '/api/whatsapp/webhook', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');

Route::get('/api/pedidos/pendientes', function () {
    if (!auth()->check()) {
        return response()->json(['count' => 0, 'pedidos' => []]);
    }
    $pedidos = App\Models\Pedido::with('cliente')
        ->where('estado', 'pendiente_pago')
        ->whereDate('created_at', today())
        ->orderBy('created_at')
        ->get();
    return response()->json([
        'count' => $pedidos->count(),
        'pedidos' => $pedidos->toArray(),
    ]);
});

Route::get('/admin/clientes/exportar', function () {
    $user = auth()->user();
    if (!$user || !in_array($user->role, ['admin', 'cajero'])) {
        abort(403);
    }
    $clientes = App\Models\Cliente::withCount('pedidos')->get();
    $callback = function () use ($clientes) {
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // BOM UTF-8
        fputcsv($output, ['Nombre', 'Teléfono', 'Dirección', 'Conjunto', 'Torre', 'Apto', 'Email', 'Notas', 'Pedidos', 'Puntos', 'Clasificación', 'Fecha Registro']);
        foreach ($clientes as $c) {
            fputcsv($output, [
                $c->nombre,
                $c->telefono,
                $c->direccion,
                $c->conjunto,
                $c->torre,
                $c->apto,
                $c->email,
                $c->notas,
                $c->pedidos_count,
                $c->puntos_acumulados,
                strip_tags($c->clasificacion_label ?? ''),
                $c->created_at?->format('d/m/Y'),
            ]);
        }
        fclose($output);
    };
    return response()->streamDownload($callback, 'clientes_' . now()->format('Y-m-d_His') . '.csv', [
        'Content-Type' => 'text/csv; charset=utf-8',
    ]);
})->name('admin.clientes.exportar');