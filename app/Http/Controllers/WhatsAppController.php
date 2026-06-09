<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function webhook(Request $request, WhatsAppService $whatsapp)
    {
        if ($request->isMethod('GET')) {
            return $this->verify($request);
        }

        return $this->handleIncoming($request, $whatsapp);
    }

    protected function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    protected function handleIncoming(Request $request, WhatsAppService $whatsapp)
    {
        $entry = $request->input('entry.0');
        $change = $entry['changes'][0]['value'] ?? [];

        if (empty($change) || !isset($change['messages'])) {
            return response('OK', 200);
        }

        foreach ($change['messages'] as $message) {
            $from = $message['from'];
            $type = $message['type'] ?? '';

            if ($type === 'text') {
                $text = strtolower(trim($message['text']['body']));
                $this->handleTextMessage($from, $text, $whatsapp);
            } elseif ($type === 'interactive' && ($message['interactive']['type'] ?? '') === 'button_reply') {
                $replyId = $message['interactive']['button_reply']['id'] ?? '';
                $this->handleButtonReply($from, $replyId, $whatsapp);
            }
        }

        return response('OK', 200);
    }

    protected function handleTextMessage(string $from, string $text, WhatsAppService $whatsapp): void
    {
        if (in_array($text, ['hola', 'buenas', 'hi', 'hello', 'info', 'menú', 'menu', 'inicio'])) {
            $whatsapp->sendText($from, "¡Bienvenido a Diego's Pizza Alameda! 🍕\n\n¿En qué puedo ayudarte?");
            $this->sendMenu($from, $whatsapp);
        } elseif (str_starts_with($text, 'pedido') || preg_match('/^\d{1,6}$/', $text)) {
            $numero = preg_replace('/[^0-9]/', '', $text);
            $this->sendOrderStatus($from, $numero, $whatsapp);
        } else {
            $whatsapp->sendText($from, "No entendí tu mensaje. Usa los botones o escribe 'Hola' para comenzar.");
            $this->sendMenu($from, $whatsapp);
        }
    }

    protected function handleButtonReply(string $from, string $replyId, WhatsAppService $whatsapp): void
    {
        if ($replyId === 'menu') {
            $whatsapp->sendText($from, "Visita nuestro menu digital:\nhttps://diegospizzabq.click");
        } elseif ($replyId === 'horarios') {
            $whatsapp->sendText($from, "Nuestro horario:\nLunes a Domingo\n12:00 PM - 10:00 PM");
        } elseif ($replyId === 'pedido') {
            $whatsapp->sendText($from, "Escribe el numero de tu pedido. Ej: *42*");
        }
    }

    protected function sendMenu(string $from, WhatsAppService $whatsapp): void
    {
        $whatsapp->sendInteractiveButtons(
            $from,
            'Diego\'s Pizza',
            'Selecciona una opción:',
            ['menu' => '🍕 Menú', 'horarios' => '🕐 Horarios', 'pedido' => '📦 Mi Pedido']
        );
    }

    protected function sendOrderStatus(string $from, string $numero, WhatsAppService $whatsapp): void
    {
        if (empty($numero)) {
            $whatsapp->sendText($from, "Escribe el número de tu pedido. Ej: *123*");
            return;
        }

        $pedido = Pedido::where('numero_pedido', (int) $numero)->first();

        if (!$pedido) {
            $whatsapp->sendText($from, "No encontré un pedido con el número *{$numero}*.");
            return;
        }

        $estados = [
            'pendiente_pago' => '⏳ Pendiente de Pago',
            'en_proceso' => '👨‍🍳 En Preparación',
            'en_camino' => '🛵 En Camino',
            'entregado' => '✅ Entregado',
            'finalizado' => '✅ Finalizado',
            'cancelado' => '❌ Cancelado',
        ];

        $estado = $estados[$pedido->estado] ?? $pedido->estado;
        $total = '$ ' . number_format($pedido->total, 0, ',', '.');

        $whatsapp->sendText($from, "Pedido #{$pedido->numero_pedido}\nEstado: {$estado}\nTotal: {$total}\n\n¡Gracias por preferirnos! 🍕");
    }
}
