<?php

namespace App\Http\Requests\Admin;

use App\Enums\LessonTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'duration' => 'required',
            'content' => 'required_if:type,' . LessonTypeEnum::TEXT->value,
            'youtube_id' => 'required_if:type,' . LessonTypeEnum::VIDEO->value,
        ];
    }
}
