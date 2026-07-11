<x-layouts.app title="SprintMind AI - السبرنتات الذكية">

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

    <div x-data="sprintsDashboard()" class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-indigo-50 text-indigo-700 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">إدارة السبرنتات (Sprints Management)</span>
                    <span class="text-xs text-on-surface-variant">🧠 مدعوم بالذكاء الاصطناعي كمستشار Agile</span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-black text-on-surface font-tajawal">مخطط السبرنتات ولوحة التحكم</h2>
                <p class="text-sm text-on-surface-variant mt-1">تخطيط وجدولة السبرنتات الذكية، تتبع التقدم، والتخطيط التلقائي لمهام الـ Backlog.</p>
            </div>

            <div class="flex gap-2.5">
                <button @click="openAiPlanner()"
                    class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold px-5 py-2.5 rounded-2xl text-xs shadow-lg shadow-indigo-200 flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-sm">auto_awesome</span>
                    <span>🚀 AI One-Click Sprint Planner</span>
                </button>
                
                <button @click="openNewSprint()"
                    class="bg-white border border-outline-variant hover:bg-surface-container/60 text-on-surface font-bold px-4 py-2.5 rounded-2xl text-xs flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>إنشاء يدوي</span>
                </button>
            </div>
        </div>

        <!-- Sprints List Grid -->
        <div class="grid grid-cols-1 gap-6">
            @php
                $activeSprints = $sprints->where('status', 'active');
                $plannedSprints = $sprints->where('status', 'planned');
                $completedSprints = $sprints->where('status', 'completed');
            @endphp

            <!-- 1. Active Sprints (Highlighted at the top) -->
            <div>
                <h3 class="text-sm font-bold text-on-surface-variant mb-3 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>السبرنت الحالي النشط (Active Sprint)</span>
                </h3>
                @if($activeSprints->isEmpty())
                    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-6 text-center text-on-surface-variant text-xs">
                        لا يوجد سبرنت نشط حالياً. استخدم مخطط الذكاء الاصطناعي لبدء سبرنت جديد بنقرة واحدة!
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($activeSprints as $sprint)
                            <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden group border border-indigo-500/20">
                                <div class="absolute -left-6 -bottom-6 text-9xl text-white/5 pointer-events-none select-none">
                                    <span class="material-symbols-outlined text-[150px]">bolt</span>
                                </div>
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-extrabold px-2.5 py-1 bg-emerald-500 text-slate-900 rounded-full">نشط الآن</span>
                                            <form action="{{ route('sprints.destroy', $sprint->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف السبرنت النشط؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-indigo-200 hover:text-rose-400 transition-all flex items-center">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                        <h4 class="text-xl font-black mt-2 font-tajawal">{{ $sprint->name }}</h4>
                                        <p class="text-xs text-indigo-200 mt-1 line-clamp-2">{{ $sprint->goal }}</p>
                                    </div>
                                    <a href="{{ route('sprints.show', $sprint->id) }}"
                                        class="bg-white text-indigo-900 hover:bg-indigo-50 font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-md transition-all shrink-0">
                                        <span>لوحة الـ Kanban</span>
                                        <span class="material-symbols-outlined text-sm">arrow_left</span>
                                    </a>
                                </div>

                                <div class="mt-6 grid grid-cols-3 gap-4 border-t border-white/10 pt-4 text-xs">
                                    <div>
                                        <span class="text-indigo-300 block">المدى الزمني</span>
                                        <span class="font-bold text-white">{{ $sprint->start_date?->format('Y-m-d') ?? '-' }} إلى {{ $sprint->end_date?->format('Y-m-d') ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-indigo-300 block">السرعة المستهدفة</span>
                                        <span class="font-bold text-white">{{ $sprint->target_velocity }} نقطة جهود</span>
                                    </div>
                                    <div>
                                        <span class="text-indigo-300 block">المهام الكلية</span>
                                        <span class="font-bold text-white">{{ $sprint->tasks->count() }} مهام ({{ $sprint->tasks->sum('story_points') }} نقطة)</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 2. Planned Sprints -->
            <div class="mt-4">
                <h3 class="text-sm font-bold text-on-surface-variant mb-3 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-violet-400"></span>
                    <span>سبرنتات مخططة ومجدولة (Planned Sprints)</span>
                </h3>
                @if($plannedSprints->isEmpty())
                    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-4 text-center text-on-surface-variant text-xs">
                        لا توجد سبرنتات مخططة.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($plannedSprints as $sprint)
                            <div class="bg-white border border-outline-variant rounded-2xl p-5 hover:border-primary/40 hover:shadow-md transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-[10px] font-bold px-2 py-0.5 bg-surface-container-high text-on-surface-variant rounded-md">مخطط</span>
                                        <div class="flex gap-1">
                                            <a href="{{ route('sprints.edit', $sprint->id) }}" class="text-on-surface-variant hover:text-primary transition-all">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                            <form action="{{ route('sprints.destroy', $sprint->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف السبرنت؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-on-surface-variant hover:text-error transition-all">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <h4 class="text-base font-bold text-on-surface mt-2">{{ $sprint->name }}</h4>
                                    <p class="text-xs text-on-surface-variant mt-1 line-clamp-2">{{ $sprint->goal ?: 'بدون هدف محدد' }}</p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-outline-variant/60 flex justify-between items-center text-xs">
                                    <span class="text-on-surface-variant">الجهد: {{ $sprint->tasks->sum('story_points') }} / {{ $sprint->target_velocity }} Pts</span>
                                    <a href="{{ route('sprints.show', $sprint->id) }}" class="text-primary font-bold hover:underline">عرض التفاصيل ←</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 3. Completed Sprints -->
            <div class="mt-4">
                <h3 class="text-sm font-bold text-on-surface-variant mb-3 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <span>سبرنتات منتهية (Completed Sprints)</span>
                </h3>
                @if($completedSprints->isEmpty())
                    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-4 text-center text-on-surface-variant text-xs">
                        لا توجد سبرنتات منتهية سابقاً.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($completedSprints as $sprint)
                            <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-5 opacity-75 hover:opacity-100 transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md">منتهي</span>
                                        <form action="{{ route('sprints.destroy', $sprint->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف السبرنت المؤرشف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-on-surface-variant hover:text-error transition-all flex items-center">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                    <h4 class="text-base font-bold text-on-surface mt-2">{{ $sprint->name }}</h4>
                                    <p class="text-xs text-on-surface-variant mt-1 line-clamp-2">{{ $sprint->goal }}</p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-outline-variant/60 flex justify-between items-center text-xs text-on-surface-variant">
                                    <span>المهام: {{ $sprint->tasks->where('status', 'completed')->count() }} / {{ $sprint->tasks->count() }}</span>
                                    <a href="{{ route('sprints.show', $sprint->id) }}" class="text-on-surface font-bold hover:underline">عرض الأرشيف ←</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- MODAL 1: Manual Sprint Creation -->
        <div x-show="manualModalOpen" x-cloak
            class="fixed inset-0 bg-black/55 z-[99] flex items-center justify-center p-4 backdrop-blur-sm transition-all"
            style="display: none;">
            <div @click.away="manualModalOpen = false"
                class="bg-white w-full max-w-lg rounded-3xl p-6 shadow-2xl border border-outline-variant/60 transform transition-all duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-black text-on-surface">إنشاء سبرنت جديد يدوياً</h3>
                    <button @click="manualModalOpen = false" class="text-on-surface-variant hover:text-error transition-all">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('sprints.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1">المشروع المرتبط</label>
                        <select name="project_id" class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-primary">
                            <option value="">بدون مشروع (عام)</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1">اسم السبرنت *</label>
                        <input type="text" name="name" required placeholder="مثال: سبرنت 1 - الأساسيات"
                            class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1">هدف السبرنت</label>
                        <textarea name="goal" rows="2" placeholder="اكتب الهدف الرئيسي من السبرنت..."
                            class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-primary"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">تاريخ البدء</label>
                            <input type="date" name="start_date"
                                class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">تاريخ الانتهاء</label>
                            <input type="date" name="end_date"
                                class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1">السرعة المستهدفة (Target Velocity - بالنقاط)</label>
                        <input type="number" name="target_velocity" value="40" min="1"
                            class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="manualModalOpen = false"
                            class="bg-surface-container hover:bg-surface-container-high text-on-surface font-bold px-4 py-2 rounded-xl text-xs transition-all">إلغاء</button>
                        <button type="submit"
                            class="bg-primary text-white hover:opacity-95 font-bold px-5 py-2 rounded-xl text-xs shadow-md shadow-primary/10 transition-all">إنشاء السبرنت</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: AI One-Click Sprint Planner -->
        <div x-show="aiModalOpen" x-cloak
            class="fixed inset-0 bg-black/55 z-[99] flex items-center justify-center p-4 backdrop-blur-sm transition-all"
            style="display: none;">
            <div @click.away="!loading && (aiModalOpen = false)"
                class="bg-white w-full max-w-2xl rounded-3xl p-6 shadow-2xl border border-outline-variant/60 transform transition-all duration-300">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-violet-600">auto_awesome</span>
                        <h3 class="text-lg font-black text-on-surface font-tajawal">مخطط السبرنتات الذكي بالذكاء الاصطناعي</h3>
                    </div>
                    <button @click="aiModalOpen = false" :disabled="loading" class="text-on-surface-variant hover:text-error transition-all disabled:opacity-50">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Step 1: Input Parameters -->
                <div x-show="!aiSuggestion" class="space-y-4">
                    <p class="text-xs text-on-surface-variant">يقوم مستشار السكرام بتحليل المهام غير المجدولة في Backlog وجمع حزمة متناسقة ذات قيمة للمطور بناءً على السرعة المستهدفة.</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">السرعة المستهدفة (Story Points)</label>
                            <input type="number" x-model="targetVelocity" min="1"
                                class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-violet-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">مدة السبرنت (بالأسابيع)</label>
                            <select x-model="durationWeeks" class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-violet-500">
                                <option value="1">أسبوع واحد (1 Week)</option>
                                <option value="2">أسبوعين (2 Weeks)</option>
                                <option value="3">3 أسابيع (3 Weeks)</option>
                                <option value="4">4 أسابيع (4 Weeks)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Generate Trigger -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="aiModalOpen = false"
                            class="bg-surface-container hover:bg-surface-container-high text-on-surface font-bold px-4 py-2 rounded-xl text-xs transition-all">إلغاء</button>
                        <button type="button" @click="generateAiPlan()"
                            class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold px-6 py-2 rounded-xl text-xs shadow-md shadow-violet-200 flex items-center gap-1.5 transition-all">
                            <span>تخطيط ذكي</span>
                            <span class="material-symbols-outlined text-sm">rocket_launch</span>
                        </button>
                    </div>
                </div>

                <!-- Loading Animation -->
                <div x-show="loading" class="flex flex-col items-center justify-center py-10 space-y-4">
                    <div class="w-12 h-12 rounded-full border-4 border-violet-100 border-t-violet-600 animate-spin"></div>
                    <p class="text-xs font-bold text-on-surface-variant animate-pulse font-tajawal">يقوم الذكاء الاصطناعي بتحليل المهام وتحديد سعة السبرنت، يرجى الانتظار...</p>
                </div>

                <!-- Step 2: Suggestion Preview & Edit -->
                <div x-show="aiSuggestion && !loading" class="space-y-4">
                    <div class="bg-violet-50 border border-violet-100 rounded-2xl p-4">
                        <div class="flex items-center gap-2 text-violet-800 font-bold text-xs mb-1">
                            <span class="material-symbols-outlined text-sm">psychology</span>
                            <span>تحليل مستشار Agile:</span>
                        </div>
                        <p class="text-xs text-violet-900" x-text="aiSuggestion?.reasoning"></p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">الاسم المقترح للسبرنت</label>
                            <input type="text" x-model="suggestedName"
                                class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-violet-500 font-bold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">هدف السبرنت</label>
                            <textarea x-model="suggestedGoal" rows="2"
                                class="w-full bg-surface-container rounded-xl px-4 py-2.5 text-xs border-0 focus:ring-2 focus:ring-violet-500"></textarea>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-xs font-bold text-on-surface-variant">المهام المختارة المقترحة</label>
                                <span class="text-[11px] font-extrabold bg-violet-100 text-violet-800 px-2 py-0.5 rounded-full"
                                    x-text="`إجمالي النقاط: ${aiSuggestion?.total_points} Pts`"></span>
                            </div>
                            <div class="bg-surface-container rounded-2xl p-3 max-h-48 overflow-y-auto space-y-2">
                                <template x-for="task in aiSuggestion?.selected_tasks" :key="task.id">
                                    <div class="bg-white p-2.5 rounded-xl border border-outline-variant/60 flex justify-between items-center text-xs">
                                        <div class="flex items-center gap-2">
                                            <span :class="{
                                                'bg-rose-100 text-rose-800': task.priority === 'high',
                                                'bg-amber-100 text-amber-800': task.priority === 'medium',
                                                'bg-slate-100 text-slate-800': task.priority === 'low'
                                            }" class="text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="task.priority === 'high' ? 'عالي' : (task.priority === 'medium' ? 'متوسط' : 'منخفض')"></span>
                                            <span class="font-bold text-on-surface line-clamp-1" x-text="task.title"></span>
                                        </div>
                                        <span class="font-bold text-on-surface-variant shrink-0 bg-surface-container px-2 py-0.5 rounded-md" x-text="`⚡ ${task.story_points}pt`"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Commit Trigger -->
                    <div class="flex justify-between pt-2 border-t border-outline-variant/60">
                        <button type="button" @click="aiSuggestion = null"
                            class="bg-surface-container hover:bg-surface-container-high text-on-surface font-bold px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">arrow_right_alt</span>
                            <span>تعديل المدخلات</span>
                        </button>
                        <button type="button" @click="commitAiSprint()"
                            class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold px-6 py-2 rounded-xl text-xs shadow-md shadow-emerald-100 flex items-center gap-1.5 transition-all">
                            <span>⚡ Start This Sprint</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Dashboard controller -->
    <script>
        function sprintsDashboard() {
            return {
                manualModalOpen: false,
                aiModalOpen: false,
                loading: false,
                targetVelocity: 40,
                durationWeeks: 2,
                aiSuggestion: null,
                suggestedName: '',
                suggestedGoal: '',

                openNewSprint() {
                    this.manualModalOpen = true;
                },

                openAiPlanner() {
                    this.aiSuggestion = null;
                    this.aiModalOpen = true;
                },

                async generateAiPlan() {
                    this.loading = true;
                    this.aiSuggestion = null;

                    try {
                        const response = await fetch("{{ route('sprints.ai-plan') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                target_velocity: this.targetVelocity,
                                duration_weeks: this.durationWeeks
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.aiSuggestion = data;
                            this.suggestedName = data.sprint_name;
                            this.suggestedGoal = data.sprint_goal;
                        } else {
                            showToast(data.message || 'فشلت عملية التخطيط بالذكاء الاصطناعي.', 'error');
                        }
                    } catch (e) {
                        showToast('حدث خطأ فني أثناء الاتصال بالخادم.', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async commitAiSprint() {
                    if (!this.suggestedName.trim()) {
                        showToast('يرجى تحديد اسم للسبرنت الذكي.', 'error');
                        return;
                    }

                    this.loading = true;

                    try {
                        const taskIds = this.aiSuggestion.selected_tasks.map(t => t.id);
                        const response = await fetch("{{ route('sprints.commit-ai-sprint') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: this.suggestedName,
                                goal: this.suggestedGoal,
                                duration_weeks: this.aiSuggestion.duration_weeks,
                                target_velocity: this.aiSuggestion.target_velocity,
                                task_ids: taskIds
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1000);
                        } else {
                            showToast(data.message || 'فشل تشغيل السبرنت.', 'error');
                            this.loading = false;
                        }
                    } catch (e) {
                        showToast('حدث خطأ فني أثناء حفظ السبرنت.', 'error');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
