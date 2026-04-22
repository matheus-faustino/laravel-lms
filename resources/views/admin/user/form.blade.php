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
    <label for="name" class="form-label">Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
        required autofocus autocomplete="name" placeholder="Full name" maxlength="50"
        class="form-input @error('name') form-input-error @enderror">
</div>

<div>
    <label for="email" class="form-label">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
        required autocomplete="email" placeholder="user@example.com"
        class="form-input @error('email') form-input-error @enderror">
</div>

<div>
    <label for="password" class="form-label">Password</label>
    <input id="password" type="password" name="password"
        required autocomplete="new-password" placeholder="Minimum 8 characters"
        class="form-input @error('password') form-input-error @enderror">
</div>

<div>
    <label for="password_confirmation" class="form-label">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation"
        required autocomplete="new-password" placeholder="Repeat your password"
        class="form-input">
</div>
