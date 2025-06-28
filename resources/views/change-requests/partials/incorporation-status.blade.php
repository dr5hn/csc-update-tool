<div class="card card-hover fade-in mb-8">
    <div class="p-8 bg-gradient-to-br from-white via-blue-50/30 to-indigo-50/30">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Incorporation Status</h3>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Last updated: {{ now()->format('M j, Y g:i A') }}</span>
            </div>
        </div>

        @if($incorporationSummary['total_approved'] > 0)
            <!-- Status Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                @foreach($incorporationSummary['by_incorporation_status'] as $status => $count)
                    @php
                        $statusConfig = match($status) {
                            'pending' => ['bg' => 'bg-gradient-to-br from-yellow-50 to-amber-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'icon' => '⏳', 'ring' => 'ring-yellow-100'],
                            'incorporated' => ['bg' => 'bg-gradient-to-br from-blue-50 to-indigo-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => '🔄', 'ring' => 'ring-blue-100'],
                            'verified' => ['bg' => 'bg-gradient-to-br from-green-50 to-emerald-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => '✅', 'ring' => 'ring-green-100'],
                            'missing' => ['bg' => 'bg-gradient-to-br from-orange-50 to-red-50', 'border' => 'border-orange-200', 'text' => 'text-orange-800', 'icon' => '⚠️', 'ring' => 'ring-orange-100'],
                            'conflicted' => ['bg' => 'bg-gradient-to-br from-red-50 to-pink-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => '❌', 'ring' => 'ring-red-100'],
                            default => ['bg' => 'bg-gradient-to-br from-gray-50 to-slate-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'icon' => '❓', 'ring' => 'ring-gray-100']
                        };
                    @endphp
                    <div class="relative {{ $statusConfig['bg'] }} {{ $statusConfig['border'] }} border-2 rounded-xl p-5 hover:shadow-lg transition-all duration-200 hover:scale-105 group">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="text-2xl">{{ $statusConfig['icon'] }}</span>
                                    <div class="text-3xl font-bold {{ $statusConfig['text'] }}">{{ $count }}</div>
                                </div>
                                <div class="text-sm font-medium {{ $statusConfig['text'] }} capitalize tracking-wide">
                                    {{ str_replace('_', ' ', $status) }}
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 {{ $statusConfig['ring'] }} ring-4 opacity-0 group-hover:opacity-100 rounded-xl transition-opacity duration-200"></div>
                    </div>
                @endforeach
            </div>

            <!-- Pending Incorporation Section -->
            @if(!empty($incorporationSummary['pending_incorporation']))
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="text-xl">⏳</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Pending Incorporation</h4>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                            {{ count($incorporationSummary['pending_incorporation']) }}
                        </span>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400 rounded-lg p-6 shadow-sm">
                        <div class="space-y-3 mb-6">
                            @foreach(array_slice($incorporationSummary['pending_incorporation'], 0, 5) as $request)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">#{{ $request['id'] }}</span>
                                        </div>
                                        <a href="{{ route('change-requests.show', $request['id']) }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline transition-colors duration-200">
                                            {{ $request['title'] }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($request['created_at'])->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                            @if(count($incorporationSummary['pending_incorporation']) > 5)
                                <div class="text-center py-2">
                                    <span class="text-sm text-gray-600 bg-white px-3 py-1 rounded-full shadow-sm">
                                        ... and {{ count($incorporationSummary['pending_incorporation']) - 5 }} more
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="pt-4 border-t border-yellow-200">
                            <div class="flex items-center space-x-2 text-sm text-gray-700">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><strong>Next steps:</strong> Mark these changes as incorporated individually after applying them to your main database.</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Verification Needed Section -->
            @if(!empty($incorporationSummary['verification_needed']))
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <span class="text-xl">🔍</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Need Verification</h4>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            {{ count($incorporationSummary['verification_needed']) }}
                        </span>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 rounded-lg p-6 shadow-sm">
                        <div class="space-y-3 mb-6">
                            @foreach(array_slice($incorporationSummary['verification_needed'], 0, 5) as $request)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-green-600 font-semibold text-sm">#{{ $request['id'] }}</span>
                                        </div>
                                        <a href="{{ route('change-requests.show', $request['id']) }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline transition-colors duration-200">
                                            {{ $request['title'] }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Incorporated: {{ \Carbon\Carbon::parse($request['incorporated_at'])->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                            @if(count($incorporationSummary['verification_needed']) > 5)
                                <div class="text-center py-2">
                                    <span class="text-sm text-gray-600 bg-white px-3 py-1 rounded-full shadow-sm">
                                        ... and {{ count($incorporationSummary['verification_needed']) - 5 }} more
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-blue-200">
                            <div class="flex items-center space-x-2 text-sm text-gray-700">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><strong>Next steps:</strong> Verify these changes are present in the current database.</span>
                            </div>
                            <button onclick="verifyAllIncorporated()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                </svg>
                                Verify All Changes
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Incorporations Section -->
            @if(!empty($incorporationSummary['recent_incorporations']))
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <span class="text-xl">📅</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Recent Incorporations</h4>
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            Last 7 days
                        </span>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-lg p-6 shadow-sm">
                        <div class="space-y-3">
                            @foreach($incorporationSummary['recent_incorporations'] as $request)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <a href="{{ route('change-requests.show', $request['id']) }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline transition-colors duration-200">
                                            #{{ $request['id'] }}: {{ $request['title'] }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-600 flex items-center space-x-2">
                                        <span>{{ \Carbon\Carbon::parse($request['incorporated_at'])->format('M j, Y') }}</span>
                                        <span class="text-gray-400">by</span>
                                        <span class="font-medium">{{ $request['incorporated_by'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">Quick Actions</h4>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if(!empty($incorporationSummary['verification_needed']))
                    <button onclick="verifyAllIncorporated()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:ring-4 focus:ring-green-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        Verify All Changes
                    </button>
                    @endif
                    <a href="{{ route('change-requests.index', ['status' => 'approved', 'incorporation_status' => 'missing']) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white font-medium rounded-lg hover:from-yellow-700 hover:to-orange-700 focus:ring-4 focus:ring-yellow-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        View Missing Changes
                    </a>
                    <button onclick="refreshIncorporationStatus()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-lg hover:from-gray-700 hover:to-gray-800 focus:ring-4 focus:ring-gray-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Status
                    </button>
                </div>
            </div>

        @else
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">No Approved Changes</h3>
                    <p class="text-gray-500 mb-6">There are currently no approved change requests to track.</p>
                    <a href="{{ route('change-requests.new') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Change Request
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Progress Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="progressTitle">Processing...</h3>
                <button onclick="hideProgressModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="progressContent" class="text-sm text-gray-700">
                <div class="flex items-center mb-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                    <span id="progressMessage">Processing request...</span>
                </div>
                <div id="progressResults" class="hidden">
                    <!-- Results will be shown here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bulk mark as incorporated
function markAllPendingAsIncorporated() {
    if (!confirm('Are you sure you want to mark ALL pending approved changes as incorporated?\n\nOnly do this if you have actually incorporated all these changes into your main database.')) {
        return;
    }

    const incorporatedBy = prompt('Enter your name for the incorporation record:');
    if (!incorporatedBy) {
        return;
    }

    showProgressModal('📝 Marking Changes as Incorporated', 'Marking all pending changes as incorporated...');

    fetch('/change-requests/bulk-mark-incorporated', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            incorporated_by: incorporatedBy
        })
    })
    .then(response => response.json())
    .then(data => {
        hideProgressModal();
        if (data.success) {
            showNotification(`✅ Successfully marked ${data.count} change requests as incorporated`, 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(`❌ Error: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        hideProgressModal();
        showNotification('❌ An error occurred while marking changes as incorporated', 'error');
        console.error('Error:', error);
    });
}

// Bulk verify changes
function verifyAllIncorporated() {
    if (!confirm('Are you sure you want to verify ALL incorporated changes?\n\nThis will check if the changes are present in your current database.')) {
        return;
    }

    showProgressModal('🔍 Verifying Changes', 'Verifying all incorporated changes...');

    fetch('/change-requests/bulk-verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideProgressModal();
        if (data.success) {
            const results = data.results;
            let message = `✅ Verification completed!\n\n`;
            message += `📊 Total checked: ${results.total_checked}\n`;
            message += `✅ Verified: ${results.verified.length}\n`;
            message += `⚠️ Missing: ${results.missing.length}\n`;
            message += `❌ Conflicted: ${results.conflicted.length}`;

            showNotification(message, 'success');
            setTimeout(() => window.location.reload(), 3000);
        } else {
            showNotification(`❌ Error: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        hideProgressModal();
        showNotification('❌ An error occurred during verification', 'error');
        console.error('Error:', error);
    });
}

// Refresh incorporation status
function refreshIncorporationStatus() {
    showProgressModal('🔄 Refreshing Status', 'Refreshing incorporation status...');

    setTimeout(() => {
        hideProgressModal();
        window.location.reload();
    }, 1000);
}

// Modal functions
function showProgressModal(title, message) {
    const modal = document.getElementById('progressModal');
    const titleEl = document.getElementById('progressTitle');
    const messageEl = document.getElementById('progressMessage');

    titleEl.textContent = title;
    messageEl.textContent = message;
    modal.classList.remove('hidden');
}

function hideProgressModal() {
    document.getElementById('progressModal').classList.add('hidden');
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 max-w-md ${
        type === 'success' ? 'bg-green-100 text-green-700 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-700 border border-red-200' :
        'bg-blue-100 text-blue-700 border border-blue-200'
    }`;
    notification.style.whiteSpace = 'pre-line';
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 8000);
}

// Close modal when clicking outside
document.getElementById('progressModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideProgressModal();
    }
});
</script>
