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
    public function reedem(Request $request)
    {
        $currentBalance = auth()->user()->pin_point;
        $upline = User::with('network')->find($request->upline);
        
        if ($upline) {
            return view('pins.reedem', compact('upline', 'currentBalance'));
        } else {
            return redirect()->route('members.network-tree');
        }
    }

    /**
     * Process PIN redemption (register new member)
     * Supports both regular and marketing PIN registration
     *
     * @param NewUserRequest $request
     * @return RedirectResponse
     */
    public function storeReedem(NewUserRequest $request): RedirectResponse
    {
        try {
            // Check if marketing PIN code is provided
            $marketingPinCode = $request->input('marketing_pin_code');
            $isMarketing = !empty($marketingPinCode);
            
            // Validate marketing PIN if provided
            if ($isMarketing) {
                $request->validate([
                    'marketing_pin_code' => 'required|string|size:8|exists:marketing_pins,code',
                ]);
            }
            
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

            // Call PinService with marketing PIN support
            $newUser = $this->pinService->reedemPin(
                sponsorId: auth()->id(),
                uplineId: $request->upline_id,
                newMemberData: $newMemberData,
                isMarketing: $isMarketing,
                marketingPinCode: $marketingPinCode
            );

            $message = $isMarketing 
                ? 'Member baru berhasil didaftarkan menggunakan Marketing PIN!'
                : 'Member baru berhasil didaftarkan!';

            return redirect()->route('members.network-tree')
                ->with('success', $message);
            
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
