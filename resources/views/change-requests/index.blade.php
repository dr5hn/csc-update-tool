<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="gradient-text text-3xl font-bold">
                    Change Requests
                </h2>
                <p class="text-gray-600 mt-2">Manage and track geographical database updates</p>
            </div>

            <a href="{{ route('change-requests.new') }}" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Request
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-6">
        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('change-requests.index') }}"
            class="mb-8 bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">Filter by
                        Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all" @selected($filters['status'] === 'all')>All Status</option>
                        <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                        <option value="approved" @selected($filters['status'] === 'approved')>Approved</option>
                        <option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option>
                        <option value="draft" @selected($filters['status'] === 'draft')>Draft</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-3">Search Requests</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ $filters['search'] }}"
                            placeholder="Search by title, description, or user..." class="form-input pl-12">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Per Page & Apply Button -->
                <div class="flex flex-col space-y-3">
                    <label for="per_page" class="block text-sm font-semibold text-gray-700">Show</label>
                    <div class="flex space-x-3">
                        <select name="per_page" id="per_page" class="form-select flex-1" onchange="this.form.submit()">
                            <option value="10" @selected($filters['per_page'] == 10)>10</option>
                            <option value="25" @selected($filters['per_page'] == 25)>25</option>
                            <option value="50" @selected($filters['per_page'] == 50)>50</option>
                        </select>
                        <button type="submit" class="btn btn-primary px-6">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @if ($changeRequests->isEmpty())
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="icon-wrapper icon-blue mx-auto mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">No Change Requests Found</h3>
                    <p class="text-gray-600 mb-6">No requests match your current filters. Try adjusting your search
                        criteria or create a new request.</p>
                    <a href="{{ route('change-requests.new') }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Request
                    </a>
                </div>
            </div>
        @else
            <!-- Results Table -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/4">
                                    Title
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">
                                    Tables
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-24">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">
                                    Incorporation
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-48">
                                    Submitted By
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">
                                    Date
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-24">
                                    Comments
                                </th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($changeRequests as $request)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 mb-1">
                                        <a href="{{ route('change-requests.show', $request->id) }}"
                                            class="text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $request->title }}
                                        </a>
                                    </div>
                                    @if ($request->description)
                                        <div class="text-sm text-gray-600 line-clamp-2">
                                            {{ Str::limit($request->description, 80) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach (array_slice($request->affected_tables, 0, 2) as $table)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                {{ ucfirst($table) }}
                                            </span>
                                        @endforeach
                                        @if (count($request->affected_tables) > 2)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                                +{{ count($request->affected_tables) - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'status-pending',
                                            'approved' => 'status-approved',
                                            'rejected' => 'status-rejected',
                                            'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        ];
                                        $statusClass = $statusClasses[$request->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($request->status === 'approved')
                                        @php
                                            $incStatusClasses = [
                                                'pending' => 'status-pending',
                                                'incorporated' => 'status-incorporated',
                                                'verified' => 'status-verified',
                                                'missing' => 'status-missing',
                                                'conflicted' => 'bg-red-100 text-red-800 border-red-200',
                                            ];
                                            $incStatusClass = $incStatusClasses[$request->incorporation_status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                        @endphp
                                        <span class="status-badge {{ $incStatusClass }}">
                                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            {{ ucfirst(str_replace('_', ' ', $request->incorporation_status)) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3 flex-shrink-0">
                                            {{ substr($request->user->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">
                                                {{ $request->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">
                                                {{ $request->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $request->created_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $request->created_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($request->comments_count > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            {{ $request->comments_count }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">No comments</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('change-requests.show', $request->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>

                                        @if ($request->status === 'draft' && $request->user_id === auth()->id())
                                            <a href="{{ route('change-requests.edit', $request->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-600 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 hover:text-yellow-700 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                <div class="text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg">
                    Showing <span class="font-semibold">{{ $changeRequests->firstItem() }}</span> to <span
                        class="font-semibold">{{ $changeRequests->lastItem() }}</span>
                    of <span class="font-semibold">{{ $changeRequests->total() }}</span> results
                </div>
                <div class="pagination-wrapper">
                    {{ $changeRequests->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
