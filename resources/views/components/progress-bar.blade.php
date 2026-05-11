@props(['percentage', 'watched', 'total'])
<div>
    <div class="mb-1.5 flex items-center justify-between">
        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $watched }}/{{ $total }}</span>
        <span class="text-xs font-semibold text-sky-600 dark:text-sky-400">{{ $percentage }}%</span>
    </div>
    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
        <div class="h-full rounded-full bg-sky-500 dark:bg-sky-400 transition-all duration-500" style="width: {{ $percentage }}%"></div>
    </div>
</div>
