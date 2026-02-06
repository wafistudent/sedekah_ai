<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\PinTransaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * PinService
 * 
 * Handles PIN operations including purchase, transfer, and redemption
 * for new member registration
 * 
 * @package App\Services
 */
class PinService
{
    /**
     * @var NetworkService
     */
    protected NetworkService $networkService;

    /**
     * @var WalletService
     */
    protected WalletService $walletService;

    /**
     * @var CommissionService
     */
    protected CommissionService $commissionService;

    /**
     * Constructor
     */
    public function __construct(
        NetworkService $networkService,
        WalletService $walletService,
        CommissionService $commissionService
    ) {
        $this->networkService = $networkService;
        $this->walletService = $walletService;
        $this->commissionService = $commissionService;
    }

    /**
     * Admin gives PIN to a member (purchase)
     * 
     * @param string $memberId
     * @param int $points
     * @param string|null $description
     * @return PinTransaction
     * @throws Exception
     */
    public function purchasePin(string $memberId, int $points, ?string $description = null): PinTransaction
    {
        if ($points <= 0) {
            throw new Exception("Points must be greater than 0");
        }

        return DB::transaction(function () use ($memberId, $points, $description) {
            $member = User::lockForUpdate()->find($memberId);

            if (!$member) {
                throw new Exception("Member with ID {$memberId} not found");
            }

            $beforePoint = $member->pin_point;
            $afterPoint = $beforePoint + $points;

            // Update member's PIN points
            $member->pin_point = $afterPoint;
            $member->save();

            // Create transaction record
            return PinTransaction::create([
                'member_id' => $memberId,
                'type' => 'purchase',
                'target_id' => null,
                'point' => $points,
                'before_point' => $beforePoint,
                'after_point' => $afterPoint,
                'status' => 'success',
                'description' => $description ?? 'PIN purchase',
            ]);
        });
    }

    /**
     * Member transfers PIN to another member
     * 
     * @param string $fromMemberId
     * @param string $toMemberId
     * @param int $points
     * @return PinTransaction
     * @throws Exception
     */
    public function transferPin(string $fromMemberId, string $toMemberId, int $points): PinTransaction
    {
        if ($points <= 0) {
            throw new Exception("Points must be greater than 0");
        }

        if ($fromMemberId === $toMemberId) {
            throw new Exception("Cannot transfer PIN to yourself");
        }

        return DB::transaction(function () use ($fromMemberId, $toMemberId, $points) {
            // Lock both users for update
            $fromMember = User::lockForUpdate()->find($fromMemberId);
            $toMember = User::lockForUpdate()->find($toMemberId);

            if (!$fromMember) {
                throw new Exception("Sender member with ID {$fromMemberId} not found");
            }

            if (!$toMember) {
                throw new Exception("Recipient member with ID {$toMemberId} not found");
            }

            // Check if sender has enough points
            if ($fromMember->pin_point < $points) {
                throw new Exception("Insufficient PIN points. Available: {$fromMember->pin_point}, Required: {$points}");
            }

            // Deduct from sender
            $fromBeforePoint = $fromMember->pin_point;
            $fromAfterPoint = $fromBeforePoint - $points;
            $fromMember->pin_point = $fromAfterPoint;
            $fromMember->save();

            // Add to recipient
            $toBeforePoint = $toMember->pin_point;
            $toAfterPoint = $toBeforePoint + $points;
            $toMember->pin_point = $toAfterPoint;
            $toMember->save();

            // Create transaction record for sender
            $senderTransaction = PinTransaction::create([
                'member_id' => $fromMemberId,
                'type' => 'transfer',
                'target_id' => $toMemberId,
                'point' => -$points,
                'before_point' => $fromBeforePoint,
                'after_point' => $fromAfterPoint,
                'status' => 'success',
                'description' => "Transfer {$points} PIN to {$toMemberId}",
            ]);

            // Create transaction record for recipient
            PinTransaction::create([
                'member_id' => $toMemberId,
                'type' => 'transfer',
                'target_id' => $fromMemberId,
                'point' => $points,
                'before_point' => $toBeforePoint,
                'after_point' => $toAfterPoint,
                'status' => 'success',
                'description' => "Received {$points} PIN from {$fromMemberId}",
            ]);

            return $senderTransaction;
        });
    }

