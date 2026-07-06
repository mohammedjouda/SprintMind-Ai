<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>TaskMaker AI - إنشاء مهمة جديدة</title>
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

<body class="text-on-surface min-h-screen flex selection:bg-primary selection:text-white" x-data="{ sidebarOpen: false, priority: 'medium', status: 'pending' }">

    <x-dashboard.sidebar :show-productivity="false" />

    <!-- ================== مساحة العمل الرئيسية (Main Canvas) ================== -->
    <main class="lg:mr-64 flex-1 flex flex-col min-h-screen">

        <!-- شريط علوي (Top Navbar) -->
        <x-dashboard.header>
            <x-slot:left>
                <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant">
                    <a href="{{ route('tasks.index') }}" class="hover:text-primary transition-colors">المهام</a>
                    <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                    <span class="text-on-surface">إضافة مهمة جديدة</span>
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

        <!-- محتوى النموذج (Create Task Form Workspace) -->
        <div class="p-4 lg:p-8 max-w-6xl mx-auto w-full space-y-6 flex-1">

            <!-- الهيدر الداخلي -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-black text-on-surface">إضافة مهمة تشغيلية جديدة</h2>
                    <p class="text-xs text-on-surface-variant mt-1">قم بتعبئة تفاصيل المهمة، أو دع الذكاء الاصطناعي
                        يساعدك في صياغة معايير الإنجاز.</p>
                </div>
                <span
                    class="bg-primary/10 text-primary border border-primary/20 text-xs font-extrabold px-3 py-1.5 rounded-full inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">edit_document</span>
                    <span>إدخال يدوي (Manual Create)</span>
                </span>
            </div>

            <!-- ================== نموذج الإدخال (Laravel Form) ================== -->
            <form action="{{ route('tasks.store') ?? '#' }}" method="POST"
                class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                @csrf <!-- توكن الحماية -->

                <!-- العمود الأيمن (8 أعمدة): التفاصيل الأساسية والمحتوى -->
                <div class="lg:col-span-8 space-y-6">

                    <div class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation space-y-5">

                        <!-- عنوان المهمة -->
                        <div>
                            <label class="block text-sm font-extrabold text-on-surface mb-2">
                                عنوان المهمة <span class="text-error">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                placeholder="مثال: برمجة واجهة الدفع الإلكتروني باستخدام Stripe API..."
                                class="w-full px-4 py-3.5 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium placeholder:text-on-surface-variant/60">
                            @error('title')
                            <p class="text-xs text-error mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- وصف المهمة + لمسة الذكاء الاصطناعي -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-extrabold text-on-surface">وصف المهمة والتفاصيل</label>
                                <!-- زر تحفيزي للـ AI -->
                                <button type="button"
                                    class="text-[11px] font-bold text-primary bg-primary/10 hover:bg-primary/20 px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1 ai-glow-sm">
                                    <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                                    <span>صياغة احترافية بالـ AI</span>
                                </button>
                            </div>
                            <textarea name="description" rows="5"
                                placeholder="اكتب شرحاً مفصلاً للمهمة، معايير القبول (Acceptance Criteria)، أو أي ملاحظات هامة لفريق العمل..."
                                class="w-full px-4 py-3.5 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium placeholder:text-on-surface-variant/60 leading-relaxed">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-xs text-error mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- قائمة التحقق الفرعية (Acceptance Criteria UI Hint) -->
                        <div class="pt-2 border-t border-outline-variant/40">
                            <label
                                class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">
                                نقاط التحقق السريعة (Sub-tasks / Acceptance Criteria)
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="material-symbols-outlined text-outline-variant text-[20px]">check_box_outline_blank</span>
                                    <input type="text"
                                        placeholder="أضف نقطة تحقق (مثال: التأكد من عمل الفالديشن في الباك إند)..."
                                        class="flex-1 bg-surface-container/30 border-none rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-primary focus:bg-white transition-all">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- العمود الأيسر (4 أعمدة): خصائص المهمة والإعدادات (Agile Metadata) -->
                <div class="lg:col-span-4 space-y-6">

                    <div class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation space-y-5">

                        <!-- 1. تحديد الحالة (Status) -->
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
                            <!-- حقل مخفي لإرسال القيمة مع الفورم -->
                            <input type="hidden" name="status" :value="status">
                        </div>

                        <hr class="border-outline-variant/40">

                        <!-- 2. تحديد الأولوية (Priority) -->
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

                        <!-- 3. ربط بالسبرنت (Sprint Association - Part 2 Link) -->
                        <div>
                            <label class="block text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2">
                                السبرنت التابع له (Sprint)
                            </label>
                            <select name="sprint_id"
                                class="w-full px-3.5 py-3 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs text-on-surface focus:outline-none focus:border-primary focus:bg-white font-bold transition-all">
                                <option value="" class="text-on-surface-variant">-- غير مرتبط (في الـ Backlog
                                    العام) --</option>
                                <option value="1" selected>🔥 Sprint #3: نظام الدفع والاشتراكات (الحالي)</option>
                                <option value="2">Sprint #4: تحسينات لوحة التحكم</option>
                                <option value="3">Sprint #5: إطلاق النسخة التجريبية MVP</option>
                            </select>
                        </div>

                        <!-- 4. تاريخ الاستحقاق (Due Date) & نقاط الجهد -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">تاريخ
                                    الاستحقاق</label>
                                <input type="date" name="due_date"
                                    value="{{ old('due_date', date('Y-m-d', strtotime('+3 days'))) }}"
                                    class="w-full px-3 py-2.5 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-geist font-bold text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">نقاط الجهد (Story
                                    Pt.)</label>
                                <input type="number" name="story_points" min="1" max="21"
                                    value="3"
                                    class="w-full px-3 py-2.5 bg-surface-container/50 border border-outline-variant/80 rounded-xl text-xs font-geist font-bold text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all text-center">
                            </div>
                        </div>

                        <hr class="border-outline-variant/40">

                        <!-- أزرار الحفظ والإرسال -->
                        <div class="pt-2 space-y-2.5">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transform active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                <span>حفظ وإنشاء المهمة</span>
                            </button>

                            <a href="{{ route('tasks.index') ?? '#' }}"
                                class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-bold py-3 px-6 rounded-2xl transition-all duration-200 text-center block text-xs">
                                إلغاء والعودة
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