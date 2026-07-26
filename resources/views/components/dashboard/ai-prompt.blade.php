<section x-data="{
    aiPromptText: '',
    isLoading: false,
    showModal: false,
    errorMessage: '',
    previewData: {
        project_name: '',
        category: 'software',
        sprint_name: '',
        sprint_goal: '',
        tasks: []
    },
    submitPrompt() {
        this.isLoading = true;
        this.errorMessage = '';
        fetch('{{ route('ai.prompt.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ai_prompt: this.aiPromptText })
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.error || 'حدث خطأ أثناء معالجة الفكرة بواسطة الذكاء الاصطناعي.');
                }
                return data;
            });
        })
        .then(data => {
            this.previewData = data;
            this.showModal = true;
        })
        .catch(error => {
            this.errorMessage = error.message || 'فشل الاتصال بالخادم. يرجى المحاولة مرة أخرى.';
            alert(this.errorMessage);
        })
        .finally(() => {
            this.isLoading = false;
        });
    },
    addNewTask() {
        if (!this.previewData.tasks) {
            this.previewData.tasks = [];
        }
        this.previewData.tasks.push({
            title: 'مهمة جديدة',
            description: '',
            priority: 'medium',
            story_points: 1,
            criteria: []
        });
    },
    removeTask(index) {
        this.previewData.tasks.splice(index, 1);
    },
    addCriteria(taskIndex) {
        if (!this.previewData.tasks[taskIndex].criteria) {
            this.previewData.tasks[taskIndex].criteria = [];
        }
        this.previewData.tasks[taskIndex].criteria.push('');
    },
    removeCriteria(taskIndex, critIndex) {
        this.previewData.tasks[taskIndex].criteria.splice(critIndex, 1);
    },
    approveAndSave() {
        this.isLoading = true;
        this.errorMessage = '';
        fetch('{{ route('ai.prompt.save') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.previewData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                throw new Error(data.error || 'حدث خطأ أثناء حفظ مساحة العمل.');
            }
        })
        .catch(error => {
            this.errorMessage = error.message || 'فشل حفظ المشروع في قاعدة البيانات.';
            this.isLoading = false;
        });
    }
}" class="bg-white p-6 rounded-3xl border border-primary/20 ai-glow relative overflow-hidden">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined text-[24px]" :class="isLoading ? 'animate-spin' : 'animate-pulse'">auto_awesome</span>
            </div>
            <div>
                <h3 class="font-extrabold text-base text-on-surface">مساعد التخطيط وتفكيك المهام بالذكاء الاصطناعي</h3>
                <p class="text-xs text-on-surface-variant">اكتب فكرة أو ميزة برمجية (مثلاً: نظام دفع، لوحة تحكم)، وسيقوم الـ AI بتحويلها لسبرنتات ومهام فرعية فوراً.</p>
            </div>
        </div>
        <span class="text-[11px] font-bold bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full">مدعوم بـ LLM Core</span>
    </div>

    <form @submit.prevent="submitPrompt()" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant">lightbulb</span>
            <input type="text" x-model="aiPromptText" required :disabled="isLoading"
                placeholder="ما الذي تريد بناءه أو إنجازه اليوم؟ (مثال: بناء متجر إلكتروني بـ Laravel)..."
                class="w-full pr-11 pl-4 py-3.5 bg-surface-container/50 border border-outline-variant/80 rounded-2xl text-sm focus:outline-none focus:border-primary focus:bg-white transition-all font-medium">
        </div>
        <button type="submit" :disabled="isLoading"
            class="bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold px-8 py-3.5 rounded-2xl text-sm shadow-lg shadow-primary/25 flex items-center justify-center gap-2 shrink-0 transition-all transform active:scale-95 group min-w-[160px]">
            <template x-if="isLoading">
                <span class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>جاري التحليل...</span>
                </span>
            </template>
            <template x-if="!isLoading">
                <span class="flex items-center gap-2">
                    <span>تفكيك الفكرة</span>
                    <span class="material-symbols-outlined text-[18px] group-hover:rotate-12 transition-transform">magic_button</span>
                </span>
            </template>
        </button>
    </form>

    <!-- ================= شاشة المراجعة والتعديل (Review Tailwind CSS Modal) ================= -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 sm:p-6" x-transition>
        <div @click.away="showModal = false" class="bg-white w-full max-w-4xl rounded-3xl border border-outline-variant/60 shadow-2xl flex flex-col max-h-[90vh] overflow-hidden animate-fadeIn">

            <!-- رأس المودال -->
            <div class="p-6 border-b border-outline-variant/40 flex items-center justify-between shrink-0 bg-surface-container/30">
                <button @click="showModal = false" type="button" class="text-on-surface-variant hover:text-on-surface p-1 rounded-xl hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">auto_awesome</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-on-surface text-right">معاينة وتخصيص مساحة العمل الذكية</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5 text-right">راجع خطة العمل المقترحة من الـ AI قبل الحفظ. يمكنك التعديل التام للمهام.</p>
                    </div>
                </div>
            </div>

            <!-- محتوى المودال (قابل للتمرير) -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-right" dir="rtl">

                <!-- تفاصيل المشروع الأساسية وقسم السبرنت -->
                <div class="bg-surface-container/30 p-5 rounded-3xl border border-outline-variant/40 space-y-4">
                    <h3 class="font-bold text-sm text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">folder</span>
                        <span>تفاصيل المشروع والسبرنت</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-[11px] font-bold text-on-surface-variant mb-1.5">اسم المشروع</label>
                            <input type="text" x-model="previewData.project_name"
                                class="w-full px-4 py-3 bg-white border border-outline-variant/80 rounded-2xl text-xs font-bold focus:outline-none focus:border-primary transition-all">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-[11px] font-bold text-on-surface-variant mb-1.5">تصنيف المشروع</label>
                            <select x-model="previewData.category"
                                class="w-full px-4 py-3 bg-white border border-outline-variant/80 rounded-2xl text-xs font-bold focus:outline-none focus:border-primary transition-all">
                                <option value="software">💻 تطوير البرمجيات</option>
                                <option value="marketing">📈 تسويق ومحتوى</option>
                                <option value="personal"> مشاريع شخصية</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-[11px] font-bold text-on-surface-variant mb-1.5">اسم السبرنت النشط</label>
                            <input type="text" x-model="previewData.sprint_name"
                                class="w-full px-4 py-3 bg-white border border-outline-variant/80 rounded-2xl text-xs font-bold focus:outline-none focus:border-primary transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-on-surface-variant mb-1.5">هدف السبرنت المخطط</label>
                        <input type="text" x-model="previewData.sprint_goal"
                            class="w-full px-4 py-3 bg-white border border-outline-variant/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-primary transition-all">
                    </div>
                </div>

                <!-- قائمة المهام المولدة والمعدلة -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <button @click="addNewTask()" type="button"
                            class="bg-indigo-50 hover:bg-primary/10 text-primary text-xs font-bold px-4 py-2.5 rounded-xl border border-primary/10 flex items-center gap-1.5 transition-all transform active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>إضافة مهمة جديدة</span>
                        </button>
                        <h3 class="font-black text-base text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary text-[20px]">format_list_bulleted</span>
                            <span>المهام التشغيلية المقترحة (<span x-text="previewData && previewData.tasks ? previewData.tasks.length : 0"></span> مهام)</span>
                        </h3>
                    </div>

                    <!-- قائمة المهام -->
                    <div class="space-y-4">
                        <template x-if="previewData && previewData.tasks">
                            <template x-for="(task, tIndex) in previewData.tasks" :key="tIndex">
                                <div class="p-5 rounded-3xl border border-outline-variant/60 bg-white hover:border-primary/30 transition-all relative group space-y-4">

                                    <!-- زر الحذف للمهمة -->
                                    <button @click="removeTask(tIndex)" type="button"
                                        class="absolute left-4 top-4 text-on-surface-variant hover:text-error hover:bg-error/5 p-1.5 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 pt-4">
                                        <!-- عنوان المهمة -->
                                        <div class="sm:col-span-8">
                                            <label class="block text-[10px] font-bold text-on-surface-variant mb-1">عنوان المهمة</label>
                                            <input type="text" x-model="task.title"
                                                class="w-full px-3 py-2.5 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-bold focus:outline-none focus:border-primary transition-all">
                                        </div>
                                        <!-- الأولوية -->
                                        <div class="sm:col-span-2">
                                            <label class="block text-[10px] font-bold text-on-surface-variant mb-1">الأولوية</label>
                                            <select x-model="task.priority"
                                                class="w-full px-3 py-2 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-bold focus:outline-none focus:border-primary transition-all">
                                                <option value="low">منخفضة</option>
                                                <option value="medium">متوسطة</option>
                                                <option value="high">عالية</option>
                                            </select>
                                        </div>
                                        <!-- نقاط الجهد -->
                                        <div class="sm:col-span-2">
                                            <label class="block text-[10px] font-bold text-on-surface-variant mb-1">النقاط (SP)</label>
                                            <input type="number" x-model="task.story_points" min="0"
                                                class="w-full px-3 py-2 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-bold focus:outline-none focus:border-primary transition-all">
                                        </div>
                                    </div>

                                    <!-- وصف المهمة -->
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">وصف المهمة</label>
                                        <textarea x-model="task.description" rows="2"
                                            class="w-full px-3 py-2 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs focus:outline-none focus:border-primary transition-all resize-none"></textarea>
                                    </div>

                                    <!-- معايير القبول للمهمة -->
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">معايير القبول (Acceptance Criteria)</label>
                                        <div class="space-y-2">
                                            <template x-for="(crit, cIndex) in task.criteria" :key="cIndex">
                                                <div class="flex items-center gap-2">
                                                    <input type="text" x-model="task.criteria[cIndex]"
                                                        class="flex-1 px-3 py-1.5 bg-surface-container/30 border border-outline-variant/80 rounded-xl text-xs focus:outline-none focus:border-primary transition-all">
                                                    <button @click="removeCriteria(tIndex, cIndex)" type="button" class="text-on-surface-variant hover:text-error p-1 rounded-lg">
                                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <button @click="addCriteria(tIndex)" type="button" class="text-primary text-[10px] font-bold hover:underline flex items-center gap-1 mt-1">
                                            <span class="material-symbols-outlined text-[12px]">add</span>
                                            <span>إضافة معيار قبول</span>
                                        </button>
                                    </div>

                                </div>
                            </template>
                        </template>
                    </div>
                </div>

            </div>

            <!-- تذييل المودال (الخطأ وأزرار الحفظ) -->
            <div class="p-6 border-t border-outline-variant/40 flex flex-col sm:flex-row-reverse sm:justify-between items-center gap-4 bg-surface-container/10 shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="approveAndSave()" type="button" :disabled="isLoading"
                        class="bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-2xl text-xs flex items-center justify-center gap-2 transition-all transform active:scale-95 shadow-md shadow-primary/20 min-w-[150px]">
                        <template x-if="isLoading">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>جاري الحفظ...</span>
                            </span>
                        </template>
                        <template x-if="!isLoading">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                <span>تأكيد وحفظ مساحة العمل</span>
                            </span>
                        </template>
                    </button>
                    <button @click="showModal = false" type="button" :disabled="isLoading"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 px-6 rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        <span>إلغاء</span>
                    </button>
                </div>

                <div x-show="errorMessage" class="text-xs text-error font-bold text-right" x-text="errorMessage"></div>
            </div>

        </div>
    </div>
</section>