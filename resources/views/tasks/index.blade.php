<x-layouts.app title="SprintMind Ai AI - المهام">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-primary-container text-on-primary-container text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">مركز العمليات (Ops Center)</span>
                <span class="text-xs text-on-surface-variant">📊 تحليلات فورية لأداء المشروع</span>
            </div>
            <h2 class="text-2xl lg:text-3xl font-black text-on-surface">إحصائيات وقائمة المهام التشغيلية</h2>
            <p class="text-sm text-on-surface-variant mt-1">تابع نسب الإنجاز، ركز على المهام ذات الضرورة القصوى، واستعن بالذكاء الاصطناعي لرفع الإنتاجية.</p>
        </div>

        <a href="{{ route('dashboard') ?? '#' }}"
            class="bg-gradient-to-r from-indigo-900 to-primary text-white hover:opacity-95 font-bold px-5 py-2.5 rounded-2xl text-xs shadow-lg shadow-primary/20 flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-[18px] text-emerald-400 animate-pulse">auto_awesome</span>
            <span>تفكيك فكرة جديدة بالـ AI</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">الإنجاز العام للمشروع</span>
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">data_usage</span>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mb-1">
                    <h3 class="text-3xl font-black font-geist text-on-surface">{{ $completedTasks }} <span class="text-sm font-normal text-on-surface-variant">/ {{ $totalTasks }}</span></h3>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $completionRate }}% مكتمل</span>
                </div>
                <p class="text-xs text-on-surface-variant font-medium">متبقي <span class="font-bold text-on-surface">{{ $pendingTasks }} مهام</span> غير مكتملة في الـ Backlog.</p>
            </div>

            <div class="mt-5 space-y-1.5">
                <div class="flex justify-between text-[11px] font-bold text-on-surface-variant">
                    <span>شريط التقدم الكلي</span>
                    <span class="font-geist">{{ $completionRate }}%</span>
                </div>
                <div class="w-full bg-surface-container h-2.5 rounded-full overflow-hidden p-0.5 border border-outline-variant/30">
                    <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: {{ $completionRate }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-error-container/40 via-white to-white p-6 rounded-3xl border border-error/30 card-elevation flex flex-col justify-between relative overflow-hidden high-priority-pulse">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-error"></div>
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-extrabold text-error uppercase tracking-wider flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-error animate-ping"></span>
                        <span>الضرورة القصوى (High Priority)</span>
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-error/10 text-error flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">priority_high</span>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mb-1">
                    <h3 class="text-3xl font-black font-geist text-error">{{ $highTasksDone }} <span class="text-sm font-normal text-on-surface-variant">/ {{ $highTasksTotal }}</span></h3>
                    <span class="text-xs font-bold text-error bg-error/10 px-2 py-0.5 rounded-full">{{ $highCompletionRate }}% إنجاز</span>
                </div>
                <p class="text-xs text-on-surface-variant font-medium">تنبيه: يوجد <span class="font-bold text-error">{{ $highTasksPending }} مهام حرجة</span> تحتاج تدخلاً فورياً اليوم!</p>
            </div>

            <div class="mt-5 space-y-1.5">
                <div class="flex justify-between text-[11px] font-bold text-error">
                    <span>نسبة حسم المهام الحرجة</span>
                    <span class="font-geist">{{ $highCompletionRate }}%</span>
                </div>
                <div class="w-full bg-error-container/50 h-2.5 rounded-full overflow-hidden p-0.5 border border-error/20">
                    <div class="bg-error h-full rounded-full transition-all duration-500" style="width: {{ $highCompletionRate }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-900 via-primary to-indigo-800 text-white p-6 rounded-3xl shadow-lg flex flex-col justify-between relative overflow-hidden">
            <span class="material-symbols-outlined absolute -left-6 -bottom-6 text-[130px] text-white/10 pointer-events-none">auto_awesome</span>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-bold text-indigo-200 uppercase tracking-wider">إنتاجية المساعد الذكي (AI Impact)</span>
                    <span class="bg-white/20 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full backdrop-blur-md">Active Copilot</span>
                </div>
                <div class="flex items-baseline gap-2 mb-1">
                    <h3 class="text-3xl font-black font-geist text-white">{{ $aiTasksTotal }} <span class="text-sm font-normal text-indigo-200">مهام AI</span></h3>
                    <span class="text-xs font-bold text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded-full border border-emerald-400/30"> خوارزمية ذكية</span>
                </div>
                <p class="text-xs text-indigo-100/80 font-medium leading-relaxed">قام الـ AI بتفكيك هذه المهام وتقدير <span class="font-bold text-white">{{ $storyPointsBurned ?? 0 }} نقطة جهد (Story Pt)</span> في سبرنتاتك.</p>
            </div>

            <div class="mt-5 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-bold text-indigo-200 relative z-10">
                <span>توزيع الجهد التلقائي:</span>
                <span class="text-white font-geist bg-white/10 px-2.5 py-1 rounded-lg">Sprint #3 Active</span>
            </div>
        </div>

    </div>

    <div x-data="{ filterTab: 'all', openFilters: false }" class="bg-white rounded-3xl border border-outline-variant/60 card-elevation overflow-hidden">

        <div class="p-6 border-b border-outline-variant/40 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-container/30">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-primary text-[22px]">list_alt</span>
                <div>
                    <h3 class="text-lg font-black text-on-surface leading-none">جدول المهام التشغيلية</h3>
                    <span class="text-xs text-on-surface-variant font-medium">اختر تصنيفاً لعرض المهام المطلوبة</span>
                </div>
            </div>

            <div class="relative w-full sm:w-auto">
                <button @click="openFilters = !openFilters" @click.away="openFilters = false" class="flex items-center gap-2 px-4 py-2 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 rounded-xl text-xs font-bold text-on-surface transition-all w-full sm:w-auto justify-between sm:justify-start">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-primary">filter_list</span>
                        <span x-text="filterTab === 'all' ? 'الكل ({{ $totalTasks }})' : (filterTab === 'high' ? 'الضرورة القصوى ({{ $highTasksTotal }})' : (filterTab === 'ai' ? 'توليد AI ({{ $aiTasksTotal }})' : (filterTab === 'pending' ? 'قيد التنفيذ' : (filterTab === 'backlog' ? 'الـ Backlog ({{ $backlogTasksTotal }})' : 'تصفية المهام'))))">تصفية المهام</span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] transition-transform text-on-surface-variant" :class="openFilters ? 'rotate-180' : ''">keyboard_arrow_down</span>
                </button>
                <div x-show="openFilters" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute left-0 mt-2 w-56 bg-white border border-outline-variant/60 rounded-2xl shadow-xl z-20 py-2 text-right">

                    <button @click="filterTab = 'all'; openFilters = false" :class="filterTab === 'all' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">all_inclusive</span>
                        <span>الكل ({{ $totalTasks }})</span>
                    </button>

                    <button @click="filterTab = 'high'; openFilters = false" :class="filterTab === 'high' ? 'bg-error-container/40 text-error' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                        <span>الضرورة القصوى ({{ $highTasksTotal }})</span>
                    </button>

                    <button @click="filterTab = 'ai'; openFilters = false" :class="filterTab === 'ai' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                        <span>توليد AI ({{ $aiTasksTotal }})</span>
                    </button>

                    <button @click="filterTab = 'pending'; openFilters = false" :class="filterTab === 'pending' ? 'bg-surface-container-high/60 text-on-surface' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        <span>قيد التنفيذ</span>
                    </button>

                    <button @click="filterTab = 'backlog'; openFilters = false" :class="filterTab === 'backlog' ? 'bg-amber-500/10 text-amber-700' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                        <span>الـ Backlog ({{ $backlogTasksTotal }})</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="divide-y divide-outline-variant/40">

            @forelse($tasks as $task)
            @php
            $isHigh = ($task->priority ?? 'medium') === 'high';
            $isAi = $task->is_ai_generated ?? false;
            $isPending = ($task->status ?? 'pending') !== 'completed';
            $isBacklog = is_null($task->sprint_id);
            @endphp

            <div x-show="(filterTab === 'all') || (filterTab === 'high' && {{ $isHigh ? 'true' : 'false' }}) || (filterTab === 'ai' && {{ $isAi ? 'true' : 'false' }}) || (filterTab === 'pending' && {{ $isPending ? 'true' : 'false' }}) || (filterTab === 'backlog' && {{ $isBacklog ? 'true' : 'false' }})"
                x-transition:enter="transition ease-out duration-200"
                class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-surface-container/40 transition-colors group relative">

                @if ($isHigh)
                <div class="absolute right-0 top-0 w-1 h-full bg-error"></div>
                @elseif($isAi)
                <div class="absolute right-0 top-0 w-1 h-full bg-primary"></div>
                @endif

                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="shrink-0">
                        @csrf @method('PATCH')
                        <input type="checkbox" id="task-{{ $task->id }}" onChange="this.form.submit()" {{ ($task->status ?? 'pending') === 'completed' ? 'checked' : '' }} class="task-checkbox w-5.5 h-5.5 rounded-lg border-2 border-outline-variant text-secondary focus:ring-secondary cursor-pointer transition-all">
                    </form>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <label for="task-{{ $task->id }}" class="text-sm sm:text-base font-bold text-on-surface cursor-pointer truncate block hover:text-primary transition-colors {{ ($task->status ?? 'pending') === 'completed' ? 'line-through text-on-surface-variant' : '' }}">
                                {{ $task->title ?? 'مهمة بدون عنوان' }}
                            </label>

                            @if ($isAi)
                            <span class="bg-primary/10 text-primary border border-primary/20 text-[10px] font-extrabold px-2 py-0.5 rounded-full inline-flex items-center gap-1 ai-badge-glow">
                                <span class="material-symbols-outlined text-[12px]">auto_awesome</span>
                                <span>AI Task</span>
                            </span>
                            @endif

                            <span class="bg-surface-container-high text-on-surface-variant text-[10px] font-bold px-2 py-0.5 rounded-md font-geist">
                                {{ $task->sprint_name ?? 'Sprint #1' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-on-surface-variant">
                            <span class="inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-outline-variant">calendar_today</span>
                                <span>الاستحقاق: <strong class="font-geist text-on-surface">{{ $task->due_date ?? 'غير محدد' }}</strong></span>
                            </span>
                            <span>•</span>
                            <span class="inline-flex items-center gap-1" title="نقاط الجهد المقدرة">
                                <span class="material-symbols-outlined text-[14px] text-outline-variant">speed</span>
                                <span>الجهد: <strong class="font-geist text-primary">{{ $task->story_points ?? '0' }} Story Pt.</strong></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if ($isHigh)
                    <span class="bg-error-container text-on-error-container border border-error/30 font-extrabold text-[11px] px-3 py-1 rounded-full flex items-center gap-1 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-error animate-pulse"></span>
                        <span>قصوى (High)</span>
                    </span>
                    @elseif(($task->priority ?? 'medium') === 'medium')
                    <span class="bg-primary-container/60 text-on-primary-container font-bold text-[11px] px-3 py-1 rounded-full">متوسطة</span>
                    @else
                    <span class="bg-secondary-container/60 text-on-secondary-container font-bold text-[11px] px-3 py-1 rounded-full">منخفضة</span>
                    @endif

                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold hidden md:inline-block border {{ ($task->status ?? 'pending') === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }} {{ ($task->status ?? 'pending') === 'in_progress' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }} {{ ($task->status ?? 'pending') === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}">
                        {{ str_replace('_', ' ', $task->status ?? 'in progress') }}
                    </span>

                    <div class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity pl-2 border-r border-outline-variant/40">
                        <a href="{{ route('tasks.show', $task->id) }}" class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-xl transition-colors" title="عرض التفاصيل">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-xl transition-colors" title="تعديل المهمة">
                            <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        </a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟')" class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/50 rounded-xl transition-colors" title="حذف">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-20 p-6">
                <div class="w-20 h-20 bg-primary/10 text-primary rounded-3xl flex items-center justify-center mx-auto mb-4 animate-bounce shadow-inner">
                    <span class="material-symbols-outlined text-4xl">inventory_2</span>
                </div>
                <h3 class="text-lg font-black text-on-surface">لا توجد مهام تطابق هذه التصفية حالياً!</h3>
                <p class="text-xs text-on-surface-variant mt-1 max-w-sm mx-auto leading-relaxed">قائمتك فارغة هنا. يمكنك إعادة التصفية إلى "الكل"، أو استدعاء المساعد الذكي بالأعلى لتوليد سبرنتات جديدة للمشروع.</p>
                <a href="{{ route('tasks.create') ?? '#' }}" class="mt-5 inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-2xl text-xs shadow-lg shadow-primary/20 hover:bg-indigo-700 transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>إضافة مهمة جديدة يدوياً</span>
                </a>
            </div>
            @endforelse

        </div>

        @if ($tasks->hasPages())
        <div class="p-6 border-t border-outline-variant/40 bg-surface-container/20">
            {{ $tasks->links() }}
        </div>
        @endif

    </div>

</x-layouts.app>