<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6 font-sans">

    <div class="bg-white w-full max-w-md rounded-xl border border-gray-100 shadow-md p-6">
        <!-- Top Navigation Bar Inside Card -->
        <div class="flex justify-between items-center mb-5">
            <span class="text-[11px] text-amber-700 font-semibold bg-amber-50 px-2.5 py-1 rounded-full">
                ⏳ In Progress
            </span>
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium cursor-pointer">
                ← Back to Dashboard
            </button>
        </div>

        <!-- Task Info -->
        <div class="border-b border-gray-100 pb-5 mb-5">
            <h1 class="text-lg font-bold text-gray-900 mb-2">Implement authentication middleware</h1>
            <p class="text-sm text-gray-600 leading-relaxed">
                Secure all backend API routes using custom token verification checks. Ensure unauthorized requests
                return proper 401 response heads.
            </p>
        </div>

        <!-- Metadata Info -->
        <div class="grid grid-cols-2 gap-4 text-xs mb-6">
            <div>
                <p class="font-semibold text-gray-400 uppercase tracking-wider mb-1">Created At</p>
                <p class="text-gray-700 font-medium">May 25, 2026</p>
            </div>
            <div>
                <p class="font-semibold text-gray-400 uppercase tracking-wider mb-1">Category</p>
                <p class="text-gray-700 font-medium">Backend Dev</p>
            </div>
        </div>

        <!-- Footer Control Buttons -->
        <div class="flex justify-between items-center pt-2">
            <button
                class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-2 rounded-lg font-medium hover:bg-emerald-100 transition cursor-pointer">
                Mark Complete
            </button>
            <button
                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-medium transition cursor-pointer">
                Edit Task
            </button>
        </div>
    </div>

</body>

</html>
