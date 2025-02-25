<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" id="form-title">
                Database Changes Request - Regions
            </h2>
            <a href="{{ route('change-requests.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="min-h-full w-full">
            <div class="mx-auto px-4">
                <div class="bg-white rounded-lg shadow-lg p-6">

                    <!-- Request Details Form -->
                    <div class="mb-8">
                        <form action="{{ route('change-requests.store')}}" method="post" id="change-request-form">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input type="text" id="request_title" name="title"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Enter Title....">
                                </div>
                                <div>
                                    <textarea id="request_description" name="description" rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Enter Description...."></textarea>
                                    <input type="hidden" name="new_data" id="new_data">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- table tabs -->
                    <div class="flex border-b border-gray-200 mb-6" id="table-tabs">
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700 active-tab"
                            id="regions-tab" data-table="#regions-table">Regions</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="subregions-tab" data-table="#subregions-table">Subregions</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="countries-tab" data-table="#countries-table">Countries</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="states-tab" data-table="#states-table">States</button>
                        <button type="button"
                            class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700"
                            id="cities-tab" data-table="#cities-table">Cities</button>
                    </div>


                    <!-- Search Bar -->
                    <div class="mb-8">
                        <div class="mb-4 flex gap-4">
                            <div class="relative flex-1 mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>
                                <input type="text" id="search-input"
                                    class="w-full p-2 border rounded-md search-input pl-10" placeholder="Search...">
                            </div>
                            <div class="mb-6 w-1/4" id="countries-dropdown" style="display:none;">
                                <select class="w-full p-2 border rounded-md" id="countries-select">
                                    <option value="" disabled>All Countries</option>
                                    @foreach ($countryData as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-6 w-1/4" id="states-dropdown" style="display:none;">
                                @include('change-requests.partials.states-dropdown', [
                                'states' => $stateData,
                                ])
                            </div>
                        </div>
                    </div>

                    <!-- Add Row Button -->
                    <div class="flex justify-start mb-4">
                        <button type="button" id="add-row-btn" class="add-row-btn flex items-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus w-4 h-4 mr-1">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Add New Row</button>
                    </div>



                    <!-- Regions Table -->
                    <div class="overflow-x-auto mt-8" id="regions-table">
                        @include('change-requests.partials.regions', [
                        'regionData' => $regionData,
                        'regionHeaders' => $regionHeaders,
                        ])
                    </div>

                    <!-- Subregions Table -->
                    <div class="overflow-x-auto mt-8" id="subregions-table">
                        @include('change-requests.partials.subregions', [
                        'subregionData' => $subregionData,
                        'subregionHeaders' => $subregionHeaders,
                        ])
                    </div>


                    <!-- Countries Table -->
                    <div class="overflow-x-auto mt-8" id="countries-table">
                        @include('change-requests.partials.countries', [
                        'countryData' => $countryData,
                        'countryHeaders' => $countryHeaders,
                        ])
                    </div>

                    <!-- States Table -->
                    <div class="overflow-x-auto mt-8" id="states-table">
                        @include('change-requests.partials.states', [
                        'stateData' => $stateData,
                        'stateHeaders' => $stateHeaders,
                        ])
                    </div>

                    <!-- Cities Table -->
                    <div class="overflow-x-auto mt-8" id="cities-table">
                        @include('change-requests.partials.cities', [
                        'cityData' => $cityData,
                        'cityHeaders' => $cityHeaders,
                        ])
                    </div>

                    <!-- Add Row Button -->
                    <div class="flex justify-start mb-4">
                        <button type="button" id="add-row-btn" class="add-row-btn mt-4 flex items-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus w-4 h-4 mr-1">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Add New Row</button>
                    </div>

        
                    <!-- Sticky buttons container -->
                    <div class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-sm border-t border-gray-200 shadow-lg z-50 transition-all duration-300">
                        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-end space-x-3">
                            <button type="button" id="save-draft-btn"
                                class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                Save Draft
                            </button>
                            <button type="button" id="review-changes-btn"
                                class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition-colors duration-200">
                                Review Changes
                            </button>
                            <button type="submit" id="change-request-submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                Submit Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the modals -->
    @include('change-requests.partials.review-changes-modal')
</x-app-layout>