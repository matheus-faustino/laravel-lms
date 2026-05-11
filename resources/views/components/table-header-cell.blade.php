@props(['align' => 'left', 'hidden' => false])
<th
    scope="col"
    class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400
        {{ $align === 'right' ? 'text-right' : 'text-left' }}
        {{ $hidden ? 'hidden sm:table-cell' : '' }}"
>
    {{ $slot }}
</th>
