<x-form-errors />

<x-form-input
    name="title"
    :label="__('admin/courses.title_label')"
    :value="$course->title ?? ''"
    :required="true"
    autocomplete="off"
    autofocus
    maxlength="255"
/>

<div>
    <label for="description" class="form-label">{{ __('admin/courses.description_label') }}</label>
    <textarea id="description" name="description" rows="5"
        class="form-input @error('description') form-input-error @enderror">{{ old('description', $course->description ?? '') }}</textarea>
</div>

<div>
    <label for="thumbnail" class="form-label">{{ __('admin/courses.thumbnail_label') }}</label>
    <input id="thumbnail" type="file" name="thumbnail" accept="image/*"
        class="form-input @error('thumbnail') form-input-error @enderror">
    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ __('admin/courses.thumbnail_hint') }}</p>
</div>

<div>
    <label for="banner" class="form-label">{{ __('admin/courses.banner_label') }}</label>
    <input id="banner" type="file" name="banner" accept="image/*"
        class="form-input @error('banner') form-input-error @enderror">
    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ __('admin/courses.banner_hint') }}</p>
</div>
