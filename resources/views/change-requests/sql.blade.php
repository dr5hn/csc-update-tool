<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                SQL for Change Request: {{ $changeRequest->title }}
            </h2>
            <a href="{{ route('change-requests.show', $changeRequest) }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to Change Request
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Forward SQL -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Forward SQL</h3>
                        <button onclick="copyToClipboard('forward-sql')"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Copy SQL
                        </button>
                    </div>
                    <pre id="forward-sql" class="bg-gray-50 p-4 rounded-lg overflow-x-auto"><code>{{ $forwardSQL }}</code></pre>
                </div>
            </div>

            <!-- Rollback SQL -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Rollback SQL</h3>
                        <button onclick="copyToClipboard('rollback-sql')"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Copy SQL
                        </button>
                    </div>
                    <pre id="rollback-sql" class="bg-gray-50 p-4 rounded-lg overflow-x-auto"><code>{{ $rollbackSQL }}</code></pre>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                // Show success message
                alert('SQL copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }
    </script>
    @endpush
</x-app-layout>