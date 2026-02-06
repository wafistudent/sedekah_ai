<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateWhatsappSettingRequest;
use App\Models\WhatsappSetting;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * WhatsappSettingController
 *
 * Handle WhatsApp settings management
 *
 * @package App\Http\Controllers\Admin
 */
class WhatsappSettingController extends Controller
{
    /**
     * Display settings form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $settings = WhatsappSetting::all()->pluck('value', 'key');

            return view('admin.whatsapp.settings.index', compact('settings'));
        } catch (\Exception $e) {
            Log::error('[WhatsApp Setting] Failed to fetch settings', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Save settings
     *
     * @param UpdateWhatsappSettingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateWhatsappSettingRequest $request)
    {
        try {
            // Update each setting
            $settingKeys = [
                'api_url' => 'text',
                'api_key' => 'text',
                'is_mode_safe' => 'boolean',
                'message_delay_seconds' => 'number',
                'auto_retry_enabled' => 'boolean',
                'retry_delay_minutes' => 'number',
                'max_retry_attempts' => 'number',
            ];

            foreach ($settingKeys as $key => $type) {
                if ($request->has($key)) {
                    WhatsappSetting::setValue(
                        $key,
                        $request->input($key),
                        $type
                    );
                }
            }

            return redirect()
                ->back()
                ->with('success', 'Pengaturan berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('[WhatsApp Setting] Failed to update settings', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
