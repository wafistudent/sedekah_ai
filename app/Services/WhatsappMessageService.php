<?php

namespace App\Services;

use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Log;

/**
 * WhatsappMessageService
 *
 * CORE ORCHESTRATOR - coordinates all message sending operations
 *
 * @package App\Services
 */
class WhatsappMessageService
{
    /**
     * @var WhatsappService
     */
    protected $whatsappService;

    /**
     * @var WhatsappTemplateService
     */
    protected $templateService;

    /**
     * WhatsappMessageService constructor
     * 
     * @param WhatsappService $whatsappService
     * @param WhatsappTemplateService $templateService
     */
    public function __construct(
        WhatsappService $whatsappService,
        WhatsappTemplateService $templateService
    ) {
        $this->whatsappService = $whatsappService;
        $this->templateService = $templateService;
    }

    /**
     * Queue message by template code (for auto notifications)
     * 
     * @param string $templateCode Template code to use
     * @param string $phone Recipient phone number
     * @param array $data Data to replace template variables
     * @param array $metadata Additional metadata for the log
     * @return WhatsappLog|null WhatsappLog object if successful, null if failed
     */
    public function sendByTemplate(
        string $templateCode,
        string $phone,
        array $data = [],
        array $metadata = []
    ): ?WhatsappLog {
        try {
            // Find template in DB
            $template = WhatsappTemplate::where('code', $templateCode)
                ->where('is_active', true)
                ->first();

            // If not found or inactive
            if (!$template) {
                Log::warning("[WhatsApp] Template not found or inactive: {$templateCode}");
                return null;
            }

            // Parse template content with data
            $message = $this->templateService->parseVariables($template->content, $data);

            // Create WhatsappLog record
            $log = WhatsappLog::create([
                'template_id' => $template->id,
                'recipient_phone' => $phone,
                'recipient_name' => $data['name'] ?? null,
                'message_content' => $message,
                'status' => 'queued',
                'metadata' => $metadata,
                'max_retry' => WhatsappSetting::getValue('max_retry_attempts', 3),
            ]);

            // TODO Phase 3: Dispatch SendWhatsappMessage::dispatch($log->id)->delay(...)

            return $log;
        } catch (\Exception $e) {
            Log::error('[WhatsApp] Send by template failed', [
                'template_code' => $templateCode,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            // For critical errors (DB), throw exception
            if ($e instanceof \Illuminate\Database\QueryException) {
                throw $e;
            }

            // For non-critical errors, return null
            return null;
        }
    }

    /**
     * Send message immediately without queue (for manual resend)
     * 
     * @param WhatsappLog $log WhatsappLog record to send
     * @return bool True if sent successfully, false otherwise
     */
    public function sendDirect(WhatsappLog $log): bool
    {
        try {
            // Update log status to pending
            $log->update(['status' => 'pending']);

            // Call API
            $result = $this->whatsappService->sendText(
                $log->recipient_phone,
                $log->message_content
            );

            // Handle result
            if ($result['success'] === true) {
                $log->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ]);
                Log::info("[WhatsApp] Message sent to {$log->recipient_phone}");
                return true;
            }

            // If failed
            $log->update([
                'status' => 'failed',
                'error_message' => $result['error'] ?? 'Unknown error',
                'retry_count' => $log->retry_count + 1,
            ]);
            Log::error("[WhatsApp] Send failed to {$log->recipient_phone}: " . ($result['error'] ?? 'Unknown'));
            return false;
        } catch (\Exception $e) {
            // On exception: mark as failed and return false
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => $log->retry_count + 1,
            ]);
            Log::error('[WhatsApp] Send direct exception', [
                'log_id' => $log->id,
                'phone' => $log->recipient_phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send message to multiple recipients (bulk send)
     * 
     * @param string $templateCode Template code to use
     * @param array $recipients Array of recipients with phone, data, and metadata
     * @param array $commonData Common data to merge with each recipient's data
     * @return array Array of results with phone, log_id, and status
     */
    public function broadcast(
        string $templateCode,
        array $recipients,
        array $commonData = []
    ): array {
        $results = [];

        foreach ($recipients as $recipient) {
            $phone = $recipient['phone'];
            $data = array_merge($commonData, $recipient['data'] ?? []);
            $metadata = $recipient['metadata'] ?? [];

            $log = $this->sendByTemplate($templateCode, $phone, $data, $metadata);

            $results[] = [
                'phone' => $phone,
                'log_id' => $log?->id,
                'status' => $log ? 'queued' : 'failed',
            ];
        }

        return $results;
    }
}
