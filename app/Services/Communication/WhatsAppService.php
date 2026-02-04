<?php

namespace App\Services\Communication;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        // These should be in .env in a real scenario
        $this->apiUrl = config('services.whatsapp.url', 'https://api.whatsapp.gateway/v1');
        $this->apiKey = config('services.whatsapp.key');
    }

    /**
     * Send a WhatsApp message.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (empty($this->apiKey) || empty($phone)) {
            Log::warning("WhatsApp notification skipped: Missing API key or phone number.");
            return false;
        }

        try {
            $response = $this->client->post("{$this->apiUrl}/send", [
                'json' => [
                    'phone' => $phone,
                    'message' => $message,
                    'key' => $this->apiKey
                ],
                'timeout' => 10
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error("WhatsApp API Error: " . $e->getMessage());
            return false;
        }
    }
}
