<?php

namespace App\Http\Controllers\Admin;

use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use App\Services\WhatsappLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * WhatsappLogController
 *
 * Handle WhatsApp log operations: view logs, resend messages
 */
class WhatsappLogController extends Controller
{
    /**
     * @var WhatsappLogService
     */
    protected $logService;

    /**
     * WhatsappLogController constructor
     */
    public function __construct(WhatsappLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display logs with filters and stats
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = WhatsappLog::query()
                ->with('template:id,code,name')
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by template
            if ($request->filled('template_id')) {
                $query->where('template_id', $request->template_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Search by phone or name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('recipient_phone', 'like', "%{$search}%")
                        ->orWhere('recipient_name', 'like', "%{$search}%");
                });
            }

            // Paginate
            $logs = $query->paginate(50);

            // Get stats
            $stats = $this->logService->getStats('today');

            // Get templates for filter dropdown
            $templates = WhatsappTemplate::select('id', 'name')
                ->orderBy('name')
                ->get();

            return view('admin.whatsapp.logs.index', compact('logs', 'stats', 'templates'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Log] Failed to fetch logs', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data log: '.$e->getMessage());
        }
    }

    /**
     * Display single log detail
     *
     * @return \Illuminate\View\View
     */
    public function show(WhatsappLog $log)
    {
        try {
            $log->load('template:id,code,name,category');

            // Build timeline
            $timeline = [
                ['label' => 'Dibuat', 'time' => $log->created_at, 'status' => 'completed'],
                ['label' => 'Antrian', 'time' => $log->created_at, 'status' => $log->status === 'queued' ? 'current' : 'completed'],
            ];

            if ($log->status === 'sent') {
                $timeline[] = ['label' => 'Terkirim', 'time' => $log->sent_at, 'status' => 'completed'];
            } elseif ($log->status === 'failed') {
                $timeline[] = ['label' => 'Gagal', 'time' => $log->updated_at, 'status' => 'failed'];
            }

            return view('admin.whatsapp.logs.show', compact('log', 'timeline'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Log] Failed to show log', [
                'error' => $e->getMessage(),
                'log_id' => $log->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.whatsapp.logs.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Manually resend single log
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(WhatsappLog $log)
    {
        try {
            if (! in_array($log->status, ['failed'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Hanya pesan dengan status "failed" yang bisa di-resend.');
            }

            $success = $this->logService->resend($log->id);

            if ($success) {
                return redirect()
                    ->back()
                    ->with('success', 'Pesan berhasil dikirim ulang!');
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal mengirim ulang pesan. Silakan cek log untuk detail.');
        } catch (\Exception $e) {
            Log::error('[WhatsApp Log] Failed to resend', [
                'error' => $e->getMessage(),
                'log_id' => $log->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Resend multiple logs at once
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkResend(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'log_ids' => 'required|array|min:1',
                'log_ids.*' => 'exists:whatsapp_logs,id',
            ]);

            // Resend
            $results = $this->logService->bulkResend($request->log_ids);
            $successCount = count(array_filter($results));
            $totalCount = count($results);

            return redirect()
                ->back()
                ->with('success', "{$successCount} dari {$totalCount} pesan berhasil dikirim ulang!");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->with('error', 'Validasi gagal: '.implode(', ', $e->errors()));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Log] Failed to bulk resend', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
