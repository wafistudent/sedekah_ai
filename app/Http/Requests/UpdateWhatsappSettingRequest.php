<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateWhatsappSettingRequest
 *
 * Form request for updating WhatsApp settings
 *
 * @package App\Http\Requests
 */
class UpdateWhatsappSettingRequest extends FormRequest
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
            'api_url' => 'required|url|max:255',
            'api_key' => 'required|string|max:255',
            'is_mode_safe' => 'required|boolean',
            'message_delay_seconds' => 'required|integer|min:0|max:60',
            'auto_retry_enabled' => 'required|boolean',
            'retry_delay_minutes' => 'required|integer|min:1|max:60',
            'max_retry_attempts' => 'required|integer|min:1|max:10',
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
            'api_url.required' => 'API URL wajib diisi.',
            'api_url.url' => 'API URL harus berupa URL yang valid.',
            'api_key.required' => 'API Key wajib diisi.',
            'is_mode_safe.required' => 'Mode Safe wajib dipilih.',
            'message_delay_seconds.required' => 'Delay pesan wajib diisi.',
            'message_delay_seconds.min' => 'Delay pesan minimal 0 detik.',
            'message_delay_seconds.max' => 'Delay pesan maksimal 60 detik.',
            'auto_retry_enabled.required' => 'Auto retry wajib dipilih.',
            'retry_delay_minutes.required' => 'Delay retry wajib diisi.',
            'retry_delay_minutes.min' => 'Delay retry minimal 1 menit.',
            'retry_delay_minutes.max' => 'Delay retry maksimal 60 menit.',
            'max_retry_attempts.required' => 'Maksimal retry wajib diisi.',
            'max_retry_attempts.min' => 'Maksimal retry minimal 1 kali.',
            'max_retry_attempts.max' => 'Maksimal retry maksimal 10 kali.',
        ];
    }
}