    /**
     * Sponsor redeems PIN to register a new member
     * 
     * Process:
     * 1. Validate sponsor has at least 1 PIN (skip if marketing)
     * 2. Deduct 1 PIN from sponsor (skip if marketing)
     * 3. Create new user
     * 4. Create wallet for new user
     * 5. Create network record
     * 6. Transfer registration fee to admin wallet
     * 7. Calculate and distribute commission (skip if marketing)
     * 
     * @param string $sponsorId
     * @param array $newMemberData
     * @param string $uplineId
     * @param bool $isMarketing
     * @param string|null $marketingPinCode
     * @return User
     * @throws Exception
     */
    public function reedemPin(
        string $sponsorId, 
        array $newMemberData, 
        string $uplineId,
        bool $isMarketing = false,
        ?string $marketingPinCode = null
    ): User
    {
        return DB::transaction(function () use ($sponsorId, $newMemberData, $uplineId, $isMarketing, $marketingPinCode) {
            // Initialize marketing PIN service if needed
            $marketingPinService = null;
            
            // If marketing PIN code is provided, validate it first
            if ($isMarketing && $marketingPinCode) {
                $marketingPinService = app(MarketingPinService::class);
                $validation = $marketingPinService->validatePin($marketingPinCode);
                
                if (!$validation['valid']) {
                    throw new Exception($validation['message']);
                }
            }

            // Validate sponsor
            $sponsor = User::lockForUpdate()->find($sponsorId);
            if (!$sponsor) {
                throw new Exception("Sponsor with ID {$sponsorId} not found");
            }

            // Initialize variables for PIN transaction
            $sponsorBeforePoint = null;
            $sponsorAfterPoint = null;

            // If NOT marketing registration, check and deduct PIN from sponsor
            if (!$isMarketing) {
                // Check if sponsor has at least 1 PIN
                if ($sponsor->pin_point < 1) {
                    throw new Exception("Sponsor has insufficient PIN points. Required: 1, Available: {$sponsor->pin_point}");
                }

                // Deduct 1 PIN from sponsor
                $sponsorBeforePoint = $sponsor->pin_point;
                $sponsorAfterPoint = $sponsorBeforePoint - 1;
                $sponsor->pin_point = $sponsorAfterPoint;
                $sponsor->save();
            }

            // Validate required fields for new member
            $requiredFields = ['id', 'email', 'password', 'name', 'dana_name', 'dana_number'];
            foreach ($requiredFields as $field) {
                if (!isset($newMemberData[$field]) || empty($newMemberData[$field])) {
                    throw new Exception("Required field '{$field}' is missing or empty");
                }
            }

            // Check if username or email already exists
            $existingUser = User::where('id', $newMemberData['id'])
                ->orWhere('email', $newMemberData['email'])
                ->first();

            if ($existingUser) {
                throw new Exception("Username or email already exists");
            }

            // Create new user
            $newUser = User::create([
                'id' => $newMemberData['id'],
                'email' => $newMemberData['email'],
                'password' => Hash::make($newMemberData['password']),
                'name' => $newMemberData['name'],
                'phone' => $newMemberData['phone'] ?? null,
                'dana_name' => $newMemberData['dana_name'],
                'dana_number' => $newMemberData['dana_number'],
                'pin_point' => 0,
                'status' => 'active',
            ]);

            // Assign member role
            $newUser->assignRole('member');

            // Create wallet for new user
            $this->walletService->createWallet($newUser->id);

            // Create network record
            $markMemberAsMarketing = $newMemberData['is_marketing'] ?? $isMarketing;
            $this->networkService->createNetwork(
                memberId: $newUser->id,
                sponsorId: $sponsorId,
                uplineId: $uplineId,
                isMarketing: $markMemberAsMarketing
            );

            // After user creation and network creation, handle marketing PIN logic
            if ($isMarketing && $marketingPinCode) {
                // Mark marketing PIN as used (reuse service instance)
                $marketingPinService->usePin($marketingPinCode, $newUser->id);
                
                // NO PIN transaction created
                // NO commission distribution
            } else {
                // Regular registration: create PIN transaction
                PinTransaction::create([
                    'member_id' => $sponsorId,
                    'type' => 'reedem',
                    'target_id' => $newUser->id,
                    'point' => -1,
                    'before_point' => $sponsorBeforePoint,
                    'after_point' => $sponsorAfterPoint,
                    'status' => 'success',
                    'description' => "Redeem 1 PIN untuk registrasi {$newUser->id}",
                ]);

                // Transfer registration fee to admin wallet
                $registrationFee = AppSetting::get('registration_fee', 20000);
                $adminUser = User::role('admin')->first();

                if ($adminUser && $registrationFee > 0) {
                    $this->walletService->credit(
                        userId: $adminUser->id,
                        amount: $registrationFee,
                        referenceType: 'registration_fee',
                        referenceId: null,
                        fromMemberId: $newUser->id,
                        level: null,
                        description: "Biaya registrasi {$newUser->id}"
                    );
                }

                // Calculate and distribute commission
                $this->commissionService->calculateCommission($newUser->id);
            }

            // Fire MemberRegistered event
            event(new \App\Events\MemberRegistered(
                user: $newUser,
                sponsor: $sponsor,
                upline: User::find($uplineId)
            ));

            return $newUser;
        });
    }
}
