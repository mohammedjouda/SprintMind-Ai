<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
        ];
    }
}
