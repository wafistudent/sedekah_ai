<?php

namespace App\Services;

use App\Models\Network;
use App\Models\User;
use Exception;

/**
 * NetworkService
 * 
 * Handles network hierarchy operations including depth calculation,
 * upline validation, and network creation
 * 
 * @package App\Services
 */
class NetworkService
{
    /**
     * Calculate the depth of uplines recursively
     * 
     * @param string $memberId
     * @return int Depth count (0 = no upline, 1 = 1 level up, etc.)
     * @throws Exception
     */
    public function getUplineDepth(string $memberId): int
    {
        $network = Network::where('member_id', $memberId)->first();

        if (!$network || !$network->upline_id) {
            return 0;
        }

        return 1 + $this->getUplineDepth($network->upline_id);
    }

    /**
     * Validate if an upline can accept new members (must have < 8 levels above)
     * 
     * @param string $uplineId
     * @return bool
     */
    public function validateUplinePlacement(string $uplineId): bool
    {
        try {
            $depth = $this->getUplineDepth($uplineId);
            return $depth < 8;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the chain of upline members from bottom to top (max 8 levels)
     * 
     * @param string $memberId
     * @param int $maxLevel Maximum levels to retrieve (default 8)
     * @return array Array of upline member IDs from immediate upline to top
     * @throws Exception
     */
    public function getUplineChain(string $memberId, int $maxLevel = 8): array
    {
        $chain = [];
        $currentMemberId = $memberId;
        $level = 0;

        while ($level < $maxLevel) {
            $network = Network::where('member_id', $currentMemberId)->first();

            if (!$network || !$network->upline_id) {
                break;
            }

            $chain[] = $network->upline_id;
            $currentMemberId = $network->upline_id;
            $level++;
        }

        return $chain;
    }

    /**
     * Create a new network record with validation
     * 
     * @param string $memberId
     * @param string $sponsorId Who registered the member
     * @param string $uplineId Position in network tree
     * @param bool $isMarketing Whether member is marketing type
     * @return Network
     * @throws Exception
     */
    public function createNetwork(
        string $memberId,
        string $sponsorId,
        string $uplineId,
        bool $isMarketing = false
    ): Network {
        // Validate member exists
        $member = User::find($memberId);
        if (!$member) {
            throw new Exception("Member with ID {$memberId} not found");
        }

        // Validate sponsor exists
        $sponsor = User::find($sponsorId);
        if (!$sponsor) {
            throw new Exception("Sponsor with ID {$sponsorId} not found");
        }

        // Validate upline exists
        $upline = User::find($uplineId);
        if (!$upline) {
            throw new Exception("Upline with ID {$uplineId} not found");
        }

        // Validate upline placement (max 8 levels)
        if (!$this->validateUplinePlacement($uplineId)) {
            throw new Exception("Upline placement invalid: maximum depth of 8 levels exceeded");
        }

        // Check if member already has a network record
        $existingNetwork = Network::where('member_id', $memberId)->first();
        if ($existingNetwork) {
            throw new Exception("Member already has a network record");
        }

        // Create network record
        return Network::create([
            'member_id' => $memberId,
            'sponsor_id' => $sponsorId,
            'upline_id' => $uplineId,
            'is_marketing' => $isMarketing,
            'status' => 'active',
        ]);
    }
}
