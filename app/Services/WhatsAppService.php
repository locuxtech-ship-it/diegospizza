<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->apiUrl = "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages";
    }

    public function sendText(string $to, string $message): bool
    {
        $response = Http::withToken($this->token)->post($this->apiUrl, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message],
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp send error', ['response' => $response->body()]);
            return false;
        }

        return true;
    }

    public function sendInteractiveButtons(string $to, string $header, string $body, array $buttons): bool
    {
        $btnList = [];
        foreach ($buttons as $id => $title) {
            $btnList[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => (string) $id,
                    'title' => mb_substr($title, 0, 20),
                ],
            ];
        }

        $response = Http::withToken($this->token)->post($this->apiUrl, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'header' => ['type' => 'text', 'text' => mb_substr($header, 0, 60)],
                'body' => ['text' => mb_substr($body, 0, 1024)],
                'action' => [
                    'buttons' => $btnList,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp interactive error', ['response' => $response->body()]);
            return false;
        }

        return true;
    }
}
