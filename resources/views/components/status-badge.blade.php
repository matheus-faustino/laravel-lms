@props(['status', 'label'])
@php
    $colors = [
        'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'published' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'completed' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
        'draft'     => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'cancelled' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
    ];
@endphp
<span class="badge {{ $colors[$status] ?? $colors['draft'] }}">
    <i class="bi bi-circle-fill text-[6px]" aria-hidden="true"></i>
    {{ $label }}
</span>
