<x-app-layout>
    <!-- Header -->
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
            <!-- Status and Creator Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $changeRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($changeRequest->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                'bg-red-100 text-red-800') }}">
                            {{ ucfirst($changeRequest->status) }}
                        </span>
                        <span class="text-gray-600">Created by: {{ $changeRequest->user->email }}</span>
                    </div>
                    @if(auth()->user()->is_admin && $changeRequest->status === 'pending')
                    <div class="flex space-x-4">
                        <button type="button" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4 mr-2">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                            Approve
                        </button>
                        <button type="button" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4 mr-2">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                            Reject
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="bg-card text-card-foreground rounded-xl border shadow">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h3 class="font-semibold leading-none tracking-tight flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 mr-2">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                    <path d="M10 9H8"></path>
                                    <path d="M16 13H8"></path>
                                    <path d="M16 17H8"></path>
                                </svg>Description</h3>
                            <div class="p-6 pt-0">
                                <p class="text-gray-700">{{ $changeRequest->description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- table tabs -->
                    <div class="flex border-b border-gray-200 mt-4 mb-6" id="view-table-tabs">
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700 active-tab"
                            id="view-region-tab">Regions</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="view-subregion-tab">Subregions</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="view-country-tab">Countries</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="view-state-tab">States</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="view-city-tab">Cities</button>
                    </div>


                    @php
                    $changes = json_decode($changeRequest->new_data, true);
                    @endphp

                    <!-- Modifications -->
                    @if(!empty($changes['modifications']))
                    <div class="bg-card text-card-foreground rounded-xl border shadow mt-4">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h4 class="text-md flex items-center font-semibold mb-4 text-blue-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Modifications
                            </h4>
                            @foreach($changes['modifications'] as $table => $modifications)
                            <div class="mb-6" data-table-content="{{ $table }}">
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
                    </div>
                    @endif

                    <!-- Additions -->
                    @if(!empty($changes['additions']))
                    <div class="bg-card text-card-foreground rounded-xl border shadow mt-4">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h4 class="text-md flex items-center font-semibold mb-4 text-green-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Additions
                            </h4>
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
                            <div class="mb-6" data-table-content="{{ $tableType }}">
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
                    </div>
                    @endif

                    <!--  -->

                    <!-- Deletions -->
                    @if(!empty($changes['deletions']))
                    <div class="bg-card text-card-foreground rounded-xl border shadow mt-4">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h4 class="text-md flex items-center font-semibold mb-4 text-red-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>Deletions
                            </h4>
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

                    @if(empty($changes['modifications']) && empty($changes['additions']) && empty($changes['deletions']))
                    <p class="text-gray-500 italic">No changes found.</p>
                    @endif



                    @if(auth()->user()->is_admin)
                    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-end space-x-3">
                        <a href="{{ route('change-requests.sql', $changeRequest) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            View SQL
                        </a>
                        <a href="{{ route('change-requests.sql.download', $changeRequest) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Download SQL
                        </a>
                    </div>
                    @endif
                </div>
            </div>



            <!-- for comments sections -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="bg-card text-card-foreground rounded-xl border shadow mt-4">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <h3 class="font-semibold leading-none tracking-tight flex items-center">Comments</h3>

                            <!-- Existing Comments -->
                            @if($changeRequest->comments->count() > 0)
                            <div class="space-y-4 mb-6">
                                @foreach($changeRequest->comments->sortBy('created_at') as $comment)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</div>
                                            <span class="mx-2 text-gray-500">•</span>
                                            <div class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Comment Form -->
                            <form action="{{ route('change-requests.storeComment', $changeRequest) }}" method="POST" id="comment-form">
                                @csrf
                                <div class="mt-2">
                                    <textarea
                                        name="content"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Add a comment..."
                                        required></textarea>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        Submit Comment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>