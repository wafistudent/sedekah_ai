<?php
namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * MemberController
 *
 * Handles member-related operations including network tree visualization
 *
 * @package App\Http\Controllers
 */
class MemberController extends Controller
{
    /**
     * Display the network tree for the authenticated user
     *
     * @param Request $request
     * @return View
     */
    public function networkTree(Request $request): View
    {
        $currentUser    = auth()->user();
        $membersPerPage = 4;

        // Build network tree grouped by level
        $networkTree = $this->buildLevelBasedTree($currentUser->id);

        // Calculate statistics
        $totalDownlines  = 0;
        $activeDownlines = 0;
        $deepestLevel    = 0;

        foreach ($networkTree as $level => $members) {
            $totalDownlines  += count($members);
            $activeDownlines += collect($members)->where('status', 'active')->count();
            $deepestLevel     = max($deepestLevel, $level);
        }

        return view('members.network-tree', compact(
            'networkTree',
            'membersPerPage',
            'totalDownlines',
            'activeDownlines',
            'deepestLevel',
        ));
    }

    /**
     * Build level-based tree using BFS algorithm
     *
     * @param string $rootMemberId
     * @param int $maxLevel
     * @return array<int, array<User>>
     */
    private function buildLevelBasedTree(string $rootMemberId, int $maxLevel = 8): array
    {
        $tree    = [];
        $queue   = [['member_id' => $rootMemberId, 'level' => 0]];
        $visited = [];

        while (! empty($queue) && count($tree) < $maxLevel) {
            $current  = array_shift($queue);
            $memberId = $current['member_id'];
            $level    = $current['level'];

            // Skip if already visited or beyond max level
            if (isset($visited[$memberId]) || $level >= $maxLevel) {
                continue;
            }

            $visited[$memberId] = true;

            // Get downline members for this member
            // $downlines = Network::where('upline_id', $memberId)
            //     ->with(['member' => function ($query) {
            //         $query->select('id', 'name', 'email', 'phone', 'status', 'created_at');
            //     }])
            //     ->get();

            $downlines = Network::where('upline_id', $memberId)
                ->with([
                    'member:id,name,email,phone,status,created_at',
                    'upline:id',
                ])
                ->get();

            foreach ($downlines as $downline) {
                if ($downline->member) {
                    $nextLevel = $level + 1;

                    // Initialize level array if not exists
                    if (! isset($tree[$nextLevel])) {
                        $tree[$nextLevel] = [];
                    }

                    // Add member to the level
                    $tree[$nextLevel][] = [$downline->member, $downline->upline];

                    // Add to queue for next iteration
                    $queue[] = [
                        'member_id' => $downline->member->id,
                        'level'     => $nextLevel,
                    ];
                }
            }
        }

        return $tree;
    }

    /**
     * Get list of available uplines (members who can accept downlines)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAvailableUplines()
    {
        // Get all active members
        // In a real scenario, you might want to filter based on network depth
        return User::where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'email')
            ->get();
    }
}
