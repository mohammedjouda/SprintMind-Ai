<x-layouts.app title="TaskMaker AI - لوحة القيادة الذكية">

    <div class="space-y-6">

        <!-- ================= تنبيهات الحالة (Alerts) ================= -->
        @if (session('success'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl shadow-sm text-sm font-bold animate-fadeIn">
            <span class="material-symbols-outlined text-[20px] text-emerald-400">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="flex items-center gap-3 bg-rose-500/10 border border-rose-500/20 text-error p-4 rounded-2xl shadow-sm text-sm font-bold animate-fadeIn">
            <span class="material-symbols-outlined text-[20px] text-error">error</span>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- 1. شريط الترحيب ومؤشر الإنجاز الكلي -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 bg-gradient-to-l from-indigo-900 via-indigo-800 to-slate-900 text-white p-6 lg:p-8 rounded-3xl shadow-lg relative overflow-hidden">
            <div class="absolute -left-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="z-10">
                <span class="bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 text-xs px-3 py-1 rounded-full font-bold inline-flex items-center gap-1 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    بيئة العمل الذكية تعمل بكفاءة
                </span>
                <h2 class="text-2xl lg:text-3xl font-black">مرحباً بك مجدداً، {{ Auth::user()->name ?? 'محمد' }} 👋</h2>
                <p class="text-indigo-200 text-sm mt-1 max-w-xl leading-relaxed">إليك ملخص سريع لما يحدث في مساحة عملك اليوم. استخدم المساعد الذكي لتفكيك مهامك المعقدة.</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center gap-4 w-full md:w-auto z-10">
                <div class="relative h-12 w-12 flex items-center justify-center shrink-0">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" class="text-white/20" fill="transparent"></circle>
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" class="text-emerald-400 transition-all duration-700" fill="transparent" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (125.6 * $completionRate / 100) }}"></circle>
                    </svg>
                    <span class="absolute font-bold text-xs">{{ $completionRate }}%</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-indigo-200">إنجاز المهام الكلي</p>
                    <p class="text-sm font-extrabold text-white">{{ $completedCount }} من أصل {{ $totalTasksCount }} مهام مكتملة</p>
                </div>
            </div>
        </section>

        <!-- 2. مؤشرات الإنتاجية الذكية (Productivity Widgets) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- إجمالي المشاريع -->
            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">folder</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">إجمالي المشاريع</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">{{ $totalProjectsCount }}</h4>
                </div>
            </div>

            <!-- إنتاجية الذكاء الاصطناعي -->
            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px] animate-pulse">auto_awesome</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">سرعة تخطيط الـ AI</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">
                        {{ $aiTasksCount }} <span class="text-xs font-normal text-indigo-500 font-tajawal">مهمة مفككة</span>
                    </h4>
                </div>
            </div>

            <!-- النقاط المحروقة -->
            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">rocket_launch</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">جهد العمل المحروق (Completed SP)</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">{{ $storyPointsBurned }} <span class="text-xs font-normal text-emerald-600 font-tajawal">نقطة مكتملة</span></h4>
                </div>
            </div>
        </div>

        <!-- 3. استدعاء صندوق الذكاء الاصطناعي كمكون مستقل -->
        <x-dashboard.ai-prompt />

        <!-- 4. شبكة المهام والسبرنت النشط وتوزيع الحالة -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

            <!-- قائمة المهام العاجلة (Critical Tasks) -->
            <section class="md:col-span-8 bg-white p-6 rounded-3xl card-elevation border border-outline-variant/60 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-error">priority_high</span>
                            <h3 class="font-extrabold text-lg text-on-surface">المهام ذات الأولوية القصوى الجارية</h3>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="text-primary font-bold text-xs hover:underline flex items-center gap-1">
                            <span>عرض الكل</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($tasks as $task)
                        <div class="flex items-center p-4 rounded-2xl border border-outline-variant/60 hover:border-primary/40 hover:shadow-md transition-all group bg-white">
                            <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="mr-4 ml-4">
                                @csrf @method('PATCH')
                                <input type="checkbox" onChange="this.form.submit()" {{ $task->status === 'completed' ? 'checked' : '' }} class="w-5 h-5 rounded-md text-primary focus:ring-primary border-outline-variant cursor-pointer">
                            </form>

                            <div class="flex-1 text-right">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-sm font-bold {{ $task->status === 'completed' ? 'line-through text-on-surface-variant' : 'text-on-surface' }} hover:text-primary transition-colors">
                                        {{ $task->title }}
                                    </a>
                                    @if($task->is_ai_generated)
                                    <span class="bg-indigo-50 text-primary border border-indigo-100 text-[9px] font-black px-1.5 py-0.5 rounded-md inline-flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[10px] animate-pulse">auto_awesome</span>
                                        AI
                                    </span>
                                    @endif
                                    @if($task->story_points > 0)
                                    <span class="bg-slate-100 text-slate-600 text-[10px] px-1.5 py-0.5 rounded font-bold font-geist">
                                        {{ $task->story_points }} SP
                                    </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-1.5 text-[11px] text-on-surface-variant flex-wrap">
                                    <span class="inline-flex items-center gap-1">أضيفت منذ {{ $task->created_at->diffForHumans() }}</span>
                                    @if($task->project)
                                    <span class="text-slate-300">•</span>
                                    <a href="{{ route('projects.show', $task->project_id) }}" class="bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded-full text-[10px] font-bold text-slate-600 inline-flex items-center gap-1 transition-colors">
                                        <span class="material-symbols-outlined text-[12px]">folder</span>
                                        {{ $task->project->name }}
                                    </a>
                                    @endif
                                    @if($task->sprint)
                                    <span class="text-slate-300">•</span>
                                    <span class="bg-indigo-50 px-2 py-0.5 rounded-full text-[10px] font-bold text-indigo-600 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">directions_run</span>
                                        {{ $task->sprint->name }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $task->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }} {{ $task->status === 'in_progress' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }} {{ $task->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12 bg-surface-container/30 rounded-2xl border border-dashed border-outline-variant">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant/50 mb-2">task_alt</span>
                            <p class="text-sm font-bold text-on-surface">لا توجد مهام عاجلة حالياً</p>
                            <p class="text-xs text-on-surface-variant mt-1">استخدم شريط الذكاء الاصطناعي بالأعلى لتوليد مهامك الجديدة</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                @if ($tasks->hasPages())
                <div class="mt-6 pt-4 border-t border-outline-variant/60">
                    {{ $tasks->links() }}
                </div>
                @endif
            </section>

            <!-- العمود الجانبي (سبرنت وتوزيع المهام) -->
            <section class="md:col-span-4 space-y-6">

                <!-- السبرنت النشط الفعلي -->
                @if ($activeSprint)
                <div class="bg-gradient-to-br from-primary to-indigo-700 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
                    <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[140px] text-white/10 pointer-events-none">directions_run</span>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $activeSprint->name }}</span>
                            @if ($sprintRemainingDays !== null)
                            @if ($sprintRemainingDays > 0)
                            <span class="text-xs font-medium text-indigo-200">باقي {{ $sprintRemainingDays }} أيام</span>
                            @elseif ($sprintRemainingDays == 0)
                            <span class="text-xs font-medium text-amber-200">ينتهي اليوم</span>
                            @else
                            <span class="text-xs font-medium text-rose-300">متأخر بـ {{ abs($sprintRemainingDays) }} يوم</span>
                            @endif
                            @else
                            <span class="text-xs font-medium text-indigo-200">غير محدد المدة</span>
                            @endif
                        </div>

                        <h3 class="text-xl font-black mb-1">
                            @if($activeSprint->project)
                            <span class="block text-xs text-indigo-200 mb-1 font-bold">{{ $activeSprint->project->name }}</span>
                            @endif
                            {{ $activeSprint->name }}
                        </h3>
                        <p class="text-xs text-indigo-100/80 mb-6 line-clamp-2">{{ $activeSprint->goal ?? 'لا يوجد هدف محدد لهذا السبرنت حالياً.' }}</p>

                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-bold">
                                <span>التقدم العام للسبرنت</span>
                                <span>{{ $sprintProgress }}%</span>
                            </div>
                            <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                                <div class="bg-secondary-container h-full rounded-full transition-all duration-500" style="width: {{ $sprintProgress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white p-6 rounded-3xl card-elevation border border-outline-variant/60 text-center relative overflow-hidden">
                    <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px]">directions_run</span>
                    </div>
                    <h3 class="font-extrabold text-base text-on-surface mb-2">لا يوجد سبرنت نشط حالياً</h3>
                    <p class="text-xs text-on-surface-variant max-w-[220px] mx-auto mb-6">قم بإنشاء سبرنت جديد من قسم المشاريع لتتبع مراحل تقدم العمل ديناميكياً.</p>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-all w-full justify-center transform active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        <span>بدء تخطيط سبرنت جديد</span>
                    </a>
                </div>
                @endif

                <!-- توزيع حالة المهام (Task Allocation Stats) -->
                <div class="bg-white p-6 rounded-3xl card-elevation border border-outline-variant/60">
                    <h3 class="font-extrabold text-base text-on-surface mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">insights</span>
                        <span>توزيع حالة المهام</span>
                    </h3>

                    <div class="space-y-4">
                        @php
                        $pendingPercent = $totalTasksCount > 0 ? round(($pendingCount / $totalTasksCount) * 100) : 0;
                        $inProgressPercent = $totalTasksCount > 0 ? round(($inProgressCount / $totalTasksCount) * 100) : 0;
                        $completedPercent = $totalTasksCount > 0 ? round(($completedCount / $totalTasksCount) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-on-surface">قيد الانتظار (To Do)</span>
                                <span class="text-on-surface-variant">{{ $pendingCount }} ({{ $pendingPercent }}%)</span>
                            </div>
                            <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $pendingPercent }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-on-surface">قيد التنفيذ (In Progress)</span>
                                <span class="text-on-surface-variant">{{ $inProgressCount }} ({{ $inProgressPercent }}%)</span>
                            </div>
                            <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                <div class="bg-primary h-full transition-all duration-500" style="width: {{ $inProgressPercent }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-on-surface">مكتملة (Done)</span>
                                <span class="text-on-surface-variant">{{ $completedCount }} ({{ $completedPercent }}%)</span>
                            </div>
                            <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $completedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </div>

    </div>

</x-layouts.app>