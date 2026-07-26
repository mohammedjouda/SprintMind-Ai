<x-layouts.app :title="'SprintMind Ai - ' . $sprint->name">

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
    </script>

    <div x-data="kanbanWorkspace(@js($sprint->tasks))" x-init="fetchHealth()" class="space-y-6">

        <!-- Breadcrumb / Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-1">
                    <a href="{{ route('sprints.index') }}" class="hover:underline hover:text-primary">السبرنتات الذكية</a>
                    <span class="material-symbols-outlined text-[12px]">chevron_left</span>
                    <span class="font-bold text-on-surface">{{ $sprint->name }}</span>
                </div>
                <h2 class="text-2xl font-black text-on-surface font-tajawal flex items-center gap-2.5">
                    <span>{{ $sprint->name }}</span>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $sprint->status === 'active' ? 'bg-emerald-500/10 text-emerald-600' : ($sprint->status === 'completed' ? 'bg-slate-200 text-slate-700' : 'bg-amber-500/10 text-amber-600') }}">
                        @if($sprint->status === 'active') نشط @elseif($sprint->status === 'completed') منتهي @else مخطط @endif
                    </span>
                </h2>
                @if($sprint->goal)
                <p class="text-xs text-on-surface-variant mt-1.5 font-tajawal bg-surface-container/30 px-3 py-1.5 rounded-xl border border-outline-variant/40 inline-block"> الهدف: {{ $sprint->goal }}</p>
                @endif
            </div>

            <!-- Stats badges -->
            <div class="flex gap-2 text-xs">
                <div class="bg-white border border-outline-variant/60 p-3 rounded-2xl flex flex-col justify-center">
                    <span class="text-on-surface-variant text-[10px] block">السرعة المستهدفة</span>
                    <span class="font-black text-on-surface text-sm mt-0.5">{{ $sprint->target_velocity }} Pts</span>
                </div>
                <div class="bg-white border border-outline-variant/60 p-3 rounded-2xl flex flex-col justify-center">
                    <span class="text-on-surface-variant text-[10px] block">النقاط الإجمالية</span>
                    <span class="font-black text-on-surface text-sm mt-0.5">{{ $sprint->tasks->sum('story_points') }} Pts</span>
                </div>
                <div class="bg-white border border-outline-variant/60 p-3 rounded-2xl flex flex-col justify-center">
                    <span class="text-on-surface-variant text-[10px] block">المدى الزمني</span>
                    <span class="font-bold text-on-surface mt-0.5">{{ $sprint->start_date?->format('m/d') ?? '-' }} - {{ $sprint->end_date?->format('m/d') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- AI Scrum Master Health Advice Banner -->
        <div x-show="health" x-cloak class="transition-all duration-300">
            <div class="bg-white border border-outline-variant/60 rounded-2xl p-3.5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <!-- Dynamic status badge/emoji -->
                    <span x-show="health.health_status === 'healthy'" class="flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 border border-emerald-200/60">
                        <span> مميز</span>
                    </span>
                    <span x-show="health.health_status === 'warning'" class="flex items-center gap-1 bg-amber-50 text-amber-700 text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 border border-amber-200/60 animate-pulse-slow">
                        <span>⏱️ انتبه للوقت</span>
                    </span>
                    <span x-show="health.health_status === 'critical'" class="flex items-center gap-1 bg-rose-50 text-rose-700 text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 border border-rose-200/60">
                        <span>🚨 تدخل سريع</span>
                    </span>

                    <span class="text-xs font-bold text-on-surface leading-relaxed" x-text="health.copilot_advice"></span>
                </div>
                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                    <span class="text-[9px] font-extrabold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-md flex items-center gap-1 border border-violet-100">
                        <span class="material-symbols-outlined text-[11px] animate-pulse">psychology</span>
                        <span>AI CO-PILOT</span>
                    </span>
                </div>
            </div>
        </div>

        <div x-show="healthLoading" class="bg-surface-container/40 rounded-2xl p-4 flex items-center gap-3 animate-pulse">
            <div class="w-8 h-8 rounded-full bg-surface-container-high shrink-0"></div>
            <div class="flex-1 space-y-1.5">
                <div class="h-2 bg-surface-container-high rounded-full w-1/4"></div>
                <div class="h-2.5 bg-surface-container-high rounded-full w-3/4"></div>
            </div>
        </div>

        <!-- 4-Column Drag-and-Drop Kanban Board -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start select-none">

            <!-- Column template macro -->
            <!-- 1. TO DO -->
            <div class="bg-surface-container/60 rounded-3xl p-4 border border-outline-variant/60 flex flex-col min-h-[500px]"
                @dragover.prevent
                @drop="dropCard('todo')">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-outline-variant/40 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-500 text-sm">assignment</span>
                        <h3 class="text-xs font-black text-on-surface">قيد الانتظار</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-200 text-slate-800 rounded-full"
                        x-text="getCardsCount('todo')"></span>
                </div>

                <div class="flex-1 space-y-2.5 overflow-y-auto">
                    <template x-for="task in getCards('todo')" :key="task.id">
                        <div class="bg-white p-4 rounded-2xl border border-outline-variant/60 shadow-sm cursor-grab active:cursor-grabbing hover:border-primary/50 hover:shadow transition-all"
                            draggable="true"
                            @dragstart="dragCard(task.id)">

                            <div class="flex justify-between items-start gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span :class="{
                                        'bg-rose-500': task.priority === 'high',
                                        'bg-amber-500': task.priority === 'medium',
                                        'bg-emerald-500': task.priority === 'low'
                                    }" class="w-2.5 h-2.5 rounded-full shrink-0"></span>
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase" x-text="task.priority"></span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full shrink-0" x-text="`⚡ ${task.story_points}pt`"></span>
                            </div>

                            <h4 class="text-xs font-black text-on-surface mt-2.5 leading-relaxed" x-text="task.title"></h4>

                            <template x-if="task.description">
                                <p class="text-[11px] text-on-surface-variant mt-1.5 line-clamp-2" x-text="task.description"></p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 2. IN PROGRESS -->
            <div class="bg-surface-container/60 rounded-3xl p-4 border border-outline-variant/60 flex flex-col min-h-[500px]"
                @dragover.prevent
                @drop="dropCard('in_progress')">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-outline-variant/40 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-sm animate-spin-slow">bolt</span>
                        <h3 class="text-xs font-black text-on-surface">قيد العمل</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full"
                        x-text="getCardsCount('in_progress')"></span>
                </div>

                <div class="flex-1 space-y-2.5 overflow-y-auto">
                    <template x-for="task in getCards('in_progress')" :key="task.id">
                        <div class="bg-white p-4 rounded-2xl border border-outline-variant/60 shadow-sm cursor-grab active:cursor-grabbing hover:border-primary/50 hover:shadow transition-all"
                            draggable="true"
                            @dragstart="dragCard(task.id)">

                            <div class="flex justify-between items-start gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span :class="{
                                        'bg-rose-500': task.priority === 'high',
                                        'bg-amber-500': task.priority === 'medium',
                                        'bg-emerald-500': task.priority === 'low'
                                    }" class="w-2.5 h-2.5 rounded-full shrink-0"></span>
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase" x-text="task.priority"></span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full shrink-0" x-text="`⚡ ${task.story_points}pt`"></span>
                            </div>

                            <h4 class="text-xs font-black text-on-surface mt-2.5 leading-relaxed" x-text="task.title"></h4>

                            <template x-if="task.description">
                                <p class="text-[11px] text-on-surface-variant mt-1.5 line-clamp-2" x-text="task.description"></p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. CODE REVIEW -->
            <div class="bg-surface-container/60 rounded-3xl p-4 border border-outline-variant/60 flex flex-col min-h-[500px]"
                @dragover.prevent
                @drop="dropCard('review')">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-outline-variant/40 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-500 text-sm">pageview</span>
                        <h3 class="text-xs font-black text-on-surface">مراجعة الكود</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full"
                        x-text="getCardsCount('review')"></span>
                </div>

                <div class="flex-1 space-y-2.5 overflow-y-auto">
                    <template x-for="task in getCards('review')" :key="task.id">
                        <div class="bg-white p-4 rounded-2xl border border-outline-variant/60 shadow-sm cursor-grab active:cursor-grabbing hover:border-primary/50 hover:shadow transition-all"
                            draggable="true"
                            @dragstart="dragCard(task.id)">

                            <div class="flex justify-between items-start gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span :class="{
                                        'bg-rose-500': task.priority === 'high',
                                        'bg-amber-500': task.priority === 'medium',
                                        'bg-emerald-500': task.priority === 'low'
                                    }" class="w-2.5 h-2.5 rounded-full shrink-0"></span>
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase" x-text="task.priority"></span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full shrink-0" x-text="`⚡ ${task.story_points}pt`"></span>
                            </div>

                            <h4 class="text-xs font-black text-on-surface mt-2.5 leading-relaxed" x-text="task.title"></h4>

                            <template x-if="task.description">
                                <p class="text-[11px] text-on-surface-variant mt-1.5 line-clamp-2" x-text="task.description"></p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 4. DONE / COMPLETED -->
            <div class="bg-surface-container/60 rounded-3xl p-4 border border-outline-variant/60 flex flex-col min-h-[500px]"
                @dragover.prevent
                @drop="dropCard('completed')">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-outline-variant/40 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500 text-sm">check_circle</span>
                        <h3 class="text-xs font-black text-on-surface">مكتمل</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full"
                        x-text="getCardsCount('completed')"></span>
                </div>

                <div class="flex-1 space-y-2.5 overflow-y-auto">
                    <template x-for="task in getCards('completed')" :key="task.id">
                        <div class="bg-white p-4 rounded-2xl border border-outline-variant/60 shadow-sm cursor-grab active:cursor-grabbing hover:border-primary/50 hover:shadow transition-all opacity-80"
                            draggable="true"
                            @dragstart="dragCard(task.id)">

                            <div class="flex justify-between items-start gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span :class="{
                                        'bg-rose-500': task.priority === 'high',
                                        'bg-amber-500': task.priority === 'medium',
                                        'bg-emerald-500': task.priority === 'low'
                                    }" class="w-2.5 h-2.5 rounded-full shrink-0"></span>
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase" x-text="task.priority"></span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full shrink-0" x-text="`⚡ ${task.story_points}pt`"></span>
                            </div>

                            <h4 class="text-xs font-black text-on-surface mt-2.5 leading-relaxed line-through decoration-slate-400" x-text="task.title"></h4>

                            <template x-if="task.description">
                                <p class="text-[11px] text-on-surface-variant mt-1.5 line-clamp-2" x-text="task.description"></p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

        </div>

    </div>

    <!-- Custom blinking animations -->
    <style>
        @keyframes pulseSlow {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.9;
                transform: scale(0.99);
            }
        }

        .animate-pulse-slow {
            animation: pulseSlow 3s infinite ease-in-out;
        }

        @keyframes bounceSubtle {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        .animate-bounce-subtle {
            animation: bounceSubtle 4s infinite ease-in-out;
        }

        .animate-spin-slow {
            animation: spin 8s infinite linear;
        }
    </style>

    <script>
        function kanbanWorkspace(initialTasks) {
            return {
                tasks: initialTasks,
                draggedId: null,
                health: null,
                healthLoading: false,

                getCards(status) {
                    return this.tasks.filter(t => t.status === status);
                },

                getCardsCount(status) {
                    return this.getCards(status).length;
                },

                dragCard(id) {
                    this.draggedId = id;
                },

                async dropCard(newStatus) {
                    if (!this.draggedId) return;

                    const cardIndex = this.tasks.findIndex(t => t.id === this.draggedId);
                    if (cardIndex === -1) return;

                    const originalStatus = this.tasks[cardIndex].status;
                    if (originalStatus === newStatus) return;

                    // Optimistic update
                    this.tasks[cardIndex].status = newStatus;
                    const draggedTaskId = this.draggedId;
                    this.draggedId = null;

                    try {
                        const response = await fetch(`/sprints/tasks/${draggedTaskId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                _method: 'PATCH',
                                status: newStatus
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            showToast(data.message, 'success');
                            // Refresh health report to capture changes
                            this.fetchHealth();
                        } else {
                            showToast('عذراً، لم نتمكن من حفظ الحالة الجديدة للمهمة حالياً.', 'error');
                            // Revert status
                            this.tasks[cardIndex].status = originalStatus;
                        }
                    } catch (e) {
                        showToast('حدث خطأ غير متوقع أثناء الاتصال بالخادم، يرجى التحقق من اتصالك بالإنترنت.', 'error');
                        // Revert status
                        this.tasks[cardIndex].status = originalStatus;
                    }
                },

                async fetchHealth() {
                    this.healthLoading = true;
                    try {
                        const response = await fetch("{{ route('sprints.health-check', $sprint->id) }}");
                        const data = await response.json();
                        if (data.success) {
                            this.health = data;
                        }
                    } catch (e) {
                        console.error('Failed to fetch sprint health from AI service.', e);
                    } finally {
                        this.healthLoading = false;
                    }
                }
            }
        }
    </script>
</x-layouts.app>