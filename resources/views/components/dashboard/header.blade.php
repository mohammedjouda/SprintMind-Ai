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
        <div class="relative w-48 sm:w-72">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input type="text" placeholder="{{ $searchPlaceholder }}"
                class="w-full pr-10 pl-4 py-2 bg-surface-container/60 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all">
        </div>
        @endif

        {{ $left ?? '' }}
    </div>

    <div class="flex items-center gap-3">

        {{ $right ?? '' }} <button class="relative p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full animate-ping"></span>
            <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-error rounded-full"></span>
        </button>

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