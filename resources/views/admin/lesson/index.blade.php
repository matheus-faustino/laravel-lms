@extends('layouts.admin')

@section('title', __('admin/lessons.manage_title', ['module' => $module->title]))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/courses.page_title'), 'url' => route('admin.courses.index')],
        ['label' => $module->course->title, 'url' => route('admin.modules.index', $module->course_id)],
        ['label' => $module->title],
    ]" />
@endsection

@section('content')

<div x-data="lessonManager()" x-init="init()">

    {{-- Lesson list --}}
    <div class="card overflow-hidden max-w-3xl">

        <ul id="lesson-list" class="divide-y divide-slate-100 dark:divide-slate-800">
            <template x-for="lesson in lessons" :key="lesson.id">
                <li :data-id="lesson.id"
                    class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors duration-100">
                    <span
                        class="sortable-handle cursor-grab text-slate-300 dark:text-slate-600 hover:text-slate-500 dark:hover:text-slate-400 shrink-0">
                        <i class="bi bi-grip-vertical text-lg" aria-hidden="true"></i>
                    </span>

                    <span x-text="lesson.order"
                        class="shrink-0 w-6 text-center text-xs font-semibold text-slate-400 dark:text-slate-500"></span>

                    <div class="flex-1 min-w-0">
                        <p x-text="lesson.title"
                            class="text-sm font-medium text-slate-800 dark:text-slate-100 truncate"></p>
                        <p x-text="lesson.description"
                            class="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5"></p>
                    </div>

                    <span x-text="lesson.type"
                        class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium capitalize"
                        :class="lesson.type === 'video'
                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                            : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'">
                    </span>

                    <span x-text="lesson.duration + 's'"
                        class="shrink-0 text-xs text-slate-400 dark:text-slate-500 tabular-nums"></span>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button @click="openEdit(lesson)" type="button" class="btn-edit py-1 px-2.5 text-xs">
                            <i class="bi bi-pencil" aria-hidden="true"></i>
                            {{ __('shared/ui.edit') }}
                        </button>
                        <button @click="confirmDelete(lesson)" type="button" class="btn-danger py-1 px-2.5 text-xs">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                            {{ __('shared/ui.delete') }}
                        </button>
                    </div>
                </li>
            </template>

            <li x-show="lessons.length === 0" class="px-6 py-16 text-center">
                <x-empty-state icon="play-circle" :message="__('admin/lessons.no_lessons_found')" />
            </li>
        </ul>

        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex justify-end">
            <button @click="openCreate()" type="button" class="btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                {{ __('admin/lessons.add_btn') }}
            </button>
        </div>

    </div>

    {{-- Modal --}}
    <div x-show="modalOpen" @keydown.escape.window="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">

        {{-- Backdrop --}}
        <div @click="closeModal()"
            class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm"></div>

        {{-- Dialog --}}
        <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-xl shadow-xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100"
                    x-text="modalMode === 'create' ? '{{ __('admin/lessons.new_lesson') }}' : '{{ __('admin/lessons.edit_lesson') }}'">
                </h2>
                <button @click="closeModal()" type="button"
                    class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors">
                    <i class="bi bi-x-lg text-lg" aria-hidden="true"></i>
                </button>
            </div>

            {{-- Body --}}
            <form @submit.prevent="submitForm()" class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                        {{ __('admin/lessons.title_label') }}
                    </label>
                    <input x-model="form.title" type="text" class="form-input w-full text-sm" required />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                        {{ __('admin/lessons.description_label') }}
                    </label>
                    <textarea x-model="form.description" rows="2" class="form-input w-full text-sm resize-none" required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                            {{ __('admin/lessons.type_label') }}
                        </label>
                        <select x-model="form.type" class="form-input w-full text-sm">
                            <option value="text">{{ __('admin/lessons.type_text') }}</option>
                            <option value="video">{{ __('admin/lessons.type_video') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                            {{ __('admin/lessons.duration_label') }}
                        </label>
                        <input x-model="form.duration" type="number" min="1" class="form-input w-full text-sm" required />
                    </div>
                </div>

                <div x-show="form.type === 'text'">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                        {{ __('admin/lessons.content_label') }}
                    </label>
                    <textarea x-model="form.content" rows="4" class="form-input w-full text-sm resize-y"></textarea>
                </div>

                <div x-show="form.type === 'video'">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                        {{ __('admin/lessons.youtube_id_label') }}
                    </label>
                    <input x-model="form.youtube_id" type="text" class="form-input w-full text-sm" />
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button @click="closeModal()" type="button" class="btn-secondary">
                        {{ __('shared/ui.cancel') }}
                    </button>
                    <button type="submit" :disabled="submitting" class="btn-primary">
                        <i class="bi bi-floppy" aria-hidden="true"></i>
                        <span x-text="submitting ? '{{ __('admin/lessons.saving') }}' : '{{ __('shared/ui.save_changes') }}'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
function lessonManager() {
    return {
        lessons: @json($lessons),

        modalOpen: false,
        modalMode: 'create',
        submitting: false,
        editingId: null,

        form: {
            title: '',
            description: '',
            type: 'text',
            duration: '',
            content: '',
            youtube_id: '',
        },

        storeUrl:   '{{ route('admin.lessons.store', [$module->course_id, $module->id]) }}',
        reorderUrl: '{{ route('admin.lessons.reorder', [$module->course_id, $module->id]) }}',
        updateBase: '{{ route('admin.lessons.update', [$module->course_id, $module->id, '__id__']) }}',
        deleteBase: '{{ route('admin.lessons.delete', [$module->course_id, $module->id, '__id__']) }}',

        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

        init() {
            this.initSortable();
        },

        initSortable() {
            Sortable.create(document.getElementById('lesson-list'), {
                handle: '.sortable-handle',
                animation: 150,
                filter: 'input,textarea,select',
                onEnd: () => this.syncOrder(),
            });
        },

        syncOrder() {
            const ids = [...document.querySelectorAll('#lesson-list [data-id]')]
                .map(el => parseInt(el.dataset.id));

            this.lessons = ids.map(id => this.lessons.find(l => l.id === id));

            fetch(this.reorderUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ lessons: ids }),
            }).catch(() => window.alert('{{ __('shared/errors.network_error') }}'));
        },

        openCreate() {
            this.modalMode = 'create';
            this.editingId = null;
            this.form = { title: '', description: '', type: 'text', duration: '', content: '', youtube_id: '' };
            this.modalOpen = true;
        },

        openEdit(lesson) {
            this.modalMode = 'edit';
            this.editingId = lesson.id;
            this.form = {
                title:      lesson.title,
                description: lesson.description,
                type:       lesson.type,
                duration:   lesson.duration,
                content:    lesson.content ?? '',
                youtube_id: lesson.youtube_id ?? '',
            };
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
            this.editingId = null;
        },

        async submitForm() {
            if (!this.form.title.trim() || !this.form.description.trim() || !this.form.duration) return;
            this.submitting = true;

            const isEdit = this.modalMode === 'edit';
            const url = isEdit
                ? this.updateBase.replace('__id__', this.editingId)
                : this.storeUrl;

            try {
                const response = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });

                if (response.ok) {
                    const lesson = await response.json();
                    if (isEdit) {
                        const idx = this.lessons.findIndex(l => l.id === lesson.id);
                        this.lessons[idx] = lesson;
                    } else {
                        this.lessons.push(lesson);
                    }
                    this.closeModal();
                } else {
                    const data = await response.json();
                    window.alert(data.message ?? '{{ __('shared/errors.generic_error') }}');
                }
            } catch {
                window.alert('{{ __('shared/errors.network_error') }}');
            } finally {
                this.submitting = false;
            }
        },

        async confirmDelete(lesson) {
            const msg = '{{ __('admin/lessons.delete_confirm', ['title' => ':title']) }}'.replace(':title', lesson.title);
            if (!window.confirm(msg)) return;
            try {
                const url = this.deleteBase.replace('__id__', lesson.id);
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                if (response.ok) {
                    this.lessons = this.lessons
                        .filter(l => l.id !== lesson.id)
                        .map((l, i) => ({ ...l, order: i + 1 }));
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
