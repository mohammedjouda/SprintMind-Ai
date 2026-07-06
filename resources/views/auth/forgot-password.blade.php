{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>استعادة كلمة المرور | TaskMaker AI</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    </style>
</head>

<body
    class="bg-background text-on-surface min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-x-hidden selection:bg-primary selection:text-white">

    <div class="absolute top-1/4 right-10 w-96 h-96 bg-primary/10 rounded-full blur-[140px] -z-10 pointer-events-none">
    </div>
    <div
        class="absolute bottom-10 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-[140px] -z-10 pointer-events-none">
    </div>

    <div
        class="max-w-5xl w-full bg-white border border-outline-variant/60 rounded-3xl card-elevation overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">

        <div
            class="lg:col-span-5 bg-gradient-to-br from-slate-950 via-indigo-950 to-primary text-white p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden border-b lg:border-b-0 lg:border-l border-outline-variant/30">

            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute -left-20 bottom-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none">
            </div>
            <span
                class="material-symbols-outlined absolute -left-12 -bottom-12 text-[280px] text-white/5 pointer-events-none">lock_reset</span>

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

            <div class="my-8 lg:my-auto z-10 space-y-4">
                <span
                    class="text-[11px] font-extrabold px-3 py-1 bg-white/10 text-indigo-200 rounded-full inline-block backdrop-blur-md border border-white/10">
                    🔒 حماية حسابك أولويتنا
                </span>
                <h2 class="text-2xl lg:text-3xl font-black leading-tight text-white">
                    نسيت كلمة المرور؟ <br>
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-l from-indigo-200 via-white to-indigo-300">لا
                        تقلق، الأمر بسيط!</span>
                </h2>
                <p class="text-indigo-100/80 text-xs sm:text-sm leading-relaxed font-medium max-w-sm">
                    سنساعدك في استعادة الوصول إلى مساحة عملك وسبرنتاتك الذكية بأمان تام عن طريق بريدك الإلكتروني المسجل.
                </p>
            </div>

            <div
                class="text-[11px] text-indigo-300/60 flex items-center justify-between pt-4 border-t border-white/10 z-10 font-medium">
                <span>© 2026 جميع الحقوق محفوظة</span>
                <span class="flex items-center gap-1"><span
                        class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> اتصال آمن ومُشفر</span>
            </div>
        </div>

        <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col justify-center bg-white relative">

            <div class="max-w-md w-full mx-auto space-y-6">

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-on-surface mb-2">استعادة الوصول للحساب 🔑</h1>
                    <p class="text-on-surface-variant text-xs sm:text-sm leading-relaxed font-medium">
                        أدخل بريدك الإلكتروني المسجل أدناه، وسنقوم فوراً بإرسال رابط آمن ومخصص لإعادة تعيين كلمة المرور
                        الخاصة بك.
                    </p>
                </div>

                @if (session('status'))
                    <div
                        class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-bold flex items-center gap-2.5 shadow-sm animate-fade-in">
                        <span class="material-symbols-outlined text-emerald-600 text-[22px]">mark_email_read</span>
                        <div class="leading-tight">
                            <p class="font-extrabold mb-0.5">تفقد صندوق الوارد!</p>
                            <p class="text-emerald-700/90 font-medium">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                    @csrf <div>
                        <label class="block text-xs font-extrabold text-on-surface mb-1.5">البريد الإلكتروني
                            للحساب</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">mail</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="name@example.com"
                                class="w-full pr-11 pl-4 py-3.5 bg-surface-container/40 border {{ $errors->has('email') ? 'border-error ring-1 ring-error/20' : 'border-outline-variant/80' }} rounded-2xl text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white transition-all font-medium placeholder:text-on-surface-variant/50">
                        </div>
                        @error('email')
                            <p class="text-xs text-error mt-1.5 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-primary to-indigo-600 hover:from-indigo-600 hover:to-primary text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transform active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 group">
                        <span>إرسال رابط استعادة كلمة المرور</span>
                        <span
                            class="material-symbols-outlined text-[18px] group-hover:translate-x-[-2px] transition-transform">send</span>
                    </button>
                </form>

                <div class="pt-5 text-center border-t border-outline-variant/30">
                    <p class="text-xs text-on-surface-variant font-medium">
                        تذكرت كلمة المرور؟
                        <a href="{{ route('login') }}"
                            class="text-primary font-extrabold hover:underline ml-1 inline-flex items-center gap-0.5 group">
                            <span>العودة لتسجيل الدخول</span>
                            <span
                                class="material-symbols-outlined text-[14px] group-hover:translate-x-0.5 transition-transform">arrow_back_ios</span>
                        </a>
                    </p>
                </div>

            </div>

        </div>

    </div>

</body>

</html>
