@if ($errors->any())
    <div class="alert-error mb-6">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li class="text-sm text-red-600 dark:text-red-400">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div>
    <label for="name" class="form-label">{{ __('shared/ui.name_label') }}</label>
    <input id="name" type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
        required autofocus autocomplete="off" maxlength="50"
        class="form-input @error('name') form-input-error @enderror">
</div>

<div>
    <label for="category_id" class="form-label">{{ __('admin/categories.parent_label') }}</label>
    <select id="category_id" name="category_id"
        class="form-input @error('category_id') form-input-error @enderror">
        <option value="">{{ __('admin/categories.no_parent') }}</option>
        @foreach ($parentOptions as $option)
            <option value="{{ $option->id }}"
                @selected(old('category_id', $category->category_id ?? '') == $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
    </select>
</div>
