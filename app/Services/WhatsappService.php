<?php
namespace App\Services;

use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsappService
 *
 * Handles WhatsApp operations via Waajo API
 *
 * @package App\Services
 */
class WhatsappService
{
    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var bool
     */
    protected $isModeSafe;

    /**
     * WhatsappService constructor
     * Load settings from database
     */
    public function __construct()
    {
        $this->apiUrl = WhatsappSetting::getValue('api_url');
        $this->apiKey = WhatsappSetting::getValue('api_key');
        $this->isModeSafe = WhatsappSetting::getValue('is_mode_safe', true);
    }

    /**
     * Send WhatsApp text message via Waajo API
     * 
     * @param string $recipientNumber Phone number (will be formatted to +62)
     * @param string $text Message content
     * @return array ['success' => bool, 'data' => mixed, 'error' => string|null, 'message' => string]
     */
    public function sendText(string $recipientNumber, string $text): array
    {
        try {
            // Format phone number to international format
            $formattedPhone = $this->formatPhoneNumber($recipientNumber);

            // Prepare request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/send-text', [
                'recipient_number' => $formattedPhone,
                'text' => $text,
                'is_mode_safe' => $this->isModeSafe,
            ]);

            // Handle response
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Message sent successfully',
                ];
            }

            // Handle failed response
            return [
                'success' => false,
                'error' => $response->body(),
                'message' => 'Failed to send message',
            ];
        } catch (\Exception $e) {
            // Log error with context
            Log::error('[WhatsApp] Send failed', [
                'phone' => $recipientNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Exception occurred while sending message',
            ];
        }
    }

    /**
     * Format phone number to international format (+62)
     * 
     * @param string $phone Phone number to format
     * @return string Formatted phone number (e.g., 6281234567890)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Apply formatting rules
        if (str_starts_with($phone, '0')) {
            // Remove 0, add 62 prefix
            return '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            // Already has 62 prefix
            return $phone;
        } else {
            // Add 62 prefix
            return '62' . $phone;
        }
    }

    /**
     * Check WhatsApp API health status
     * 
     * @return \Illuminate\Http\Client\Response
     */
    public function health()
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->post($this->apiUrl . '/is_online', [
                'apikey' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                Log::warning('[WhatsApp] Health check failed', [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('[WhatsApp] Health check exception', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate if a phone number is registered on WhatsApp
     * 
     * @param string $phoneNumber Phone number to check
     * @return string Response body
     */
    public function checkNumber(string $phoneNumber)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->post($this->apiUrl . '/validate_number', [
                'phone_number' => $formattedPhone,
            ]);

            if (!$response->successful()) {
                Log::warning('[WhatsApp] Number validation failed', [
                    'phone' => $formattedPhone,
                    'status' => $response->status(),
                ]);
            }

            return $response->body();
        } catch (\Exception $e) {
            Log::error('[WhatsApp] Number validation exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
