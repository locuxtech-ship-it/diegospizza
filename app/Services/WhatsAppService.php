<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.waha.base_url', 'http://waha:3000');
        $this->apiKey = config('services.waha.api_key');
    }

    protected function withAuth(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::timeout(10);
        if ($this->apiKey) {
            $client->withHeader('X-Api-Key', $this->apiKey);
        }
        return $client;
    }

    public function sendText(string $chatId, string $message): bool
    {
        $response = $this->withAuth()->post("{$this->baseUrl}/api/sendText", [
            'session' => 'default',
            'chatId' => $chatId,
            'text' => $message,
        ]);

        if ($response->failed()) {
            Log::error('WAHA sendText error', ['chatId' => $chatId, 'response' => $response->body()]);
            return false;
        }

        return true;
    }

    public function getStatus(): array
    {
        try {
            $response = $this->withAuth()->timeout(5)->get("{$this->baseUrl}/api/sessions/default");
            if ($response->successful()) {
                return $response->json() ?? ['status' => 'DISCONNECTED'];
            }
        } catch (\Exception $e) {
            Log::warning('WAHA status check failed', ['error' => $e->getMessage()]);
        }
        return ['status' => 'DISCONNECTED'];
    }

    public function getQR(): ?string
    {
        try {
            $response = $this->withAuth()->get("{$this->baseUrl}/api/sessions/default/qr");
            if ($response->successful()) {
                $data = $response->json();
                return $data['qr'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('WAHA QR fetch failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    public function logout(): bool
    {
        try {
            $response = $this->withAuth()->delete("{$this->baseUrl}/api/sessions/default");
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WAHA logout failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function startSession(): bool
    {
        try {
            $response = $this->withAuth()->post("{$this->baseUrl}/api/sessions/default", []);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WAHA start session failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
