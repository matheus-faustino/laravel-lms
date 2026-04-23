<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Interfaces\Services\CategoryServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryServiceInterface $categoryService) {}

    public function index(Request $request): View
    {
        $categories = $this->categoryService->getPaginatedCategories(
            $request->query('perPage'),
            [],
            ['id', 'name', 'category_id', 'created_at'],
        );

        return view('admin.category.index', compact('categories'));
    }

    public function create(): View
    {
        $parentOptions = $this->categoryService->getAllParentCategories(['id', 'name']);

        return view('admin.category.create', compact('parentOptions'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->createCategory($request->validated());

        return to_route('admin.categories.index')->with('success', __('admin/categories.created_success'));
    }

    public function edit(int $categoryId): View
    {
        $category = $this->categoryService->getCategory($categoryId);

        $parentOptions = $this->categoryService->getAllCategories(['id', 'name'])
            ->reject(fn($c) => $c->id === $categoryId);

        return view('admin.category.edit', compact('category', 'parentOptions'));
    }

    public function update(int $categoryId, UpdateCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->updateCategory($categoryId, $request->validated());

        return to_route('admin.categories.index')->with('success', __('admin/categories.updated_success'));
    }

    public function delete(int $categoryId): JsonResponse
    {
        $this->categoryService->deleteCategory($categoryId);

        return response()->json(['message' => __('admin/categories.deleted_success')]);
    }
}
