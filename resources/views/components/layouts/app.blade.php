@props(['title' => 'SprintMind AI - مساحة العمل الذكية'])

<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $title }}</title>

    <!-- استدعاء Tailwind CSS و Alpine.js -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- الخطوط والأيقونات -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Geist:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- إعدادات Tailwind CSS المخصصة لمشروعنا -->
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
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .material-symbols-outlined.filled {
            font-variation-settings: 'FILL' 1;
        }

        .card-elevation {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .ai-glow {
            box-shadow: 0 0 25px rgba(79, 70, 229, 0.15);
        }
    </style>
</head>

<body class="bg-background text-on-surface min-h-screen flex selection:bg-primary selection:text-white"
    x-data="{ sidebarOpen: false }">

    <!-- القائمة الجانبية الثابتة -->
    <x-dashboard.sidebar />

    <!-- مساحة العمل الرئيسية -->
    <main class="lg:mr-64 flex-1 flex flex-col min-h-screen">

        <x-dashboard.header />
        <!-- المحتوى الديناميكي للصفحة (هنا سيتم حقن كود صفحاتك) -->
        <div class="p-4 lg:p-8 max-w-7xl mx-auto w-full space-y-6 flex-1">
            {{ $slot }}
        </div>

        <!-- التذييل الثابت -->
        <x-dashboard.footer />

    </main>
</body>

</html>