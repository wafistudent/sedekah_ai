<?php
namespace App\Http\Controllers;

use App\Http\Requests\NewUserRequest;
use App\Models\PinTransaction;
use App\Models\User;
use App\Services\PinService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * PinController
 *
 * Handles PIN operations including viewing history, transferring, and redeeming PINs
 *
 * @package App\Http\Controllers
 */
class PinController extends Controller
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
     * Display PIN transaction history
     *
     * @return View
     */
    public function index(): View
    {
        return view('pins.index');
    }

    /**
     * Show transfer PIN form
     *
     * @return View
     */
    public function transfer(): View
    {
        $currentBalance = auth()->user()->pin_point;
        $members        = User::where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->select('id', 'name', 'email')
            ->get();

        return view('pins.transfer', compact('currentBalance', 'members'));
    }

    /**
     * Process PIN transfer
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeTransfer(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'amount'       => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->pinService->transferPin(
                fromMemberId: auth()->id(),
                toMemberId: $request->recipient_id,
                points: $request->amount
            );

            return redirect()->route('pins.index')
                ->with('success', 'PIN transferred successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show redeem PIN form (register new member)
     *
     * @param Request $request
     * @return View
     */
    public function reedem(Request $request): View
    {
        $upline         = null;
        $currentBalance = auth()->user()->pin_point;

        // Check if upline is specified in query parameter
        if ($request->has('upline')) {
            $upline = User::with('network')->find($request->upline);
        }

        // Get available uplines
        $availableUplines = User::where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return view('pins.reedem', compact('upline', 'availableUplines', 'currentBalance'));
    }

    /**
     * Process PIN redemption (register new member)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeReedem(NewUserRequest $request)
    {
        // return response()->json($request);

        if (! $request->validated()) {
            return redirect()->back()
                ->withErrors($request->errors())
                ->withInput();
        }

        try {
            $newMemberData = [
                'id'           => $request->username,
                'name'         => $request->name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'password'     => $request->password,
                'dana_name'    => $request->dana_name,
                'dana_number'  => $request->dana_number,
                'is_marketing' => $request->boolean('is_marketing', false),
            ];

            $newUser = $this->pinService->reedemPin(
                sponsorId: auth()->id(),
                newMemberData: $newMemberData,
                uplineId: $request->upline_id
            );

            return redirect()->route('members.network-tree')
                ->with('success', "Member {$newUser->id} registered successfully");
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function testing()
    {
        $current = 'member2';
        $network = [];

        while ($current) {
            $next = DB::table('network')
                ->where('upline_id', $current)
                ->first();

            if (! $next) {
                break;
            }

            $network[] = $next;
            $current   = $next->member_id;
        }

        dd($network);
    }
}
