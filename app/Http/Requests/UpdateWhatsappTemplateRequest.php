<?php

namespace App\Http\Requests;

use App\Services\WhatsappTemplateService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateWhatsappTemplateRequest
 *
 * Form request for updating an existing WhatsApp template
 *
 * @package App\Http\Requests
 */
class UpdateWhatsappTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                'unique:whatsapp_templates,code,' . $this->route('template')->id
            ],
            'name' => 'required|string|max:255',
            'category' => 'required|in:member,commission,withdrawal,admin,general',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string|min:10|max:4000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom validation error messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Code template wajib diisi.',
            'code.regex' => 'Code hanya boleh berisi huruf kecil, angka, dan underscore.',
            'code.unique' => 'Code template sudah digunakan.',
            'name.required' => 'Nama template wajib diisi.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'content.required' => 'Konten template wajib diisi.',
            'content.min' => 'Konten minimal 10 karakter.',
            'content.max' => 'Konten maksimal 4000 karakter (batas WhatsApp).',
        ];
    }

    /**
     * Configure the validator instance with custom validation
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('content') && $this->filled('category')) {
                $templateService = app(WhatsappTemplateService::class);

                $availableVars = array_keys(
                    $templateService->getAvailableVariables($this->category)
                );

                $validation = $templateService->validateTemplate(
                    $this->content,
                    $availableVars
                );

                if (!$validation['valid']) {
                    $invalidVars = implode(', ', $validation['invalid_variables']);
                    $validator->errors()->add(
                        'content',
                        "Variabel tidak valid untuk kategori {$this->category}: {$invalidVars}"
                    );
                }
            }
        });
    }
}
