<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationSender
{
    public static function send($message, $numbers = [])
    {
        if (empty($numbers)) {
            Log::warning("SMS sending skipped: no numbers provided.");
            return false;
        }

        $apiKey = env('SEMAPHORE_API_KEY');
        $sender = env('SEMAPHORE_SENDER_NAME', 'SEMAPHORE');

        Log::info("Loaded API KEY: " . $apiKey);

        foreach ($numbers as $number) {
            $response = Http::post('https://api.semaphore.co/api/v4/messages', [
                'apikey' => $apiKey,
                'number' => $number,
                'message' => $message,
                'sendername' => $sender,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Log success with details
                Log::info(" SMS sent successfully", [
                    'number' => $number,
                    'message' => $message,
                    'response' => $data,
                ]);
            } else {
                // Log failure with status + body
                Log::error(" SMS failed to send", [
                    'number' => $number,
                    'message' => $message,
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
            }
        }

        return true;
    }
}
