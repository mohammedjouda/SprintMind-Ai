{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}


<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>تسجيل الدخول | TaskMaker AI</title>
    <!-- استدعاء Tailwind CSS و Alpine.js -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- الخطوط العربية والأيقونات -->
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Geist:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4f46e5",
                        "primary-container": "#e0e7ff",
                        "on-primary-container": "#312e81",
                        "secondary": "#0d9488",
                        "secondary-container": "#ccfbf1",
                        "on-secondary-container": "#115e59",
                        "surface": "#f8fafc",
                        "surface-container": "#f1f5f9",
                        "surface-container-high": "#e2e8f0",
                        "surface-container-highest": "#cbd5e1",
                        "on-surface": "#0f172a",
                        "on-surface-variant": "#475569",
                        "outline-variant": "#cbd5e1",
                        "error": "#e11d48",
                        "error-container": "#ffe4e6",
                        "on-error-container": "#881337",
                        "background": "#f8fafc"
                    },
                    fontFamily: {
                        "tajawal": ["Tajawal", "sans-serif"],
                        "geist": ["Geist", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }

        .material-symbols-outlined.filled {
            font-variation-settings: 'FILL' 1;
        }

        .card-elevation {
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07), 0 10px 20px -10px rgba(0, 0, 0, 0.04);
        }

        .ai-glow {
            box-shadow: 0 0 40px rgba(79, 70, 229, 0.22);
        }
    </style>
</head>

