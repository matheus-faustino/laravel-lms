@props(['icon', 'message'])
<div class="flex flex-col items-center gap-2">
    <i class="bi bi-{{ $icon }} text-4xl text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
    <p class="text-sm font-medium text-slate-400 dark:text-slate-500">{{ $message }}</p>
</div>
