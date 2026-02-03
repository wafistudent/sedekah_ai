<?php

namespace App\Services;

use App\Models\MarketingPin;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * MarketingPinService
 * 
 * Handles marketing PIN operations including generation,
 * validation, and usage tracking
 * 
 * @package App\Services
 */
class MarketingPinService
{
    /**
     * Generate a single marketing PIN
     * 
     * @param string $adminId
     * @param string|null $designatedMemberId
     * @param string|null $expiredAt
     * @return MarketingPin
     * @throws Exception
     */
    public function generatePin(
        string $adminId,
        ?string $designatedMemberId = null,
        ?string $expiredAt = null
    ): MarketingPin {
        return DB::transaction(function () use ($adminId, $designatedMemberId, $expiredAt) {
            return MarketingPin::create([
                'code' => MarketingPin::generateCode(),
                'admin_id' => $adminId,
                'designated_member_id' => $designatedMemberId,
                'status' => 'active',
                'expired_at' => $expiredAt,
            ]);
        });
    }

    /**
     * Generate multiple marketing PINs at once
     * 
     * @param string $adminId
     * @param int $quantity
     * @param string|null $designatedMemberId
     * @param string|null $expiredAt
     * @return array<MarketingPin>
     * @throws Exception
     */
    public function generateBulkPins(
        string $adminId,
        int $quantity,
        ?string $designatedMemberId = null,
        ?string $expiredAt = null
    ): array {
        if ($quantity <= 0 || $quantity > 100) {
            throw new Exception("Quantity must be between 1 and 100");
        }

        $pins = [];
        
        DB::transaction(function () use ($adminId, $quantity, $designatedMemberId, $expiredAt, &$pins) {
            for ($i = 0; $i < $quantity; $i++) {
                $pins[] = $this->generatePin($adminId, $designatedMemberId, $expiredAt);
            }
        });

        return $pins;
    }

    /**
     * Validate marketing PIN
     * Returns validation result with message and PIN object
     * 
     * @param string $code
     * @return array{valid: bool, message: string, pin: MarketingPin|null}
     */
    public function validatePin(string $code): array
    {
        $pin = MarketingPin::where('code', $code)->first();

        if (!$pin) {
            return [
                'valid' => false,
                'message' => 'PIN marketing tidak ditemukan',
                'pin' => null,
            ];
        }

        if (!$pin->isValid()) {
            $message = $pin->status === 'used' 
                ? 'PIN marketing sudah digunakan'
                : 'PIN marketing sudah expired';
            
            return [
                'valid' => false,
                'message' => $message,
                'pin' => $pin,
            ];
        }

        return [
            'valid' => true,
            'message' => 'PIN marketing valid',
            'pin' => $pin,
        ];
    }

    /**
     * Use marketing PIN for registration
     * Marks PIN as used and assigns to new member
     * 
     * @param string $code
     * @param string $newMemberId
     * @return bool
     * @throws Exception
     */
    public function usePin(string $code, string $newMemberId): bool
    {
        return DB::transaction(function () use ($code, $newMemberId) {
            $validation = $this->validatePin($code);
            
            if (!$validation['valid']) {
                throw new Exception($validation['message']);
            }

            /** @var MarketingPin $pin */
            $pin = $validation['pin'];
            $pin->markAsUsed($newMemberId);

            return true;
        });
    }
}
