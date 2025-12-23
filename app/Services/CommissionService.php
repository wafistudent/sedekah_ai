<?php

namespace App\Services;

use App\Models\CommissionConfig;
use App\Models\Network;
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
        $this->walletService = $walletService;
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

            if (!$newMemberNetwork) {
                return; // No network record, nothing to do
            }

            // CRITICAL: If the NEW MEMBER is marketing, DON'T distribute commission
            // Marketing members do NOT give bonus upward when they register
            if ($newMemberNetwork->is_marketing) {
                return; // Stop here - no commission distributed
            }

            // Get upline chain (max 8 levels)
            $uplineChain = $this->networkService->getUplineChain($newMemberId, 8);

            if (empty($uplineChain)) {
                return; // No uplines, nothing to do
            }

            // Get active commission configs
            $commissionConfigs = CommissionConfig::where('is_active', true)
                ->where('level', '<=', count($uplineChain))
                ->orderBy('level')
                ->get()
                ->keyBy('level');

            // Process each level
            $currentLevel = 1;
            foreach ($uplineChain as $uplineMemberId) {
                // Check if there's a commission config for this level
                if (!isset($commissionConfigs[$currentLevel])) {
                    $currentLevel++;
                    continue;
                }

                $config = $commissionConfigs[$currentLevel];

                // Skip if commission amount is 0
                if ($config->amount <= 0) {
                    $currentLevel++;
                    continue;
                }

                // IMPORTANT: Do NOT check if upline is marketing here
                // Marketing members in the upline chain STILL RECEIVE commission from downline
                // They just don't GIVE commission when they register themselves
                // The chain is NOT broken by marketing members in the upline

                // Credit commission to upline's wallet
                try {
                    $this->walletService->credit(
                        userId: $uplineMemberId,
                        amount: $config->amount,
                        referenceType: 'commission',
                        referenceId: null,
                        fromMemberId: $newMemberId,
                        level: $currentLevel,
                        description: "Commission level {$currentLevel} from member {$newMemberId}"
                    );
                } catch (Exception $e) {
                    // Log error but continue processing other levels
                    // In production, you might want to use Laravel's logging
                    // For now, we'll rethrow to ensure data consistency
                    throw new Exception(
                        "Failed to credit commission to {$uplineMemberId} at level {$currentLevel}: " . $e->getMessage()
                    );
                }

                $currentLevel++;
            }
        });
    }
}
