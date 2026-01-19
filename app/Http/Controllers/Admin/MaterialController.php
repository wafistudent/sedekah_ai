<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Material;
use App\Services\MaterialService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * MaterialController (Admin)
 * 
 * Handles CRUD operations for learning materials
 * 
 * @package App\Http\Controllers\Admin
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
     * Display a listing of materials
     * 
     * @return View
     */
    public function index(): View
    {
        $materials = Material::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new material
     * 
     * @return View
     */
    public function create(): View
    {
        $maxPdfSize = AppSetting::get('max_pdf_size', 50);
        
        return view('admin.materials.create', compact('maxPdfSize'));
    }

    /**
     * Store a newly created material in storage
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $maxPdfSize = AppSetting::get('max_pdf_size', 50);
        $maxPdfSizeKB = $maxPdfSize * 1024; // Convert MB to KB for validation

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pdf,youtube',
            'access_type' => 'required|in:all,marketing_only,non_marketing_only',
            'order' => 'nullable|integer|min:0',
        ];

        // Type-specific validation
        if ($request->type === 'youtube') {
            $rules['youtube_url'] = 'required|url';
        } else {
            $rules['pdf_file'] = "required|file|mimes:pdf|max:{$maxPdfSizeKB}";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $content = '';

            if ($request->type === 'pdf') {
                // Validate and upload PDF
                if (!$this->materialService->validatePdfSize($request->file('pdf_file'))) {
                    throw new Exception("File size exceeds maximum allowed size of {$maxPdfSize} MB");
                }
                $content = $this->materialService->uploadPdf($request->file('pdf_file'));
            } else {
                // Validate YouTube URL
                if (!$this->materialService->validateYoutubeUrl($request->youtube_url)) {
                    throw new Exception("Invalid YouTube URL format");
                }
                $content = $request->youtube_url;
            }

            $this->materialService->createMaterial([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'content' => $content,
                'access_type' => $request->access_type,
                'order' => $request->order ?? 0,
            ]);

            return redirect()->route('admin.materials.index')
                ->with('success', 'Material created successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create material: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified material
     * 
     * @param string $id
     * @return View|RedirectResponse
     */
    public function edit(string $id): View|RedirectResponse
    {
        $material = Material::find($id);

        if (!$material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material not found');
        }

        $maxPdfSize = AppSetting::get('max_pdf_size', 50);

        return view('admin.materials.edit', compact('material', 'maxPdfSize'));
    }

    /**
     * Update the specified material in storage
     * 
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $material = Material::find($id);

        if (!$material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material not found');
        }

        $maxPdfSize = AppSetting::get('max_pdf_size', 50);
        $maxPdfSizeKB = $maxPdfSize * 1024; // Convert MB to KB for validation

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pdf,youtube',
            'access_type' => 'required|in:all,marketing_only,non_marketing_only',
            'order' => 'nullable|integer|min:0',
        ];

        // Type-specific validation
        if ($request->type === 'youtube') {
            $rules['youtube_url'] = 'required|url';
        } else {
            // PDF is optional on update (only if uploading new file)
            $rules['pdf_file'] = "nullable|file|mimes:pdf|max:{$maxPdfSizeKB}";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $content = $material->content;

            // If type changed or new content uploaded
            if ($request->type === 'pdf') {
                if ($request->hasFile('pdf_file')) {
                    // Delete old PDF if it exists and type was PDF
                    if ($material->type === 'pdf' && Storage::disk('public')->exists($material->content)) {
                        Storage::disk('public')->delete($material->content);
                    }
                    
                    // Upload new PDF
                    if (!$this->materialService->validatePdfSize($request->file('pdf_file'))) {
                        throw new Exception("File size exceeds maximum allowed size of {$maxPdfSize} MB");
                    }
                    $content = $this->materialService->uploadPdf($request->file('pdf_file'));
                } elseif ($material->type !== 'pdf') {
                    // Type changed from YouTube to PDF but no file uploaded
                    throw new Exception("Please upload a PDF file");
                }
            } else {
                // Type is YouTube
                if (!$this->materialService->validateYoutubeUrl($request->youtube_url)) {
                    throw new Exception("Invalid YouTube URL format");
                }
                
                // Delete old PDF if type changed from PDF to YouTube
                if ($material->type === 'pdf' && Storage::disk('public')->exists($material->content)) {
                    Storage::disk('public')->delete($material->content);
                }
                
                $content = $request->youtube_url;
            }

            $this->materialService->updateMaterial($id, [
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'content' => $content,
                'access_type' => $request->access_type,
                'order' => $request->order ?? $material->order,
            ]);

            return redirect()->route('admin.materials.index')
                ->with('success', 'Material updated successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update material: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified material from storage
     * 
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->materialService->deleteMaterial($id);

            return redirect()->route('admin.materials.index')
                ->with('success', 'Material deleted successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete material: ' . $e->getMessage());
        }
    }
}
