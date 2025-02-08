<?php

namespace App\Http\Controllers;

use App\Models\{City, Country, Region, State, Subregion, ChangeRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
class ChangeRequestController extends Controller
{
    /**
     * Display the change request form with all necessary data
     */
    public function changeRequest(): View
    {
        try {
            $viewData = [
                'regions' => $this->getModelData(Region::class),
                'subregions' => $this->getModelData(Subregion::class),
                'countries' => $this->getModelData(Country::class),
                'states' => $this->getModelData(State::class),
                'cities' => $this->getModelData(City::class),
                'stateDropdownData' => State::getDropdownData()
            ];

            $formattedData = $this->formatViewData($viewData);

            return view('change-requests.new', $formattedData);
        } catch (\Exception $e) {
            Log::error('Error in change request view: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generic search method for models
     */
    private function searchModel(string $modelClass, Request $request, ?string $relationField = null): View
    {
        try {
            $query = $modelClass::query();
            $searchText = $request->input('search');
            $fieldId = $request->input($relationField) ?? null;

            // Special handling for different models
            switch ($modelClass) {
                case City::class:
                    $query->select(
                        'id',
                        'name',
                        'state_id',
                        'state_code',
                        'country_id',
                        'country_code',
                        'latitude',
                        'longitude',
                        'wikiDataId'
                    )->with(['state:id,name', 'country:id,name']);
                    break;

                case State::class:
                    $query->select(
                        'id',
                        'name',
                        'country_id',
                        'country_code',
                        'fips_code',
                        'iso2',
                        'type',
                        'latitude',
                        'longitude',
                        'wikiDataId'
                    )->with('country:id,name');
                    break;

                case Country::class:
                    $query->select(
                        'id',
                        'name',
                        'iso3',
                        'numeric_code',
                        'iso2',
                        'phonecode',
                        'capital',
                        'currency',
                        'currency_name',
                        'currency_symbol',
                        'tld',
                        'native',
                        'region_id',
                        'subregion_id',
                        'nationality',
                        'timezones',
                        'translations',
                        'latitude',
                        'longitude',
                        'emoji',
                        'emojiU',
                        'wikiDataId'
                    )->with(['subregion:id,name,region_id', 'subregion.region:id,name']);
                    break;
            }

            // Apply search and relation filters
            if ($searchText && $fieldId !== 'null') {
                $query->where('name', 'like', "%{$searchText}%")
                    ->when($fieldId, fn($q) => $q->where($relationField, $fieldId));
            } elseif ($fieldId !== 'null') {
                $query->where($relationField, $fieldId);
            } elseif ($searchText) {
                $query->where('name', 'like', "%{$searchText}%");
            }

            if ($relationField === 'country_id' && $modelClass === City::class) {
                $data = $query->paginate(100);
            } else {
                $data = $query->get();
            }
            // Determine if pagination should be used
            $data = ($relationField === 'country_id') ? $query->get() : $query->paginate(100);

            $headers = $modelClass::getTableHeaders();
            $viewName = $this->getViewName($modelClass);
            $viewName = $viewName === 'citys' ? 'cities' : $viewName;
            if ($modelClass === City::class) {
                $dataKey = 'cityData';
                $headersKey = 'cityHeaders';
            } elseif ($modelClass === State::class) {
                $dataKey = 'stateData';
                $headersKey = 'stateHeaders';
            } elseif ($modelClass === Country::class) {
                $dataKey = 'countryData';
                $headersKey = 'countryHeaders';
            }

            return view("change-requests.partials.{$viewName}", [
                $dataKey => $data,
                $headersKey => $headers
            ]);
        } catch (\Exception $e) {
            Log::error("Error in searchModel for {$modelClass}: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Helper method to get model data and headers
     */
    private function getModelData(string $modelClass): array
    {
        $query = $modelClass::query();


        // Add selective loading based on model
        switch ($modelClass) {
            case City::class:
                $perPage = 10; // Configurable page size
                $query->select('id', 'name', 'state_id', 'state_code', 'country_id', 'country_code', 'latitude', 'longitude', 'wikiDataId')
                    ->with(['state:id,name', 'country:id,name']);
                break;
            case State::class:
                $perPage = 100; // Configurable page size
                $query->select('id', 'name', 'country_id', 'country_code', 'fips_code', 'iso2', 'type', 'latitude', 'longitude', 'wikiDataId')
                    ->with('country:id,name');
                break;
            case Country::class:
                $perPage = 300; // Configurable page size
                $query->select('id', 'name', 'iso3', 'numeric_code', 'iso2', 'phonecode', 'capital', 'currency', 'currency_name', 'currency_symbol', 'tld', 'wikiDataId')
                    ->with(['subregion:id,name,region_id', 'subregion.region:id,name']);
                break;
            default:
                $perPage = 100; // Configurable page size
                break;
        }

        return [
            'headers' => $modelClass::getTableHeaders(),
            'data' => $query->paginate($perPage)
        ];
    }

    /**
     * Helper method to format view data
     */
    private function formatViewData(array $data): array
    {
        return [
            'regionHeaders' => $data['regions']['headers'],
            'regionData' => $data['regions']['data'],
            'subregionHeaders' => $data['subregions']['headers'],
            'subregionData' => $data['subregions']['data'],
            'countryHeaders' => $data['countries']['headers'],
            'countryData' => $data['countries']['data'],
            'stateHeaders' => $data['states']['headers'],
            'stateData' => $data['states']['data'],
            'cityHeaders' => $data['cities']['headers'],
            'cityData' => $data['cities']['data'],
            'stateDropdownData' => $data['stateDropdownData']
        ];
    }

    /**
     * Generic search method for models
     */
    /**
     * Get countries with optional search
     */
    public function getRegions(Request $request): View
    {
        return $this->searchModel(Region::class, $request);
    }

    public function getSubregions(Request $request): View
    {
        return $this->searchModel(Subregion::class, $request);
    }

    public function getCountries(Request $request): View
    {
        return $this->searchModel(Country::class, $request);
    }

    public function getStates(Request $request): View
    {
        return $this->searchModel(State::class, $request, 'country_id');
    }

    public function getCitiesByCountry(Request $request): View
    {
        return $this->searchModel(City::class, $request, 'country_id');
    }

    public function getCitiesByState(Request $request): View
    {
        return $this->searchModel(City::class, $request, 'state_id');
    }

    /**
     * Get states dropdown data for a specific country
     */
    public function getStatesDropdown(Request $request): View
    {
        try {
            $states = State::with('country')
                ->where('country_id', $request->input('country_id'))
                ->get();

            return view('change-requests.partials.states-dropdown', ['states' => $states]);
        } catch (\Exception $e) {
            Log::error('Error in getStatesDropdown: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the view name from model class
     */
    private function getViewName(string $modelClass): string
    {
        return strtolower(class_basename($modelClass)) . 's';
    }

    /**
     * Save the change request
     */
    public function changeRequestSave(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data'],
                'status' => 'pending'
            ]);

            return redirect('dashboard');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving change request: ' . $e->getMessage()
            ], 500);
        }
    }
}
