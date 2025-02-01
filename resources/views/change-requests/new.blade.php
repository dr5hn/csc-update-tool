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
                    <!-- Add the action attribute with the URL where the form should be submitted -->

                    <h1 class="text-2xl font-bold text-gray-900 mb-6" id="form-title">Database Changes Request - Regions</h1>

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <input
                            type="text"
                            id="search-input"
                            class="w-full p-2 border rounded-md"
                            placeholder="Search..." />
                    </div>

                    <!-- dropdown for table selection -->
                    <div class="flex justify-between mb-4">
                        <div class="mb-6 w-1/2" id="countries-dropdown" style="display:none;">
                            <select class="w-full p-2 border rounded-md" id="countries-select">
                                <option value="" disabled>All Countries</option>
                                @foreach($countryData as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-6 w-1/2" id="states-dropdown" style="display:none;">
                            @include('change-requests.partials.states-dropdown', ['states' => $stateData])
                        </div>
                    </div>


                    <!-- table tabs -->
                    <div class="flex border-b border-gray-200 mb-6" id="table-tabs">
                        <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700 active-tab" id="regions-tab" data-table="#regions-table">Regions</button>
                        <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="subregions-tab" data-table="#subregions-table">Subregions</button>
                        <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="countries-tab" data-table="#countries-table">Countries</button>
                        <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="states-tab" data-table="#states-table">States</button>
                        <button type="button" class="py-2 px-4 font-medium border-transparent text-gray-500 hover:text-gray-700" id="cities-tab" data-table="#cities-table">Cities</button>

                    </div>

                    <!-- Add Row Button -->
                    <div class="flex justify-start mb-4">
                        <button type="button" id="add-row-btn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Add Row</button>
                    </div>

                    <!-- Regions Table -->
                    <div class="overflow-x-auto mt-8" id="regions-table">
                        <table class="min-w-full divide-y divide-gray-200" id="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach ($regionHeaders as $header)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                @foreach ($regionData as $region)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->translations }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->created_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->updated_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->flag }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $region->wikiDataId }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Subregions Table -->
                    <div class="overflow-x-auto mt-8" id="subregions-table">
                        <table class="min-w-full divide-y divide-gray-200" id="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach ($subregionHeaders as $header)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                @foreach ($subregionData as $subregion)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->translations }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->region_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->created_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->updated_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->flag }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->wikiDataId }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                    <!-- Countries Table -->
                    <div class="overflow-x-auto mt-8" id="countries-table">
                        <table class="min-w-full divide-y divide-gray-200" id="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach ($countryHeaders as $header)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                @foreach ($countryData as $country)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->iso3 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->numeric_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->iso2 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->phonecode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->capital }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->currency }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->currency_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->currency_symbol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->tld }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->native }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->region }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->region_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->subregion }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->subregion_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->nationality }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->timezones }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->translations }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->latitude }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->longitude }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->emoji }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->emojiU }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->created_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->updated_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->flag }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->wikiDataId }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- States Table -->
                    <div class="overflow-x-auto mt-8" id="states-table">
                        @include('change-requests.partials.states', ['states' => $stateData, 'stateHeaders' => $stateHeaders])
                    </div>

                    <!-- Cities Table -->
                    <div class="overflow-x-auto mt-8" id="cities-table">
                        @include('change-requests.partials.cities', ['cities' => $cityData, 'cityHeaders' => $cityHeaders])
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
</x-app-layout>