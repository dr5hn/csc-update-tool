<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Change Request: {{ $changeRequest->title }}
            </h2>
            <a href="{{ route('change-requests.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Description</h3>
                        <p class="mt-2">{{ $changeRequest->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <p class="mt-2">{{ ucfirst($changeRequest->status) }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Changes</h3>
                        @php
                        $changes = json_decode($changeRequest->new_data, true);
                        @endphp

                        <!-- Modifications -->
                        @if(!empty($changes['modifications']))
                        <div class="mb-8">
                            <h4 class="text-md font-semibold mb-3 text-blue-600">Modifications</h4>
                            @foreach($changes['modifications'] as $table => $modifications)
                            <div class="mb-6">
                                <h5 class="text-sm font-semibold mb-2 text-gray-600">{{ ucfirst($table) }}</h5>
                                @foreach($modifications as $id => $fields)
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
                                                @foreach($fields as $field => $value)
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r last:border-r-0">
                                                    {{ $field }}
                                                </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <!-- Original Values -->
                                            <tr>
                                                @foreach($fields as $field => $value)
                                                    @php
                                                        $originalValue = $originalRecord ? $originalRecord->$field : 'N/A';
                                                        if (is_array($originalValue) || is_object($originalValue)) {
                                                            $originalValue = json_encode($originalValue);
                                                        }
                                                        if (is_array($value) || is_object($value)) {
                                                            $value = json_encode($value);
                                                        }
                                                        $hasChanged = (string)$originalValue !== (string)$value;
                                                    @endphp
                                                    <td class="px-6 py-3 text-sm border-r last:border-r-0 {{ $hasChanged ? 'bg-red-50 text-red-700' : 'bg-white text-gray-700' }}">
                                                        {{ $originalValue }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                            <!-- New Values -->
                                            <tr>
                                                @foreach($fields as $field => $value)
                                                    @php
                                                        $originalValue = $originalRecord ? $originalRecord->$field : 'N/A';
                                                        if (is_array($originalValue) || is_object($originalValue)) {
                                                            $originalValue = json_encode($originalValue);
                                                        }
                                                        if (is_array($value) || is_object($value)) {
                                                            $value = json_encode($value);
                                                        }
                                                        $hasChanged = (string)$originalValue !== (string)$value;
                                                    @endphp
                                                    <td class="px-6 py-3 text-sm border-r last:border-r-0 {{ $hasChanged ? 'bg-green-50 text-green-700' : 'bg-white text-gray-700' }}">
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
                        @endif

                        <!-- Additions -->
                        @if(!empty($changes['additions']))
                        <div class="mb-8">
                            <h4 class="text-md font-semibold mb-3 text-green-600">Additions</h4>
                            @php
                                // Group additions by table type
                                $groupedAdditions = [];
                                foreach($changes['additions'] as $key => $data) {
                                    $tableType = explode('-', $key)[1] ?? '';
                                    $tableType = explode('_', $tableType)[0];
                                    if (!isset($groupedAdditions[$tableType])) {
                                        $groupedAdditions[$tableType] = [];
                                    }
                                    $groupedAdditions[$tableType][] = $data;
                                }
                            @endphp

                            @foreach($groupedAdditions as $tableType => $additions)
                            <div class="mb-6">
                                <h5 class="text-sm font-semibold mb-2 text-gray-600">{{ ucfirst($tableType) }}</h5>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                @foreach($additions[0] as $field => $value)
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $field }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($additions as $data)
                                                <tr>
                                                    @foreach($data as $field => $value)
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
                        @endif

                        <!-- Deletions -->
                        @if(!empty($changes['deletions']))
                        <div class="mb-8">
                            <h4 class="text-md font-semibold mb-3 text-red-600">Deletions</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Table</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($changes['deletions'] as $deletion)
                                        @php
                                        $parts = explode('_', $deletion);
                                        $table = $parts[0] ?? '';
                                        $id = $parts[1] ?? '';
                                        @endphp
                                        <tr>
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
                        @endif

                        @if(empty($changes['modifications']) && empty($changes['additions']) && empty($changes['deletions']))
                        <p class="text-gray-500 italic">No changes found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>