<body
    class="bg-background text-on-surface min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-x-hidden selection:bg-primary selection:text-white"
    x-data="{ showPassword: false }">

    <!-- تأثيرات الإضاءة في خلفية الصفحة (Ambient Gradients) -->
    <div class="absolute top-1/4 right-10 w-96 h-96 bg-primary/10 rounded-full blur-[140px] -z-10 pointer-events-none">
    </div>
    <div
        class="absolute bottom-10 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-[140px] -z-10 pointer-events-none">
    </div>

    <!-- الحاوية الرئيسية (Bento Split Layout) -->
    <div
        class="max-w-5xl w-full bg-white border border-outline-variant/60 rounded-3xl card-elevation overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[620px]">

        <!-- ================== القسم الأيمن (5 أعمدة): الهوية والبصمة الذكية (Brand Banner) ================== -->
        <div
            class="lg:col-span-5 bg-gradient-to-br from-slate-950 via-indigo-950 to-primary text-white p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden border-b lg:border-b-0 lg:border-l border-outline-variant/30">

            <!-- أشكال وتأثيرات خلفية -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute -left-20 bottom-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none">
            </div>
            <span
                class="material-symbols-outlined absolute -left-12 -bottom-12 text-[280px] text-white/5 pointer-events-none">auto_awesome</span>

            <!-- الشعار -->
            <div class="flex items-center gap-3 z-10">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary to-indigo-500 flex items-center justify-center shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[24px] text-white">bolt</span>
                    </div>
                    <div>
                        <span
                            class="text-2xl font-black tracking-tight leading-none text-white block font-geist">TaskMaker</span>
                        <span class="text-[10px] font-bold text-indigo-300 tracking-wider">AI CO-PILOT WORKSPACE</span>
                    </div>
                </a>
            </div>

            <!-- الرسالة الترحيبية وبطاقة الـ AI (Middle Content) -->
            <div class="my-8 lg:my-auto z-10 space-y-6">
                <div class="space-y-2">
                    <span
                        class="text-[11px] font-extrabold px-3 py-1 bg-white/10 text-indigo-200 rounded-full inline-block backdrop-blur-md border border-white/10">
                        ✨ عودة سريعة للإنتاجية
                    </span>
                    <h2 class="text-2xl lg:text-3xl font-black leading-tight text-white">
                        مرحباً بك مجدداً في <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-l from-indigo-200 via-white to-indigo-300">مساحة
                            عملك الذكية</span>
                    </h2>
                    <p class="text-indigo-100/80 text-xs sm:text-sm leading-relaxed font-medium max-w-sm">
                        سبرنتاتك، مهامك، ومساعدك الذكي بانتظارك. سجل دخولك لمتابعة إنجاز مشاريعك بكفاءة عالية.
                    </p>
                </div>

                <!-- بطاقة مصغرة تحاكي نشاط الحساب (Teaser Card) -->
                <div
                    class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4 space-y-3 shadow-inner max-w-sm">
                    <div class="flex items-center justify-between text-xs text-indigo-200">
                        <span class="font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>السبرنت النشط الآن:</span>
                        </span>
                        <span class="font-geist font-bold text-white bg-white/10 px-2 py-0.5 rounded">Sprint #3</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[11px] font-bold text-white">
                            <span>نظام الدفع والاشتراكات (Stripe)</span>
                            <span class="font-geist text-emerald-300">85%</span>
                        </div>
                        <div
                            class="w-full bg-slate-900/60 h-2 rounded-full overflow-hidden p-0.5 border border-white/10">
                            <div class="bg-gradient-to-r from-emerald-400 to-indigo-300 h-full w-[85%] rounded-full">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-indigo-200/80 pt-1 border-t border-white/10">
                        <span class="material-symbols-outlined text-[14px] text-indigo-300">smart_toy</span>
                        <span>الـ AI جاهز لتفكيك مهامك القادمة فور دخولك.</span>
                    </div>
                </div>
            </div>

            <!-- الفوتر الصغير للقسم الأيمن -->
            <div
                class="text-[11px] text-indigo-300/60 flex items-center justify-between pt-4 border-t border-white/10 z-10 font-medium">
                <span>© 2026 جميع الحقوق محفوظة</span>
                <span class="flex items-center gap-1"><span
                        class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> أنظمة الـ AI متصلة</span>
            </div>
        </div>

        <!-- ================== القسم الأيسر (7 أعمدة): نموذج تسجيل الدخول (Login Form) ================== -->
        <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col justify-center bg-white relative">

            <div class="max-w-md w-full mx-auto space-y-6">

                <!-- الترحيب ورأس النموذج -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-on-surface mb-1.5">تسجيل الدخول لحسابك 👋</h1>
                    <p class="text-on-surface-variant text-xs sm:text-sm font-medium">أدخل البريد الإلكتروني وكلمة
                        المرور للمتابعة إلى لوحة التحكم</p>
                </div>

                <!-- رسالة الحالة (مثل: تم إرسال رابط استعادة كلمة المرور بنجاح) -->
                @if (session('status'))
                    <div
                        class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-bold flex items-center gap-2 animate-fade-in">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- نموذج لارافل الرئيسي -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf <!-- حماية CSRF الأساسية في لارافل -->

                    <!-- حقل البريد الإلكتروني -->
                    <div>
                        <label class="block text-xs font-extrabold text-on-surface mb-1.5">البريد الإلكتروني</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">mail</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                autocomplete="username" placeholder="name@example.com"
                                class="w-full pr-11 pl-4 py-3.5 bg-surface-container/40 border {{ $errors->has('email') ? 'border-error ring-1 ring-error/20' : 'border-outline-variant/80' }} rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium placeholder:text-on-surface-variant/50">
                        </div>
                        @error('email')
                            <p class="text-xs text-error mt-1.5 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- حقل كلمة المرور (مع ميزة الإظهار/الإخفاء التفاعلية) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-extrabold text-on-surface">كلمة المرور</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-xs text-primary hover:underline font-bold transition-colors">نسيت كلمة
                                    المرور؟</a>
                            @endif
                        </div>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">lock</span>
                            <!-- ربط نوع الحقل بمتغير Alpine.js لإظهاره وإخفائه -->
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                autocomplete="current-password" placeholder="••••••••••••"
                                class="w-full pr-11 pl-11 py-3.5 bg-surface-container/40 border {{ $errors->has('password') ? 'border-error ring-1 ring-error/20' : 'border-outline-variant/80' }} rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium placeholder:text-on-surface-variant/50 font-geist">

                            <!-- زر التبديل بين إظهار وإخفاء كلمة المرور -->
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface focus:outline-none p-1 transition-colors">
                                <span class="material-symbols-outlined text-[20px]"
                                    x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-error mt-1.5 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- تذكرني (Remember Me) -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer group select-none">
                            <input type="checkbox" id="remember_me" name="remember"
                                class="w-4.5 h-4.5 rounded text-primary focus:ring-primary border-outline-variant cursor-pointer transition-colors">
                            <span
                                class="text-xs text-on-surface-variant group-hover:text-on-surface font-bold transition-colors">تذكر
                                تسجيل دخولي في هذا الجهاز</span>
                        </label>
                    </div>

                    <!-- زر الدخول الرئيسي -->
                    <button type="submit"
                        class="w-full mt-2 bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transform active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 group">
                        <span>دخول إلى مساحة العمل</span>
                        <span
                            class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back_ios</span>
                    </button>
                </form>

                <!-- خط فاصل أنيق (Social Login Or) -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-outline-variant/40"></div>
                    </div>
                    <div class="relative flex justify-center text-[11px] uppercase"><span
                            class="bg-white px-3 text-on-surface-variant font-bold">أو المتابعة باستخدام</span></div>
                </div>

                <!-- أزرار الدخول السريع (Socialite Ready) -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button"
                        class="flex items-center justify-center gap-2 py-2.5 px-4 border border-outline-variant/80 rounded-2xl hover:bg-surface-container/50 transition-colors font-bold text-xs text-on-surface group">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#EA4335"
                                d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.4 1 3.5 3.6 1.6 7.4l3.7 2.8C6.2 7.3 8.9 5 12 5z" />
                            <path fill="#4285F4"
                                d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z" />
                            <path fill="#FBBC05"
                                d="M5.3 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.5.4-2.3L1.6 7.4C.6 9.4 0 11.6 0 14s.6 4.6 1.6 6.6l3.7-2.8z" />
                            <path fill="#34A853"
                                d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3.1 0-5.8-2.3-6.7-5.2L1.6 16C3.5 19.8 7.4 23 12 23z" />
                        </svg>
                        <span>Google</span>
                    </button>

                    <button type="button"
                        class="flex items-center justify-center gap-2 py-2.5 px-4 border border-outline-variant/80 rounded-2xl hover:bg-surface-container/50 transition-colors font-bold text-xs text-on-surface group">
                        <svg class="w-4 h-4 fill-current text-slate-800" viewBox="0 0 24 24">
                            <path
                                d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                        </svg>
                        <span>Github</span>
                    </button>
                </div>

                <!-- رابط الانتقال لإنشاء حساب جديد -->
                <div class="pt-4 text-center border-t border-outline-variant/30">
                    <p class="text-xs text-on-surface-variant font-medium">
                        ليس لديك حساب حتى الآن؟
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="text-primary font-extrabold hover:underline ml-1 inline-flex items-center gap-0.5">
                                <span>أنشئ حساباً مجانياً</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                            </a>
                        @else
                            <a href="{{ url('/register') }}"
                                class="text-primary font-extrabold hover:underline ml-1 inline-flex items-center gap-0.5">
                                <span>أنشئ حساباً مجانياً</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                            </a>
                        @endif
                    </p>
                </div>

            </div>

        </div>

    </div>

</body>

</html>
