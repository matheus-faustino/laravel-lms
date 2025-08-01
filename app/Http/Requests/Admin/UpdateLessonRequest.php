<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:video,text',
            'content' => 'required_if:type,text|nullable|string',
            'video_url' => 'required_if:type,video|nullable|url',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'sometimes|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required_if' => 'Content is required for text lessons.',
            'video_url.required_if' => 'Video URL is required for video lessons.',
            'video_url.url' => 'Video URL must be a valid URL.',
            'type.in' => 'Lesson type must be either video or text.',
        ];
    }
}
