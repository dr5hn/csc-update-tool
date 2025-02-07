<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="min-h-full w-full" id="artifacts-component-root-react">
            <div class="min-h-screen bg-gray-50 py-8">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <!-- Add the action attribute with the URL where the form should be submitted -->

                        <h1 class="text-2xl font-bold text-gray-900 mb-6" id="form-title">Database Changes Request - Regions</h1>

                        <!-- table tabs -->
                        <div class="flex border-b border-gray-200 mb-6" id="table-tabs">
                            <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700 active-tab" id="regions-tab" data-table="#regions-table">Regions</button>
                            <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="subregions-tab" data-table="#subregions-table">Subregions</button>
                            <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="countries-tab" data-table="#countries-table">Countries</button>
                            <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="states-tab" data-table="#states-table">States</button>
                            <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="cities-tab" data-table="#cities-table">Cities</button>
                        </div>


                        <!-- Search Bar -->
                        <div class="mb-8">
                            <div class="mb-4 flex gap-4">
                                <div class="relative flex-1 mb-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                    <input
                                        type="text"
                                        id="search-input"
                                        class="w-full p-2 border rounded-md search-input pl-10"
                                        placeholder="Search...">
                                </div>
                                <div class="mb-6 w-1/4" id="countries-dropdown" style="display:none;">
                                    <select class="w-full p-2 border rounded-md" id="countries-select">
                                        <option value="" disabled>All Countries</option>
                                        @foreach($countryData as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-6 w-1/4" id="states-dropdown" style="display:none;">
                                    @include('change-requests.partials.states-dropdown', ['states' => $stateData])
                                </div>
                            </div>
                        </div>



                        <!-- Regions Table -->
                        <div class="overflow-x-auto mt-1" id="regions-table">
                            @include('change-requests.partials.regions', ['regionData' => $regionData, 'regionHeaders' => $regionHeaders])
                        </div>

                        <!-- Subregions Table -->
                        <div class="overflow-x-auto mt-8" id="subregions-table">
                            @include('change-requests.partials.subregions', ['subregionData' => $subregionData, 'subregionHeaders' => $subregionHeaders])
                        </div>


                        <!-- Countries Table -->
                        <div class="overflow-x-auto mt-8" id="countries-table">
                            @include('change-requests.partials.countries', ['countryData' => $countryData, 'countryHeaders' => $countryHeaders])
                        </div>

                        <!-- States Table -->
                        <div class="overflow-x-auto mt-8" id="states-table">
                            @include('change-requests.partials.states', ['states' => $stateData, 'stateHeaders' => $stateHeaders])
                        </div>

                        <!-- Cities Table -->
                        <div class="overflow-x-auto mt-8" id="cities-table">
                            @include('change-requests.partials.cities', ['cities' => $cityData, 'cityHeaders' => $cityHeaders])
                        </div>

                        <!-- Add Row Button -->
                        <div class="flex justify-start mb-4">
                            <button type="button" id="add-row-btn" class="mt-4 flex items-center text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus w-4 h-4 mr-1">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5v14"></path>
                                </svg>
                                Add New Row</button>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Submit Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>