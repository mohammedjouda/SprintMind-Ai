<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            تفضيلات التنبيهات البريدية
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            اختر التنبيهات التي ترغب في تلقيها عبر البريد الإلكتروني.
        </p>
    </header>

    <form method="post" action="{{ route('profile.notifications.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-4 bg-slate-50/50 p-5 rounded-2xl border border-gray-100">
            <!-- Task Assigned -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="notify_task_assigned_email" name="notify_task_assigned_email" type="checkbox" 
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer" 
                           {{ $user->notify_task_assigned_email ? 'checked' : '' }}>
                </div>
                <div class="mr-3 text-sm">
                    <label for="notify_task_assigned_email" class="font-medium text-gray-700 cursor-pointer">تنبيهات إسناد المهام</label>
                    <p class="text-gray-500 text-xs mt-0.5">إرسال بريد إلكتروني عند إسناد مهمة جديدة إليك.</p>
                </div>
            </div>

            <!-- Task Reminders -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="notify_task_reminder_email" name="notify_task_reminder_email" type="checkbox" 
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer" 
                           {{ $user->notify_task_reminder_email ? 'checked' : '' }}>
                </div>
                <div class="mr-3 text-sm">
                    <label for="notify_task_reminder_email" class="font-medium text-gray-700 cursor-pointer">تنبيهات اقتراب موعد استحقاق المهام</label>
                    <p class="text-gray-500 text-xs mt-0.5">تلقي تذكير بريدي قبل 24 ساعة من تاريخ استحقاق المهمة.</p>
                </div>
            </div>

            <!-- Sprint Reminders -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="notify_sprint_reminder_email" name="notify_sprint_reminder_email" type="checkbox" 
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer" 
                           {{ $user->notify_sprint_reminder_email ? 'checked' : '' }}>
                </div>
                <div class="mr-3 text-sm">
                    <label for="notify_sprint_reminder_email" class="font-medium text-gray-700 cursor-pointer">تنبيهات السبرنتات والتذكيرات المتعلقة بها</label>
                    <p class="text-gray-500 text-xs mt-0.5">تلقي تذكير بريدي عند بدء السبرنت أو اقتراب نهاية مدته.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>حفظ التغييرات</x-primary-button>

            @if (session('status') === 'notification-settings-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >تم الحفظ بنجاح.</p>
            @endif
        </div>
    </form>
</section>
