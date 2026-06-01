<x-app-layout>

    <div class="max-w-5xl mx-auto mt-10">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div>
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-700">Create New Task</h2>

                </div>

                <div class="p-5">
                    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" required placeholder="Task title"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" placeholder="Task description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select status</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm cursor-pointer">
                                Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
</x-app-layout>
