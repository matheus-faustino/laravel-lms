{{-- Error summary --}}
@if ($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li class="text-sm text-red-600">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Name --}}
<div>
    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
        Name
    </label>
    <input
        id="name"
        type="text"
        name="name"
        value="{{ old('name', $user->name ?? '') }}"
        required
        autofocus
        autocomplete="name"
        placeholder="Full name"
        maxlength="50"
        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
               @error('name') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror"
    >
</div>

{{-- Email --}}
<div>
    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
        Email
    </label>
    <input
        id="email"
        type="email"
        name="email"
        value="{{ old('email', $user->email ?? '') }}"
        required
        autocomplete="email"
        placeholder="user@example.com"
        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
               @error('email') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror"
    >
</div>

{{-- Password --}}
<div>
    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
        Password
    </label>
    <input
        id="password"
        type="password"
        name="password"
        required
        autocomplete="new-password"
        placeholder="Minimum 8 characters"
        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
               @error('password') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror"
    >
</div>

{{-- Password Confirmation --}}
<div>
    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
        Confirm password
    </label>
    <input
        id="password_confirmation"
        type="password"
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder="Repeat your password"
        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
    >
</div>
