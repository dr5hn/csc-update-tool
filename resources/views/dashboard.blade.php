<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="gradient-text text-4xl font-bold">
                    Dashboard
                </h2>
                <p class="text-gray-600 mt-3 text-lg">Welcome back, {{ auth()->user()->name }}! Manage your geographical
                    database updates</p>
            </div>
        </div>
    </x-slot>

    <!-- Welcome Section -->
    <x-welcome-section />

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Change Requests -->
            <div class="card fade-in">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="icon-wrapper icon-blue mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_requests'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Requests</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="card fade-in">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="icon-wrapper icon-yellow mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Pending Review</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Requests -->
            <div class="card fade-in">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="icon-wrapper icon-green mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['approved_requests'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Approved</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Requests -->
            <div class="card fade-in">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="icon-wrapper icon-purple mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['user_requests'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Your Requests</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity (Admin Only) -->
        @if (auth()->user()->is_admin && !empty($recentRequests))
            <div class="card fade-in">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="icon-wrapper icon-green">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Recent Activity</h3>
                        </div>
                        <a href="{{ route('change-requests.index') }}"
                            class="text-blue-600 hover:text-blue-800 font-medium">
                            View All →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @foreach ($recentRequests as $request)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($request->title, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('change-requests.show', $request) }}"
                                            class="font-medium text-gray-900 hover:text-blue-600">
                                            {{ $request->title }}
                                        </a>
                                        <div class="text-sm text-gray-500">
                                            {{ $request->created_at->diffForHumans() }} •
                                            {{ ucfirst($request->status) }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('change-requests.show', $request) }}"
                                    class="btn btn-secondary btn-sm">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
                    @endif
        </div>
    </div>
</x-app-layout>
