<x-form-errors />

<div>
    <label for="user_id" class="form-label">{{ __('admin/enrollments.user_label') }}</label>
    <select id="user_id" name="user_id" class="form-input select2 @error('user_id') form-input-error @enderror">
        <option value=""></option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}"
                {{ old('user_id', $enrollment->user_id ?? '') == $user->id ? 'selected' : '' }}>
                {{ $user->name }} — {{ $user->email }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label for="course_id" class="form-label">{{ __('admin/enrollments.course_label') }}</label>
    <select id="course_id" name="course_id" class="form-input select2 @error('course_id') form-input-error @enderror">
        <option value=""></option>
        @foreach ($courses as $course)
            <option value="{{ $course->id }}"
                {{ old('course_id', $enrollment->course_id ?? '') == $course->id ? 'selected' : '' }}>
                {{ $course->title }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label for="status" class="form-label">{{ __('admin/enrollments.status_label') }}</label>
    <select id="status" name="status" class="form-input @error('status') form-input-error @enderror">
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}"
                {{ old('status', $enrollment->status->value ?? '') === $status->value ? 'selected' : '' }}>
                {{ __('admin/enrollments.status_' . $status->value) }}
            </option>
        @endforeach
    </select>
</div>
