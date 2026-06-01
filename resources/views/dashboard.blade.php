<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mt-8">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-700">Your Tasks</h2>
                <a href="{{ route('tasks.create') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm cursor-pointer">
                    <i class="fas fa-plus"></i> Create Task</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th
                                class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-12 text-center">
                                Status</th>
                            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Task
                                Title</th>
                            <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Progress State</th>
                            <th
                                class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center w-32">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tasks as $task)
                            <tr class="hover:bg-gray-50/80 transition-colors">

                                <td class="p-4 text-center align-middle">
                                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" onChange="this.form.submit()"
                                            {{ $task->status === 'completed' ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer transition">
                                    </form>
                                </td>

                                <td class="p-4 text-right align-middle">
                                    <p
                                        class="text-sm font-semibold text-gray-800 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">
                                        {{ $task->title }}
                                    </p>
                                </td>

                                <td class="p-4 text-center align-middle">
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full inline-block uppercase tracking-wide
                                    {{ $task->status === 'pending' ? 'text-amber-700 bg-amber-50 border border-amber-100' : '' }}
                                    {{ $task->status === 'in_progress' ? 'text-blue-700 bg-blue-50 border border-blue-100' : '' }}
                                    {{ $task->status === 'completed' ? 'text-green-700 bg-green-50 border border-green-100' : '' }}">
                                        {{ str_replace('_', ' ', $task->status) }}
                                    </span>
                                </td>

                                <td class="p-4 text-center align-middle">
                                    <div class="flex items-center justify-center space-x-1.5 space-x-reverse">
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                            class="text-gray-400 hover:text-gray-600 p-1.5 rounded-md hover:bg-gray-100 transition cursor-pointer"
                                            title="View Details">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        <a href="{{ route('tasks.edit', $task->id) }}"
                                            class="text-gray-400 hover:text-blue-600 p-1.5 rounded-md hover:bg-blue-50 transition cursor-pointer"
                                            title="Edit Task">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>

                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this task?')"
                                                class="text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition cursor-pointer"
                                                title="Delete Task">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="p-12 text-center text-sm text-gray-400 font-medium bg-gray-50/30">
                                    <span class="text-2xl block mb-2">☕</span>
                                    No tasks found. Create your first task to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tasks->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
