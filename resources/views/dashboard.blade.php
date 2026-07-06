<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>TaskMaker AI - لوحة القيادة الذكية</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Geist:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4f46e5",
                        "primary-container": "#e0e7ff",
                        "on-primary-container": "#312e81",
                        "secondary": "#0d9488",
                        "secondary-container": "#ccfbf1",
                        "on-secondary-container": "#115e59",
                        "surface": "#f8fafc",
                        "surface-container": "#f1f5f9",
                        "surface-container-high": "#e2e8f0",
                        "surface-container-highest": "#cbd5e1",
                        "on-surface": "#0f172a",
                        "on-surface-variant": "#475569",
                        "outline-variant": "#cbd5e1",
                        "error": "#e11d48",
                        "error-container": "#ffe4e6",
                        "on-error-container": "#881337",
                        "background": "#f8fafc"
                    },
                    fontFamily: {
                        "tajawal": ["Tajawal", "sans-serif"],
                        "geist": ["Geist", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .material-symbols-outlined.filled {
            font-variation-settings: 'FILL' 1;
        }

        .card-elevation {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .ai-glow {
            box-shadow: 0 0 25px rgba(79, 70, 229, 0.15);
        }
    </style>
</head>

<body class="bg-background text-on-surface min-h-screen flex selection:bg-primary selection:text-white"
    x-data="{ sidebarOpen: false }">

    <x-dashboard.sidebar />

    <main class="lg:mr-64 flex-1 flex flex-col min-h-screen">

        <x-dashboard.header>
            <x-slot:left>
                <div class="relative w-48 sm:w-72">
                    <span
                        class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text" placeholder="ابحث في المهام، السبرنتات..."
                        class="w-full pr-10 pl-4 py-2 bg-surface-container/60 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all">
                </div>
            </x-slot:left>
            <x-slot:right>
                <button
                    class="relative p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full animate-ping"></span>
                    <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full"></span>
                </button>

                <x-dashboard.profile-dropdown />

                <a href="{{ route('tasks.create') }}"
                    class="bg-primary hover:bg-indigo-700 text-white px-5 py-2 rounded-xl font-bold text-sm shadow-md shadow-primary/20 flex items-center gap-2 transition-all transform active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span class="hidden sm:inline">مهمة جديدة</span>
                </a>
            </x-slot:right>
        </x-dashboard.header>

        <div class="p-4 lg:p-8 max-w-7xl mx-auto w-full space-y-6 flex-1">

            <section
                class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 bg-gradient-to-l from-indigo-900 via-indigo-800 to-slate-900 text-white p-6 lg:p-8 rounded-3xl shadow-lg relative overflow-hidden">
                <div
                    class="absolute -left-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none">
                </div>

                <div class="z-10">
                    <span
                        class="bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 text-xs px-3 py-1 rounded-full font-bold inline-flex items-center gap-1 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        بيئة العمل الذكية تعمل بكفاءة
                    </span>
                    <h2 class="text-2xl lg:text-3xl font-black">مرحباً بك مجدداً، {{ Auth::user()->name ?? 'محمد' }} 👋
                    </h2>
                    <p class="text-indigo-200 text-sm mt-1 max-w-xl leading-relaxed">إليك ملخص سريع لما يحدث في مساحة
                        عملك اليوم. استخدم المساعد الذكي لتفكيك مهامك المعقدة.</p>
                </div>

                <div
                    class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center gap-4 w-full md:w-auto z-10">
                    <div class="relative h-12 w-12 flex items-center justify-center shrink-0">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4"
                                class="text-white/20" fill="transparent"></circle>
                            <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4"
                                class="text-emerald-400" fill="transparent" stroke-dasharray="125.6"
                                stroke-dashoffset="50.2"></circle>
                        </svg>
                        <span class="absolute font-bold text-xs">60%</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-indigo-200">إنجاز اليوم</p>
                        <p class="text-sm font-extrabold text-white">6 من أصل 10 مهام مكتملة</p>
                    </div>
                </div>
            </section>

            <section class="bg-white p-6 rounded-3xl border border-primary/20 ai-glow relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-[24px] animate-pulse">auto_awesome</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-on-surface">مساعد التخطيط وتفكيك المهام بالذكاء
                                الاصطناعي</h3>
                            <p class="text-xs text-on-surface-variant">اكتب فكرة أو ميزة برمجية (مثلاً: نظام دفع، لوحة
                                تحكم)، وسيقوم الـ AI بتحويلها لسبرنتات ومهام فرعية فوراً.</p>
                        </div>
                    </div>
                    <span
                        class="text-[11px] font-bold bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full">مدعوم
                        بـ LLM Core</span>
                </div>

                <form action="#" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="relative flex-1">
                        <span
                            class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant">lightbulb</span>
                        <input type="text" name="ai_prompt" required
                            placeholder="ما الذي تريد بناءه أو إنجازه اليوم؟ (مثال: بناء متجر إلكتروني بـ Laravel)..."
                            class="w-full pr-11 pl-4 py-3.5 bg-surface-container/50 border border-outline-variant/80 rounded-2xl text-sm focus:outline-none focus:border-primary focus:bg-white transition-all font-medium">
                    </div>
                    <button type="submit"
                        class="bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold px-8 py-3.5 rounded-2xl text-sm shadow-lg shadow-primary/25 flex items-center justify-center gap-2 shrink-0 transition-all transform active:scale-95 group">
                        <span>تفكيك الفكرة</span>
                        <span
                            class="material-symbols-outlined text-[18px] group-hover:rotate-12 transition-transform">magic_button</span>
                    </button>
                </form>
            </section>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                <section
                    class="md:col-span-8 bg-white p-6 rounded-3xl card-elevation border border-outline-variant/60 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-error">priority_high</span>
                                <h3 class="font-extrabold text-lg text-on-surface">المهام ذات الأولوية القصوى</h3>
                            </div>
                            <a href="{{ route('tasks.index') }}"
                                class="text-primary font-bold text-xs hover:underline flex items-center gap-1">
                                <span>عرض الكل</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($tasks as $task)
                            <div
                                class="flex items-center p-4 rounded-2xl border border-outline-variant/60 hover:border-primary/40 hover:shadow-md transition-all group bg-white">
                                <form action="{{ route('tasks.toggle', $task->id) }}" method="POST"
                                    class="mr-4 ml-4">
                                    @csrf @method('PATCH')
                                    <input type="checkbox" onChange="this.form.submit()"
                                        {{ $task->status === 'completed' ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-md text-primary focus:ring-primary border-outline-variant cursor-pointer">
                                </form>

                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-bold {{ $task->status === 'completed' ? 'line-through text-on-surface-variant' : 'text-on-surface' }}">
                                        {{ $task->title }}
                                    </h4>
                                    <span class="text-[11px] text-on-surface-variant">أضيفت منذ
                                        {{ $task->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span
                                        class="text-[11px] font-bold px-2.5 py-1 rounded-full border
                                            {{ $task->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }}
                                            {{ $task->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}">
                                        {{ str_replace('_', ' ', $task->status) }}
                                    </span>
                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                        class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div
                                class="text-center py-12 bg-surface-container/30 rounded-2xl border border-dashed border-outline-variant">
                                <span
                                    class="material-symbols-outlined text-4xl text-on-surface-variant/50 mb-2">task_alt</span>
                                <p class="text-sm font-bold text-on-surface">لا توجد مهام عاجلة حالياً</p>
                                <p class="text-xs text-on-surface-variant mt-1">استخدم شريط الذكاء الاصطناعي
                                    بالأعلى لتوليد مهامك الجديدة</p>
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

                <section class="md:col-span-4 space-y-6">

                    <div
                        class="bg-gradient-to-br from-primary to-indigo-700 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
                        <span
                            class="material-symbols-outlined absolute -right-6 -bottom-6 text-[140px] text-white/10 pointer-events-none">directions_run</span>
                        <div class="relative z-10">
                            <div class="flex justify-between items-center mb-4">
                                <span
                                    class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Sprint
                                    #3 Active</span>
                                <span class="text-xs font-medium text-indigo-200">باقي 4 أيام</span>
                            </div>
                            <h3 class="text-xl font-black mb-1">بناء نظام الدفع (Stripe)</h3>
                            <p class="text-xs text-indigo-100/80 mb-6">تم تفكيك هذا السبرنت تلقائياً بواسطة AI
                                Architect إلى 8 مهام تشغيلية.</p>

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
                                    <span
                                        class="text-on-surface-variant">{{ $tasks->where('status', 'pending')->count() }}</span>
                                </div>
                                <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-full w-[35%]"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1.5">
                                    <span class="text-on-surface">قيد التنفيذ (In Progress)</span>
                                    <span
                                        class="text-on-surface-variant">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                                </div>
                                <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                    <div class="bg-primary h-full w-[50%]"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1.5">
                                    <span class="text-on-surface">مكتملة (Done)</span>
                                    <span
                                        class="text-on-surface-variant">{{ $tasks->where('status', 'completed')->count() }}</span>
                                </div>
                                <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[80%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

            </div>
        </div>

        <x-dashboard.footer />

    </main>
</body>

</html>