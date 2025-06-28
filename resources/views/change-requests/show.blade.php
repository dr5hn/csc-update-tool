<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="gradient-text text-3xl font-bold">
                    {{ $changeRequest->title }}
                </h2>
                <p class="text-gray-600 mt-2 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Change Request #{{ $changeRequest->id }}
                </p>
            </div>
            <a href="{{ route('change-requests.index') }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-6">
        <!-- Status and Creator Info -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3">
                        @php
                            $statusConfig = match ($changeRequest->status) {
                                'pending' => [
                                    'bg' => 'bg-gradient-to-r from-yellow-100 to-amber-100',
                                    'text' => 'text-yellow-800',
                                    'icon' => '⏳',
                                ],
                                'approved' => [
                                    'bg' => 'bg-gradient-to-r from-green-100 to-emerald-100',
                                    'text' => 'text-green-800',
                                    'icon' => '✅',
                                ],
                                'rejected' => [
                                    'bg' => 'bg-gradient-to-r from-red-100 to-pink-100',
                                    'text' => 'text-red-800',
                                    'icon' => '❌',
                                ],
                                default => [
                                    'bg' => 'bg-gradient-to-r from-gray-100 to-slate-100',
                                    'text' => 'text-gray-800',
                                    'icon' => '❓',
                                ],
                            };
                        @endphp
                        <div
                            class="w-10 h-10 {{ $statusConfig['bg'] }} rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-lg">{{ $statusConfig['icon'] }}</span>
                        </div>
                        <span
                            class="px-4 py-2 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} text-sm font-semibold rounded-full shadow-sm border">
                            {{ ucfirst($changeRequest->status) }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Created by:</span>
                        <span
                            class="text-blue-600 dark:text-blue-400 font-semibold">{{ $changeRequest->user->email }}</span>
                    </div>
                </div>

                @if (auth()->user()->is_admin && $changeRequest->status === 'pending')
                    <div class="flex space-x-3">
                        <button type="button" id="approve-request-btn" data-request-id="{{ $changeRequest->id }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:ring-4 focus:ring-green-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                            Approve
                        </button>
                        <button type="button" id="reject-request-btn" data-request-id="{{ $changeRequest->id }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-lg hover:from-red-700 hover:to-red-800 focus:ring-4 focus:ring-red-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                            Reject
                        </button>
                    </div>
                @endif

                @if (auth()->user()->is_admin && $changeRequest->status === 'approved')
                    <div class="flex items-center space-x-4">
                        <!-- Incorporation Status Badge -->
                        @php
                            $incStatusConfig = match ($changeRequest->incorporation_status) {
                                'pending' => [
                                    'bg' => 'bg-gradient-to-r from-yellow-100 to-amber-100',
                                    'text' => 'text-yellow-800',
                                    'icon' => '⏳',
                                ],
                                'incorporated' => [
                                    'bg' => 'bg-gradient-to-r from-blue-100 to-indigo-100',
                                    'text' => 'text-blue-800',
                                    'icon' => '🔄',
                                ],
                                'verified' => [
                                    'bg' => 'bg-gradient-to-r from-green-100 to-emerald-100',
                                    'text' => 'text-green-800',
                                    'icon' => '✅',
                                ],
                                'missing' => [
                                    'bg' => 'bg-gradient-to-r from-orange-100 to-red-100',
                                    'text' => 'text-orange-800',
                                    'icon' => '⚠️',
                                ],
                                'conflicted' => [
                                    'bg' => 'bg-gradient-to-r from-red-100 to-pink-100',
                                    'text' => 'text-red-800',
                                    'icon' => '❌',
                                ],
                                default => [
                                    'bg' => 'bg-gradient-to-r from-gray-100 to-slate-100',
                                    'text' => 'text-gray-800',
                                    'icon' => '❓',
                                ],
                            };
                        @endphp
                        <div class="flex items-center space-x-2">
                            <div
                                class="w-8 h-8 {{ $incStatusConfig['bg'] }} rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-sm">{{ $incStatusConfig['icon'] }}</span>
                            </div>
                            <span
                                class="px-3 py-1 {{ $incStatusConfig['bg'] }} {{ $incStatusConfig['text'] }} text-sm font-semibold rounded-full shadow-sm border">
                                {{ ucfirst(str_replace('_', ' ', $changeRequest->incorporation_status)) }}
                            </span>
                        </div>

                        <!-- Action Buttons based on status -->
                        @if ($changeRequest->incorporation_status === 'pending')
                            <button type="button" id="mark-incorporated-btn"
                                data-request-id="{{ $changeRequest->id }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                Mark as Incorporated
                            </button>
                        @elseif($changeRequest->incorporation_status === 'incorporated')
                            <button type="button" id="verify-changes-btn" data-request-id="{{ $changeRequest->id }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:ring-4 focus:ring-green-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="m8 11 2 2 4-4"></path>
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                Verify Changes
                            </button>
                        @elseif($changeRequest->incorporation_status === 'missing')
                            <button type="button" id="re-incorporate-btn"
                                data-request-id="{{ $changeRequest->id }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-lg hover:from-orange-700 hover:to-orange-800 focus:ring-4 focus:ring-orange-200 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                    <path d="M21 3v5h-5"></path>
                                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                    <path d="M3 21v-5h5"></path>
                                </svg>
                                Re-incorporate
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($changeRequest->status === 'rejected' && $changeRequest->rejected_at)
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col space-y-4">
                    <!-- Header -->
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-400 mr-2"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                        <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">
                            Request Rejected
                        </h3>
                    </div>

                    <!-- Rejection Details -->
                    <div class="pl-7">
                        <!-- Rejection Metadata -->
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            Rejected by
                            <span class="font-medium text-red-700 dark:text-red-400">
                                {{ $changeRequest->rejectedby ?? 'Admin' }}
                            </span>
                            on
                            <span class="font-medium text-red-700 dark:text-red-400">
                                {{ $changeRequest->rejected_at }}
                            </span>
                        </div>

                        <!-- Rejection Reason -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-md p-4 border border-red-200 dark:border-red-800">
                            <h4 class="text-sm font-medium text-red-700 dark:text-red-400 mb-2">
                                Reason for Rejection:
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ $changeRequest->rejection_reason }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Description -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Description</h3>
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <p class="text-gray-700 leading-relaxed">{{ $changeRequest->description }}</p>
                </div>
            </div>

            @php
                $changes = json_decode($changeRequest->new_data, true);

                // Calculate counts for each table type
                $tableCounts = [
                    'region' => 0,
                    'subregion' => 0,
                    'country' => 0,
                    'state' => 0,
                    'city' => 0,
                ];

                // Count modifications
                if (!empty($changes['modifications'])) {
                    foreach ($changes['modifications'] as $table => $modifications) {
                        $tableCounts[$table] = ($tableCounts[$table] ?? 0) + count($modifications);
                    }
                }

                // Count additions
                if (!empty($changes['additions'])) {
                    foreach ($changes['additions'] as $key => $data) {
                        $tableType = explode('-', $key)[1] ?? '';
                        $tableType = explode('_', $tableType)[0];
                        if (isset($tableCounts[$tableType])) {
                            $tableCounts[$tableType]++;
                        }
                    }
                }

                // Count deletions
                if (!empty($changes['deletions'])) {
                    foreach ($changes['deletions'] as $deletion) {
                        $parts = explode('_', $deletion);
                        $table = $parts[0] ?? '';
                        if (isset($tableCounts[$table])) {
                            $tableCounts[$table]++;
                        }
                    }
                }
            @endphp

            <!-- table tabs -->
            <div class="flex border-b border-gray-200 mt-4 mb-6" id="view-table-tabs">
                <button type="button"
                    class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700 active-tab"
                    id="view-region-tab">
                    Regions
                    @if ($tableCounts['region'] > 0)
                        <span
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tableCounts['region'] }}
                        </span>
                    @endif
                </button>
                <button type="button"
                    class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                    id="view-subregion-tab">
                    Subregions
                    @if ($tableCounts['subregion'] > 0)
                        <span
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tableCounts['subregion'] }}
                        </span>
                    @endif
                </button>
                <button type="button"
                    class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                    id="view-country-tab">
                    Countries
                    @if ($tableCounts['country'] > 0)
                        <span
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tableCounts['country'] }}
                        </span>
                    @endif
                </button>
                <button type="button"
                    class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                    id="view-state-tab">
                    States
                    @if ($tableCounts['state'] > 0)
                        <span
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tableCounts['state'] }}
                        </span>
                    @endif
                </button>
                <button type="button"
                    class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                    id="view-city-tab">
                    Cities
                    @if ($tableCounts['city'] > 0)
                        <span
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tableCounts['city'] }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- Modifications -->
            @if (!empty($changes['modifications']))
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-blue-600 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Modifications
                        </h4>
                        @foreach ($changes['modifications'] as $table => $modifications)
                            <div class="mb-6" data-table-content="{{ $table }}">
                                <h5 class="text-sm font-semibold mb-2 text-gray-600">{{ ucfirst($table) }}</h5>
                                @foreach ($modifications as $id => $fields)
                                    @php
                                        $tableName = explode('_', $id)[0];
                                        $modelClass = 'App\\Models\\' . ucfirst($tableName);
                                        $recordId = explode('_', $id)[1];
                                        $originalRecord = $modelClass::find($recordId);
                                    @endphp
                                    <div class="overflow-x-auto mb-4 bg-gray-50 rounded-lg">
                                        <div class="p-4 border-b border-gray-200">
                                            <span class="font-medium text-gray-700">ID: </span>
                                            <span class="text-gray-600">{{ explode('_', $id)[1] ?? $id }}</span>
                                        </div>
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    @foreach ($fields as $field => $value)
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r last:border-r-0">
                                                            {{ $field }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <!-- Original Values -->
                                                <tr>
                                                    @foreach ($fields as $field => $value)
                                                        @php
                                                            $originalValue = $originalRecord
                                                                ? $originalRecord->$field
                                                                : 'N/A';
                                                            if (is_array($originalValue) || is_object($originalValue)) {
                                                                $originalValue = json_encode($originalValue);
                                                            }
                                                            if (is_array($value) || is_object($value)) {
                                                                $value = json_encode($value);
                                                            }
                                                            $hasChanged = (string) $originalValue !== (string) $value;
                                                        @endphp
                                                        <td
                                                            class="px-6 py-3 text-sm border-r last:border-r-0 {{ $hasChanged ? 'bg-red-50 text-red-700' : 'bg-white text-gray-700' }}">
                                                            {{ $originalValue }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <!-- New Values -->
                                                <tr>
                                                    @foreach ($fields as $field => $value)
                                                        @php
                                                            $originalValue = $originalRecord
                                                                ? $originalRecord->$field
                                                                : 'N/A';
                                                            if (is_array($originalValue) || is_object($originalValue)) {
                                                                $originalValue = json_encode($originalValue);
                                                            }
                                                            if (is_array($value) || is_object($value)) {
                                                                $value = json_encode($value);
                                                            }
                                                            $hasChanged = (string) $originalValue !== (string) $value;
                                                        @endphp
                                                        <td
                                                            class="px-6 py-3 text-sm border-r last:border-r-0 {{ $hasChanged ? 'bg-green-50 text-green-700' : 'bg-white text-gray-700' }}">
                                                            {{ $value }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Additions -->
            @if (!empty($changes['additions']))
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-green-600 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Additions
                        </h4>
                        @php
                            // Group additions by table type
                            $groupedAdditions = [];
                            foreach ($changes['additions'] as $key => $data) {
                                $tableType = explode('-', $key)[1] ?? '';
                                $tableType = explode('_', $tableType)[0];
                                if (!isset($groupedAdditions[$tableType])) {
                                    $groupedAdditions[$tableType] = [];
                                }
                                $groupedAdditions[$tableType][] = $data;
                            }
                        @endphp

                        @foreach ($groupedAdditions as $tableType => $additions)
                            <div class="mb-6" data-table-content="{{ $tableType }}">
                                <h5 class="text-sm font-semibold mb-2 text-gray-600">{{ ucfirst($tableType) }}</h5>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                @foreach ($additions[0] as $field => $value)
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        {{ $field }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($additions as $data)
                                                <tr>
                                                    @foreach ($data as $field => $value)
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-sky-500">
                                                            {{ $value }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!--  -->

            <!-- Deletions -->
            @if (!empty($changes['deletions']))
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-red-600 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>Deletions
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Table</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($changes['deletions'] as $deletion)
                                        @php
                                            $parts = explode('_', $deletion);
                                            $table = $parts[0] ?? '';
                                            $id = $parts[1] ?? '';
                                        @endphp
                                        <tr data-table-content="{{ $table }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ ucfirst($table) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">
                                                {{ $id }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if (empty($changes['modifications']) && empty($changes['additions']) && empty($changes['deletions']))
                <p class="text-gray-500 italic">No changes found.</p>
            @endif



            @if (auth()->user()->is_admin)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('change-requests.sql', $changeRequest) }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            View SQL
                        </a>
                        <a href="{{ route('change-requests.sql.download', $changeRequest) }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Download SQL
                        </a>
                    </div>
                </div>
            @endif



            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Comments</h3>

                    <!-- Existing Comments -->
                    @if ($changeRequest->comments->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach ($changeRequest->comments->sortBy('created_at') as $comment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center">
                                            <div class="font-medium text-gray-900">{{ $comment->user->name }}</div>
                                            <span class="mx-2 text-gray-500">•</span>
                                            <div class="text-sm text-gray-500">
                                                {{ $comment->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-gray-700">{{ $comment->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Comment Form -->
                    <form action="{{ route('change-requests.storeComment', $changeRequest) }}" method="POST"
                        id="comment-form">
                        @csrf
                        <div class="mt-2">
                            <textarea name="content" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Add a comment..." required></textarea>
                        </div>
                        <div class="mt-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                Submit Comment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Reject Request Modal -->
        <x-modal name="reject-request-modal" :show="false">
            <form id="reject-form" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Reject Change Request
                </h2>

                <div class="mb-4">
                    <label for="rejection-reason"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Rejection
                    </label>
                    <textarea id="rejection-reason" name="rejection_reason" rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Submit Rejection
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Mark as Incorporated Modal -->
        <x-modal name="mark-incorporated-modal" :show="false">
            <form id="mark-incorporated-form" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Mark Change Request as Incorporated
                </h2>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">
                        Please confirm that you have successfully incorporated this change request into your main
                        database.
                    </p>

                    <div class="mb-4">
                        <label for="incorporated-by"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Incorporated by
                        </label>
                        <input type="text" id="incorporated-by" name="incorporated_by"
                            value="{{ auth()->user()->name }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="incorporation-notes"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes (optional)
                        </label>
                        <textarea id="incorporation-notes" name="incorporation_notes" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Add any notes about the incorporation process..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Mark as Incorporated
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Verify Changes Modal -->
        <x-modal name="verify-changes-modal" :show="false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Verify Incorporated Changes
                </h2>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">
                        This will verify if the incorporated changes are present in your current database after
                        synchronization.
                    </p>

                    <div id="verification-progress" class="hidden">
                        <div class="flex items-center mb-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                            <span class="text-sm text-gray-600">Verifying changes...</span>
                        </div>
                    </div>

                    <div id="verification-results" class="hidden">
                        <!-- Results will be populated via JavaScript -->
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Close
                    </x-secondary-button>
                    <x-primary-button id="start-verification" type="button">
                        Start Verification
                    </x-primary-button>
                </div>
            </div>
        </x-modal>

        <!-- Include Change Request Show JavaScript -->
        @vite('resources/js/change-request-show.js')
</x-app-layout>
