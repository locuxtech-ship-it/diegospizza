<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
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

Route::get('/api/agent/ticket/{pedido}', function (App\Models\Pedido $pedido) {
    $key = request('key');
    $expectedKey = config('services.print_agent.key');
    if (!$expectedKey || $key !== $expectedKey) {
        return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
    }
    $pedido->load('cliente');
    $productos = App\Models\PedidoProducto::with('producto')->where('pedido_id', $pedido->id)->get();
    $negocio = App\Models\NegocioSetting::getSettings();
    return response()->view('ticket', compact('pedido', 'productos', 'negocio'))->header('Content-Type', 'text/html; charset=utf-8');
})->name('api.agent.ticket');

Route::get('/page/print-monitor', function () {
    $key = request('key');
    $expectedKey = config('services.print_agent.key');
    if (!$expectedKey || $key !== $expectedKey) { return response('Unauthorized', 403); }
    return response()->view('print-monitor-page');
});

Route::post('/api/agent/guardar-ultimo-id', function () {
    $key = request('key');
    $expectedKey = config('services.print_agent.key');
    if (!$expectedKey || $key !== $expectedKey) { return response()->json(['ok' => false], 403); }
    $id = (int) request('id', 0);
    if ($id > 0) {
        file_put_contents(storage_path('app/print-monitor-id.txt'), $id);
    }
    return response()->json(['ok' => true]);
});

Route::match(['GET', 'POST'], '/api/whatsapp/webhook', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');
Route::post('/api/whatsapp/waha-webhook', [WhatsAppController::class, 'wahaWebhook'])->name('whatsapp.waha-webhook');

Route::get('/review/{numero}', [ReviewController::class, 'showForm'])->name('review.form');
Route::post('/review/{numero}', [ReviewController::class, 'store'])->name('review.store');

Route::get('/api/agent/pendientes', function () {
    $key = request('key');
    $expectedKey = config('services.print_agent.key');
    if (!$expectedKey || $key !== $expectedKey) {
        return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
    }
    $afterId = (int) request('after_id', 0);
    $pedidos = App\Models\Pedido::with('cliente')
        ->where('estado', 'pendiente_pago')
        ->whereDate('created_at', today())
        ->where('id', '>', $afterId)
        ->orderBy('created_at')
        ->get();
    $orders = [];
    foreach ($pedidos as $p) {
        $controller = new App\Http\Controllers\TicketController();
        $rawResponse = $controller->raw($p);
        $orders[] = [
            'id' => $p->id,
            'numero_pedido' => $p->numero_pedido,
            'raw_text' => $rawResponse->getContent(),
        ];
    }
    return response()->json(['ok' => true, 'orders' => $orders]);
});

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