@props(['message' => null])
@if ($message)
<div class="alert-success mb-6">
    <i class="bi bi-check-circle-fill text-green-500 dark:text-green-400 shrink-0" aria-hidden="true"></i>
    <p class="text-sm text-green-700 dark:text-green-300">{{ $message }}</p>
</div>
@endif
