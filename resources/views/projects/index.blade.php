<x-layouts.app title="SprintMind Ai AI - إدارة المشاريع">

    <x-slot:headerLeft>
        <div class="relative w-48 sm:w-72">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input type="text" placeholder="ابحث في مشاريعك، السبرنتات الجارية..."
                class="w-full pr-10 pl-4 py-2 bg-surface-container/60 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all">
        </div>
    </x-slot:headerLeft>

    <x-slot:headerRight>
        <button class="relative p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full animate-ping"></span>
            <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full"></span>
        </button>

        <x-dashboard.profile-dropdown />

        <button @click="$dispatch('open-create-modal')"
            class="bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-primary/20 flex items-center gap-2 transition-all transform active:scale-95">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span class="hidden sm:inline">مشروع جديد</span>
        </button>
    </x-slot:headerRight>

    <div x-data="{ filterCategory: 'all', showCreateModal: false }" @open-create-modal.window="showCreateModal = true">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-primary-container text-on-primary-container text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">مساحات العمل (Workspaces)</span>
                    <span class="text-xs text-on-surface-variant">📁 إدارة السبرنتات والتخطيط المنهجي</span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-black text-on-surface">المشاريع والسبرنتات النشطة</h2>
                <p class="text-sm text-on-surface-variant mt-1">قم بإدارة مشاريعك البرمجية، قسّم العمل إلى سبرنتات، وتابع نسبة الإنجاز بدقة.</p>
            </div>

            <div x-data="{ openFilters: false }" class="relative w-full sm:w-auto">
                <button @click="openFilters = !openFilters" @click.away="openFilters = false" class="flex items-center gap-2 px-4 py-2 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 rounded-xl text-xs font-bold text-on-surface transition-all w-full sm:w-auto justify-center sm:justify-start">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    <span>تصفية المشاريع</span>
                    <span class="material-symbols-outlined text-[16px] transition-transform" :class="openFilters ? 'rotate-180' : ''">keyboard_arrow_down</span>
                </button>
                <div x-show="openFilters" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute left-0 mt-2 w-52 bg-white border border-outline-variant/60 rounded-2xl shadow-xl z-20 py-2 text-right">
                    <button @click="filterCategory = 'all'; openFilters = false" :class="filterCategory === 'all' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">all_inclusive</span>
                        <span>الكل</span>
                    </button>
                    <button @click="filterCategory = 'software'; openFilters = false" :class="filterCategory === 'software' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">code</span>
                        <span>تطوير البرمجيات</span>
                    </button>
                    <button @click="filterCategory = 'marketing'; openFilters = false" :class="filterCategory === 'marketing' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">campaign</span>
                        <span>التسويق والمحتوى</span>
                    </button>
                    <button @click="filterCategory = 'personal'; openFilters = false" :class="filterCategory === 'personal' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">task</span>
                        <span>مشاريع شخصية</span>
                    </button>
                    @foreach($projects->pluck('category')->unique() as $cat)
                    @if(!in_array($cat, ['software', 'marketing', 'personal']) && !empty($cat))
                    <button @click="filterCategory = '{{ $cat }}'; openFilters = false" :class="filterCategory === '{{ $cat }}' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[16px]">folder</span>
                        <span>{{ $cat }}</span>
                    </button>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[26px]">folder_special</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">إجمالي المشاريع النشطة</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">3 <span class="text-xs font-normal text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-tajawal">يعمل بكفاءة</span></h4>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[26px]">directions_run</span>
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant font-bold">السبرنتات الجارية (Active Sprints)</p>
                    <h4 class="text-2xl font-black font-geist text-on-surface mt-0.5">4 <span class="text-xs font-normal text-on-surface-variant font-tajawal">سبرنتات قيد الإنجاز</span></h4>
                </div>
            </div>

            <div class="bg-gradient-to-r from-indigo-900 to-primary text-white p-5 rounded-3xl shadow-md flex items-center justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-xs text-indigo-200 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-emerald-400 animate-pulse">auto_awesome</span>
                        <span>تأثير الذكاء الاصطناعي في التخطيط</span>
                    </p>
                    <h4 class="text-2xl font-black font-geist text-white mt-0.5">85% <span class="text-xs font-normal text-indigo-100 font-tajawal">من المهام فُككت بالـ AI</span></h4>
                </div>
                <span class="material-symbols-outlined text-6xl text-white/10 absolute -left-2 -bottom-2 pointer-events-none">psychology</span>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div @click="showCreateModal = true"
                class="rounded-3xl border-2 border-dashed border-outline-variant hover:border-primary bg-surface-container/20 hover:bg-surface-container/50 p-6 flex flex-col items-center justify-center text-center cursor-pointer transition-all min-h-[340px] group">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                    <span class="material-symbols-outlined text-3xl">create_new_folder</span>
                </div>
                <h3 class="text-lg font-black text-on-surface mb-1">إضافة مشروع أو مساحة عمل</h3>
                <p class="text-xs text-on-surface-variant max-w-xs leading-relaxed mb-6">
                    أنشئ مشروعاً جديداً، وسيقوم المساعد الذكي بتقسيمه فوراً إلى سبرنتات ومهام تشغيلية.
                </p>
                <span class="bg-white group-hover:bg-primary group-hover:text-white text-primary border border-primary/20 text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>البدء بمشروع جديد</span>
                </span>
            </div>

            @foreach ($projects as $project)
            @php
            $gradients = [
            'software' => 'from-primary to-indigo-400',
            'marketing' => 'from-amber-500 to-orange-400',
            'personal' => 'from-secondary to-teal-400',
            ];
            $gradient = $gradients[$project->category] ?? 'from-slate-500 to-slate-400';

            $activeSprint = $project->sprints->first();
            $totalTasks = $project->tasks_count;
            $completedTasks = $project->completed_tasks_count;
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            @endphp

            <div x-show="filterCategory === 'all' || filterCategory === '{{ $project->category }}'" x-transition
                class="bg-white rounded-3xl border border-outline-variant/60 card-elevation p-6 flex flex-col justify-between hover:border-primary/50 transition-all group relative overflow-hidden">

                <div class="absolute top-0 right-0 left-0 h-1.5 bg-gradient-to-r {{ $gradient }}"></div>

                <div>
                    <div class="flex justify-between items-start mb-4">
                        @if ($project->category === 'software')
                        <span class="bg-primary/10 text-primary text-[11px] font-extrabold px-3 py-1 rounded-full border border-primary/20 inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">code</span>
                            <span> تطوير البرمجيات</span>
                        </span>
                        @elseif ($project->category === 'marketing')
                        <span class="bg-amber-500/10 text-amber-700 text-[11px] font-extrabold px-3 py-1 rounded-full border border-amber-500/20 inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">campaign</span>
                            <span> التسويق والمحتوى</span>
                        </span>
                        @elseif ($project->category === 'personal')
                        <span class="bg-secondary/10 text-secondary text-[11px] font-extrabold px-3 py-1 rounded-full border border-secondary/20 inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">task</span>
                            <span> مشاريع شخصية</span>
                        </span>
                        @else
                        <span class="bg-slate-500/10 text-slate-700 text-[11px] font-extrabold px-3 py-1 rounded-full border border-slate-500/20 inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">folder</span>
                            <span> {{ $project->category }}</span>
                        </span>
                        @endif

                        <div class="flex items-center gap-1">
                            @if ($project->status === 'active')
                            <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">نشط</span>
                            @elseif ($project->status === 'completed')
                            <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-200">مكتمل</span>
                            @else
                            <span class="bg-slate-50 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-slate-200">مؤرشف</span>
                            @endif

                            <div x-data="{ open: false }" class="relative">
                                <button @click.stop="open = !open" @click.away="open = false" class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container">
                                    <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                </button>
                                <div x-show="open" x-transition class="absolute left-0 mt-1 w-40 bg-white border border-outline-variant/60 rounded-2xl shadow-xl z-20 py-2 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-all">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        <span>عرض المشروع</span>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-all">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        <span>تعديل المشروع</span>
                                    </a>
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع بجميع سبرنتاته ومهامه؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-2 w-full text-right px-4 py-2 text-xs font-bold text-error hover:bg-error/5 transition-all">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                            <span>حذف المشروع</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-on-surface group-hover:text-primary transition-colors mb-2">
                        <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                    </h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2 mb-6">
                        {{ $project->description }}
                    </p>

                    @if ($activeSprint)
                    <div class="bg-surface-container/50 rounded-2xl p-3.5 border border-outline-variant/40 mb-6 space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-on-surface flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                                <span>السبرنت الحالي: <strong class="font-geist text-primary">{{ $activeSprint->name }}</strong></span>
                            </span>
                            @if ($activeSprint->end_date)
                            <span class="text-[11px] text-on-surface-variant">باقي {{ now()->diffInDays($activeSprint->end_date) }} يوم</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-on-surface-variant truncate font-medium"> الهدف: {{ $activeSprint->goal }}</p>
                    </div>
                    @else
                    <div class="bg-surface-container/30 rounded-2xl p-3.5 border border-dashed border-outline-variant/40 mb-6 text-center">
                        <p class="text-xs text-on-surface-variant font-medium text-center py-2">لا يوجد سبرنت نشط حالياً</p>
                    </div>
                    @endif
                </div>

                <div class="space-y-4 pt-4 border-t border-outline-variant/40 mt-auto">
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-on-surface-variant">الإنجاز العام</span>
                            <span class="font-geist text-primary">{{ $completionRate }}%</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden p-0.5">
                            <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[16px] text-outline-variant">task_alt</span>
                            <span><strong class="text-on-surface font-geist font-bold">{{ $completedTasks }}/{{ $totalTasks }}</strong> مهمة</span>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="bg-surface-container hover:bg-primary hover:text-white text-on-surface text-xs font-bold px-4 py-2 rounded-xl transition-all flex items-center gap-1 group/btn">
                            <span>إدارة السبرنتات</span>
                            <span class="material-symbols-outlined text-[14px] group-hover/btn:-translate-x-1 transition-transform">arrow_back_ios</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        @if ($projects->hasPages())
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
        @endif

        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-data="{ useAiScaffold: true, categoryMode: 'select', durationMode: 'select' }">
            <div x-show="showCreateModal" x-transition.opacity @click="showCreateModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div x-show="showCreateModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-right shadow-2xl transition-all border border-outline-variant/60 relative">

                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/40 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">rocket_launch</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-on-surface">تأسيس مشروع برمجي جديد</h3>
                                <p class="text-xs text-on-surface-variant">قم بضبط تفاصيل المشروع، ودع خوارزمية الـ AI تتولى التخطيط.</p>
                            </div>
                        </div>
                        <button @click="showCreateModal = false" class="text-on-surface-variant hover:text-error p-1 rounded-lg">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('projects.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-extrabold text-on-surface mb-1.5">اسم المشروع <span class="text-error">*</span></label>
                            <input type="text" name="name" required placeholder="مثال: تطبيق متجر ذكي..." class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">تصنيف المشروع</label>
                                <div x-show="categoryMode === 'select'">
                                    <select :name="categoryMode === 'select' ? 'category' : ''" @change="if ($event.target.value === 'custom') { categoryMode = 'input' }" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all">
                                        <option value="software"> تطوير البرمجيات</option>
                                        <option value="marketing">التسويق والمحتوى</option>
                                        <option value="personal">مشاريع شخصية</option>
                                        <option value="custom">✍️ تصنيف مخصص...</option>
                                    </select>
                                </div>
                                <div x-show="categoryMode === 'input'" x-cloak>
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="categoryMode === 'input' ? 'category' : ''" placeholder="اكتب التصنيف المخصص..." class="flex-1 px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all focus:border-primary">
                                        <button type="button" @click="categoryMode = 'select'" class="text-xs text-primary font-bold hover:underline shrink-0 px-2">العودة</button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label class="block text-xs font-extrabold text-on-surface mb-1.5">المدة الزمنية</label>
                                <div x-show="durationMode === 'select'">
                                    <select :name="durationMode === 'select' ? 'expected_duration' : ''" @change="if ($event.target.value === 'custom') { durationMode = 'input' }" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all">
                                        <option value="3_months" selected>🔥 مشروع متوسط (3 أشهر)</option>
                                        <option value="6_months">🚀 مشروع ضخم (6 أشهر)</option>
                                        <option value="custom">✍️ تحديد يدوي...</option>
                                    </select>
                                </div>
                                <div x-show="durationMode === 'input'" x-cloak>
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="durationMode === 'input' ? 'expected_duration' : ''" placeholder="مثال: سنتين، 4 أسابيع..." class="flex-1 px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all focus:border-primary">
                                        <button type="button" @click="durationMode = 'select'" class="text-xs text-primary font-bold hover:underline shrink-0 px-2">العودة</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-on-surface mb-1.5">وصف الفكرة <span class="text-error">*</span></label>
                            <textarea name="description" required rows="4" placeholder="اكتب شرحاً عاماً للمميزات... سيقرؤه الـ AI لإنشاء خطة السبرنتات" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs transition-all"></textarea>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-950 via-primary to-indigo-900 text-white p-5 rounded-2xl text-right relative overflow-hidden border-2" :class="useAiScaffold ? 'border-indigo-400 shadow-lg shadow-indigo-500/20' : 'border-transparent'">
                            <span class="material-symbols-outlined absolute -left-6 -bottom-6 text-[120px] text-white/5 pointer-events-none">auto_awesome</span>

                            <div class="flex items-start justify-between gap-4 relative z-10">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-emerald-400 text-slate-950 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">مستحسن جداً</span>
                                        <h4 class="text-sm font-black text-white">التخطيط بالذكاء الاصطناعي (AI Architect)</h4>
                                    </div>
                                    <p class="text-xs text-indigo-100/80 leading-relaxed max-w-lg font-medium">سيقوم الذكاء الاصطناعي فور حفظ المشروع بإنشاء سبرنتات تحتوي على مهام تشغيلية جاهزة.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer mt-1">
                                    <input type="checkbox" name="use_ai_scaffold" value="1" x-model="useAiScaffold" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-3 flex flex-col sm:flex-row-reverse items-center justify-between gap-3">
                            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-primary to-indigo-600 text-white font-bold py-3.5 px-8 rounded-2xl shadow-lg flex items-center justify-center gap-2">
                                <span>تأسيس المشروع</span>
                                <span class="material-symbols-outlined text-[18px]" x-text="useAiScaffold ? 'auto_awesome' : 'check'"></span>
                            </button>
                            <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-bold py-3.5 px-6 rounded-2xl transition-all text-xs">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>