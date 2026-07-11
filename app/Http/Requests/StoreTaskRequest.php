<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يتم التحقق من الصلاحيات داخل الكونترولر
    }

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'exists:projects,id'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            // مصفوفة معايير القبول (نقاط التحقق الفرعية) إن وجدت
            'acceptance_criteria' => ['nullable', 'array'],
            'acceptance_criteria.*' => ['string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المهمة مطلوب.',
            'priority.required' => 'يرجى تحديد أولوية المهمة.',
        ];
    }
}
