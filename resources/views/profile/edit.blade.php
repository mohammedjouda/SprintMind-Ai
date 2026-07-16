<x-layouts.app title="SprintMind Ai - الملف الشخصي">

    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation">
            <h2 class="text-2xl font-black text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[28px]">manage_accounts</span>
                إعدادات الحساب الشخصي
            </h2>
            <p class="text-on-surface-variant text-xs mt-1">تحديث بياناتك الشخصية، إدارة كلمات المرور وتفضيلات الإشعارات.</p>
        </div>

        <!-- Update Profile Information -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-outline-variant/60 card-elevation">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-outline-variant/60 card-elevation">
            <div class="max-w-xl">
                @include('profile.partials.update-notification-settings-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-outline-variant/60 card-elevation">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete User -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-outline-variant/60 card-elevation">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

</x-layouts.app>
