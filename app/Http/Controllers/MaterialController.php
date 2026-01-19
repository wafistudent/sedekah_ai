<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCompletion;
use App\Services\MaterialService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * MaterialController (Member Side)
 * 
 * Handles material viewing and completion tracking for members
 * 
 * @package App\Http\Controllers
 */
class MaterialController extends Controller
{
    /**
     * Material service instance
     *
     * @var MaterialService
     */
    protected MaterialService $materialService;

    /**
     * Create a new controller instance
     *
     * @param MaterialService $materialService
     */
    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    /**
     * Display a listing of accessible materials
     * 
     * @return View
     */
    public function index(): View
    {
        $userId = Auth::id();
        $materials = $this->materialService->getAccessibleMaterials($userId);

        // Check completion status for each material
        foreach ($materials as $material) {
            $material->is_completed = $this->materialService->isCompleted($material->id, $userId);
        }

        return view('materials.index', compact('materials'));
    }

    /**
     * Display the specified material
     * 
     * @param string $id
     * @return View|RedirectResponse
     */
    public function show(string $id): View|RedirectResponse
    {
        $userId = Auth::id();
        $material = Material::find($id);

        if (!$material) {
            return redirect()->route('materials.index')
                ->with('error', 'Material not found');
        }

        // Check if user has access to this material
        $accessibleMaterials = $this->materialService->getAccessibleMaterials($userId);
        $hasAccess = $accessibleMaterials->contains('id', $id);

        if (!$hasAccess) {
            return redirect()->route('materials.index')
                ->with('error', 'You do not have access to this material');
        }

        // Check completion status
        $completion = MaterialCompletion::where('material_id', $id)
            ->where('user_id', $userId)
            ->first();

        return view('materials.show', compact('material', 'completion'));
    }

    /**
     * Mark material as completed (AJAX endpoint)
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function complete(Request $request, string $id): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            // Check if user has access to this material
            $accessibleMaterials = $this->materialService->getAccessibleMaterials($userId);
            $hasAccess = $accessibleMaterials->contains('id', $id);

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this material',
                ], 403);
            }

            $completion = $this->materialService->markAsCompleted($id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Material marked as completed',
                'completed_at' => $completion->completed_at->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark material as completed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
