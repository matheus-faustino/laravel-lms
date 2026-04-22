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
    <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
        required autofocus autocomplete="name" placeholder="{{ __('shared/ui.full_name_placeholder') }}" maxlength="50"
        class="form-input @error('name') form-input-error @enderror">
</div>

<div>
    <label for="email" class="form-label">{{ __('shared/ui.email_label') }}</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
        required autocomplete="email" placeholder="{{ __('admin/users.form_email_placeholder') }}"
        class="form-input @error('email') form-input-error @enderror">
</div>

<div>
    <label for="password" class="form-label">{{ __('shared/ui.password_label') }}</label>
    <input id="password" type="password" name="password"
        required autocomplete="new-password" placeholder="{{ __('shared/ui.password_placeholder') }}"
        class="form-input @error('password') form-input-error @enderror">
</div>

<div>
    <label for="password_confirmation" class="form-label">{{ __('shared/ui.confirm_password_label') }}</label>
    <input id="password_confirmation" type="password" name="password_confirmation"
        required autocomplete="new-password" placeholder="{{ __('shared/ui.confirm_password_placeholder') }}"
        class="form-input">
</div>
