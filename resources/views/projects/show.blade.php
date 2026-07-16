<x-layouts.app title="SprintMind Ai AI - {{ $project->name }}">

    <x-slot:headerLeft>
        <a href="{{ route('projects.index') }}" class="flex items-center gap-1.5 text-xs font-bold text-on-surface-variant hover:text-primary transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            <span>العودة للمشاريع</span>
        </a>
    </x-slot:headerLeft>

    <x-slot:headerRight>
        <a href="{{ route('projects.edit', $project) }}" class="bg-surface-container hover:bg-primary/10 hover:text-primary text-on-surface-variant px-4 py-2.5 rounded-xl font-bold text-xs border border-outline-variant/60 flex items-center gap-1.5 transition-all">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            <span>تعديل المشروع</span>
        </a>

        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع بجميع ملحقاته؟');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-error/5 hover:bg-error hover:text-white text-error px-4 py-2.5 rounded-xl font-bold text-xs border border-error/20 flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>حذف المشروع</span>
            </button>
        </form>
    </x-slot:headerRight>

    <div x-data="{ activeTab: 'sprints', showSprintModal: false, showTaskModal: false }">

        <!-- 1. بطاقة تفاصيل المشروع العليا -->
        <section class="bg-gradient-to-l from-indigo-900 via-indigo-800 to-slate-900 text-white p-6 lg:p-8 rounded-3xl shadow-lg relative overflow-hidden mb-8">
            <div class="absolute -left-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="z-10 relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        @if ($project->category === 'software')
                        <span class="bg-primary-container text-on-primary-container text-[11px] font-black px-3 py-1 rounded-full flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">code</span>
                            <span> تطوير البرمجيات</span>
                        </span>
                        @elseif ($project->category === 'marketing')
                        <span class="bg-amber-500/20 text-amber-200 text-[11px] font-black px-3 py-1 rounded-full flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">campaign</span>
                            <span> التسويق والمحتوى</span>
                        </span>
                        @elseif ($project->category === 'personal')
                        <span class="bg-indigo-500/20 text-indigo-200 text-[11px] font-black px-3 py-1 rounded-full flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">task</span>
                            <span> مشاريع شخصية</span>
                        </span>
                        @else
                        <span class="bg-slate-500/20 text-slate-700 text-[11px] font-black px-3 py-1 rounded-full flex items-center gap-1 border border-slate-300/30">
                            <span class="material-symbols-outlined text-[14px]">folder</span>
                            <span>📂 {{ $project->category }}</span>
                        </span>
                        @endif

                        <span class="bg-white/10 text-white/90 text-[10px] font-bold px-2 py-0.5 rounded-full backdrop-blur-md">
                            🕒 {{ $project->expected_duration === '3_months' ? 'مشروع متوسط (3 أشهر)' : ($project->expected_duration === '6_months' ? 'مشروع ضخم (6 أشهر)' : $project->expected_duration) }}
                        </span>
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-black">{{ $project->name }}</h2>
                    <p class="text-indigo-200 text-xs mt-1.5 max-w-2xl leading-relaxed font-medium">{{ $project->description }}</p>
                </div>

                <div class="flex items-center gap-4 w-full md:w-auto z-10 shrink-0">
                    <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center gap-4 w-full md:w-auto">
                        <div class="relative h-12 w-12 flex items-center justify-center shrink-0">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" class="text-white/20" fill="transparent"></circle>
                                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" class="text-emerald-400" fill="transparent" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (125.6 * $completionRate) / 100 }}"></circle>
                            </svg>
                            <span class="absolute font-bold text-xs">{{ $completionRate }}%</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-indigo-200">الإنجاز العام</p>
                            <p class="text-sm font-extrabold text-white">{{ $completedTasks }} من أصل {{ $totalTasks }} مهام مكتملة</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. شبكة الإحصائيات والأرقام -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[26px]">task_alt</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">إجمالي المهام</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">{{ $totalTasks }} <span class="text-xs font-normal text-on-surface-variant font-tajawal">مهام مسجلة</span></h4>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[26px]">view_agenda</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">عدد السبرنتات</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">{{ $project->sprints->count() }} <span class="text-xs font-normal text-on-surface-variant font-tajawal">دورات عمل</span></h4>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[26px]">bolt</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">إجمالي نقاط الجهد</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">{{ $totalStoryPoints }} <span class="text-xs font-normal text-on-surface-variant font-tajawal">Story Pt</span></h4>
                </div>
            </div>

            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 text-white p-5 rounded-3xl shadow-sm flex items-center gap-4 relative overflow-hidden">
                <span class="material-symbols-outlined text-6xl text-white/10 absolute -left-2 -bottom-2 pointer-events-none">auto_awesome</span>
                <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center shrink-0 font-bold backdrop-blur-md">
                    <span class="material-symbols-outlined text-[26px]">offline_pin</span>
                </div>
                <div>
                    <p class="text-xs text-emerald-100 font-bold">الإنتاجية والتقدم</p>
                    <h4 class="text-xl font-black text-white mt-0.5">{{ $completionRate }}% إنجاز</h4>
                </div>
            </div>
        </div>

        <!-- 3. التبويبات والمحتوى -->
        <div class="flex items-center justify-between border-b border-outline-variant/60 pb-3 mb-6">
            <div class="flex gap-4">
                <button @click="activeTab = 'sprints'" :class="activeTab === 'sprints' ? 'text-primary border-b-2 border-primary pb-3' : 'text-on-surface-variant hover:text-on-surface pb-3'" class="text-sm font-black transition-all">
                    🔲 السبرنتات الجارية والتخطيط
                </button>
                <button @click="activeTab = 'backlog'" :class="activeTab === 'backlog' ? 'text-primary border-b-2 border-primary pb-3' : 'text-on-surface-variant hover:text-on-surface pb-3'" class="text-sm font-black transition-all">
                    📁 المهام غير المجدولة (Backlog)
                </button>
            </div>

            <div>
                <button x-show="activeTab === 'sprints'" @click="showSprintModal = true" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow-sm transform active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>سبرنت جديد</span>
                </button>
                <button x-show="activeTab === 'backlog'" @click="showTaskModal = true" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow-sm transform active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>إضافة مهمة للـ Backlog</span>
                </button>
            </div>
        </div>

        <!-- تبويب السبرنتات -->
        <div x-show="activeTab === 'sprints'" class="space-y-6">
            @forelse($project->sprints as $sprint)
            @php
            $sprintStatusColors = [
            'planned' => 'bg-slate-100 text-slate-700 border-slate-200',
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
            ];
            $sprintStatusTexts = [
            'planned' => 'مخطط له',
            'active' => 'نشط حالياً',
            'completed' => 'مكتمل',
            ];
            @endphp
            <div class="bg-white rounded-3xl border border-outline-variant/60 card-elevation overflow-hidden">
                <div class="p-6 border-b border-outline-variant/40 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-container/20">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="bg-primary/10 text-primary text-[10px] font-black px-2 py-0.5 rounded-full font-geist">Sprint</span>
                            <h3 class="text-lg font-black text-on-surface">{{ $sprint->name }}</h3>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $sprintStatusColors[$sprint->status] ?? 'bg-slate-100' }}">
                                {{ $sprintStatusTexts[$sprint->status] ?? $sprint->status }}
                            </span>
                        </div>
                        <p class="text-xs text-on-surface-variant font-medium">🎯 الهدف العام: {{ $sprint->goal }}</p>
                    </div>

                    <div class="flex items-center gap-4 shrink-0 text-xs font-bold text-on-surface-variant">
                        @if ($sprint->start_date && $sprint->end_date)
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-outline-variant">calendar_month</span>
                            <span>{{ $sprint->start_date->format('Y-m-d') }} إلى {{ $sprint->end_date->format('Y-m-d') }}</span>
                        </span>
                        @endif

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false" class="text-on-surface-variant hover:text-on-surface p-1.5 rounded-lg hover:bg-surface-container">
                                <span class="material-symbols-outlined text-[18px]">settings</span>
                            </button>
                            <div x-show="open" x-transition class="absolute left-0 mt-1 w-40 bg-white border border-outline-variant/60 rounded-2xl shadow-xl z-20 py-2 text-right">
                                <a href="{{ route('sprints.edit', $sprint) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-all">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                    <span>تعديل السبرنت</span>
                                </a>
                                <form action="{{ route('sprints.destroy', $sprint) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا السبرنت؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 w-full text-right px-4 py-2 text-xs font-bold text-error hover:bg-error/5 transition-all">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        <span>حذف السبرنت</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قائمة مهام هذا السبرنت -->
                <div class="p-6 space-y-3">
                    @php
                    $sprintTasks = $sprint->tasks()->latest()->get();
                    @endphp
                    @forelse($sprintTasks as $task)
                    <div class="flex items-center justify-between p-4 rounded-2xl border border-outline-variant/50 hover:border-primary/30 transition-all bg-white hover:shadow-sm">
                        <div class="flex items-center gap-3">
                            <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="checkbox" onChange="this.form.submit()" {{ $task->status === 'completed' ? 'checked' : '' }} class="w-5 h-5 rounded-md text-primary focus:ring-primary border-outline-variant cursor-pointer">
                            </form>
                            <div>
                                <h4 class="text-sm font-bold {{ $task->status === 'completed' ? 'line-through text-on-surface-variant' : 'text-on-surface' }} hover:text-primary transition-colors">
                                    <a href="{{ route('tasks.show', $task->id) }}">{{ $task->title }}</a>
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-on-surface-variant font-medium">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">bolt</span>
                                        <span>{{ $task->story_points }} Pts</span>
                                    </span>
                                    <span>•</span>
                                    <span>أولوية:
                                        <strong class="{{ $task->priority === 'high' ? 'text-error' : ($task->priority === 'medium' ? 'text-primary' : 'text-on-surface-variant') }}">
                                            {{ $task->priority === 'high' ? 'عالية' : ($task->priority === 'medium' ? 'متوسطة' : 'منخفضة') }}
                                        </strong>
                                    </span>
                                    @if ($task->due_date)
                                    <span>•</span>
                                    <span>تاريخ الاستحقاق: {{ $task->due_date->format('Y-m-d') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('tasks.show', $task->id) }}" class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors" title="عرض التفاصيل">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            <a href="{{ route('tasks.edit', $task->id) }}" class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors" title="تعديل">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-xs text-on-surface-variant font-medium">
                        💤 لا توجد مهام مضافة في هذا السبرنت حالياً.
                    </div>
                    @endforelse
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-surface-container/20 rounded-3xl border border-dashed border-outline-variant">
                <span class="material-symbols-outlined text-4xl text-on-surface-variant/50 mb-2">view_week</span>
                <h4 class="text-sm font-bold text-on-surface">لا توجد سبرنتات حالياً</h4>
                <p class="text-xs text-on-surface-variant mt-1.5">ابدأ بتقسيم مشروعك إلى دورات عمل (سبرنتات) لتنظيم سير العمل.</p>
            </div>
            @endforelse
        </div>

        <!-- تبويب الـ Backlog -->
        <div x-show="activeTab === 'backlog'" class="space-y-4">
            <div class="bg-white rounded-3xl border border-outline-variant/60 card-elevation p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-base font-black text-on-surface">المهام المعلقة في الـ Backlog</h3>
                        <p class="text-xs text-on-surface-variant mt-1">المهام التي تم إنشاؤها ولكن لم تسند بعد إلى سبرنت محدد.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($project->tasks as $task)
                    <div class="flex items-center justify-between p-4 rounded-2xl border border-outline-variant/50 hover:border-primary/30 transition-all bg-white hover:shadow-sm">
                        <div class="flex items-center gap-3">
                            <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="checkbox" onChange="this.form.submit()" {{ $task->status === 'completed' ? 'checked' : '' }} class="w-5 h-5 rounded-md text-primary focus:ring-primary border-outline-variant cursor-pointer">
                            </form>
                            <div>
                                <h4 class="text-sm font-bold {{ $task->status === 'completed' ? 'line-through text-on-surface-variant' : 'text-on-surface' }} hover:text-primary transition-colors">
                                    <a href="{{ route('tasks.show', $task->id) }}">{{ $task->title }}</a>
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-on-surface-variant font-medium">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">bolt</span>
                                        <span>{{ $task->story_points }} Pts</span>
                                    </span>
                                    <span>•</span>
                                    <span>أولوية:
                                        <strong class="{{ $task->priority === 'high' ? 'text-error' : ($task->priority === 'medium' ? 'text-primary' : 'text-on-surface-variant') }}">
                                            {{ $task->priority === 'high' ? 'عالية' : ($task->priority === 'medium' ? 'متوسطة' : 'منخفضة') }}
                                        </strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('tasks.show', $task->id) }}" class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors" title="عرض التفاصيل">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            <a href="{{ route('tasks.edit', $task->id) }}" class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors" title="تعديل">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 bg-surface-container/20 rounded-2xl border border-dashed border-outline-variant">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant/50 mb-2">task_alt</span>
                        <h4 class="text-sm font-bold text-on-surface">الـ Backlog فارغ!</h4>
                        <p class="text-xs text-on-surface-variant mt-1.5">كل المهام مجدولة حالياً في سبرنتات، أو يمكنك إضافة مهام جديدة هنا.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- مودال إنشاء سبرنت جديد -->
        <div x-show="showSprintModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div x-show="showSprintModal" x-transition.opacity @click="showSprintModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div x-show="showSprintModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="w-full max-w-lg transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-right shadow-2xl transition-all border border-outline-variant/60 relative">

                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/40 mb-6">
                        <h3 class="text-lg font-black text-on-surface">إضافة سبرنت جديد للمشروع</h3>
                        <button @click="showSprintModal = false" class="text-on-surface-variant hover:text-error p-1 rounded-lg">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('sprints.store') }}" method="POST" class="space-y-4 text-right">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div>
                            <label class="block text-xs font-extrabold text-on-surface mb-1.5">اسم السبرنت <span class="text-error">*</span></label>
                            <input type="text" name="name" required placeholder="مثال: Sprint #1 - التأسيس" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-on-surface mb-1.5">الهدف من السبرنت</label>
                            <input type="text" name="goal" placeholder="مثال: إنهاء واجهات لوحة التحكم الأساسية" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">تاريخ البدء</label>
                                <input type="date" name="start_date" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">تاريخ الانتهاء</label>
                                <input type="date" name="end_date" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                            </div>
                        </div>

                        <div class="pt-3 flex flex-col sm:flex-row-reverse items-center justify-between gap-3">
                            <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary-hover text-white font-bold py-3 px-8 rounded-2xl shadow-md">
                                حفظ السبرنت
                            </button>
                            <button type="button" @click="showSprintModal = false" class="w-full sm:w-auto bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-bold py-3 px-6 rounded-2xl text-xs">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- مودال إنشاء مهمة جديدة للـ Backlog -->
        <div x-show="showTaskModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div x-show="showTaskModal" x-transition.opacity @click="showTaskModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div x-show="showTaskModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="w-full max-w-lg transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-right shadow-2xl transition-all border border-outline-variant/60 relative">

                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/40 mb-6">
                        <h3 class="text-lg font-black text-on-surface">إضافة مهمة جديدة لمشروعك</h3>
                        <button @click="showTaskModal = false" class="text-on-surface-variant hover:text-error p-1 rounded-lg">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 text-right">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div>
                            <label class="block text-xs font-extrabold text-on-surface mb-1.5">عنوان المهمة <span class="text-error">*</span></label>
                            <input type="text" name="title" required placeholder="مثال: ربط بوابة الدفع Stripe..." class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-on-surface mb-1.5">الوصف</label>
                            <textarea name="description" rows="3" placeholder="تفاصيل المهمة ومتطلباتها..." class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs focus:border-primary transition-all"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">الأولوية</label>
                                <select name="priority" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all">
                                    <option value="low">منخفضة</option>
                                    <option value="medium" selected>متوسطة</option>
                                    <option value="high">عالية</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">نقاط الجهد (Story Points)</label>
                                <input type="number" name="story_points" min="0" value="0" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                            </div>
                        </div>

                        <div class="pt-3 flex flex-col sm:flex-row-reverse items-center justify-between gap-3">
                            <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary-hover text-white font-bold py-3 px-8 rounded-2xl shadow-md">
                                حفظ المهمة
                            </button>
                            <button type="button" @click="showTaskModal = false" class="w-full sm:w-auto bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-bold py-3 px-6 rounded-2xl text-xs">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>