<x-layouts.app title="TaskMaker AI - لوحة القيادة الذكية">



    <!-- ================= محتوى الداشبورد الفعلي ================= -->

    <!-- 1. شريط الترحيب -->
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
                    <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" class="text-emerald-400" fill="transparent" stroke-dasharray="125.6" stroke-dashoffset="50.2"></circle>
                </svg>
                <span class="absolute font-bold text-xs">60%</span>
            </div>
            <div>
                <p class="text-xs font-bold text-indigo-200">إنجاز اليوم</p>
                <p class="text-sm font-extrabold text-white">6 من أصل 10 مهام مكتملة</p>
            </div>
        </div>
    </section>

    <!-- 2. استدعاء صندوق الذكاء الاصطناعي كمكون مستقل -->
    <x-dashboard.ai-prompt />

    <!-- 3. شبكة المهام والسبرنت النشط -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        <!-- قائمة المهام العاجلة -->
        <section class="md:col-span-8 bg-white p-6 rounded-3xl card-elevation border border-outline-variant/60 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-error">priority_high</span>
                        <h3 class="font-extrabold text-lg text-on-surface">المهام ذات الأولوية القصوى</h3>
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

                        <div class="flex-1">
                            <h4 class="text-sm font-bold {{ $task->status === 'completed' ? 'line-through text-on-surface-variant' : 'text-on-surface' }}">
                                {{ $task->title }}
                            </h4>
                            <span class="text-[11px] text-on-surface-variant">أضيفت منذ {{ $task->created_at->diffForHumans() }}</span>
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

        <!-- العمود الجانبي (السبرنت وحالة التقدم) -->
        <section class="md:col-span-4 space-y-6">

            <div class="bg-gradient-to-br from-primary to-indigo-700 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
                <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[140px] text-white/10 pointer-events-none">directions_run</span>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Sprint #3 Active</span>
                        <span class="text-xs font-medium text-indigo-200">باقي 4 أيام</span>
                    </div>
                    <h3 class="text-xl font-black mb-1">بناء نظام الدفع (Stripe)</h3>
                    <p class="text-xs text-indigo-100/80 mb-6">تم تفكيك هذا السبرنت تلقائياً بواسطة AI Architect إلى 8 مهام تشغيلية.</p>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-bold">
                            <span>التقدم العام</span>
                            <span>75%</span>
                        </div>
                        <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                            <div class="bg-secondary-container h-full w-[75%] rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl card-elevation border border-outline-variant/60">
                <h3 class="font-extrabold text-base text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">insights</span>
                    <span>توزيع الحالة</span>
                </h3>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1.5">
                            <span class="text-on-surface">قيد الانتظار (To Do)</span>
                            <span class="text-on-surface-variant">{{ $tasks->where('status', 'pending')->count() }}</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full w-[35%]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1.5">
                            <span class="text-on-surface">قيد التنفيذ (In Progress)</span>
                            <span class="text-on-surface-variant">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full w-[50%]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1.5">
                            <span class="text-on-surface">مكتملة (Done)</span>
                            <span class="text-on-surface-variant">{{ $tasks->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full w-[80%]"></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    </div>

</x-layouts.app>