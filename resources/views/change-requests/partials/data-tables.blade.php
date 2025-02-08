<div class="overflow-x-auto">
    <!-- Table Tabs -->
    <div class="mb-4 border-b border-gray-200" id="table-tabs">
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

    <!-- Dropdown Filters -->
    <div class="mb-4 flex gap-4" id="dropdowns">
        <div class="w-48 hidden" id="countries-dropdown">
            <select class="w-full p-2 border rounded-md" id="countries-select">
                <option value="" selected disabled>Select Country</option>
                @foreach($countryData as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48 hidden" id="states-dropdown">
            <select class="w-full p-2 border rounded-md" id="states-select">
                <option value="" selected disabled>Select State</option>
            </select>
        </div>
    </div>

    <!-- Data Tables -->
    <div id="regions-table">
        @include('change-requests.partials.regions', [
            'regionData' => $regionData,
            'regionHeaders' => $regionHeaders
        ])
    </div>

    <div id="subregions-table" style="display: none;">
        @include('change-requests.partials.subregions', [
            'subregionData' => $subregionData,
            'subregionHeaders' => $subregionHeaders
        ])
    </div>

    <div id="countries-table" style="display: none;">
        @include('change-requests.partials.countries', [
            'countryData' => $countryData,
            'countryHeaders' => $countryHeaders
        ])
    </div>

    <div id="states-table" style="display: none;">
        @include('change-requests.partials.states', [
            'stateData' => $stateData,
            'stateHeaders' => $stateHeaders
        ])
    </div>

    <div id="cities-table" style="display: none;">
        @include('change-requests.partials.cities', [
            'cityData' => $cityData,
            'cityHeaders' => $cityHeaders
        ])
    </div>
</div>
