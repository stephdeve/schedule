<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message. No-op if number or provider config missing.
     */
    public function send(?string $to, string $message): void
    {
        if (empty($to)) {
            Log::debug('SmsService: no destination');
            return;
        }
        $provider = config('services.sms.provider');
        if (!$provider) {
            Log::debug('SmsService: no provider configured');
            return;
        }
        // Example: integrate with Twilio/Vonage here using env creds
        Log::info('SmsService: sending (mock) to '.$to.' : '.$message);
    }
}
