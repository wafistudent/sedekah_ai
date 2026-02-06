<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreWhatsappTemplateRequest;
use App\Http\Requests\UpdateWhatsappTemplateRequest;
use App\Models\WhatsappTemplate;
use App\Services\WhatsappService;
use App\Services\WhatsappTemplateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * WhatsappTemplateController
 *
 * Handle all WhatsApp template CRUD operations
 */
class WhatsappTemplateController extends Controller
{
    /**
     * @var WhatsappTemplateService
     */
    protected $templateService;

    /**
     * WhatsappTemplateController constructor
     */
    public function __construct(WhatsappTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of WhatsApp templates with filters
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = WhatsappTemplate::query()
                ->with('creator:id,name')
                ->withCount('logs');

            // Filter by category
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            // Filter by status
            if ($request->filled('status')) {
                $isActive = $request->status === 'active';
                $query->where('is_active', $isActive);
            }

            // Search by name or code
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Order and paginate
            $templates = $query->orderBy('created_at', 'desc')->paginate(20);

            return view('admin.whatsapp.templates.index', compact('templates'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to fetch templates', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data template: '.$e->getMessage());
        }
    }

    /**
     * Show form to create new template
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $categories = ['member', 'commission', 'withdrawal', 'admin', 'general'];
            $dummyData = config('whatsapp.dummy_data');

            return view('admin.whatsapp.templates.create', compact('categories', 'dummyData'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to show create form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.whatsapp.templates.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Validate and save new template
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreWhatsappTemplateRequest $request)
    {
        try {
            // Create template
            $template = WhatsappTemplate::create([
                'code' => $request->code,
                'name' => $request->name,
                'category' => $request->category,
                'subject' => $request->subject,
                'content' => $request->content,
                'variables' => $this->extractVariables($request->content),
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.whatsapp.templates.index')
                ->with('success', 'Template berhasil dibuat!');
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to create', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Display template detail (read-only view)
     *
     * @return \Illuminate\View\View
     */
    public function show(WhatsappTemplate $template)
    {
        try {
            $template->load('creator:id,name');
            $template->loadCount([
                'logs',
                'logs as sent_count' => function ($q) {
                    $q->where('status', 'sent');
                },
                'logs as failed_count' => function ($q) {
                    $q->where('status', 'failed');
                },
            ]);

            $successRate = $template->logs_count > 0
                ? round(($template->sent_count / $template->logs_count) * 100, 2)
                : 0;

            return view('admin.whatsapp.templates.show', compact('template', 'successRate'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to show template', [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.whatsapp.templates.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Show form to edit template
     *
     * @return \Illuminate\View\View
     */
    public function edit(WhatsappTemplate $template)
    {
        try {
            $categories = ['member', 'commission', 'withdrawal', 'admin', 'general'];
            $dummyData = config('whatsapp.dummy_data');

            return view('admin.whatsapp.templates.edit', compact('template', 'categories', 'dummyData'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to show edit form', [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.whatsapp.templates.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Validate and update template
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateWhatsappTemplateRequest $request, WhatsappTemplate $template)
    {
        try {
            $template->update([
                'code' => $request->code,
                'name' => $request->name,
                'category' => $request->category,
                'subject' => $request->subject,
                'content' => $request->content,
                'variables' => $this->extractVariables($request->content),
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()
                ->route('admin.whatsapp.templates.index')
                ->with('success', 'Template berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to update', [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Soft delete template
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(WhatsappTemplate $template)
    {
        try {
            // Check if template is being used in pending/queued logs
            $activeLogsCount = $template->logs()
                ->whereIn('status', ['pending', 'queued'])
                ->count();

            if ($activeLogsCount > 0) {
                return redirect()
                    ->back()
                    ->with('error', "Template tidak dapat dihapus karena masih ada {$activeLogsCount} pesan yang sedang diproses.");
            }

            $template->delete();

            return redirect()
                ->route('admin.whatsapp.templates.index')
                ->with('success', 'Template berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to delete', [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Clone existing template
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(WhatsappTemplate $template)
    {
        try {
            $newTemplate = $template->replicate();
            $newTemplate->code = $template->code.'_copy';
            $newTemplate->name = $template->name.' (Copy)';
            $newTemplate->is_active = false;
            $newTemplate->created_by = auth()->id();
            $newTemplate->created_at = now();
            $newTemplate->save();

            return redirect()
                ->route('admin.whatsapp.templates.edit', $newTemplate)
                ->with('success', 'Template berhasil diduplikasi! Silakan edit sebelum mengaktifkan.');
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to duplicate', [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Send test WhatsApp message (AJAX endpoint)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testSend(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'phone' => 'required|string',
                'content' => 'required|string',
                'test_data' => 'nullable|array',
            ]);

            // Parse content with test data
            $message = $request->content;

            if ($request->filled('test_data')) {
                $message = $this->templateService->parseVariables(
                    $request->content,
                    $request->test_data
                );
            }

            // Send via API (direct, no queue, no log)
            $whatsappService = app(WhatsappService::class);
            $result = $whatsappService->sendText($request->phone, $message);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan test berhasil dikirim ke '.$request->phone,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan: '.($result['error'] ?? 'Unknown error'),
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: '.implode(', ', $e->errors()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('[WhatsApp Template] Failed to send test message', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract variables from content for storage
     */
    protected function extractVariables(string $content): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);

        return array_unique($matches[1] ?? []);
    }
}
