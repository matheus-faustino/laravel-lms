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
    <label for="title" class="form-label">{{ __('admin/courses.title_label') }}</label>
    <input id="title" type="text" name="title" value="{{ old('title', $course->title ?? '') }}"
        required autofocus autocomplete="off" maxlength="255"
        class="form-input @error('title') form-input-error @enderror">
</div>

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
