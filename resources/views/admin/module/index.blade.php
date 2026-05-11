@extends('layouts.admin')

@section('title', __('admin/modules.manage_title', ['course' => $course->title]))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/courses.page_title'), 'url' => route('admin.courses.index')],
        ['label' => $course->title],
    ]" />
@endsection

@section('content')

<div x-data="moduleManager()" x-init="init()" class="max-w-2xl">
    <div class="card overflow-hidden">

        <ul id="module-list" class="divide-y divide-slate-100 dark:divide-slate-800">
            <template x-for="module in modules" :key="module.id">
                <li :data-id="module.id"
                    class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors duration-100">
                    <span
                        class="sortable-handle cursor-grab text-slate-300 dark:text-slate-600 hover:text-slate-500 dark:hover:text-slate-400 shrink-0">
                        <i class="bi bi-grip-vertical text-lg" aria-hidden="true"></i>
                    </span>

                    <span x-show="editingId !== module.id" x-text="module.order"
                        class="shrink-0 w-6 text-center text-xs font-semibold text-slate-400 dark:text-slate-500"></span>
                    <span x-show="editingId === module.id" class="shrink-0 w-6"></span>

                    <span x-show="editingId !== module.id" x-text="module.title"
                        class="flex-1 text-sm font-medium text-slate-800 dark:text-slate-100 truncate"></span>

                    <input x-show="editingId === module.id" x-model="editTitle"
                        @keydown.enter.prevent="saveEdit(module)" @keydown.escape.prevent="cancelEdit()"
                        class="form-input flex-1 py-1.5 text-sm" type="text" />

                    <div class="flex items-center gap-1.5 shrink-0">
                        <a x-show="editingId !== module.id"
                            :href="lessonsBase.replace('__id__', module.id)"
                            class="btn-secondary py-1 px-2.5 text-xs">
                            <i class="bi bi-play-circle" aria-hidden="true"></i>
                            {{ __('admin/modules.lessons_btn') }}
                        </a>

                        <button x-show="editingId !== module.id" @click="startEdit(module)" type="button"
                            class="btn-edit py-1 px-2.5 text-xs">
                            <i class="bi bi-pencil" aria-hidden="true"></i>
                            {{ __('shared/ui.edit') }}
                        </button>
                        <button x-show="editingId === module.id" @click="saveEdit(module)" :disabled="saving"
                            type="button" class="btn-primary py-1 px-2.5 text-xs">
                            <i class="bi bi-floppy" aria-hidden="true"></i>
                            <span
                                x-text="saving ? '{{ __('admin/modules.saving') }}' : '{{ __('shared/ui.save_changes') }}'"></span>
                        </button>
                        <button x-show="editingId === module.id" @click="cancelEdit()" type="button"
                            class="btn-secondary py-1 px-2.5 text-xs">
                            {{ __('shared/ui.cancel') }}
                        </button>

                        <button x-show="editingId !== module.id" @click="confirmDelete(module)" type="button"
                            class="btn-danger py-1 px-2.5 text-xs">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                            {{ __('shared/ui.delete') }}
                        </button>
                    </div>
                </li>
            </template>

            <li x-show="modules.length === 0" class="px-6 py-16 text-center">
                <x-empty-state icon="collection" :message="__('admin/modules.no_modules_found')" />
            </li>
        </ul>

        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
            <div class="flex items-center gap-3">
                <input x-model="newTitle" @keydown.enter.prevent="addModule()" type="text"
                    class="form-input flex-1 text-sm" placeholder="{{ __('admin/modules.title_placeholder') }}" />
                <button @click="addModule()" :disabled="adding" type="button" class="btn-primary shrink-0">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    <span
                        x-text="adding ? '{{ __('admin/modules.adding') }}' : '{{ __('admin/modules.add_btn') }}'"></span>
                </button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
    function moduleManager() {
    return {
        modules: @json($modules),
        newTitle: '',
        editingId: null,
        editTitle: '',
        adding: false,
        saving: false,

        storeUrl:   '{{ route('admin.modules.store', $course->id) }}',
        reorderUrl: '{{ route('admin.modules.reorder', $course->id) }}',
        updateBase:  '{{ route('admin.modules.update', [$course->id, '__id__']) }}',
        deleteBase:  '{{ route('admin.modules.delete', [$course->id, '__id__']) }}',
        lessonsBase: '{{ route('admin.lessons.index', [$course->id, '__id__']) }}',

        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

        init() {
            this.initSortable();
        },

        initSortable() {
            Sortable.create(document.getElementById('module-list'), {
                handle: '.sortable-handle',
                animation: 150,
                filter: 'input',
                onEnd: () => this.syncOrder(),
            });
        },

        syncOrder() {
            const ids = [...document.querySelectorAll('#module-list [data-id]')]
                .map(el => parseInt(el.dataset.id));

            this.modules = ids.map(id => this.modules.find(m => m.id === id));

            fetch(this.reorderUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ modules: ids }),
            }).catch(() => window.alert('{{ __('shared/errors.network_error') }}'));
        },

        async addModule() {
            if (!this.newTitle.trim()) return;
            this.adding = true;
            try {
                const response = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.newTitle }),
                });
                if (response.ok) {
                    const module = await response.json();
                    this.modules.push(module);
                    this.newTitle = '';
                } else {
                    const data = await response.json();
                    window.alert(data.message ?? '{{ __('shared/errors.generic_error') }}');
                }
            } catch {
                window.alert('{{ __('shared/errors.network_error') }}');
            } finally {
                this.adding = false;
            }
        },

        startEdit(module) {
            this.editingId = module.id;
            this.editTitle = module.title;
        },

        cancelEdit() {
            this.editingId = null;
            this.editTitle = '';
        },

        async saveEdit(module) {
            if (!this.editTitle.trim()) return;
            this.saving = true;
            try {
                const url = this.updateBase.replace('__id__', module.id);
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.editTitle }),
                });
                if (response.ok) {
                    const updated = await response.json();
                    const idx = this.modules.findIndex(m => m.id === module.id);
                    this.modules[idx] = updated;
                    this.cancelEdit();
                } else {
                    const data = await response.json();
                    window.alert(data.message ?? '{{ __('shared/errors.generic_error') }}');
                }
            } catch {
                window.alert('{{ __('shared/errors.network_error') }}');
            } finally {
                this.saving = false;
            }
        },

        async confirmDelete(module) {
            const msg = '{{ __('admin/modules.delete_confirm', ['title' => ':title']) }}'.replace(':title', module.title);
            if (!window.confirm(msg)) return;
            try {
                const url = this.deleteBase.replace('__id__', module.id);
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                if (response.ok) {
                    this.modules = this.modules
                        .filter(m => m.id !== module.id)
                        .map((m, i) => ({ ...m, order: i + 1 }));
                } else {
                    const data = await response.json();
                    window.alert(data.message ?? '{{ __('shared/errors.generic_error') }}');
                }
            } catch {
                window.alert('{{ __('shared/errors.network_error') }}');
            }
        },
    };
}
</script>

@endsection