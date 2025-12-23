<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\CommissionConfig;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * SettingController
 * 
 * Handles application settings and commission configuration
 * 
 * @package App\Http\Controllers
 */
class SettingController extends Controller
{
    /**
     * Display commission configuration form
     * 
     * @return View
     */
    public function commissionConfig(): View
    {
        $commissionLevels = CommissionConfig::orderBy('level')->get();
        
        // Ensure all 8 levels exist
        for ($i = 1; $i <= 8; $i++) {
            if (!$commissionLevels->where('level', $i)->first()) {
                $commissionLevels->push(
                    CommissionConfig::create([
                        'level' => $i,
                        'amount' => 0,
                        'is_active' => true,
                    ])
                );
            }
        }
        
        $commissionLevels = $commissionLevels->sortBy('level');

        return view('admin.settings.commission-config', compact('commissionLevels'));
    }

    /**
     * Update commission configuration
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateCommissionConfig(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'levels' => 'required|array',
            'levels.*.level' => 'required|integer|min:1|max:8',
            'levels.*.amount' => 'required|numeric|min:0',
            'levels.*.is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            foreach ($request->levels as $levelData) {
                CommissionConfig::updateOrCreate(
                    ['level' => $levelData['level']],
                    [
                        'amount' => $levelData['amount'],
                        'is_active' => $levelData['is_active'] ?? true,
                    ]
                );
            }

            return redirect()->route('admin.settings.commission-config')
                ->with('success', 'Commission configuration updated successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update commission configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display app settings form
     * 
     * @return View
     */
    public function appSettings(): View
    {
        $registrationFee = AppSetting::get('registration_fee', 20000);
        $pinPrice = AppSetting::get('pin_price', 50000);
        $minWithdrawal = AppSetting::get('min_withdrawal', 100000);

        return view('admin.settings.app-settings', compact(
            'registrationFee',
            'pinPrice',
            'minWithdrawal'
        ));
    }

    /**
     * Update app settings
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAppSettings(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'registration_fee' => 'required|numeric|min:0',
            'pin_price' => 'required|numeric|min:0',
            'min_withdrawal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            AppSetting::set('registration_fee', $request->registration_fee, 'decimal');
            AppSetting::set('pin_price', $request->pin_price, 'decimal');
            AppSetting::set('min_withdrawal', $request->min_withdrawal, 'decimal');

            return redirect()->route('admin.settings.app-settings')
                ->with('success', 'App settings updated successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update app settings: ' . $e->getMessage())
                ->withInput();
        }
    }
}
