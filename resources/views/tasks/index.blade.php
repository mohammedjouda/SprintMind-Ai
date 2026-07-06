<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>TaskMaker AI - قائمة المهام والإحصائيات</title>
    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- الخطوط العربية والأيقونات -->
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
            background-color: #f8fafc;
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

        .task-checkbox:checked+label {
            text-decoration: line-through;
            color: #64748b;
            font-weight: 500;
        }

        .ai-badge-glow {
            box-shadow: 0 0 12px rgba(79, 70, 229, 0.25);
        }

        .high-priority-pulse {
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {

            0%,
            100% {
                border-color: rgba(225, 29, 72, 0.3);
            }

            50% {
                border-color: rgba(225, 29, 72, 0.8);
            }
        }
    </style>
</head>

<body class="text-on-surface min-h-screen flex selection:bg-primary selection:text-white" x-data="{ sidebarOpen: false, filterTab: 'all' }">

    <x-dashboard.sidebar :show-productivity="false" />

    <!-- ================== مساحة العمل الرئيسية (Main Workspace) ================== -->
    <main class="lg:mr-64 flex-1 flex flex-col min-h-screen">

        <!-- شريط علوي (Top Navbar) -->
        <x-dashboard.header>
            <x-slot:left>
                <div class="relative w-full max-w-md">
                    <span
                        class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text" placeholder="ابحث عن مهمة، سبرنت، أو وسوم أولوية..."
                        class="w-full pr-11 pl-4 py-2 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-sm focus:outline-none focus:border-primary focus:bg-white transition-all">
                </div>
            </x-slot:left>
            <x-slot:right>
                <button
                    class="relative p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full"></span>
                </button>

                <x-dashboard.profile-dropdown />

                <a href="{{ route('tasks.create') }}"
                    class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-primary/20 flex items-center gap-2 transition-all transform active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>إضافة مهمة جديدة</span>
                </a>
            </x-slot:right>
        </x-dashboard.header>

        <!-- محتوى الصفحة (Task & Stats Dashboard) -->
        <div class="p-4 lg:p-8 max-w-7xl mx-auto w-full space-y-8 flex-1">

            <!-- الهيدر الترحيبي والتوضيحي -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="bg-primary-container text-on-primary-container text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">مركز
                            العمليات (Ops Center)</span>
                        <span class="text-xs text-on-surface-variant">📊 تحليلات فورية لأداء المشروع</span>
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-black text-on-surface">إحصائيات وقائمة المهام التشغيلية</h2>
                    <p class="text-sm text-on-surface-variant mt-1">تابع نسب الإنجاز، ركز على المهام ذات الضرورة القصوى،
                        واستعن بالذكاء الاصطناعي لرفع الإنتاجية.</p>
                </div>

                <!-- زر استدعاء سريع للمساعد الذكي -->
                <a href="{{ route('dashboard') ?? '#' }}"
                    class="bg-gradient-to-r from-indigo-900 to-primary text-white hover:opacity-95 font-bold px-5 py-2.5 rounded-2xl text-xs shadow-lg shadow-primary/20 flex items-center gap-2 transition-all">
                    <span
                        class="material-symbols-outlined text-[18px] text-emerald-400 animate-pulse">auto_awesome</span>
                    <span>تفكيك فكرة جديدة بالـ AI</span>
                </a>
            </div>

            <!-- ========================================================================= -->
            <!-- 1. شريط الإحصائيات التنفيذية العليا (Executive Bento Stat Grid)       -->
            <!-- ========================================================================= -->
            @php
            // تجهيز متغيرات محاكاة آمنة للارفيل لضمان عدم حدوث خطأ عند القسمة على صفر
            $totalTasks = $tasks->count() > 0 ? $tasks->count() : 16;
            $completedTasks =
            $tasks->where('status', 'completed')->count() > 0
            ? $tasks->where('status', 'completed')->count()
            : 10;
            $pendingTasks = $totalTasks - $completedTasks;
            $completionRate = round(($completedTasks / $totalTasks) * 100);

            // إحصائيات المهام ذات الضرورة القصوى (High Priority)
            $highTasksTotal =
            $tasks->where('priority', 'high')->count() > 0 ? $tasks->where('priority', 'high')->count() : 5;
            $highTasksDone =
            $tasks->where('priority', 'high')->where('status', 'completed')->count() > 0
            ? $tasks->where('priority', 'high')->where('status', 'completed')->count()
            : 3;
            $highTasksPending = $highTasksTotal - $highTasksDone;
            $highCompletionRate = round(($highTasksDone / $highTasksTotal) * 100);

            // إحصائيات الذكاء الاصطناعي (AI Velocity)
            $aiTasksTotal =
            $tasks->where('is_ai_generated', true)->count() > 0
            ? $tasks->where('is_ai_generated', true)->count()
            : 9;
            $storyPointsBurned = 34; // مجموع نقاط الجهد المنجزة
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- بطاقة 1: الإنجاز العام لجميع المهام (All Tasks Overview) -->
                <div
                    class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">الإنجاز
                                العام للمشروع</span>
                            <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">data_usage</span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2 mb-1">
                            <h3 class="text-3xl font-black font-geist text-on-surface">{{ $completedTasks }} <span
                                    class="text-sm font-normal text-on-surface-variant">/ {{ $totalTasks }}</span>
                            </h3>
                            <span
                                class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $completionRate }}%
                                مكتمل</span>
                        </div>
                        <p class="text-xs text-on-surface-variant font-medium">متبقي <span
                                class="font-bold text-on-surface">{{ $pendingTasks }} مهام</span> غير مكتملة في الـ
                            Backlog.</p>
                    </div>

                    <div class="mt-5 space-y-1.5">
                        <div class="flex justify-between text-[11px] font-bold text-on-surface-variant">
                            <span>شريط التقدم الكلي</span>
                            <span class="font-geist">{{ $completionRate }}%</span>
                        </div>
                        <div
                            class="w-full bg-surface-container h-2.5 rounded-full overflow-hidden p-0.5 border border-outline-variant/30">
                            <div class="bg-primary h-full rounded-full transition-all duration-500"
                                style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة 2: مهام الضرورة القصوى (High Priority Alerts) -->
                <div
                    class="bg-gradient-to-br from-error-container/40 via-white to-white p-6 rounded-3xl border border-error/30 card-elevation flex flex-col justify-between relative overflow-hidden high-priority-pulse">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-error"></div>
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span
                                class="text-xs font-extrabold text-error uppercase tracking-wider flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-error animate-ping"></span>
                                <span>الضرورة القصوى (High Priority)</span>
                            </span>
                            <div class="w-8 h-8 rounded-xl bg-error/10 text-error flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">priority_high</span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2 mb-1">
                            <h3 class="text-3xl font-black font-geist text-error">{{ $highTasksDone }} <span
                                    class="text-sm font-normal text-on-surface-variant">/ {{ $highTasksTotal }}</span>
                            </h3>
                            <span
                                class="text-xs font-bold text-error bg-error/10 px-2 py-0.5 rounded-full">{{ $highCompletionRate }}%
                                إنجاز</span>
                        </div>
                        <p class="text-xs text-on-surface-variant font-medium">تنبيه: يوجد <span
                                class="font-bold text-error">{{ $highTasksPending }} مهام حرجة</span> تحتاج تدخلاً
                            فورياً اليوم!</p>
                    </div>

                    <div class="mt-5 space-y-1.5">
                        <div class="flex justify-between text-[11px] font-bold text-error">
                            <span>نسبة حسم المهام الحرجة</span>
                            <span class="font-geist">{{ $highCompletionRate }}%</span>
                        </div>
                        <div
                            class="w-full bg-error-container/50 h-2.5 rounded-full overflow-hidden p-0.5 border border-error/20">
                            <div class="bg-error h-full rounded-full transition-all duration-500"
                                style="width: {{ $highCompletionRate }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة 3: سرعة وإنتاجية الذكاء الاصطناعي (AI Agile Co-pilot Impact) -->
                <div
                    class="bg-gradient-to-br from-indigo-900 via-primary to-indigo-800 text-white p-6 rounded-3xl shadow-lg flex flex-col justify-between relative overflow-hidden">
                    <span
                        class="material-symbols-outlined absolute -left-6 -bottom-6 text-[130px] text-white/10 pointer-events-none">auto_awesome</span>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-xs font-bold text-indigo-200 uppercase tracking-wider">إنتاجية المساعد
                                الذكي (AI Impact)</span>
                            <span
                                class="bg-white/20 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full backdrop-blur-md">Active
                                Copilot</span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-1">
                            <h3 class="text-3xl font-black font-geist text-white">{{ $aiTasksTotal }} <span
                                    class="text-sm font-normal text-indigo-200">مهام AI</span></h3>
                            <span
                                class="text-xs font-bold text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded-full border border-emerald-400/30">✨
                                خوارزمية ذكية</span>
                        </div>
                        <p class="text-xs text-indigo-100/80 font-medium leading-relaxed">قام الـ AI بتفكيك هذه المهام
                            وتقدير <span class="font-bold text-white">{{ $storyPointsBurned }} نقطة جهد (Story
                                Pt)</span> في سبرنتاتك.</p>
                    </div>

                    <div
                        class="mt-5 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-bold text-indigo-200 relative z-10">
                        <span>توزيع الجهد التلقائي:</span>
                        <span class="text-white font-geist bg-white/10 px-2.5 py-1 rounded-lg">Sprint #3 Active</span>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- 2. قائمة جدول المهام التنفيذية (Tasks Table & Linear Grid)            -->
            <!-- ========================================================================= -->
            <div class="bg-white rounded-3xl border border-outline-variant/60 card-elevation overflow-hidden">

                <!-- هيدر الجدول وأزرار التصفية السريعة (Filter Tabs) -->
                <div
                    class="p-6 border-b border-outline-variant/40 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-container/30">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-primary text-[22px]">list_alt</span>
                        <div>
                            <h3 class="text-lg font-black text-on-surface leading-none">جدول المهام التشغيلية</h3>
                            <span class="text-xs text-on-surface-variant font-medium">اختر تصنيفاً لعرض المهام
                                المطلوبة</span>
                        </div>
                    </div>

                    <!-- تبويبات التصفية (Alpine.js Tabs) -->
                    <div
                        class="flex flex-wrap gap-1.5 bg-surface-container p-1 rounded-2xl border border-outline-variant/50 w-full sm:w-auto text-xs font-bold">
                        <button @click="filterTab = 'all'"
                            :class="filterTab === 'all' ? 'bg-white text-primary shadow-sm' :
                                'text-on-surface-variant hover:text-on-surface'"
                            class="px-3.5 py-1.5 rounded-xl transition-all">الكل ({{ $totalTasks }})</button>
                        <button @click="filterTab = 'high'"
                            :class="filterTab === 'high' ? 'bg-error text-white shadow-sm font-extrabold' :
                                'text-error hover:bg-error-container/30'"
                            class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            <span>الضرورة القصوى ({{ $highTasksTotal }})</span>
                        </button>
                        <button @click="filterTab = 'ai'"
                            :class="filterTab === 'ai' ? 'bg-primary text-white shadow-sm' :
                                'text-primary hover:bg-primary/10'"
                            class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                            <span>توليد AI ({{ $aiTasksTotal }})</span>
                        </button>
                        <button @click="filterTab = 'pending'"
                            :class="filterTab === 'pending' ? 'bg-white text-on-surface shadow-sm' :
                                'text-on-surface-variant hover:text-on-surface'"
                            class="px-3.5 py-1.5 rounded-xl transition-all">قيد التنفيذ</button>
                    </div>
                </div>

                <!-- قائمة سرد المهام (Structured Linear Table) -->
                <div class="divide-y divide-outline-variant/40">

                    @forelse($tasks as $task)
                    @php
                    // شروط المحاكاة للتبويبات باستخدام Alpine
                    $isHigh = ($task->priority ?? 'medium') === 'high';
                    $isAi = $task->is_ai_generated ?? false;
                    $isPending = ($task->status ?? 'pending') !== 'completed';
                    @endphp

                    <!-- صف مهمة تفاعلي (Row Item) -->
                    <div x-show="(filterTab === 'all') || (filterTab === 'high' && {{ $isHigh ? 'true' : 'false' }}) || (filterTab === 'ai' && {{ $isAi ? 'true' : 'false' }}) || (filterTab === 'pending' && {{ $isPending ? 'true' : 'false' }})"
                        x-transition:enter="transition ease-out duration-200"
                        class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-surface-container/40 transition-colors group relative">

                        <!-- شريط تمييز جانبي للمهام الحرجة أو مهام الـ AI -->
                        @if ($isHigh)
                        <div class="absolute right-0 top-0 w-1 h-full bg-error"></div>
                        @elseif($isAi)
                        <div class="absolute right-0 top-0 w-1 h-full bg-primary"></div>
                        @endif

                        <!-- الجزء اليمين: زر الإنجاز + العنوان والبيانات الوصفية -->
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <form action="{{ route('tasks.toggle', $task->id) }}" method="POST"
                                class="shrink-0">
                                @csrf @method('PATCH')
                                <input type="checkbox" id="task-{{ $task->id }}"
                                    onChange="this.form.submit()"
                                    {{ ($task->status ?? 'pending') === 'completed' ? 'checked' : '' }}
                                    class="task-checkbox w-5.5 h-5.5 rounded-lg border-2 border-outline-variant text-secondary focus:ring-secondary cursor-pointer transition-all">
                            </form>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <label for="task-{{ $task->id }}"
                                        class="text-sm sm:text-base font-bold text-on-surface cursor-pointer truncate block hover:text-primary transition-colors {{ ($task->status ?? 'pending') === 'completed' ? 'line-through text-on-surface-variant' : '' }}">
                                        {{ $task->title ?? 'برمجة واجهة الدفع الإلكتروني باستخدام Stripe API' }}
                                    </label>

                                    <!-- شارة الذكاء الاصطناعي -->
                                    @if ($isAi)
                                    <span
                                        class="bg-primary/10 text-primary border border-primary/20 text-[10px] font-extrabold px-2 py-0.5 rounded-full inline-flex items-center gap-1 ai-badge-glow">
                                        <span class="material-symbols-outlined text-[12px]">auto_awesome</span>
                                        <span>AI Task</span>
                                    </span>
                                    @endif

                                    <!-- شارة السبرنت التابع له -->
                                    <span
                                        class="bg-surface-container-high text-on-surface-variant text-[10px] font-bold px-2 py-0.5 rounded-md font-geist">
                                        {{ $task->sprint_name ?? 'Sprint #3' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="inline-flex items-center gap-1">
                                        <span
                                            class="material-symbols-outlined text-[14px] text-outline-variant">calendar_today</span>
                                        <span>الاستحقاق: <strong
                                                class="font-geist text-on-surface">{{ $task->due_date ?? '2026-07-10' }}</strong></span>
                                    </span>
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1" title="نقاط الجهد المقدرة">
                                        <span
                                            class="material-symbols-outlined text-[14px] text-outline-variant">speed</span>
                                        <span>الجهد: <strong
                                                class="font-geist text-primary">{{ $task->story_points ?? '3' }}
                                                Story Pt.</strong></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- الجزء اليسار: الأولوية + الحالة + أزرار التحكم -->
                        <div class="flex items-center gap-3 shrink-0">

                            <!-- بادج الأولوية (Priority) -->
                            @if ($isHigh)
                            <span
                                class="bg-error-container text-on-error-container border border-error/30 font-extrabold text-[11px] px-3 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-error animate-pulse"></span>
                                <span>قصوى (High)</span>
                            </span>
                            @elseif(($task->priority ?? 'medium') === 'medium')
                            <span
                                class="bg-primary-container/60 text-on-primary-container font-bold text-[11px] px-3 py-1 rounded-full">
                                متوسطة
                            </span>
                            @else
                            <span
                                class="bg-secondary-container/60 text-on-secondary-container font-bold text-[11px] px-3 py-1 rounded-full">
                                منخفضة
                            </span>
                            @endif

                            <!-- بادج الحالة (Status) -->
                            <span
                                class="px-3 py-1 rounded-full text-[11px] font-extrabold hidden md:inline-block border
                                    {{ ($task->status ?? 'pending') === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ ($task->status ?? 'pending') === 'in_progress' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }}
                                    {{ ($task->status ?? 'pending') === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}">
                                {{ str_replace('_', ' ', $task->status ?? 'in progress') }}
                            </span>

                            <!-- أزرار الإجراءات (تظهر عند التمرير Hover) -->
                            <div
                                class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity pl-2 border-r border-outline-variant/40">
                                <a href="{{ route('tasks.edit', $task->id) }}"
                                    class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-xl transition-colors"
                                    title="تعديل المهمة">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                </a>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟')"
                                        class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/50 rounded-xl transition-colors"
                                        title="حذف">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                    @empty
                    <!-- الحالة الفارغة في حال عدم وجود مهام -->
                    <div class="text-center py-20 p-6">
                        <div
                            class="w-20 h-20 bg-primary/10 text-primary rounded-3xl flex items-center justify-center mx-auto mb-4 animate-bounce shadow-inner">
                            <span class="material-symbols-outlined text-4xl">inventory_2</span>
                        </div>
                        <h3 class="text-lg font-black text-on-surface">لا توجد مهام تطابق هذه التصفية حالياً!</h3>
                        <p class="text-xs text-on-surface-variant mt-1 max-w-sm mx-auto leading-relaxed">قائمتك
                            فارغة هنا. يمكنك إعادة التصفية إلى "الكل"، أو استدعاء المساعد الذكي بالأعلى لتوليد
                            سبرنتات جديدة للمشروع.</p>
                        <a href="{{ route('tasks.create') ?? '#' }}"
                            class="mt-5 inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-2xl text-xs shadow-lg shadow-primary/20 hover:bg-indigo-700 transition-all">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            <span>إضافة مهمة جديدة يدوياً</span>
                        </a>
                    </div>
                    @endforelse

                </div>

                <!-- الترقيم والصفحات (Pagination) -->
                @if ($tasks->hasPages())
                <div class="p-6 border-t border-outline-variant/40 bg-surface-container/20">
                    {{ $tasks->links() }}
                </div>
                @endif

            </div>

        </div>

        <x-dashboard.footer />

    </main>
</body>

</html>