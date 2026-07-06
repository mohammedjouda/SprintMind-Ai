<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center focus:outline-none">
        <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary-container font-bold flex items-center justify-center text-sm select-none hover:bg-primary-container/80 transition-colors">
            {{ substr(Auth::user()->name ?? 'م', 0, 1) }}
        </div>
    </button>

    <div x-show="open" @click.outside="open = false" x-cloak
        class="absolute left-0 mt-2 w-48 bg-white border border-outline-variant/60 rounded-xl shadow-lg py-2 z-50 transition-all origin-top-left"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95">
        
        <div class="px-4 py-2 border-b border-outline-variant/40 flex flex-col leading-tight">
            <span class="text-xs font-bold text-on-surface">{{ Auth::user()->name ?? 'محمد جودة' }}</span>
            <span class="text-[10px] text-on-surface-variant">مطور البرمجيات</span>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full text-right px-4 py-2 text-xs text-on-surface-variant hover:text-error hover:bg-error-container/20 flex items-center gap-2 transition-colors">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
