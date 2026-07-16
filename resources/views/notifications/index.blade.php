<x-layouts.app title="SprintMind Ai - مركز التنبيهات">

    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Alerts -->
        @if (session('success'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl shadow-sm text-sm font-bold animate-fadeIn">
            <span class="material-symbols-outlined text-[20px] text-emerald-400">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation">
            <div>
                <h2 class="text-2xl font-black text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[28px]">notifications</span>
                    مركز التنبيهات
                </h2>
                <p class="text-on-surface-variant text-xs mt-1">تتبع آخر المستجدات والمهام والسبرنتات النشطة والتذكيرات اليومية.</p>
            </div>

            @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="bg-primary/10 hover:bg-primary/20 text-primary text-xs px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">done_all</span>
                    تعيين الكل كمقروء
                </button>
            </form>
            @endif
        </div>

        <div class="bg-white rounded-3xl border border-outline-variant/60 card-elevation overflow-hidden">
            <div class="divide-y divide-outline-variant/40">
                @forelse($notifications as $notification)
                @php
                    $isUnread = $notification->unread();
                    $type = $notification->data['type'] ?? 'default';
                    $icon = 'notifications';
                    $iconBg = 'bg-slate-100 text-slate-600';
                    
                    if ($type === 'task_created') {
                        $icon = 'assignment';
                        $iconBg = 'bg-indigo-50 text-indigo-600';
                    } elseif ($type === 'task_due') {
                        $icon = 'event_busy';
                        $iconBg = 'bg-rose-50 text-rose-600';
                    } elseif ($type === 'sprint_started') {
                        $icon = 'directions_run';
                        $iconBg = 'bg-teal-50 text-teal-600';
                    } elseif ($type === 'sprint_due') {
                        $icon = 'schedule';
                        $iconBg = 'bg-amber-50 text-amber-600';
                    }
                @endphp
                <div class="flex items-start gap-4 p-5 hover:bg-slate-50 transition-all relative group {{ $isUnread ? 'bg-primary/5 hover:bg-primary/10' : '' }}">
                    @if($isUnread)
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-primary rounded-full"></span>
                    @endif

                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 {{ $iconBg }}">
                        <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                    </div>

                    <div class="flex-1 min-w-0 text-right pr-2">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h4 class="text-sm font-bold text-on-surface truncate">
                                {{ $notification->data['title'] ?? 'تنبيه جديد' }}
                            </h4>
                            <span class="text-[10px] text-on-surface-variant font-medium">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        @if(!empty($notification->data['action_url']))
                        @php
                            $actionUrl = $notification->data['action_url'];
                            if (str_starts_with($actionUrl, 'http://') || str_starts_with($actionUrl, 'https://')) {
                                $parsed = parse_url($actionUrl);
                                $path = $parsed['path'] ?? '';
                                $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
                                $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
                                $actionUrl = url($path . $query . $fragment);
                            } else {
                                $actionUrl = url($actionUrl);
                            }
                        @endphp
                        <div class="mt-3">
                            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1 text-primary text-xs font-bold hover:underline">
                                <span>عرض التفاصيل</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 self-center">
                        @if($isUnread)
                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" title="تحديد كمقروء" class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-xl transition-all">
                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التنبيه؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="حذف" class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-xl transition-all">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-16 px-4">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <span class="material-symbols-outlined text-[36px]">notifications_off</span>
                    </div>
                    <h3 class="font-extrabold text-base text-on-surface">لا توجد تنبيهات حالياً</h3>
                    <p class="text-xs text-on-surface-variant mt-1">عندما تتلقى إشعارات جديدة، ستظهر هنا مباشرة.</p>
                </div>
                @endforelse
            </div>

            @if ($notifications->hasPages())
            <div class="p-4 border-t border-outline-variant/60">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>

</x-layouts.app>
