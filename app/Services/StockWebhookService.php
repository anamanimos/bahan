<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StockWebhookService
{
    /**
     * Send stock change data to all active webhooks.
     * 
     * @param string $action (create, update, delete)
     * @param string $source (sale, purchase, adjustment)
     * @param mixed $data
     * @return bool
     */
    public static function notify($action, $source, $data)
    {
        $webhooks = \App\Models\Webhook::where('is_active', true)->get();

        if ($webhooks->isEmpty()) {
            // Fallback to .env if no DB webhooks (optional, for backward compatibility)
            $envUrl = config('services.erp.webhook_url');
            if ($envUrl) {
                return self::send($envUrl, null, $action, $source, $data);
            }
            return false;
        }

        foreach ($webhooks as $webhook) {
            self::send($webhook->url, $webhook->secret, $action, $source, $data);
        }

        return true;
    }

    private static function send($url, $secret, $action, $source, $data)
    {
        try {
            $payload = [
                'timestamp' => now()->toIso8601String(),
                'action' => $action,
                'source' => $source,
                'data' => $data
            ];

            $request = Http::withHeaders([
                'X-Source-App' => config('app.name'),
                'Accept' => 'application/json'
            ]);

            if ($secret) {
                $request->withHeaders(['X-Webhook-Secret' => $secret]);
            }

            $response = $request->post($url, $payload);

            if ($response->successful()) {
                Log::info("Webhook Sent to {$url}: {$source} - {$action}");
                return true;
            }

            Log::error("Webhook Failed to {$url}: {$response->status()}");
            return false;
        } catch (\Exception $e) {
            Log::error("Webhook Exception for {$url}: " . $e->getMessage());
            return false;
        }
    }
}
