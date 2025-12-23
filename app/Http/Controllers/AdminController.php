<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PinService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * AdminController
 * 
 * Handles admin-specific operations
 * 
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * @var PinService
     */
    protected PinService $pinService;

    /**
     * Constructor
     * 
     * @param PinService $pinService
     */
    public function __construct(PinService $pinService)
    {
        $this->pinService = $pinService;
    }

    /**
     * Display all members
     * 
     * @return View
     */
    public function members(): View
    {
        $members = User::with(['network', 'wallet'])
            ->latest()
            ->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    /**
     * Show purchase PIN form
     * 
     * @return View
     */
    public function purchasePinForm(): View
    {
        $members = User::where('status', 'active')
            ->select('id', 'name', 'email')
            ->get();

        return view('admin.pins.purchase', compact('members'));
    }

    /**
     * Process PIN purchase for member
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function storePurchasePin(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->pinService->purchasePin(
                memberId: $request->member_id,
                points: $request->points,
                description: $request->description ?? 'Admin PIN purchase'
            );

            return redirect()->route('admin.pins.purchase')
                ->with('success', 'PIN purchased successfully for member');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
