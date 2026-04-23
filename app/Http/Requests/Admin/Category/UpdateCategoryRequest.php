<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:50|unique:categories,name,' . $this->categoryId,
            'category_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}
