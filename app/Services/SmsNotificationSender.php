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

        // Debug: Check if API key is loaded
        Log::info("Loaded API KEY: " . ($apiKey ? '***' . substr($apiKey, -4) : 'EMPTY OR NOT SET'));

        if (empty($apiKey)) {
            Log::error("Semaphore API key is not set in environment variables");
            return false;
        }

        $successCount = 0;

        foreach ($numbers as $number) {
            try {
                $response = Http::timeout(30)->post('https://api.semaphore.co/api/v4/messages', [
                    'apikey' => $apiKey,
                    'number' => $number,
                    'message' => $message,
                    'sendername' => $sender,
                ]);

                $responseData = $response->json();

                if ($response->successful()) {
                    // Check if the response indicates success
                    if (isset($responseData[0]['status']) && $responseData[0]['status'] === 'Pending') {
                        Log::info("SMS sent successfully", [
                            'number' => $number,
                            'message_id' => $responseData[0]['message_id'] ?? 'unknown',
                        ]);
                        $successCount++;
                    } else {
                        Log::error("SMS API returned error", [
                            'number' => $number,
                            'response' => $responseData,
                        ]);
                    }
                } else {
                    Log::error("SMS failed to send - API error", [
                        'number' => $number,
                        'status' => $response->status(),
                        'error' => $response->body(),
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("SMS Exception for {$number}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $successCount > 0;
    }
}