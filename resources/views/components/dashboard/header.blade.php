@props([
'searchPlaceholder' => 'ابحث في المهام، السبرنتات...',
'actionText' => 'مهمة جديدة',
'actionLink' => route('tasks.create'),
'actionIcon' => 'add',
'showSearch' => true,
'showAction' => true,
])

<header class="h-16 bg-white/80 backdrop-blur-md border-b border-outline-variant/60 flex justify-between items-center px-4 lg:px-8 sticky top-0 z-30">

    <div class="flex items-center gap-4 flex-1">
        <button @click="sidebarOpen = true" class="lg:hidden p-2 text-on-surface-variant hover:bg-surface-container rounded-lg transition-colors">
            <span class="material-symbols-outlined">menu</span>
        </button>

        @if($showSearch)
        <div x-data="{
            query: '',
            results: [],
            open: false,
            loading: false,
            async performSearch() {
                if (this.query.trim().length < 2) {
                    this.results = [];
                    this.open = false;
                    return;
                }
                this.loading = true;
                this.open = true;
                try {
                    const response = await fetch(`/search?q=${encodeURIComponent(this.query)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (response.ok) {
                        this.results = await response.json();
                    } else {
                        this.results = [];
                    }
                } catch (e) {
                    console.error('Error during search:', e);
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            }
        }" class="relative w-48 sm:w-72" @click.outside="open = false">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input type="text"
                placeholder="{{ $searchPlaceholder }}"
                x-model="query"
                x-on:input.debounce.300ms="performSearch"
                x-on:focus="if (query.trim().length >= 2) open = true"
                class="w-full pr-10 pl-4 py-2 bg-surface-container/60 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all">

            <!-- Pop-up Results Dropdown -->
            <div x-show="open"
                x-cloak
                class="absolute right-0 mt-2 w-72 sm:w-[400px] bg-white/95 backdrop-blur-md border border-outline-variant/60 rounded-2xl shadow-2xl py-3 z-50 transition-all origin-top-right text-right overflow-hidden"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                style="display: none;">

                <!-- Loading State -->
                <div x-show="loading" class="px-4 py-6 text-center text-on-surface-variant flex flex-col items-center justify-center gap-2">
                    <svg class="animate-spin h-6 w-6 text-primary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs font-bold font-tajawal">جاري البحث...</span>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && results.length === 0" class="px-4 py-8 text-center text-on-surface-variant flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-3xl text-on-surface-variant/40">search_off</span>
                    <p class="text-xs font-bold">لم نجد أي نتائج لـ "<span class="text-primary font-black" x-text="query"></span>"</p>
                    <p class="text-[10px] text-on-surface-variant/75">تأكد من كتابة أحرف صحيحة أو جرب كلمة أخرى</p>
                </div>

                <!-- Results List -->
                <div x-show="!loading && results.length > 0" class="max-h-80 overflow-y-auto divide-y divide-outline-variant/30">
                    <template x-for="result in results" :key="result.type + '-' + result.id">
                        <a :href="result.url" class="flex items-start gap-3 p-3 hover:bg-primary/5 transition-colors group">
                            <!-- Icon -->
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                :class="result.type === 'task' ? 'text-indigo-600 bg-indigo-50 group-hover:bg-indigo-100' : 'text-teal-600 bg-teal-50 group-hover:bg-teal-100'">
                                <span class="material-symbols-outlined text-[18px]" x-text="result.type === 'task' ? 'assignment' : 'directions_run'"></span>
                            </div>
                            <!-- Content Details -->
                            <div class="flex-1 min-w-0 pr-1 text-right">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-xs font-bold text-on-surface group-hover:text-primary transition-colors truncate" x-text="result.title"></p>

                                    <!-- Task Status -->
                                    <template x-if="result.type === 'task'">
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md border"
                                            :class="{
                                                  'bg-amber-50 text-amber-700 border-amber-200': result.status === 'pending' || result.status === 'todo',
                                                  'bg-indigo-50 text-indigo-700 border-indigo-200': result.status === 'in_progress',
                                                  'bg-emerald-50 text-emerald-700 border-emerald-200': result.status === 'completed'
                                              }"
                                            x-text="result.status === 'pending' || result.status === 'todo' ? 'قيد الانتظار' : (result.status === 'in_progress' ? 'قيد التنفيذ' : 'مكتمل')">
                                        </span>
                                    </template>

                                    <!-- Sprint Status -->
                                    <template x-if="result.type === 'sprint'">
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md border"
                                            :class="{
                                                  'bg-emerald-50 text-emerald-700 border-emerald-200': result.status === 'active',
                                                  'bg-slate-50 text-slate-700 border-slate-200': result.status === 'completed',
                                                  'bg-amber-50 text-amber-700 border-amber-200': result.status === 'planned'
                                              }"
                                            x-text="result.status === 'active' ? 'نشط' : (result.status === 'completed' ? 'مكتمل' : 'مخطط')">
                                        </span>
                                    </template>
                                </div>
                                <p class="text-[10px] text-on-surface-variant/80 mt-1 truncate" x-text="result.subtitle"></p>
                            </div>
                        </a>
                    </template>
                </div>

            </div>
        </div>
        @endif

        {{ $left ?? '' }}
    </div>

    <div class="flex items-center gap-3">

        {{ $right ?? '' }}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="relative p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors focus:outline-none">
                <span class="material-symbols-outlined">notifications</span>
                @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full animate-ping"></span>
                <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full"></span>
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.outside="open = false" x-cloak
                class="absolute left-0 mt-2 w-80 bg-white border border-outline-variant/60 rounded-2xl shadow-xl py-3 z-50 transition-all origin-top-left text-right"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                style="display: none;">

                <div class="px-4 py-2 border-b border-outline-variant/40 flex justify-between items-center leading-tight">
                    <span class="text-xs font-black text-on-surface">التنبيهات الأخيرة</span>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[10px] text-primary font-bold hover:underline">
                            تعيين الكل كمقروء
                        </button>
                    </form>
                    @endif
                </div>

                <div class="max-h-64 overflow-y-auto divide-y divide-outline-variant/30">
                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                    @php
                    $type = $notification->data['type'] ?? 'default';
                    $icon = 'notifications';
                    $iconColor = 'text-slate-600 bg-slate-50';
                    if ($type === 'task_created') {
                    $icon = 'assignment';
                    $iconColor = 'text-indigo-600 bg-indigo-50/50';
                    } elseif ($type === 'task_due') {
                    $icon = 'event_busy';
                    $iconColor = 'text-rose-600 bg-rose-50/50';
                    } elseif ($type === 'sprint_started') {
                    $icon = 'directions_run';
                    $iconColor = 'text-teal-600 bg-teal-50/50';
                    } elseif ($type === 'sprint_due') {
                    $icon = 'schedule';
                    $iconColor = 'text-amber-600 bg-amber-50/50';
                    }
                    @endphp
                    <a href="{{ route('notifications.go', $notification->id) }}" class="flex items-start gap-3 p-3 hover:bg-slate-50 transition-colors">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $iconColor }}">
                            <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0 pr-1 text-right">
                            <p class="text-xs font-bold text-on-surface truncate">{{ $notification->data['title'] ?? 'تنبيه جديد' }}</p>
                            <p class="text-[10px] text-on-surface-variant truncate mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                            <span class="text-[9px] text-on-surface-variant/75 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-3xl text-on-surface-variant/40">notifications_off</span>
                        <p class="text-xs font-bold text-on-surface-variant mt-1">لا توجد تنبيهات غير مقروءة</p>
                    </div>
                    @endforelse
                </div>

                <div class="px-4 pt-2 border-t border-outline-variant/40 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                        <span>عرض كل التنبيهات</span>
                    </a>
                </div>
            </div>
        </div>

        <x-dashboard.profile-dropdown />

        @if($showAction)
        <a href="{{ $actionLink }}"
            class="bg-primary hover:bg-indigo-700 text-white px-5 py-2 rounded-xl font-bold text-sm shadow-md shadow-primary/20 flex items-center gap-2 transition-all transform active:scale-95">
            <span class="material-symbols-outlined text-[18px]">{{ $actionIcon }}</span>
            <span class="hidden sm:inline">{{ $actionText }}</span>
        </a>
        @endif
    </div>
</header>