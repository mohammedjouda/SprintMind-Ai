<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'expected_duration' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'use_ai_scaffold' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,archived,completed',
        ];
    }
}
