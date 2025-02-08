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
                        <div class="mt-2">
                            <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md overflow-x-auto">
                                {{ json_encode(json_decode($changeRequest->new_data), JSON_PRETTY_PRINT) }}
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
