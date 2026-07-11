<x-layouts.app title="TaskMaker AI - {{ $task->title }}">

    <x-slot:headerLeft>
        <a href="{{ route('projects.show', $task->project_id) }}" class="flex items-center gap-1.5 text-xs font-bold text-on-surface-variant hover:text-primary transition-all font-tajawal">
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            <span>العودة للمشروع</span>
        </a>
    </x-slot:headerLeft>

    <x-slot:headerRight>
        <a href="{{ route('tasks.edit', $task) }}" class="bg-surface-container hover:bg-primary/10 hover:text-primary text-on-surface-variant px-4 py-2.5 rounded-xl font-bold text-xs border border-outline-variant/60 flex items-center gap-1.5 transition-all font-tajawal">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            <span>تعديل المهمة</span>
        </a>

        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة نهائياً؟');" class="inline font-tajawal">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-error/5 hover:bg-error hover:text-white text-error px-4 py-2.5 rounded-xl font-bold text-xs border border-error/20 flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>حذف المهمة</span>
            </button>
        </form>
    </x-slot:headerRight>

    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- بطاقة تفاصيل المهمة -->
        <div class="bg-white rounded-3xl border border-outline-variant/60 card-elevation p-6 lg:p-8 space-y-6">
            
            <div class="flex flex-wrap justify-between items-center gap-3">
                <div class="flex items-center gap-2 font-tajawal">
                    @if ($task->status === 'completed')
                    <span class="bg-emerald-50 text-emerald-700 text-xs font-black px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        <span>مكتملة</span>
                    </span>
                    @elseif ($task->status === 'in_progress')
                    <span class="bg-indigo-50 text-indigo-700 text-xs font-black px-3 py-1 rounded-full border border-indigo-200 flex items-center gap-1 animate-pulse">
                        <span class="material-symbols-outlined text-[14px]">autorenew</span>
                        <span>قيد التنفيذ</span>
                    </span>
                    @else
                    <span class="bg-amber-50 text-amber-700 text-xs font-black px-3 py-1 rounded-full border border-amber-200 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        <span>قيد الانتظار</span>
                    </span>
                    @endif

                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $task->priority === 'high' ? 'bg-error-container text-on-error-container border-error/30' : ($task->priority === 'medium' ? 'bg-primary-container text-on-primary-container border-primary/30' : 'bg-secondary-container text-on-secondary-container border-secondary/30') }}">
                        الأولوية: {{ $task->priority === 'high' ? 'عالية' : ($task->priority === 'medium' ? 'متوسطة' : 'منخفضة') }}
                    </span>
                </div>

                <div class="text-xs font-bold text-on-surface-variant flex items-center gap-2 font-geist">
                    <span class="bg-surface-container px-2.5 py-1 rounded-full">⚡ {{ $task->story_points }} Pts</span>
                    @if ($task->due_date)
                    <span class="flex items-center gap-1 bg-surface-container px-2.5 py-1 rounded-full text-error">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                        <span>استحقاق: {{ $task->due_date->format('Y-m-d') }}</span>
                    </span>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                <h1 class="text-2xl font-black text-on-surface font-tajawal">{{ $task->title }}</h1>
                <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line bg-surface-container/20 p-4 rounded-2xl border border-outline-variant/30">
                    {{ $task->description ?? 'لا يوجد وصف مضاف لهذه المهمة.' }}
                </p>
            </div>

            <!-- معلومات المشروع والسبرنت -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-outline-variant/40 font-tajawal">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">folder</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-on-surface-variant font-bold">المشروع التابع له</p>
                        <p class="text-xs font-extrabold text-on-surface">{{ $task->project->name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">view_agenda</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-on-surface-variant font-bold">السبرنت</p>
                        <p class="text-xs font-extrabold text-on-surface">
                            {{ $task->sprint ? $task->sprint->name : 'غير مرتبط بسبرنت (Backlog العام)' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- قائمة معايير القبول -->
            @if ($task->acceptanceCriteria->count() > 0)
            <div class="pt-6 border-t border-outline-variant/40 space-y-3 font-tajawal">
                <h3 class="text-xs font-black text-on-surface-variant uppercase tracking-wider">معايير القبول ونقاط التحقق (Acceptance Criteria)</h3>
                <div class="space-y-2.5">
                    @foreach ($task->acceptanceCriteria as $criterion)
                    <div class="flex items-center gap-3 p-3.5 bg-surface-container/30 rounded-2xl border border-outline-variant/40">
                        <span class="material-symbols-outlined text-[20px] {{ $criterion->is_completed ? 'text-emerald-500' : 'text-outline-variant' }}">
                            {{ $criterion->is_completed ? 'check_box' : 'check_box_outline_blank' }}
                        </span>
                        <span class="text-xs font-medium {{ $criterion->is_completed ? 'line-through text-on-surface-variant opacity-70' : 'text-on-surface' }}">
                            {{ $criterion->title }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- زر التبديل السريع وتحديث البيانات -->
            <div class="pt-6 border-t border-outline-variant/40 flex flex-col sm:flex-row gap-3 justify-between items-center font-tajawal">
                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-xs transition-all {{ $task->status === 'completed' ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                        <span class="material-symbols-outlined text-[18px]">{{ $task->status === 'completed' ? 'restart_alt' : 'task_alt' }}</span>
                        <span>{{ $task->status === 'completed' ? 'إعادة فتح المهمة (Pending)' : 'تعليم المهمة كمكتملة' }}</span>
                    </button>
                </form>
                
                <span class="text-[11px] font-bold text-on-surface-variant font-geist">
                    تاريخ الإنشاء: {{ $task->created_at->format('Y-m-d H:i') }}
                </span>
            </div>

        </div>

    </div>
</x-layouts.app>
