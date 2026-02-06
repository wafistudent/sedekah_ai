<?php

namespace App\Jobs;

use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendWhatsappMessage Job
 *
 * Background job to send WhatsApp messages via queue with retry logic
 *
 * @package App\Jobs
 */
class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted
     *
     * @var int
     */
    public $tries = 1; // Don't auto-retry from queue system (we handle manually)

    /**
     * Timeout for the job execution
     *
     * @var int
     */
    public $timeout = 30; // 30 seconds timeout

    /**
     * WhatsappLog ID
     *
     * @var int
     */
    protected $logId;

    /**
     * Create a new job instance
     *
     * @param int $logId WhatsappLog ID
     */
    public function __construct(int $logId)
    {
        $this->logId = $logId;
    }

    /**
     * Execute the job
     *
     * @param WhatsappService $whatsappService
     * @return void
     */
    public function handle(WhatsappService $whatsappService): void
    {
        // Get log from DB
        $log = WhatsappLog::find($this->logId);

        if (!$log) {
            Log::warning("[WhatsApp Job] Log not found: {$this->logId}");
            return;
        }

        // Update status to pending
        $log->update(['status' => 'pending']);

        // Call API
        try {
            $result = $whatsappService->sendText(
                $log->recipient_phone,
                $log->message_content
            );

            if ($result['success']) {
                $this->handleSuccess($log);
            } else {
                $this->handleFailure($log, $result['error'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            $this->handleFailure($log, $e->getMessage());
        }
    }

    /**
     * Handle successful message send
     *
     * @param WhatsappLog $log
     * @return void
     */
    protected function handleSuccess(WhatsappLog $log): void
    {
        $log->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ]);

        Log::info("[WhatsApp Job] Message sent successfully", [
            'log_id' => $log->id,
            'phone' => $log->recipient_phone
        ]);
    }

    /**
     * Handle failed message send
     *
     * @param WhatsappLog $log
     * @param string $error
     * @return void
     */
    protected function handleFailure(WhatsappLog $log, string $error): void
    {
        // Increment retry count
        $log->increment('retry_count');

        // Update log
        $log->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);

        // Log error
        Log::error("[WhatsApp Job] Send failed", [
            'log_id' => $log->id,
            'phone' => $log->recipient_phone,
            'error' => $error,
            'retry_count' => $log->retry_count,
        ]);

        // Check auto retry
        $autoRetryEnabled = WhatsappSetting::getValue('auto_retry_enabled', true);

        if ($autoRetryEnabled && $log->retry_count < $log->max_retry) {
            $retryDelay = WhatsappSetting::getValue('retry_delay_minutes', 5);

            // Re-dispatch job with delay
            self::dispatch($log->id)->delay(now()->addMinutes($retryDelay));

            Log::info("[WhatsApp Job] Retry scheduled", [
                'log_id' => $log->id,
                'retry_count' => $log->retry_count,
                'retry_in_minutes' => $retryDelay,
            ]);
        } else {
            Log::warning("[WhatsApp Job] Max retry exceeded", [
                'log_id' => $log->id,
                'retry_count' => $log->retry_count,
            ]);
        }
    }

    /**
     * Handle job failure
     *
     * Called when job throws unhandled exception
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        $log = WhatsappLog::find($this->logId);

        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }

        Log::error("[WhatsApp Job] Job failed permanently", [
            'log_id' => $this->logId,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
