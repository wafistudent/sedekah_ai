<?php
namespace App\Services;

use App\Models\CommissionConfig;
use App\Models\Network;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * CommissionService
 *
 * Handles commission calculation and distribution to upline members
 * Implements is_marketing logic where marketing members don't pass
 * commission up but still receive commission from downlines
 *
 * @package App\Services
 */
class CommissionService
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
     * Constructor
     */
    public function __construct(NetworkService $networkService, WalletService $walletService)
    {
        $this->networkService = $networkService;
        $this->walletService  = $walletService;
    }

    /**
     * Calculate and distribute commission to upline members
     *
     * Business Logic for Marketing Members:
     * 1. Marketing member does NOT give bonus to upline when they register
     * 2. Marketing member STILL RECEIVES bonus from their downline
     * 3. Marketing member does NOT stop the chain - bonus continues upward
     *
     * When a new member registers:
     * - Check if the NEW MEMBER is marketing
     * - If marketing: STOP - no commission distributed to anyone
     * - If normal: Distribute commission to ALL upline members (max 8 levels)
     * - Marketing members in upline chain RECEIVE commission (they don't stop the chain)
     *
     * @param string $newMemberId
     * @return void
     * @throws Exception
     */
    public function calculateCommission(string $newMemberId): void
    {
        DB::transaction(function () use ($newMemberId) {
            // Get the new member's network record
            $newMemberNetwork = Network::where('member_id', $newMemberId)->first();

            if (! $newMemberNetwork) {
                return; // No network record, nothing to do
            }

            // CRITICAL: If the NEW MEMBER is marketing, DON'T distribute commission
            // Marketing members do NOT give bonus upward when they register
            if ($newMemberNetwork->is_marketing) {
                return; // Stop here - no commission distributed
            }

            // Process each level
            $currentLevel = 1;

            // Get upline chain (max 8 levels) return array
            $uplineChain = $this->networkService->getUplineChain($newMemberId, 8);
            $adminId     = User::role('admin')->first();

            if (! $adminId) {
                throw new Exception('Admin user not found for commission fallback');
            }

            // if (empty($uplineChain)) {
            //     // Insert bonus to admin
            //     return;
            //     }

            // Get active commission configs
            $commissionConfigs = CommissionConfig::where('is_active', true)
                ->where('level', '<=', 8)
                ->orderBy('level')
                ->get()
                ->keyBy('level');

            // Loop FIXED 8 LEVEL
            for ($currentLevel = 1; $currentLevel <= 8; $currentLevel++) {

                // Ambil upline berdasarkan index (level - 1)
                $uplineMemberId = $uplineChain[$currentLevel - 1] ?? null;

                // Kalau config level ini tidak ada → skip
                if (! isset($commissionConfigs[$currentLevel])) {
                    continue;
                }

                $config = $commissionConfigs[$currentLevel];

                // Kalau komisi 0 → skip
                if ($config->amount <= 0) {
                    continue;
                }

                try {
                    if ($uplineMemberId) {
                        $this->walletService->credit(
                            userId: $uplineMemberId,
                            amount: $config->amount,
                            referenceType: 'commission',
                            referenceId: null,
                            fromMemberId: $newMemberId,
                            level: $currentLevel,
                            description: "Komisi level {$currentLevel} dari member {$newMemberId}"
                        );
                    } else {
                        $this->walletService->credit(
                            userId: $adminId->id,
                            amount: $config->amount,
                            referenceType: 'commission',
                            referenceId: null,
                            fromMemberId: $newMemberId,
                            level: $currentLevel,
                            description: "Sisa Komisi level {$currentLevel} dari member {$newMemberId}"
                        );
                    }
                } catch (Exception $e) {
                    throw new Exception(
                        "Failed to credit commission at level {$currentLevel}: " . $e->getMessage()
                    );
                }

                logger()->info('Commission loop', [
                    'level'             => $currentLevel,
                    'upline_from_chain' => $uplineChain[$currentLevel - 1] ?? 'NULL',
                    'fallback_admin'    => $adminId,
                ]);
            }
        });
    }
}
