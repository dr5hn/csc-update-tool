<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="gradient-text text-2xl sm:text-3xl font-bold" id="form-title">
                    Database Changes Request - Regions
                </h2>
                <p class="text-gray-600 mt-2">Create and manage geographical database changes</p>
            </div>
            <a href="{{ route('change-requests.index') }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-6 container-responsive">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Request Details Form -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h3>
                <form action="{{ route('change-requests.store') }}" method="post" id="change-request-form">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="request_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="request_title" name="title" class="form-input"
                                placeholder="Enter a descriptive title for your change request..." required>
                        </div>
                        <div>
                            <label for="request_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea id="request_description" name="description" rows="3" class="form-input"
                                placeholder="Describe the changes you want to make..." required></textarea>
                            <input type="hidden" name="new_data" id="new_data">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Data Management Section -->
            <div class="p-6">
                <!-- Table Tabs -->
                <div class="tab-container" id="table-tabs">
                    <button type="button" class="tab-button active-tab" id="regions-tab" data-table="#regions-table">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Regions
                    </button>
                    <button type="button" class="tab-button" id="subregions-tab" data-table="#subregions-table">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                            </path>
                        </svg>
                        Subregions
                    </button>
                    <button type="button" class="tab-button" id="countries-tab" data-table="#countries-table">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9">
                            </path>
                        </svg>
                        Countries
                    </button>
                    <button type="button" class="tab-button" id="states-tab" data-table="#states-table">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        States
                    </button>
                    <button type="button" class="tab-button" id="cities-tab" data-table="#cities-table">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Cities
                    </button>
                </div>

                <!-- Search and Filter Controls -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search Input -->
                        <div class="md:col-span-6 lg:col-span-8">
                            <label for="search-input" class="block text-sm font-medium text-gray-700 mb-2">
                                Search Records
                            </label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>
                                <input type="text" id="search-input" class="form-input pl-10"
                                    placeholder="Search by name, code, or other fields...">
                            </div>
                        </div>

                        <!-- Country Filter -->
                        <div class="md:col-span-3" id="countries-dropdown" style="display:none;">
                            <label for="countries-select" class="block text-sm font-medium text-gray-700 mb-2">
                                Filter by Country
                            </label>
                            <select class="form-select" id="countries-select">
                                <option value="">All Countries</option>
                                @foreach ($countryData as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- State Filter -->
                        <div class="md:col-span-3" id="states-dropdown" style="display:none;">
                            <label for="states-select" class="block text-sm font-medium text-gray-700 mb-2">
                                Filter by State
                            </label>
                            @include('change-requests.partials.states-dropdown', [
                                'states' => $stateData,
                            ])
                        </div>
                    </div>
                </div>

                <!-- Add Row Button -->
                <div class="flex justify-between items-center mb-6">
                    <button type="button" id="add-row-btn" class="btn btn-primary add-row-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                        Add New Row
                    </button>

                    <!-- Table Info -->
                    <div class="hidden sm:flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Click "Edit" to modify existing records or "Add New Row" to create new ones
                    </div>
                </div>

                <!-- Data Tables -->
                <div class="space-y-6">
                    <!-- Regions Table -->
                    <div class="fade-in" id="regions-table">
                        @include('change-requests.partials.regions', [
                            'regionData' => $regionData,
                            'regionHeaders' => $regionHeaders,
                        ])
                    </div>

                    <!-- Subregions Table -->
                    <div class="fade-in" id="subregions-table">
                        @include('change-requests.partials.subregions', [
                            'subregionData' => $subregionData,
                            'subregionHeaders' => $subregionHeaders,
                        ])
                    </div>

                    <!-- Countries Table -->
                    <div class="fade-in" id="countries-table">
                        @include('change-requests.partials.countries', [
                            'countryData' => $countryData,
                            'countryHeaders' => $countryHeaders,
                        ])
                    </div>

                    <!-- States Table -->
                    <div class="fade-in" id="states-table">
                        @include('change-requests.partials.states', [
                            'stateData' => $stateData,
                            'stateHeaders' => $stateHeaders,
                        ])
                    </div>

                    <!-- Cities Table -->
                    <div class="fade-in" id="cities-table">
                        @include('change-requests.partials.cities', [
                            'cityData' => $cityData,
                            'cityHeaders' => $cityHeaders,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Action Bar -->
    <div
        class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 shadow-lg z-50 transition-all duration-300">
        <div class="container-responsive py-4">
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <button type="button" id="save-draft-btn" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    Save Draft
                </button>
                <button type="button" id="review-changes-btn" class="btn btn-warning">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Review Changes
                </button>
                <button type="submit" id="change-request-submit" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Submit Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Include the modals -->
    @include('change-requests.partials.review-changes-modal')

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form functionality is handled by script.js
            console.log('Change request form initialized');

            // Add mobile-friendly interactions
            if (window.innerWidth <= 768) {
                // Adjust sticky bar for mobile
                document.body.style.paddingBottom = '120px';
            }
        });
    </script>
</x-app-layout>
