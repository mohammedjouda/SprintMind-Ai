<section class="bg-white p-6 rounded-3xl border border-primary/20 ai-glow relative overflow-hidden">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined text-[24px] animate-pulse">auto_awesome</span>
            </div>
            <div>
                <h3 class="font-extrabold text-base text-on-surface">مساعد التخطيط وتفكيك المهام بالذكاء الاصطناعي</h3>
                <p class="text-xs text-on-surface-variant">اكتب فكرة أو ميزة برمجية (مثلاً: نظام دفع، لوحة تحكم)، وسيقوم الـ AI بتحويلها لسبرنتات ومهام فرعية فوراً.</p>
            </div>
        </div>
        <span class="text-[11px] font-bold bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full">مدعوم بـ LLM Core</span>
    </div>

    <form action="#" method="POST" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant">lightbulb</span>
            <input type="text" name="ai_prompt" required
                placeholder="ما الذي تريد بناءه أو إنجازه اليوم؟ (مثال: بناء متجر إلكتروني بـ Laravel)..."
                class="w-full pr-11 pl-4 py-3.5 bg-surface-container/50 border border-outline-variant/80 rounded-2xl text-sm focus:outline-none focus:border-primary focus:bg-white transition-all font-medium">
        </div>
        <button type="submit"
            class="bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold px-8 py-3.5 rounded-2xl text-sm shadow-lg shadow-primary/25 flex items-center justify-center gap-2 shrink-0 transition-all transform active:scale-95 group">
            <span>تفكيك الفكرة</span>
            <span class="material-symbols-outlined text-[18px] group-hover:rotate-12 transition-transform">magic_button</span>
        </button>
    </form>
</section>