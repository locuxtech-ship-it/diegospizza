<?php

namespace App\Filament\Pages;

use App\Models\NegocioSetting;
use App\Services\WhatsAppService;
use Filament\Pages\Page;
use UnitEnum;

class ChatBot extends Page
{
    protected string $view = 'filament.pages.chat-bot';
    protected static ?string $slug = 'chat-bot';
    protected static ?string $title = 'Chat Bot';
    protected static ?string $navigationLabel = 'Chat Bot';
    protected static UnitEnum|string|null $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public $status = 'DISCONNECTED';
    public $pushName = '';
    public $qrCode = null;
    public $mensaje = '';

    public $enabled = false;
    public $welcome_enabled = true;
    public $welcome_message = '';
    public $menu_options = [];
    public $order_notifications = true;
    public $notifications = [];
    public $review_enabled = false;
    public $review_message = '';

    public function mount(): void
    {
        $settings = NegocioSetting::getSettings();
        $chatbot = $settings->chatbot_settings ?? [];

        $this->enabled = $chatbot['enabled'] ?? false;
        $this->welcome_enabled = $chatbot['welcome_enabled'] ?? true;
        $this->welcome_message = $chatbot['welcome_message'] ?? "Hola {nombre}\n¡Bienvenido/a a {empresa}! 🍕\n\n¿Cómo podemos ayudarte hoy?";
        $this->menu_options = $chatbot['menu_options'] ?? [
            ['key' => 'A', 'label' => 'Realizar un pedido 🍽️'],
            ['key' => 'B', 'label' => 'Obtener más información ℹ️'],
        ];
        $this->order_notifications = $chatbot['order_notifications'] ?? true;
        $this->notifications = $chatbot['notifications'] ?? [
            'pendiente_pago' => '📦 Pedido N° {numero} fue recibido. ¡Lo estamos preparando!',
            'en_proceso' => '👨‍🍳 Pedido N° {numero} está siendo preparado.',
            'en_camino' => '🛵 Pedido N° {numero} está en camino.',
            'entregado' => 'El pedido N° {numero} fue entregado. ¡Gracias por preferirnos! 🍕',
            'cancelado' => '❌ Pedido N° {numero} fue cancelado.',
        ];

        $this->review_enabled = $chatbot['review_enabled'] ?? false;
        $this->review_message = $chatbot['review_message'] ?? "Gracias por tu pedido {nombre}!\nDejanos tu reseña aqui: {link} 🍕";

        $this->checkStatus();
    }

    public function checkStatus(): void
    {
        $waha = app(WhatsAppService::class);
        $data = $waha->getStatus();
        $this->status = $data['status'] ?? 'DISCONNECTED';
        $this->pushName = $data['pushName'] ?? '';
        if ($this->status === 'SCAN_QR_CODE' && !$this->qrCode) {
            $this->qrCode = $waha->getQR();
        }
    }

    public function showQR(): void
    {
        $waha = app(WhatsAppService::class);
        $this->status = 'WAITING_QR';
        $waha->startSession();
        $this->pollQR();
    }

    public function pollQR(): void
    {
        if ($this->status !== 'WAITING_QR') return;
        $waha = app(WhatsAppService::class);
        $data = $waha->getStatus();
        if (($data['status'] ?? '') === 'SCAN_QR_CODE') {
            $this->qrCode = $waha->getQR();
            if ($this->qrCode) {
                $this->status = 'SCAN_QR_CODE';
            }
        }
    }

    public function logout(): void
    {
        $waha = app(WhatsAppService::class);
        $waha->logout();
        $this->qrCode = null;
        $this->status = 'DISCONNECTED';
        $this->mensaje = '🔌 Sesión cerrada. Escaneá el QR para reconectar.';
    }

    public function addMenuOption(): void
    {
        $this->menu_options[] = ['key' => '', 'label' => ''];
    }

    public function removeMenuOption(int $index): void
    {
        unset($this->menu_options[$index]);
        $this->menu_options = array_values($this->menu_options);
    }

    public function save(): void
    {
        $this->validate([
            'welcome_message' => 'required',
            'menu_options.*.key' => 'required',
            'menu_options.*.label' => 'required',
            'review_message' => 'required_if:review_enabled,true',
        ]);

        $chatbot = [
            'enabled' => $this->enabled,
            'welcome_enabled' => $this->welcome_enabled,
            'welcome_message' => $this->welcome_message,
            'menu_options' => $this->menu_options,
            'order_notifications' => $this->order_notifications,
            'notifications' => $this->notifications,
            'review_enabled' => $this->review_enabled,
            'review_message' => $this->review_message,
        ];

        NegocioSetting::first()->update([
            'chatbot_settings' => $chatbot,
        ]);

        $this->mensaje = '✅ Configuración del Chat Bot guardada correctamente';
    }
}
