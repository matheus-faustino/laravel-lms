@props([
    'submitLabel',
    'submitIcon' => 'check',
    'cancelRoute',
    'cancelLabel' => null,
])
<div class="flex items-center gap-3 pt-2">
    <button type="submit" class="btn-primary">
        <i class="bi bi-{{ $submitIcon }}" aria-hidden="true"></i>
        {{ $submitLabel }}
    </button>
    <a href="{{ $cancelRoute }}" class="btn-secondary">
        {{ $cancelLabel ?? __('shared/ui.cancel') }}
    </a>
</div>
