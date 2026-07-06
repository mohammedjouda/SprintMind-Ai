<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>TaskMaker AI - تعديل المهمة</title>
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

        .ai-glow-sm {
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.15);
        }
    </style>
</head>

<body class="text-on-surface min-h-screen flex selection:bg-primary selection:text-white" x-data="{
    sidebarOpen: false,
    priority: '{{ old('priority', $task->priority ?? 'medium') }}',
    status: '{{ old('status', $task->status ?? 'pending') }}'
}">

    <x-dashboard.sidebar :show-productivity="false" />

    <!-- ================== مساحة العمل الرئيسية (Main Canvas) ================== -->
    <main class="lg:mr-64 flex-1 flex flex-col min-h-screen">

        <!-- شريط علوي (Top Navbar) -->
        <x-dashboard.header>
            <x-slot:left>
                <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant">
                    <a href="{{ route('tasks.index') }}" class="hover:text-primary transition-colors">المهام</a>
                    <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                    <span class="text-on-surface">تعديل المهمة #{{ $task->id }}</span>
                </div>
            </x-slot:left>
            <x-slot:right>
                <x-dashboard.profile-dropdown />
                <a href="{{ route('tasks.index') }}"
                    class="text-xs font-bold bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2 rounded-xl transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    <span>إلغاء</span>
                </a>
            </x-slot:right>
        </x-dashboard.header>

        <!-- محتوى النموذج (Edit Task Form Workspace) -->
        <div class="p-4 lg:p-8 max-w-6xl mx-auto w-full space-y-6 flex-1">

            <!-- الهيدر الداخلي -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">تحديث
                            بيانات</span>
                        @if ($task->is_ai_generated ?? false)
                        <span
                            class="bg-primary/10 text-primary border border-primary/20 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1 ai-glow-sm">
                            <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                            <span>تم توليدها بالذكاء الاصطناعي</span>
                        </span>
                        @endif
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-black text-on-surface">تعديل المهمة: <span
                            class="text-primary font-medium">{{ Str::limit($task->title ?? 'برمجة واجهة الدفع الإلكتروني', 30) }}</span>
                    </h2>
                    <p class="text-xs text-on-surface-variant mt-1">قم بتحديث حالة الإنجاز، معايير القبول، أو إعادة
                        توزيع الجهد والساعات.</p>
                </div>

                <div class="flex items-center gap-2">
                    <form action="{{ route('tasks.destroy', $task->id ?? 0) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه المهمة نهائياً؟')"
                            class="bg-error-container/50 hover:bg-error-container text-error border border-error/20 text-xs font-extrabold px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                            <span>حذف المهمة</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- ================== نموذج التعديل (Laravel Form) ================== -->
            <form action="{{ route('tasks.update', $task->id ?? 0) }}" method="POST"
                class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                @csrf
                @method('PUT') <!-- حاسم جداً لعمليات التحديث في لارفيل -->

                <!-- العمود الأيمن (8 أعمدة): التفاصيل والوصف -->
                <div class="lg:col-span-8 space-y-6">

                    <div class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation space-y-5">

                        <!-- عنوان المهمة -->
                        <div>
                            <label class="block text-sm font-extrabold text-on-surface mb-2">
                                عنوان المهمة <span class="text-error">*</span>
                            </label>
                            <input type="text" name="title"
                                value="{{ old('title', $task->title ?? 'برمجة واجهة الدفع الإلكتروني باستخدام Stripe API') }}"
                                required
                                class="w-full px-4 py-3.5 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium">
                            @error('title')
                            <p class="text-xs text-error mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- وصف المهمة + أزرار الـ AI Copilot -->
                        <div>
                            <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                                <label class="block text-sm font-extrabold text-on-surface">وصف المهمة والتفاصيل</label>

                                <div class="flex items-center gap-1.5">
                                    <button type="button"
                                        class="text-[11px] font-bold text-on-surface-variant bg-surface-container hover:bg-surface-container-high px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">auto_fix_high</span>
                                        <span>تحسين الصياغة</span>
                                    </button>
                                    <button type="button"
                                        class="text-[11px] font-bold text-primary bg-primary/10 hover:bg-primary/20 px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1 ai-glow-sm">
                                        <span class="material-symbols-outlined text-[14px]">call_split</span>
                                        <span>تفكيك إلى مهام فرعية (Break Down)</span>
                                    </button>
                                </div>
                            </div>

                            <textarea name="description" rows="6"
                                class="w-full px-4 py-3.5 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium leading-relaxed">{{ old('description', $task->description ?? "تجهيز واجهة الدفع لعملاء المتجر مع مراعاة النقاط التالية:\n- ربط بوابة Stripe بالـ Backend.\n- التحقق من صحة البطاقات وحفظ الـ Token بأمان.\n- إظهار رسائل خطأ واضحة في حال رفض العملية.") }}</textarea>
                            @error('description')
                            <p class="text-xs text-error mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- نقاط التحقق السريع (Acceptance Criteria UI) -->
                        <div class="pt-2 border-t border-outline-variant/40">
                            <label
                                class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">
                                معايير القبول ونقاط التحقق (Acceptance Criteria)
                            </label>

                            <div class="space-y-2.5">
                                <div
                                    class="flex items-center gap-2 bg-surface-container/30 p-2.5 rounded-xl border border-outline-variant/40">
                                    <input type="checkbox" checked
                                        class="w-4 h-4 rounded text-secondary focus:ring-secondary border-outline-variant cursor-pointer">
                                    <input type="text" value="ربط بوابة Stripe بالـ Backend بنجاح"
                                        class="flex-1 bg-transparent border-none text-xs text-on-surface focus:ring-0 p-0 font-medium line-through opacity-70">
                                    <button type="button" class="text-on-surface-variant hover:text-error"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>

                                <div
                                    class="flex items-center gap-2 bg-surface-container/30 p-2.5 rounded-xl border border-outline-variant/40">
                                    <input type="checkbox"
                                        class="w-4 h-4 rounded text-secondary focus:ring-secondary border-outline-variant cursor-pointer">
                                    <input type="text"
                                        value="إضافة فحص الأخطاء (Error Handling) وإظهار Alert للمستخدم"
                                        class="flex-1 bg-transparent border-none text-xs text-on-surface focus:ring-0 p-0 font-medium">
                                    <button type="button" class="text-on-surface-variant hover:text-error"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <span
                                        class="material-symbols-outlined text-outline-variant text-[20px]">add_box</span>
                                    <input type="text" placeholder="أضف معيار قبول جديد..."
                                        class="flex-1 bg-surface-container/20 border border-dashed border-outline-variant rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-primary focus:bg-white transition-all">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- العمود الأيسر (4 أعمدة): الإعدادات وحالة التقدم -->
                <div class="lg:col-span-4 space-y-6">

                    <div class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation space-y-5">

                        <!-- 1. تحديث الحالة (Status) -->
                        <div>
                            <label
                                class="block text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2.5">
                                حالة المهمة (Status)
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="status = 'pending'"
                                    :class="status === 'pending' ?
                                        'bg-amber-50 text-amber-700 border-amber-300 font-bold shadow-sm' :
                                        'bg-surface-container/50 text-on-surface-variant border-transparent'"
                                    class="py-2.5 rounded-xl border text-xs text-center transition-all">قيد
                                    الانتظار</button>

                                <button type="button" @click="status = 'in_progress'"
                                    :class="status === 'in_progress' ?
                                        'bg-indigo-50 text-indigo-700 border-indigo-300 font-bold shadow-sm' :
                                        'bg-surface-container/50 text-on-surface-variant border-transparent'"
                                    class="py-2.5 rounded-xl border text-xs text-center transition-all">قيد
                                    التنفيذ</button>

                                <button type="button" @click="status = 'completed'"
                                    :class="status === 'completed' ?
                                        'bg-emerald-50 text-emerald-700 border-emerald-300 font-bold shadow-sm' :
                                        'bg-surface-container/50 text-on-surface-variant border-transparent'"
                                    class="py-2.5 rounded-xl border text-xs text-center transition-all">مكتملة</button>
                            </div>
                            <input type="hidden" name="status" :value="status">
                        </div>

                        <hr class="border-outline-variant/40">

                        <!-- 2. تحديث الأولوية (Priority) -->
                        <div>
                            <label
                                class="block text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2.5">
                                الأولوية (Priority)
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="priority = 'high'"
                                    :class="priority === 'high' ?
                                        'bg-error-container text-on-error-container border-error font-extrabold shadow-sm' :
                                        'bg-surface-container/50 text-on-surface-variant border-transparent'"
                                    class="py-2.5 rounded-xl border text-xs text-center transition-all flex items-center justify-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                    <span>عالية</span>
                                </button>

                                <button type="button" @click="priority = 'medium'"
                                    :class="priority === 'medium' ?
                                        'bg-primary-container text-on-primary-container border-primary font-extrabold shadow-sm' :
                                        'bg-surface-container/50 text-on-surface-variant border-transparent'"
                                    class="py-2.5 rounded-xl border text-xs text-center transition-all flex items-center justify-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    <span>متوسطة</span>
                                </button>

                                <button type="button" @click="priority = 'low'"
                                    :class="priority === 'low' ?
                                        'bg-secondary-container text-on-secondary-container border-secondary font-extrabold shadow-sm' :
                                        'bg-surface-container/50 text-on-surface-variant border-transparent'"
                                    class="py-2.5 rounded-xl border text-xs text-center transition-all flex items-center justify-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                    <span>منخفضة</span>
                                </button>
                            </div>
                            <input type="hidden" name="priority" :value="priority">
                        </div>

                        <hr class="border-outline-variant/40">

                        <!-- 3. السبرنت (Sprint) -->
                        <div>
                            <label class="block text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2">
                                السبرنت التابع له (Sprint)
                            </label>
                            <select name="sprint_id"
                                class="w-full px-3.5 py-3 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs text-on-surface focus:outline-none focus:border-primary focus:bg-white font-bold transition-all">
                                <option value="">-- في الـ Backlog العام --</option>
                                <option value="1" {{ ($task->sprint_id ?? 1) == 1 ? 'selected' : '' }}>🔥 Sprint
                                    #3: نظام الدفع والاشتراكات</option>
                                <option value="2" {{ ($task->sprint_id ?? 0) == 2 ? 'selected' : '' }}>Sprint #4:
                                    تحسينات لوحة التحكم</option>
                            </select>
                        </div>

                        <!-- 4. تاريخ الاستحقاق ونقاط الجهد -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">تاريخ
                                    الاستحقاق</label>
                                <input type="date" name="due_date"
                                    value="{{ old('due_date', $task->due_date ?? date('Y-m-d', strtotime('+2 days'))) }}"
                                    class="w-full px-3 py-2.5 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-geist font-bold text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">نقاط الجهد (Story
                                    Pt.)</label>
                                <input type="number" name="story_points" min="1" max="21"
                                    value="{{ old('story_points', $task->story_points ?? 5) }}"
                                    class="w-full px-3 py-2.5 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-geist font-bold text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all text-center">
                            </div>
                        </div>

                        <hr class="border-outline-variant/40">

                        <!-- معلومات الإضافة التلقائية -->
                        <div
                            class="bg-surface-container-low p-3.5 rounded-2xl border border-outline-variant/40 space-y-1 text-[11px] text-on-surface-variant">
                            <div class="flex justify-between">
                                <span>تاريخ الإنشاء:</span>
                                <span
                                    class="font-geist font-bold text-on-surface">{{ $task->created_at ?? '2026-07-01' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>آخر تحديث:</span>
                                <span
                                    class="font-geist font-bold text-on-surface">{{ $task->updated_at ?? 'الآن' }}</span>
                            </div>
                        </div>

                        <!-- أزرار التحديث والإلغاء -->
                        <div class="pt-1 space-y-2.5">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transform active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">update</span>
                                <span>حفظ التعديلات (Update)</span>
                            </button>

                            <a href="{{ route('tasks.index') ?? '#' }}"
                                class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-bold py-3 px-6 rounded-2xl transition-all duration-200 text-center block text-xs">
                                الرجوع دون حفظ
                            </a>
                        </div>

                    </div>

                </div>

            </form>

        </div>

        <x-dashboard.footer />

    </main>
</body>

</html>