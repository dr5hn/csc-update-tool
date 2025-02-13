<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Draft Change Request
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="change-request-form" class="space-y-6">
                        @csrf
                        <div>
                            <label for="request_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input type="text" name="title" id="request_title"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   value="{{ $changeRequest->title }}" required>
                        </div>

                        <div>
                            <label for="request_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea name="description" id="request_description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      required>{{ $changeRequest->description }}</textarea>
                        </div>

                        @include('change-requests.partials.data-tables', [
                            'regionData' => $regionData,
                            'regionHeaders' => $regionHeaders,
                            'subregionData' => $subregionData,
                            'subregionHeaders' => $subregionHeaders,
                            'countryData' => $countryData,
                            'countryHeaders' => $countryHeaders,
                            'stateData' => $stateData,
                            'stateHeaders' => $stateHeaders,
                            'cityData' => $cityData,
                            'cityHeaders' => $cityHeaders,
                        ])

                        <div class="flex justify-end space-x-4">
                            <input type="hidden" name="change_request_id"  value="{{ $changeRequest->id }}">
                            <button type="button" id="save-draft-btn"
                                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                Save Draft
                            </button>
                            <button type="button" id="final-submit-btn"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                Submit Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
            window.existingChanges = @json($changeRequest->new_data);
    </script>
</x-app-layout>
