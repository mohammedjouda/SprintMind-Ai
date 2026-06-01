<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-gray-50 p-6 font-sans">

    <div class="max-w-5xl mx-auto">
        <div
            class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Task Dashboard</h1>
                <p class="text-sm text-gray-500">Manage your daily activities at a glance</p>
            </div>

            <div class="relative inline-block text-left" id="user-dropdown-wrapper">

                <button id="dropdown-btn" type="button"
                    class="flex items-center space-x-3 space-x-reverse bg-gray-50 pr-4 pl-3 py-1.5 rounded-lg border border-gray-100 hover:bg-gray-100/70 transition cursor-pointer focus:outline-none">
                    <div
                        class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm overflow-hidden">
                        <span>MJ</span>
                    </div>

                    <div class="text-right mr-2">
                        <p class="text-sm font-semibold text-gray-800">test</p>
                        <p class="text-[11px] text-gray-400">Developer</p>
                    </div>

                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="dropdown-menu"
                    class="hidden absolute left-0 mt-2 w-56 rounded-xl bg-white shadow-xl border border-gray-100 z-50 transition-all origin-top-left">

                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                        <p class="text-sm font-bold text-gray-800 truncate mt-0.5">test</p>
                    </div>

                    <div class="p-1">

                        <hr class="border-gray-100 my-1">

                        <form action="" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg font-medium transition-colors text-right">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dropdownBtn = document.getElementById('dropdown-btn');
                const dropdownMenu = document.getElementById('dropdown-menu');
                const wrapper = document.getElementById('user-dropdown-wrapper');

                // وظيفة تبديل الحالة عند الضغط على الزر (فتح / إغلاق)
                dropdownBtn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                });

                // إغلاق القائمة تلقائياً إذا ضغط المستخدم في أي مكان خارج الحاوية
                document.addEventListener('click', function(event) {
                    if (!wrapper.contains(event.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            });
        </script>

        <div class="flex justify-end mb-4">
            <a href="{{ route('tasks.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm cursor-pointer">
                + Add New Task
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-700">Your Tasks</h2>
                <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium">Active List</span>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($tasks as $task)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">{{ $task->title }}</p>
                                <span
                                    class="text-[10px] {{ $task->status === 'pending' ? 'text-amber-700 bg-amber-50' : ($task->status === 'in_progress' ? 'text-blue-700 bg-blue-50' : 'text-green-700 bg-green-50') }} font-medium px-2 py-0.5 rounded">
                                    {{ $task->status }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button
                                class="text-gray-400 hover:text-gray-600 p-1.5 rounded-md hover:bg-gray-100 cursor-pointer"><i
                                    class="fas fa-eye"></i></button>
                            <a href="{{ route('tasks.edit', $task->id) }}"
                                class="text-gray-400 hover:text-blue-600 p-1.5 rounded-md hover:bg-blue-50 cursor-pointer"><i
                                    class="fas fa-edit"></i></a>
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this task?')"
                                    class="text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 cursor-pointer"><i
                                        class="fas fa-trash"></i></button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

</body>

</html>
