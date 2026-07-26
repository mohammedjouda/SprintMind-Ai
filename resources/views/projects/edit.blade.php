<x-layouts.app title="تعديل المشروع - {{ $project->name }}">

    <x-slot:headerLeft>
        <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-1.5 text-xs font-bold text-on-surface-variant hover:text-primary transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            <span>العودة للمشروع</span>
        </a>
    </x-slot:headerLeft>

    <div class="max-w-2xl mx-auto bg-white rounded-3xl border border-outline-variant/60 card-elevation p-6 sm:p-8" x-data="{ categoryMode: '{{ in_array($project->category, ['software', 'marketing', 'personal']) ? 'select' : 'input' }}', durationMode: '{{ in_array($project->expected_duration, ['3_months', '6_months']) ? 'select' : 'input' }}' }">

        <div class="flex items-center gap-3 pb-4 border-b border-outline-variant/40 mb-6">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">edit_note</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-on-surface">تعديل بيانات المشروع</h3>
                <p class="text-xs text-on-surface-variant">قم بتعديل تفاصيل المشروع ومساحة العمل الخاصة بك.</p>
            </div>
        </div>

        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-5 text-right">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-extrabold text-on-surface mb-1.5">اسم المشروع <span class="text-error">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $project->name) }}" placeholder="مثال: تطبيق متجر ذكي..." class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-sm focus:border-primary transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-bold text-xs">
                <div class="flex flex-col">
                    <label class="block text-xs font-extrabold text-on-surface mb-1.5">تصنيف المشروع</label>
                    <div x-show="categoryMode === 'select'">
                        <select :name="categoryMode === 'select' ? 'category' : ''" @change="if ($event.target.value === 'custom') { categoryMode = 'input' }" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all">
                            <option value="software" {{ $project->category === 'software' ? 'selected' : '' }}>💻 Software Development</option>
                            <option value="marketing" {{ $project->category === 'marketing' ? 'selected' : '' }}>📈 Marketing & Content</option>
                            <option value="personal" {{ $project->category === 'personal' ? 'selected' : '' }}> Personal Projects</option>
                            <option value="custom">✍️ تصنيف مخصص...</option>
                        </select>
                    </div>
                    <div x-show="categoryMode === 'input'" x-cloak>
                        <div class="flex items-center gap-2">
                            <input type="text" :name="categoryMode === 'input' ? 'category' : ''" value="{{ old('category', $project->category) }}" placeholder="اكتب التصنيف..." class="flex-1 px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all focus:border-primary">
                            <button type="button" @click="categoryMode = 'select'" class="text-xs text-primary font-bold hover:underline shrink-0">اختيار</button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-extrabold text-on-surface mb-1.5">المدة الزمنية</label>
                    <div x-show="durationMode === 'select'">
                        <select :name="durationMode === 'select' ? 'expected_duration' : ''" @change="if ($event.target.value === 'custom') { durationMode = 'input' }" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all">
                            <option value="3_months" {{ $project->expected_duration === '3_months' ? 'selected' : '' }}>🔥 مشروع متوسط (3 أشهر)</option>
                            <option value="6_months" {{ $project->expected_duration === '6_months' ? 'selected' : '' }}>🚀 مشروع ضخم (6 أشهر)</option>
                            <option value="custom">✍️ تحديد يدوي...</option>
                        </select>
                    </div>
                    <div x-show="durationMode === 'input'" x-cloak>
                        <div class="flex items-center gap-2">
                            <input type="text" :name="durationMode === 'input' ? 'expected_duration' : ''" value="{{ old('expected_duration', $project->expected_duration) }}" placeholder="سنتين، 4 أسابيع..." class="flex-1 px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all focus:border-primary">
                            <button type="button" @click="durationMode = 'select'" class="text-xs text-primary font-bold hover:underline shrink-0">اختيار</button>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-on-surface mb-1.5">حالة المشروع</label>
                    <select name="status" class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs font-bold transition-all">
                        <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>مؤرشف</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-on-surface mb-1.5">وصف الفكرة <span class="text-error">*</span></label>
                <textarea name="description" required rows="5" placeholder="اكتب شرحاً عاماً للمميزات..." class="w-full px-4 py-3 bg-surface-container/40 border border-outline-variant/80 rounded-2xl text-xs transition-all">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="pt-3 flex flex-col sm:flex-row-reverse items-center justify-between gap-3 border-t border-outline-variant/40">
                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-primary to-indigo-600 text-white font-bold py-3.5 px-8 rounded-2xl shadow-lg flex items-center justify-center gap-2">
                    <span>تحديث البيانات</span>
                    <span class="material-symbols-outlined text-[18px]">save</span>
                </button>
                <a href="{{ route('projects.show', $project) }}" class="w-full sm:w-auto bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-bold py-3.5 px-6 rounded-2xl transition-all text-xs text-center">إلغاء</a>
            </div>
        </form>

    </div>
</x-layouts.app>