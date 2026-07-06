<header
    class="h-16 bg-white/80 backdrop-blur-md border-b border-outline-variant/60 flex justify-between items-center px-4 lg:px-8 sticky top-0 z-30">
    <div class="flex items-center gap-4 flex-1">
        <button @click="sidebarOpen = true"
            class="lg:hidden p-2 text-on-surface-variant hover:bg-surface-container rounded-lg">
            <span class="material-symbols-outlined">menu</span>
        </button>
        {{ $left ?? '' }}
    </div>

    <div class="flex items-center gap-3">
        {{ $right ?? '' }}
    </div>
</header>
