<?php

namespace App\Services;

use App\Models\WhatsappLog;
use Illuminate\Support\Facades\DB;

/**
 * WhatsappLogService
 *
 * Manages logs, retry mechanism, and statistics
 *
 * @package App\Services
 */
class WhatsappLogService
{
    /**
     * @var WhatsappMessageService
     */
    protected $messageService;

    /**
     * WhatsappLogService constructor
     * 
     * @param WhatsappMessageService $messageService
     */
    public function __construct(WhatsappMessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Manually resend single failed message (bypass queue)
     * 
     * @param int $logId WhatsappLog ID to resend
     * @return bool True if resent successfully, false otherwise
     */
    public function resend(int $logId): bool
    {
        $log = WhatsappLog::findOrFail($logId);

        // Reset for resend
        $log->update([
            'status' => 'pending',
            'is_manual_resend' => true,
            'error_message' => null,
        ]);

        // Send direct (no queue)
        return $this->messageService->sendDirect($log);
    }

    /**
     * Resend multiple failed messages
     * 
     * @param array $logIds Array of WhatsappLog IDs to resend
     * @return array Array of results [log_id => success/failed]
     */
    public function bulkResend(array $logIds): array
    {
        $results = [];

        foreach ($logIds as $logId) {
            $results[$logId] = $this->resend($logId);
        }

        return $results;
    }

    /**
     * Auto retry failed messages (for scheduler - Phase 3)
     * 
     * @return int Count of successful retries
     */
    public function retryFailed(): int
    {
        $count = 0;

        // Query eligible logs
        $logs = WhatsappLog::where('status', 'failed')
            ->where('retry_count', '<', DB::raw('max_retry'))
            ->where('is_manual_resend', false)
            ->get();

        foreach ($logs as $log) {
            if ($this->messageService->sendDirect($log)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get statistics for dashboard
     * 
     * @param string $period Period: today, week, month
     * @return array Statistics with total, sent, failed, pending, success_rate
     */
    public function getStats(string $period = 'today'): array
    {
        // Build query based on period
        $query = WhatsappLog::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
        }

        // Count by status
        $total = $query->count();
        $sent = (clone $query)->where('status', 'sent')->count();
        $failed = (clone $query)->where('status', 'failed')->count();
        $pending = (clone $query)->whereIn('status', ['pending', 'queued'])->count();

        // Calculate success rate
        $successRate = $total > 0 ? round(($sent / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $successRate,
        ];
    }

    /**
     * Get paginated failed logs for UI
     * 
     * @param array $filters Optional filters (date_from, date_to, template_id, phone)
     * @param int $perPage Results per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFailedLogs(array $filters = [], int $perPage = 50)
    {
        // Build query
        $query = WhatsappLog::where('status', 'failed')
            ->with('template')
            ->orderBy('created_at', 'desc');

        // Apply optional filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        if (!empty($filters['template_id'])) {
            $query->where('template_id', $filters['template_id']);
        }
        if (!empty($filters['phone'])) {
            $query->where('recipient_phone', 'like', "%{$filters['phone']}%");
        }

        // Paginate
        return $query->paginate($perPage);
    }
}
