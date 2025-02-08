<x-modal name="review-changes-modal" :show="false" maxWidth="6xl">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Review Changes
        </h2>

        <!-- Tabs -->
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px" role="tablist">
                <li class="mr-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg review-tab active"
                            data-target="modifications">Modifications</button>
                </li>
                <li class="mr-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg review-tab"
                            data-target="additions">Additions</button>
                </li>
                <li class="mr-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg review-tab"
                            data-target="deletions">Deletions</button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content overflow-x-auto">
            <div id="modifications-content" class="review-content active">
                <div class="space-y-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200" id="modifications-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="additions-content" class="review-content hidden">
                <div class="space-y-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200" id="additions-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="deletions-content" class="review-content hidden">
                <div class="space-y-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200" id="deletions-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <x-secondary-button x-on:click="$dispatch('close')">
                Close
            </x-secondary-button>
            <x-primary-button id="confirm-changes-btn">
                Confirm Changes
            </x-primary-button>
        </div>
    </div>
</x-modal>

<x-modal name="confirm-submit-modal" :show="false">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Confirm Submission
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            Are you sure you want to submit these changes? This action cannot be undone.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
            <x-secondary-button x-on:click="$dispatch('close')">
                Cancel
            </x-secondary-button>
            <x-primary-button id="final-submit-btn">
                Submit Changes
            </x-primary-button>
        </div>
    </div>
</x-modal>
