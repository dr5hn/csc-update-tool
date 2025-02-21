<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Change Requests') }}
            </h2>

            <a href="{{ route('change-requests.new') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md inline-flex items-center">
                <span class="mr-2">+</span>
                Create New Change Request
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('change-requests.index') }}" class="mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-4 md:space-y-0">
                            <!-- Status Filter -->
                            <div class="w-full md:w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select name="status" id="status" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        
                                    <option value="all" @selected($filters['status'] === 'all')>All Status</option>
                                    <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                                    <option value="approved" @selected($filters['status'] === 'approved')>Approved</option>
                                    <option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option>
                                    <option value="draft" @selected($filters['status'] === 'draft')>Draft</option>
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="search" 
                                           id="search"
                                           value="{{ $filters['search'] }}"
                                           placeholder="Search requests..." 
                                           class="w-full pl-10 pr-4 py-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Per Page Select -->
                            <div class="w-full md:w-48">
                                <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Show</label>
                                <select name="per_page" id="per_page" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        onchange="this.form.submit()">
                                    <option value="10" @selected($filters['per_page'] === 10)>10 per page</option>
                                    <option value="25" @selected($filters['per_page'] === 25)>25 per page</option>
                                    <option value="50" @selected($filters['per_page'] === 50)>50 per page</option>
                                </select>
                            </div>

                            <!-- Apply Filters Button -->
                            <div class="md:self-end">
                                <button type="submit" 
                                        class="w-full md:w-auto px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($changeRequests->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No change requests found.</p>
                        </div>
                    @else
                        <!-- Results Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tables
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Submitted By
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Notifications
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($changeRequests as $request)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $request->title }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ implode(', ', array_map('ucfirst', $request->affected_tables)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($request->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                                    ($request->status === 'draft' ? 'bg-gray-100 text-gray-800' : 
                                                    'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($request->comments_count > 0)
                                                <div class="flex items-center space-x-1">
                                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 h-5 w-5 rounded-full text-xs">
                                                        {{ $request->comments_count }}
                                                    </span>
                                                    <span class="text-gray-500 dark:text-gray-400">
                                                        {{ Str::plural('comment', $request->comments_count) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('change-requests.show', $request->id) }}"
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                View
                                            </a>

                                            @if($request->status === 'draft' && $request->user_id === auth()->id())
                                                <a href="{{ route('change-requests.edit', $request->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Edit
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ $changeRequests->firstItem() }} to {{ $changeRequests->lastItem() }} 
                                of {{ $changeRequests->total() }} results
                            </div>
                            {{ $changeRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>