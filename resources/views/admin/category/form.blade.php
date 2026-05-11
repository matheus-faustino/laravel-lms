<x-form-errors />

<x-form-input
    name="name"
    :label="__('shared/ui.name_label')"
    :value="$category->name ?? ''"
    :required="true"
    autocomplete="off"
    autofocus
    maxlength="50"
/>

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
