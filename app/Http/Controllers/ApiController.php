<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * ApiController
 * 
 * Handles API endpoints for frontend AJAX requests
 * 
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    /**
     * Get member details for modal display
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function getMemberDetail(string $id): JsonResponse
    {
        $member = User::find($id);

        if (!$member) {
            return response()->json([
                'error' => 'Member not found'
            ], 404);
        }

        return response()->json([
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'join_date' => $member->created_at->format('d M Y'),
            'status' => $member->status,
            'pin_point' => $member->pin_point,
            'wallet_balance' => $member->wallet->balance ?? 0,
        ]);
    }
}
