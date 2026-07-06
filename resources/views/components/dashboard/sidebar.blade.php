@props(['showProductivity' => true])

<aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
    class="h-screen w-64 fixed right-0 top-0 bg-white border-l border-outline-variant/60 shadow-lg lg:shadow-none flex flex-col py-6 px-4 transition-transform duration-300 ease-in-out z-50">

    <div class="mb-8 px-3 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary to-indigo-600 flex items-center justify-center text-white shadow-md shadow-primary/20">
                <span class="material-symbols-outlined text-[22px]">bolt</span>
            </div>
            <div>
                <h1 class="text-xl font-black text-on-surface tracking-tight leading-none">TaskMaker</h1>
                <span class="text-[10px] font-bold px-1.5 py-0.5 bg-primary/10 text-primary rounded-md">AI
                    CO-PILOT</span>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-on-surface-variant hover:text-error">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <nav class="flex-1 space-y-1.5">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3.5 px-4 py-3 {{ request()->routeIs('dashboard') ? 'text-primary font-bold bg-primary/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container/60' }} rounded-xl transition-all">
            <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'filled text-primary' : '' }}">dashboard</span>
            <span class="text-sm">لوحة التحكم</span>
        </a>
        <a href="{{ route('tasks.index') }}"
            class="flex items-center gap-3.5 px-4 py-3 {{ request()->routeIs('tasks.*') ? 'text-primary font-bold bg-primary/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container/60' }} rounded-xl transition-all font-medium">
            <span class="material-symbols-outlined {{ request()->routeIs('tasks.*') ? 'filled text-primary' : '' }}">format_list_bulleted</span>
            <span class="text-sm">جميع المهام</span>
            <span
                class="mr-auto {{ request()->routeIs('tasks.*') ? 'bg-primary text-white' : 'bg-surface-container-high' }} text-xs px-2 py-0.5 rounded-full font-bold">
                {{ auth()->user()->tasks()->count() }}
            </span>
        </a>
        <a href="#"
            class="flex items-center gap-3.5 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container/60 rounded-xl transition-all font-medium">
            <span class="material-symbols-outlined">folder</span>
            <span class="text-sm">المشاريع</span>
        </a>
        <a href="#"
            class="flex items-center gap-3.5 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container/60 rounded-xl transition-all font-medium">
            <span class="material-symbols-outlined">auto_awesome</span>
            <span class="text-sm">السبرنتات الذكية</span>
        </a>
        <a href="#"
            class="flex items-center gap-3.5 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container/60 rounded-xl transition-all font-medium">
            <span class="material-symbols-outlined">calendar_month</span>
            <span class="text-sm">الجدول الزمني</span>
        </a>
    </nav>

    <div class="mt-auto">
        @if($showProductivity)
        <div
            class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-4 rounded-2xl relative overflow-hidden mb-4 shadow-md">
            <span
                class="material-symbols-outlined absolute -left-4 -bottom-4 text-7xl text-white/5 pointer-events-none">rocket_launch</span>
            <p class="text-xs text-indigo-200 font-bold mb-1">الإنتاجية هذا الأسبوع</p>
            <h4 class="text-2xl font-black mb-2">84% <span class="text-xs font-normal text-emerald-400">↑ مرتفع</span>
            </h4>
            <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-indigo-400 h-full w-[84%]"></div>
            </div>
        </div>
        @endif
    </div>
</aside>

<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
    class="fixed inset-0 bg-black/40 z-40 lg:hidden backdrop-blur-sm" style="display: none;"></div>