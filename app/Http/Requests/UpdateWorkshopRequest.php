<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkshopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === \App\Models\User::ROLE_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'sometimes|required|date|after:now',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'capacity' => 'sometimes|required|integer|min:1',
            'speaker_name' => 'nullable|string|max:255',
            'speaker_bio' => 'nullable|string',
            'cover_photo' => 'nullable|image|max:2048',
            'speaker_photo' => 'nullable|image|max:2048',
        ];
    }
}
