<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingPin;
use App\Models\User;
use App\Services\MarketingPinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * MarketingPinController
 * 
 * Handles admin operations for marketing PIN management
 * 
 * @package App\Http\Controllers\Admin
 */
class MarketingPinController extends Controller
{
    /**
     * @var MarketingPinService
     */
    protected MarketingPinService $marketingPinService;

    /**
     * Constructor
     */
    public function __construct(MarketingPinService $marketingPinService)
    {
        $this->marketingPinService = $marketingPinService;
    }

    /**
     * Display a listing of marketing PINs with statistics
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Build query
        $query = MarketingPin::with(['admin', 'designatedMember', 'redeemedByMember'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by code
        if ($request->has('search') && $request->search !== '') {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Paginate results
        $pins = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => MarketingPin::count(),
            'active' => MarketingPin::where('status', 'active')->count(),
            'used' => MarketingPin::where('status', 'used')->count(),
            'expired' => MarketingPin::where('status', 'expired')->count(),
        ];

        return view('admin.marketing-pins.index', compact('pins', 'stats'));
    }

    /**
     * Show the form for generating new marketing PINs
     *
     * @return View
     */
    public function create(): View
    {
        // Get active members for designated member dropdown
        $members = User::role('member')
            ->where('status', 'active')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.marketing-pins.create', compact('members'));
    }

    /**
     * Store newly generated marketing PINs
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:100',
            'designated_member_id' => 'nullable|exists:users,id',
            'expired_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pins = $this->marketingPinService->generateBulkPins(
                adminId: auth()->id(),
                quantity: $request->quantity,
                designatedMemberId: $request->designated_member_id,
                expiredAt: $request->expired_at
            );

            $message = count($pins) . ' Marketing PIN berhasil di-generate';

            return redirect()->route('admin.marketing-pins.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate PIN: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified marketing PIN with usage details
     *
     * @param MarketingPin $marketingPin
     * @return View
     */
    public function show(MarketingPin $marketingPin): View
    {
        $marketingPin->load(['admin', 'designatedMember', 'redeemedByMember']);

        return view('admin.marketing-pins.show', compact('marketingPin'));
    }
}
