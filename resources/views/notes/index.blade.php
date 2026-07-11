<x-layouts.app title="SprintMind AI - ملاحظات العمل الذكية">

    <!-- Toast Notification System Script -->
    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 left-5 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl border text-sm font-bold transition-all duration-300 transform translate-y-10 opacity-0`;
            if (type === 'success') {
                toast.className += ' bg-emerald-50 text-emerald-800 border-emerald-200';
                toast.innerHTML = `<span class="material-symbols-outlined text-emerald-600">check_circle</span> <span>${message}</span>`;
            } else {
                toast.className += ' bg-rose-50 text-rose-800 border-rose-200';
                toast.innerHTML = `<span class="material-symbols-outlined text-rose-600">error</span> <span>${message}</span>`;
            }
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3500);
        }

        // Show session flash notification
        @if(session('success'))
        window.addEventListener('DOMContentLoaded', () => {
            showToast("{{ session('success') }}", 'success');
        });
        @endif
    </script>

    <!-- Notes Dashboard Container -->
    <div x-data="notesHub()" class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-primary-container text-on-primary-container text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">مركز الأفكار والملاحظات (Notes Hub)</span>
                    <span class="text-xs text-on-surface-variant">✨ حول أفكارك إلى مهام فورية بالذكاء الاصطناعي</span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-black text-on-surface font-tajawal">ملاحظات العمل والتحليل الذكي</h2>
                <p class="text-sm text-on-surface-variant mt-1">اكتب أفكارك وملاحظاتك بصيغة Markdown، وقم باستخراج المهام Agile Backlog مع مراجعتها وتعديلها يدوياً.</p>
            </div>

            <button @click="openNewNote()"
                class="bg-gradient-to-r from-indigo-900 to-primary text-white hover:opacity-95 font-bold px-5 py-2.5 rounded-2xl text-xs shadow-lg shadow-primary/20 flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>إضافة ملاحظة جديدة</span>
            </button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-3xl border border-outline-variant/60 card-elevation flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto items-center flex-1">
                <!-- Search Input -->
                <div class="relative w-full sm:max-w-xs">
                    <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text" x-model="search" placeholder="ابحث في الملاحظات..."
                        class="w-full pl-4 pr-10 py-2 bg-surface-container border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                </div>

                <!-- Pinned Toggle -->
                <label class="flex items-center gap-2 cursor-pointer self-start sm:self-auto shrink-0 select-none">
                    <input type="checkbox" x-model="filterPinned" class="rounded border-outline-variant text-primary focus:ring-primary">
                    <span class="text-xs font-bold text-on-surface-variant">عرض المثبتة فقط 📌</span>
                </label>
            </div>

            <!-- Tag Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto py-1 justify-start md:justify-end no-scrollbar">
                <button @click="selectedTag = 'all'"
                    :class="selectedTag === 'all' ? 'bg-primary text-white shadow-md shadow-primary/10' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shrink-0">
                    الكل
                </button>
                <template x-for="tag in allTags" :key="tag">
                    <button @click="selectedTag = tag"
                        :class="selectedTag === tag ? 'bg-primary text-white shadow-md shadow-primary/10' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shrink-0">
                        <span x-text="'#' + tag"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Notes Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Empty State -->
            <div x-show="filteredNotes.length === 0" x-cloak class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-2xl bg-surface-container flex items-center justify-center text-on-surface-variant mb-4">
                    <span class="material-symbols-outlined text-[32px]">sticky_note_2</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface">لا توجد ملاحظات</h3>
                <p class="text-xs text-on-surface-variant mt-1">ابدأ بإنشاء ملاحظتك الأولى أو غير خيارات التصفية.</p>
            </div>

            <!-- Card Template -->
            <template x-for="note in filteredNotes" :key="note.id">
                <div :class="{
                    'bg-white border-outline-variant/60': note.color === 'default',
                    'bg-blue-50/70 border-blue-200 text-blue-950': note.color === 'blue',
                    'bg-teal-50/70 border-teal-200 text-teal-950': note.color === 'green',
                    'bg-amber-50/70 border-amber-200 text-amber-950': note.color === 'amber',
                    'bg-rose-50/70 border-rose-200 text-rose-950': note.color === 'red'
                }"
                    class="rounded-3xl border p-6 flex flex-col justify-between card-elevation relative overflow-hidden transition-all duration-300 hover:shadow-md group">

                    <!-- Decorative Top Border -->
                    <div :class="{
                        'bg-primary/20': note.color === 'default',
                        'bg-blue-500': note.color === 'blue',
                        'bg-teal-500': note.color === 'green',
                        'bg-amber-500': note.color === 'amber',
                        'bg-rose-500': note.color === 'red'
                    }" class="absolute top-0 left-0 w-full h-1"></div>

                    <!-- Card Body Wrapper (Clickable to view note detail) -->
                    <div class="flex-grow flex flex-col cursor-pointer" @click="openViewNote(note)">
                        <!-- Title & Actions -->
                        <div class="flex justify-between items-start gap-4 mb-3">
                            <h3 class="text-base font-black truncate font-tajawal max-w-[70%]" x-text="note.title"></h3>
                            <div class="flex items-center gap-1.5" @click.stop>
                                <!-- Pin Button -->
                                <button @click="togglePin(note)" class="text-on-surface-variant hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[18px]" :class="note.is_pinned ? 'filled text-amber-500' : ''" x-text="note.is_pinned ? 'push_pin' : 'keep'"></span>
                                </button>
                                <!-- Edit Button -->
                                <button @click="openEditNote(note)" class="text-on-surface-variant hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <!-- Delete Button -->
                                <form :action="'/notes/' + note.id" method="POST" class="inline" @submit.prevent="if(confirm('هل أنت متأكد من حذف هذه الملاحظة؟')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-on-surface-variant hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Content Truncated -->
                        <p class="text-xs font-medium leading-relaxed whitespace-pre-line line-clamp-5 mb-4 text-on-surface-variant/90" x-text="note.content"></p>
                    </div>

                    <!-- Card Footer -->
                    <div>
                        <!-- Project Association -->
                        <div x-show="note.project" class="mb-3 flex items-center gap-1.5 text-[10px] font-bold text-primary">
                            <span class="material-symbols-outlined text-[14px]">folder</span>
                            <span x-text="note.project ? note.project.name : ''"></span>
                        </div>

                        <!-- Tags & Date -->
                        <div class="flex items-center justify-between gap-2 pt-3 border-t border-outline-variant/30 text-[10px] text-on-surface-variant font-bold">
                            <!-- Tags list -->
                            <div class="flex flex-wrap gap-1">
                                <template x-for="tag in note.tags" :key="tag">
                                    <span class="bg-black/5 px-2 py-0.5 rounded text-[9px]" x-text="'#' + tag"></span>
                                </template>
                            </div>
                            <!-- Date -->
                            <span x-text="formatDate(note.updated_at)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- View Note Modal (Drawer/Modal overlay) -->
        <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Modal Container -->
            <div class="bg-white rounded-3xl w-full max-w-2xl border border-outline-variant/60 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                @click.away="showViewModal = false">

                <!-- Header -->
                <div :class="{
                    'bg-slate-100 text-on-surface border-b border-outline-variant/40': viewingNote.color === 'default',
                    'bg-blue-500 text-white': viewingNote.color === 'blue',
                    'bg-teal-500 text-white': viewingNote.color === 'green',
                    'bg-amber-500 text-white': viewingNote.color === 'amber',
                    'bg-rose-500 text-white': viewingNote.color === 'red'
                }" class="px-6 py-4 flex justify-between items-center transition-colors duration-300">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[22px]">sticky_note_2</span>
                        <h3 class="text-base font-black font-tajawal truncate max-w-[80%]" x-text="viewingNote.title"></h3>
                    </div>
                    <button @click="showViewModal = false" class="hover:opacity-80 transition-opacity">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <!-- Note Metadata (Tags and Project) -->
                    <div class="flex flex-wrap items-center justify-between gap-4 py-2 border-b border-outline-variant/30 text-xs font-bold text-on-surface-variant">
                        <!-- Project Binding -->
                        <div class="flex items-center gap-1.5 text-primary">
                            <span class="material-symbols-outlined text-[16px]">folder</span>
                            <span x-text="viewingNote.project ? viewingNote.project.name : 'صندوق الوارد (Inbox)'"></span>
                        </div>

                        <!-- Date -->
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            <span x-text="formatDate(viewingNote.updated_at)"></span>
                        </div>
                    </div>

                    <!-- Note Content -->
                    <div class="text-sm font-medium leading-relaxed whitespace-pre-line text-on-surface/90" x-text="viewingNote.content"></div>

                    <!-- Tags list -->
                    <div x-show="viewingNote.tags && viewingNote.tags.length > 0" class="flex flex-wrap gap-1.5 pt-3">
                        <template x-for="tag in viewingNote.tags" :key="tag">
                            <span class="bg-primary/10 text-primary border border-primary/20 px-2.5 py-1 rounded-xl text-[10px] font-bold" x-text="'#' + tag"></span>
                        </template>
                    </div>
                </div>

                <!-- Footer / AI Actions -->
                <div class="px-6 py-4 border-t border-outline-variant/40 flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-container/30">

                    <!-- AI Actions inside View Note Modal -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <select x-model="aiMode"
                            class="px-3 py-2 bg-white border border-outline-variant/50 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="tasks">استخراج مهام (Backlog)</option>
                            <option value="project">تأسيس مشروع جديد</option>
                        </select>
                        <button type="button" @click="analyzeViewedNote()" :disabled="aiLoading"
                            class="bg-gradient-to-r from-indigo-900 via-primary to-indigo-800 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-lg shadow-primary/10 flex items-center gap-2 transition-all disabled:opacity-50">
                            <span x-show="!aiLoading" class="material-symbols-outlined text-[16px] text-emerald-400 animate-pulse">auto_awesome</span>
                            <span x-show="aiLoading" class="border-2 border-white border-t-transparent rounded-full w-4 h-4 animate-spin"></span>
                            <span x-text="aiLoading ? 'جاري التحليل...' : '✨ حول أفكارك إلى مهام فورية بالذكاء الاصطناعي'"></span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showViewModal = false; openEditNote(viewingNote)"
                            class="w-full sm:w-auto px-4 py-2 bg-primary/10 hover:bg-primary/25 text-primary text-xs font-bold rounded-xl transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            <span>تعديل الملاحظة</span>
                        </button>
                        <button type="button" @click="showViewModal = false"
                            class="w-full sm:w-auto px-4 py-2 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-xs font-bold text-on-surface rounded-xl transition-colors">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note Editor Modal (Drawer/Modal overlay) -->
        <div x-show="showEditorModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Modal Container -->
            <div class="bg-white rounded-3xl w-full max-w-2xl border border-outline-variant/60 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                @click.away="showEditorModal = false">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-outline-variant/40 flex justify-between items-center bg-surface-container/30">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]">edit_note</span>
                        <h3 class="text-base font-black text-on-surface" x-text="isEdit ? 'تعديل ملاحظة العمل' : 'إضافة ملاحظة عمل جديدة'"></h3>
                    </div>
                    <button @click="showEditorModal = false" class="text-on-surface-variant hover:text-error">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Form Body (Scrollable) -->
                <form :action="isEdit ? '/notes/' + activeNoteId : '/notes'" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Note Color Selection -->
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-2">لون الملاحظة البصري</label>
                        <div class="flex gap-2.5">
                            <input type="hidden" name="color" x-model="noteForm.color">
                            <!-- Default -->
                            <button type="button" @click="noteForm.color = 'default'"
                                :class="noteForm.color === 'default' ? 'ring-2 ring-primary ring-offset-2' : ''"
                                class="w-7 h-7 rounded-full bg-white border border-outline-variant/60 transition-all"></button>
                            <!-- Blue -->
                            <button type="button" @click="noteForm.color = 'blue'"
                                :class="noteForm.color === 'blue' ? 'ring-2 ring-primary ring-offset-2' : ''"
                                class="w-7 h-7 rounded-full bg-blue-100 border border-blue-300 transition-all"></button>
                            <!-- Green -->
                            <button type="button" @click="noteForm.color = 'green'"
                                :class="noteForm.color === 'green' ? 'ring-2 ring-primary ring-offset-2' : ''"
                                class="w-7 h-7 rounded-full bg-teal-100 border border-teal-300 transition-all"></button>
                            <!-- Amber -->
                            <button type="button" @click="noteForm.color = 'amber'"
                                :class="noteForm.color === 'amber' ? 'ring-2 ring-primary ring-offset-2' : ''"
                                class="w-7 h-7 rounded-full bg-amber-100 border border-amber-300 transition-all"></button>
                            <!-- Red -->
                            <button type="button" @click="noteForm.color = 'red'"
                                :class="noteForm.color === 'red' ? 'ring-2 ring-primary ring-offset-2' : ''"
                                class="w-7 h-7 rounded-full bg-rose-100 border border-rose-300 transition-all"></button>
                        </div>
                    </div>

                    <!-- Title & Project Binding -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="note_title" class="block text-xs font-bold text-on-surface-variant mb-1.5">العنوان</label>
                            <input type="text" id="note_title" name="title" x-model="noteForm.title" required
                                placeholder="اكتب عنواناً معبراً..."
                                class="w-full px-4 py-2.5 bg-surface-container border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                        </div>

                        <div>
                            <label for="note_project" class="block text-xs font-bold text-on-surface-variant mb-1.5">ربط بمشروع قائم (اختياري)</label>
                            <select id="note_project" name="project_id" x-model="noteForm.project_id"
                                class="w-full px-4 py-2.5 bg-surface-container border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                <option value="">لا يوجد ربط (صندوق الوارد)</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Content Input (Markdown Notes) -->
                    <div>
                        <label for="note_content" class="block text-xs font-bold text-on-surface-variant mb-1.5">محتوى الملاحظة (Markdown / نص تفصيلي)</label>
                        <textarea id="note_content" name="content" x-model="noteForm.content" rows="8" required
                            placeholder="اكتب ملاحظة العمل أو تفاصيل الفكرة هنا، مثل الأفكار، أو متطلبات الفيتشر، أو سيناريوهات الاختبار..."
                            class="w-full px-4 py-3 bg-surface-container border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                    </div>

                    <!-- Tags Section -->
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5">الوسوم (Tags)</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="newTagInput" @keydown.enter.prevent="addTagToForm()"
                                placeholder="مثال: ui, database"
                                class="flex-1 px-4 py-2 bg-surface-container border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            <button type="button" @click="addTagToForm()"
                                class="px-4 py-2 bg-surface-container-high hover:bg-surface-container-highest border border-outline-variant/60 text-xs font-bold text-on-surface rounded-xl transition-colors">
                                إضافة وسم
                            </button>
                        </div>
                        <!-- Hidden Input for Laravel -->
                        <template x-for="tag in noteForm.tags" :key="tag">
                            <input type="hidden" name="tags[]" :value="tag">
                        </template>
                        <!-- Tags pills -->
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <template x-for="(tag, index) in noteForm.tags" :key="index">
                                <span class="bg-primary/10 text-primary border border-primary/20 px-2.5 py-1 rounded-xl text-[10px] font-bold flex items-center gap-1">
                                    <span x-text="tag"></span>
                                    <button type="button" @click="removeTagFromForm(index)" class="text-primary hover:text-error">
                                        <span class="material-symbols-outlined text-[12px]">close</span>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Pinned Checkbox hidden field logic -->
                    <input type="hidden" name="is_pinned" :value="noteForm.is_pinned ? 1 : 0">
                    <label class="flex items-center gap-2 cursor-pointer py-1 select-none">
                        <input type="checkbox" x-model="noteForm.is_pinned" class="rounded border-outline-variant text-primary focus:ring-primary">
                        <span class="text-xs font-bold text-on-surface-variant">تثبيت هذه الملاحظة في الأعلى 📌</span>
                    </label>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-outline-variant/40 flex justify-center items-center gap-2">
                        <button type="button" @click="showEditorModal = false"
                            class="w-full sm:w-auto px-5 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-xs font-bold text-on-surface rounded-xl transition-colors">
                            إلغاء
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 bg-primary hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors">
                            حفظ الملاحظة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Human-in-the-Loop Preview Modal -->
        <div x-show="showPreviewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Modal Container -->
            <div class="bg-white rounded-3xl w-full max-w-4xl border border-outline-variant/60 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                @click.away="showPreviewModal = false">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-outline-variant/40 flex justify-between items-center bg-gradient-to-r from-slate-900 to-indigo-950 text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-emerald-400 text-[24px]">auto_awesome</span>
                        <div>
                            <h3 class="text-base font-black font-tajawal">مراجعة المهام المستخرجة بالذكاء الاصطناعي (AI Preview Hub)</h3>
                            <p class="text-[10px] text-indigo-200 mt-0.5">قم بمراجعة المهام، تعديلها، حذف غير المرغوب فيه، أو إضافة معايير القبول قبل الحفظ.</p>
                        </div>
                    </div>
                    <button @click="showPreviewModal = false" class="text-indigo-200 hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    <!-- AI Summary Alert -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex gap-3.5 items-start">
                        <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[18px]">summarize</span>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-indigo-950">الملخص التحليلي (AI Analysis Summary)</h4>
                            <p class="text-xs text-indigo-900/90 leading-relaxed mt-1 font-medium" x-text="aiResult.summary"></p>
                        </div>
                    </div>

                    <!-- Project Scaffold Binding Controls (Dynamic based on selected_action) -->
                    <div class="p-5 border border-outline-variant/60 rounded-2xl bg-surface-container/20 space-y-4">
                        <h4 class="text-xs font-black text-on-surface flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-primary">settings_applications</span>
                            <span>إعدادات حفظ المشروع والمهام</span>
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Scaffold New Project Fields -->
                            <template x-if="aiResult.suggested_action === 'project' && !commitProjectSelection.project_id">
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-on-surface-variant mb-1">اسم المشروع الجديد المقترح</label>
                                    <input type="text" x-model="commitProjectSelection.new_project_name"
                                        class="w-full px-3.5 py-2 bg-white border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                </div>
                            </template>

                            <template x-if="aiResult.suggested_action === 'project' && !commitProjectSelection.project_id">
                                <div>
                                    <label class="block text-[11px] font-bold text-on-surface-variant mb-1">تصنيف المشروع</label>
                                    <select x-model="commitProjectSelection.new_project_category"
                                        class="w-full px-3.5 py-2 bg-white border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                        <option value="software">تطوير برمجيات (Software)</option>
                                        <option value="marketing">تسويق (Marketing)</option>
                                        <option value="personal">شخصي (Personal)</option>
                                    </select>
                                </div>
                            </template>

                            <!-- Target Project Selection -->
                            <div :class="aiResult.suggested_action === 'project' ? 'md:col-span-3' : 'md:col-span-3'">
                                <label class="block text-[11px] font-bold text-on-surface-variant mb-1" x-text="aiResult.suggested_action === 'project' ? 'بدلاً من مشروع جديد، هل تريد الحفظ في مشروع قائم؟' : 'حدد المشروع المستهدف لحفظ المهام'"></label>
                                <select x-model="commitProjectSelection.project_id"
                                    class="w-full px-3.5 py-2 bg-white border border-outline-variant/50 rounded-xl text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                    <option value="" x-text="aiResult.suggested_action === 'project' ? 'تأسيس المشروع الجديد المكتوب بالأعلى' : 'الرجاء اختيار المشروع...'"></option>
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Table/List -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-black text-on-surface flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-primary">playlist_add_check</span>
                                <span>قائمة المهام المستخرجة (المعاينة التفاعلية)</span>
                            </h4>
                            <button type="button" @click="addPreviewTask()"
                                class="flex items-center gap-1 px-3 py-1.5 bg-secondary/10 hover:bg-secondary/20 text-secondary text-xs font-bold rounded-xl transition-all">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>مهمة يدوية جديدة</span>
                            </button>
                        </div>

                        <div class="divide-y divide-outline-variant/40 border border-outline-variant/50 rounded-2xl overflow-hidden bg-white">
                            <!-- Header -->
                            <div class="grid grid-cols-12 gap-3 px-4 py-2.5 bg-surface-container text-[11px] font-bold text-on-surface-variant text-right">
                                <div class="col-span-6 md:col-span-5">عنوان المهمة الجهد</div>
                                <div class="col-span-3 md:col-span-2">الأولوية</div>
                                <div class="col-span-3 md:col-span-1">Story Points</div>
                                <div class="col-span-12 md:col-span-3">معايير القبول (Acceptance Criteria)</div>
                                <div class="col-span-12 md:col-span-1 text-left hidden md:block">حذف</div>
                            </div>

                            <!-- Rows -->
                            <template x-for="(task, index) in aiResult.items" :key="task.temp_id">
                                <div class="grid grid-cols-12 gap-3 px-4 py-3 hover:bg-surface-container/20 items-center">
                                    <!-- Title -->
                                    <div class="col-span-12 md:col-span-5 flex gap-2 items-center">
                                        <span class="text-[10px] font-bold text-on-surface-variant font-geist w-5 shrink-0" x-text="index + 1 + '.'"></span>
                                        <input type="text" x-model="task.title" required
                                            class="w-full px-2 py-1 bg-surface-container/50 border border-outline-variant/40 rounded-lg text-xs font-bold focus:outline-none focus:ring-1 focus:ring-primary">
                                    </div>

                                    <!-- Priority -->
                                    <div class="col-span-6 md:col-span-2">
                                        <select x-model="task.priority"
                                            class="w-full px-2 py-1 bg-surface-container/50 border border-outline-variant/40 rounded-lg text-[11px] font-bold focus:outline-none">
                                            <option value="high">🔴 عالية (High)</option>
                                            <option value="medium">🟡 متوسطة (Medium)</option>
                                            <option value="low">🟢 منخفضة (Low)</option>
                                        </select>
                                    </div>

                                    <!-- Story Points -->
                                    <div class="col-span-6 md:col-span-1">
                                        <select x-model.number="task.story_points"
                                            class="w-full px-2 py-1 bg-surface-container/50 border border-outline-variant/40 rounded-lg text-[11px] font-bold focus:outline-none">
                                            <option value="1">1 pt</option>
                                            <option value="2">2 pt</option>
                                            <option value="3">3 pt</option>
                                            <option value="5">5 pt</option>
                                            <option value="8">8 pt</option>
                                        </select>
                                    </div>

                                    <!-- Acceptance Criteria -->
                                    <div class="col-span-10 md:col-span-3">
                                        <input type="text" x-model="task.acceptance_criteria" placeholder="الشرط المطلوب للإنجاز..."
                                            class="w-full px-2 py-1 bg-surface-container/50 border border-outline-variant/40 rounded-lg text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary">
                                    </div>

                                    <!-- Delete Button -->
                                    <div class="col-span-2 md:col-span-1 text-left">
                                        <button type="button" @click="removePreviewTask(task.temp_id)"
                                            class="text-on-surface-variant hover:text-error transition-colors p-1 hover:bg-error-container/20 rounded-lg">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 border-t border-outline-variant/40 flex justify-between items-center bg-surface-container/30">
                    <button type="button" @click="showPreviewModal = false; showViewModal = true"
                        class="px-5 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-xs font-bold text-on-surface rounded-xl transition-colors">
                        العودة للمعاينة
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="showPreviewModal = false"
                            class="px-5 py-2.5 bg-surface-container-high hover:bg-surface-container-highest text-xs font-bold text-on-surface rounded-xl transition-colors">
                            إلغاء تماماً
                        </button>
                        <button type="button" @click="commitTasks()"
                            class="px-6 py-2.5 bg-secondary hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            <span>💾 موافقة وحفظ في الـ Backlog</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine Notes Manager Definition -->
    <script>
        function notesHub() {
            return {
                search: '',
                selectedTag: 'all',
                filterPinned: false,
                notes: @json($notes),
                allTags: @json($allTags),

                // View properties
                showViewModal: false,
                viewingNote: {
                    title: '',
                    content: '',
                    color: 'default',
                    tags: [],
                    project: null
                },

                // Editor properties
                isEdit: false,
                activeNoteId: null,
                showEditorModal: false,
                noteForm: {
                    title: '',
                    content: '',
                    project_id: '',
                    is_pinned: false,
                    color: 'default',
                    tags: []
                },
                newTagInput: '',

                // AI properties
                aiLoading: false,
                aiMode: 'tasks',
                showPreviewModal: false,
                aiResult: {
                    summary: '',
                    suggested_action: 'tasks',
                    project_name: '',
                    category: 'software',
                    items: []
                },
                commitProjectSelection: {
                    project_id: '',
                    new_project_name: '',
                    new_project_category: 'software'
                },

                init() {
                    // Reactive initialization logic if required
                },

                openNewNote() {
                    this.isEdit = false;
                    this.activeNoteId = null;
                    this.noteForm = {
                        title: '',
                        content: '',
                        project_id: '',
                        is_pinned: false,
                        color: 'default',
                        tags: []
                    };
                    this.newTagInput = '';
                    this.showEditorModal = true;
                },

                openEditNote(note) {
                    this.isEdit = true;
                    this.activeNoteId = note.id;
                    this.noteForm = {
                        title: note.title,
                        content: note.content,
                        project_id: note.project_id || '',
                        is_pinned: !!note.is_pinned,
                        color: note.color || 'default',
                        tags: Array.isArray(note.tags) ? [...note.tags] : []
                    };
                    this.newTagInput = '';
                    this.showEditorModal = true;
                },

                openViewNote(note) {
                    this.viewingNote = note;
                    this.aiMode = 'tasks';
                    this.showViewModal = true;
                },

                addTagToForm() {
                    const tag = this.newTagInput.trim().toLowerCase();
                    if (tag && !this.noteForm.tags.includes(tag)) {
                        this.noteForm.tags.push(tag);
                    }
                    this.newTagInput = '';
                },

                removeTagFromForm(index) {
                    this.noteForm.tags.splice(index, 1);
                },

                togglePin(note) {
                    fetch(`/notes/${note.id}/toggle-pin`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                note.is_pinned = data.is_pinned;

                                // Re-sort notes by pinned status
                                this.notes.sort((a, b) => {
                                    if (a.is_pinned && !b.is_pinned) return -1;
                                    if (!a.is_pinned && b.is_pinned) return 1;
                                    return new Date(b.created_at) - new Date(a.created_at);
                                });

                                showToast(data.is_pinned ? 'تم تثبيت الملاحظة 📌' : 'تم إلغاء التثبيت 🔓');
                            }
                        })
                        .catch(err => {
                            console.error('Pin toggle failed: ', err);
                            showToast('فشلت عملية التثبيت.', 'error');
                        });
                },

                analyzeViewedNote() {
                    if (!this.viewingNote.content.trim()) {
                        showToast('الرجاء كتابة محتوى للملاحظة أولاً لتحليلها بالـ AI.', 'error');
                        return;
                    }

                    this.aiLoading = true;

                    fetch('/notes/analyze', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                content: this.viewingNote.content,
                                mode: this.aiMode
                            })
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('فشل في الاتصال بمزود الذكاء الاصطناعي.');
                            return res.json();
                        })
                        .then(data => {
                            if (data.error) {
                                showToast(data.error, 'error');
                            } else {
                                this.aiResult = {
                                    summary: data.summary || '',
                                    suggested_action: data.suggested_action || this.aiMode,
                                    project_name: data.project_name || this.viewingNote.title || '',
                                    category: data.category || 'software',
                                    items: Array.isArray(data.items) ? data.items.map((item, idx) => ({
                                        temp_id: item.temp_id || (idx + 1),
                                        title: item.title || '',
                                        priority: item.priority || 'medium',
                                        story_points: item.story_points || 1,
                                        acceptance_criteria: item.acceptance_criteria || ''
                                    })) : []
                                };

                                this.commitProjectSelection.project_id = this.viewingNote.project_id || '';
                                this.commitProjectSelection.new_project_name = this.aiResult.project_name;
                                this.commitProjectSelection.new_project_category = this.aiResult.category;

                                // Display preview
                                this.showPreviewModal = true;
                                this.showViewModal = false;
                                showToast('تم استخراج المهام بنجاح! راجعها وقم بحفظها.');
                            }
                        })
                        .catch(err => {
                            showToast(err.message || 'فشلت عملية التحليل.', 'error');
                        })
                        .finally(() => {
                            this.aiLoading = false;
                        });
                },

                addPreviewTask() {
                    const nextId = this.aiResult.items.length ? Math.max(...this.aiResult.items.map(t => t.temp_id)) + 1 : 1;
                    this.aiResult.items.push({
                        temp_id: nextId,
                        title: 'مهمة جديدة',
                        priority: 'medium',
                        story_points: 1,
                        acceptance_criteria: ''
                    });
                },

                removePreviewTask(tempId) {
                    this.aiResult.items = this.aiResult.items.filter(t => t.temp_id !== tempId);
                },

                commitTasks() {
                    if (!this.aiResult.items.length) {
                        showToast('لا توجد مهام للحفظ.', 'error');
                        return;
                    }

                    // Validation if task mode and no project is selected
                    if (this.aiResult.suggested_action === 'tasks' && !this.commitProjectSelection.project_id) {
                        showToast('الرجاء اختيار مشروع مستهدف أولاً لحفظ المهام.', 'error');
                        return;
                    }

                    const bodyData = {
                        project_id: this.commitProjectSelection.project_id || null,
                        new_project_name: this.commitProjectSelection.new_project_name,
                        new_project_category: this.commitProjectSelection.new_project_category,
                        tasks: this.aiResult.items
                    };

                    fetch('/notes/commit-tasks', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(bodyData)
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('فشل حفظ المهام في قاعدة البيانات.');
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showToast(`تم حفظ ${data.count} مهمة بنجاح في الـ Backlog! 🎉`);
                                this.showPreviewModal = false;

                                // Reload page to refresh sidebar count and display updates
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1200);
                            } else {
                                showToast(data.error || 'فشل حفظ المهام.', 'error');
                            }
                        })
                        .catch(err => {
                            showToast(err.message || 'حدث خطأ.', 'error');
                        });
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('ar-EG', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                get filteredNotes() {
                    return this.notes.filter(note => {
                        const matchesSearch = this.search.trim() === '' ||
                            note.title.toLowerCase().includes(this.search.toLowerCase()) ||
                            note.content.toLowerCase().includes(this.search.toLowerCase());

                        const matchesTag = this.selectedTag === 'all' ||
                            (Array.isArray(note.tags) && note.tags.includes(this.selectedTag));

                        const matchesPin = !this.filterPinned || note.is_pinned;

                        return matchesSearch && matchesTag && matchesPin;
                    });
                }
            };
        }
    </script>
</x-layouts.app>