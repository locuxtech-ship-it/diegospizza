<?php

namespace App\Http\Controllers;

use App\Models\NegocioSetting;
use App\Models\Pedido;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppController extends Controller
{
    public function webhook(Request $request, WhatsAppService $whatsapp): Response
    {
        if ($request->isMethod('GET')) {
            return $this->verify($request);
        }
        return $this->handleIncoming($request, $whatsapp);
    }

    public function wahaWebhook(Request $request): Response
    {
        $settings = NegocioSetting::getSettings();
        $chatbot = $settings->chatbot_settings ?? [];

        if (!($chatbot['enabled'] ?? false)) {
            return response('OK', 200);
        }

        $event = $request->input('event');
        if ($event !== 'message') {
            return response('OK', 200);
        }

        $payload = $request->input('payload', []);
        $fromMe = $payload['fromMe'] ?? false;

        if ($fromMe) {
            return response('OK', 200);
        }

        $from = $payload['from'] ?? '';
        $text = strtolower(trim($payload['body'] ?? ''));
        $name = $payload['notifyName'] ?? '';

        if (empty($from) || empty($text)) {
            return response('OK', 200);
        }

        $whatsapp = app(WhatsAppService::class);
        $this->processMessage($from, $text, $name, $whatsapp, $settings, $chatbot);

        return response('OK', 200);
    }

    protected function processMessage(string $from, string $text, string $name, WhatsAppService $whatsapp, NegocioSetting $settings, array $chatbot): void
    {
        $empresa = $settings->nombre_negocio ?? "Diego's Pizza";

        if (in_array($text, ['hola', 'buenas', 'hi', 'hello', 'info', 'menú', 'menu', 'inicio'])) {
            $welcome = $chatbot['welcome_message'] ?? "Hola {nombre}\n¡Bienvenido/a a {empresa}! 🍕\n\n¿Cómo podemos ayudarte hoy?";
            $welcome = str_replace(['{nombre}', '{empresa}'], [$name, $empresa], $welcome);
            $whatsapp->sendText($from, $welcome);

            $options = $chatbot['menu_options'] ?? [
                ['key' => 'A', 'label' => 'Realizar un pedido 🍽️'],
                ['key' => 'B', 'label' => 'Obtener más información ℹ️'],
            ];
            $menuText = "Seleccioná la letra de la opción y envíala como respuesta:\n";
            foreach ($options as $opt) {
                $menuText .= "\n{$opt['key']}. {$opt['label']}";
            }
            $whatsapp->sendText($from, $menuText);
        } elseif ($text === 'a') {
            $whatsapp->sendText($from, "Visitá nuestro menú digital y hacé tu pedido:\nhttps://diegospizzabq.click 🍕");
        } elseif ($text === 'b') {
            $horarios = $settings->horario_apertura ?? '11:00 AM';
            $cierre = $settings->horario_cierre ?? '10:00 PM';
            $telefono = $settings->telefono ?? '3106444759';
            $whatsapp->sendText($from, "📍 *Dirección:* {$settings->direccion}\n🕐 *Horario:* {$horarios} - {$cierre}\n📞 *Teléfono:* {$telefono}\n\n¡Gracias por comunicarte con nosotros! 🍕");
        } elseif (str_starts_with($text, 'pedido') || preg_match('/^\d{1,6}$/', $text)) {
            $numero = preg_replace('/[^0-9]/', '', $text);
            $this->sendOrderStatus($from, $numero, $whatsapp);
        } else {
            $whatsapp->sendText($from, "No entendí tu mensaje. Escribí *Hola* para comenzar.");
        }
    }

    protected function sendOrderStatus(string $from, string $numero, WhatsAppService $whatsapp): void
    {
        if (empty($numero)) {
            $whatsapp->sendText($from, "Escribí el número de tu pedido. Ej: *123*");
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

    protected function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    protected function handleIncoming(Request $request, WhatsAppService $whatsapp): Response
    {
        $entry = $request->input('entry.0');
        $change = $entry['changes'][0]['value'] ?? [];

        if (empty($change) || !isset($change['messages'])) {
            return response('OK', 200);
        }

        $settings = NegocioSetting::getSettings();
        $chatbot = $settings->chatbot_settings ?? [];

        foreach ($change['messages'] as $message) {
            $from = $message['from'];
            $type = $message['type'] ?? '';
            $text = '';

            if ($type === 'text') {
                $text = strtolower(trim($message['text']['body']));
            } elseif ($type === 'interactive' && ($message['interactive']['type'] ?? '') === 'button_reply') {
                $text = strtolower($message['interactive']['button_reply']['id'] ?? '');
            }

            if (!empty($text)) {
                $name = $from;
                $this->processMessage($from, $text, $name, $whatsapp, $settings, $chatbot);
            }
        }

        return response('OK', 200);
    }
}
