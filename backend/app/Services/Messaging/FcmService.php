<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send push notification to a device token. No-op if token/config missing.
     */
    public function sendToToken(?string $token, string $title, string $body, array $data = []): void
    {
        if (empty($token)) {
            Log::debug('FcmService: no token provided');
            return;
        }
        $serverKey = config('services.fcm.server_key');
        if (!$serverKey) {
            Log::debug('FcmService: no server key configured');
            return;
        }
        // Example: use curl/http client to send to FCM here
        Log::info('FcmService: sending (mock) to token '.$token.' : '.$title.' - '.$body);
    }
}
