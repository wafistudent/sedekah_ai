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
     * When a new member registers:
     * - Get upline chain (max 8 levels)
     * - For each level, check commission_config
     * - Pay commission to upline members (including marketing members)
     * - Stop chain after paying a marketing member (don't continue to their uplines)
     * 
     * Important: Marketing members RECEIVE commission from their downline,
     * but STOP the commission chain from continuing upward to their uplines
     * 
     * @param string $newMemberId
     * @return void
     * @throws Exception
     */
    public function calculateCommission(string $newMemberId): void
    {
        DB::transaction(function () use ($newMemberId) {
            // Get upline chain
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

                // Get upline's network record to check is_marketing flag
                $uplineNetwork = Network::where('member_id', $uplineMemberId)->first();

                if (!$uplineNetwork) {
                    $currentLevel++;
                    continue;
                }

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

                // CRITICAL: If upline is marketing member, STOP the commission chain AFTER paying them
                // Marketing members receive bonuses from their downline but don't pass it up
                if ($uplineNetwork->is_marketing) {
                    // Stop processing further uplines
                    break;
                }

                $currentLevel++;
            }
        });
    }
}
