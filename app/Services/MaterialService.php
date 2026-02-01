<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Material;
use App\Models\MaterialCompletion;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * MaterialService
 * 
 * Handles material operations including CRUD, file uploads,
 * and completion tracking
 * 
 * @package App\Services
 */
class MaterialService
{
    /**
     * Create a new material
     * 
     * @param array $data
     * @return Material
     * @throws Exception
     */
    public function createMaterial(array $data): Material
    {
        return Material::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'content' => $data['content'],
            'access_type' => $data['access_type'],
            'order' => $data['order'] ?? 0,
        ]);
    }

    /**
     * Update an existing material
     * 
     * @param string $materialId
     * @param array $data
     * @return Material
     * @throws Exception
     */
    public function updateMaterial(string $materialId, array $data): Material
    {
        $material = Material::find($materialId);

        if (!$material) {
            throw new Exception("Material with ID {$materialId} not found");
        }

        $material->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'content' => $data['content'],
            'access_type' => $data['access_type'],
            'order' => $data['order'] ?? $material->order,
        ]);

        return $material->fresh();
    }

    /**
     * Delete a material and associated file
     * 
     * @param string $materialId
     * @return bool
     * @throws Exception
     */
    public function deleteMaterial(string $materialId): bool
    {
        $material = Material::find($materialId);

        if (!$material) {
            throw new Exception("Material with ID {$materialId} not found");
        }

        // Delete PDF file if it exists
        if ($material->type === 'pdf' && Storage::disk('public')->exists($material->content)) {
            Storage::disk('public')->delete($material->content);
        }

        return $material->delete();
    }

    /**
     * Upload PDF file to storage
     * 
     * @param UploadedFile $file
     * @return string Path to uploaded file
     * @throws Exception
     */
    public function uploadPdf(UploadedFile $file): string
    {
        if (!$this->validatePdfSize($file)) {
            $maxSize = AppSetting::get('max_pdf_size', 50);
            throw new Exception("File size exceeds maximum allowed size of {$maxSize} MB");
        }

        // Generate unique filename
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // Store in storage/app/public/materials/
        $path = $file->storeAs('materials', $filename, 'public');

        return $path;
    }

    /**
     * Get materials accessible by user
     * 
     * @param string $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleMaterials(string $userId)
    {
        return Material::accessibleBy($userId)->ordered()->get();
    }

    /**
     * Mark material as completed by user
     * 
     * @param string $materialId
     * @param string $userId
     * @return MaterialCompletion
     * @throws Exception
     */
    public function markAsCompleted(string $materialId, string $userId): MaterialCompletion
    {
        // Check if material exists
        $material = Material::find($materialId);
        if (!$material) {
            throw new Exception("Material with ID {$materialId} not found");
        }

        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            throw new Exception("User with ID {$userId} not found");
        }

        // Check if already completed
        $existing = MaterialCompletion::where('material_id', $materialId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Create completion record
        return MaterialCompletion::create([
            'material_id' => $materialId,
            'user_id' => $userId,
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if material is completed by user
     * 
     * @param string $materialId
     * @param string $userId
     * @return bool
     */
    public function isCompleted(string $materialId, string $userId): bool
    {
        return MaterialCompletion::where('material_id', $materialId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Validate YouTube URL format
     * 
     * @param string $url
     * @return bool
     */
    public function validateYoutubeUrl(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/(www\.)?youtube\.com\/watch\?v=[a-zA-Z0-9_-]+/',
            '/^https?:\/\/(www\.)?youtube\.com\/embed\/[a-zA-Z0-9_-]+/',
            '/^https?:\/\/youtu\.be\/[a-zA-Z0-9_-]+/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate PDF file size against max_pdf_size setting
     * 
     * @param UploadedFile $file
     * @return bool
     */
    public function validatePdfSize(UploadedFile $file): bool
    {
        $maxSizeMB = AppSetting::get('max_pdf_size', 50);
        $maxSizeBytes = $maxSizeMB * 1024 * 1024; // Convert MB to bytes

        return $file->getSize() <= $maxSizeBytes;
    }
}
