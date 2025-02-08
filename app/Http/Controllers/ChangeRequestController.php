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
    private function getModelSelects(string $modelClass): array
    {
        $selects = [
            City::class => [
                'fields' => ['id', 'name', 'state_id', 'state_code', 'country_id', 'country_code', 'latitude', 'longitude', 'wikiDataId'],
                'relations' => ['state:id,name', 'country:id,name'],
                'perPage' => 100
            ],
            State::class => [
                'fields' => ['id', 'name', 'country_id', 'country_code', 'fips_code', 'iso2', 'type', 'latitude', 'longitude', 'wikiDataId'],
                'relations' => ['country:id,name'],
                'perPage' => 100
            ],
            Country::class => [
                'fields' => ['id', 'name', 'iso3', 'numeric_code', 'iso2', 'phonecode', 'capital', 'currency', 'currency_name', 'currency_symbol', 'tld', 'native', 'region_id', 'subregion_id', 'nationality', 'timezones', 'translations', 'latitude', 'longitude', 'emoji', 'emojiU', 'wikiDataId'],
                'relations' => ['subregion:id,name,region_id', 'subregion.region:id,name'],
                'perPage' => 300
            ]
        ];

        return $selects[$modelClass] ?? ['fields' => ['*'], 'relations' => [], 'perPage' => 100];
    }

    private function searchModel(string $modelClass, Request $request, ?string $relationField = null): View
    {
        try {
            $query = $modelClass::query();
            $searchText = $request->input('search');
            $fieldId = $request->input($relationField);

            // Get model configuration
            $config = $this->getModelSelects($modelClass);

            // Apply selects and relations
            $query->select($config['fields']);
            if (!empty($config['relations'])) {
                $query->with($config['relations']);
            }

            // Apply filters
            if ($searchText && $fieldId !== 'null') {
                $query->where('name', 'like', "%{$searchText}%")
                    ->when($fieldId, fn($q) => $q->where($relationField, $fieldId));
            } elseif ($fieldId !== 'null') {
                $query->where($relationField, $fieldId);
            } elseif ($searchText) {
                $query->where('name', 'like', "%{$searchText}%");
            }

            // Get data with appropriate pagination
            $data = $relationField === 'country_id' ? $query->get() : $query->paginate($config['perPage']);

            // Get view data
            $viewName = strtolower(class_basename($modelClass)) . 's';
            $viewName = $viewName === 'citys' ? 'cities' : $viewName;
            $dataKey = strtolower(class_basename($modelClass)) . 'Data';
            $headersKey = strtolower(class_basename($modelClass)) . 'Headers';

            return view("change-requests.partials.{$viewName}", [
                $dataKey => $data,
                $headersKey => $modelClass::getTableHeaders()
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
        $config = $this->getModelSelects($modelClass);

        $query->select($config['fields']);
        if (!empty($config['relations'])) {
            $query->with($config['relations']);
        }

        return [
            'headers' => $modelClass::getTableHeaders(),
            'data' => $query->paginate($config['perPage'])
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
     * Store a draft change request.
     */
    public function storeDraft(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            $changeRequest = ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data'],
                'status' => 'draft'
            ]);

            return response()->json([
                'message' => 'Draft saved successfully',
                'redirect' => route('change-requests.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a change request.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            $changeRequest = ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data'],
                'status' => 'pending'
            ]);

            return redirect()
                ->route('change-requests.show', $changeRequest)
                ->with('success', 'Change request submitted successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error submitting change request: ' . $e->getMessage()]);
        }
    }

    /**
     * Show a specific change request.
     */
    public function show(ChangeRequest $changeRequest): View
    {
        return view('change-requests.show', compact('changeRequest'));
    }

    public function index(): View
    {
        try {
            $viewData = [
                'changeRequests' => ChangeRequest::with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10),
                'countries' => Country::orderBy('name')->get()
            ];

            return view('change-requests.index', $viewData);
        } catch (\Exception $e) {
            Log::error('Error in change requests index: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Edit draft change request
     */
    public function editDraft(ChangeRequest $changeRequest): View
    {
        if ($changeRequest->status !== 'draft') {
            abort(403, 'Only draft requests can be edited');
        }

        $viewData = [
            'regions' => $this->getModelData(Region::class),
            'subregions' => $this->getModelData(Subregion::class),
            'countries' => $this->getModelData(Country::class),
            'states' => $this->getModelData(State::class),
            'cities' => $this->getModelData(City::class),
            'stateDropdownData' => State::getDropdownData(),
            'changeRequest' => $changeRequest
        ];

        $formattedData = $this->formatViewData($viewData);
        $formattedData['changeRequest'] = $changeRequest;

        return view('change-requests.edit', $formattedData);
    }

    /**
     * Update draft change request
     */
    public function updateDraft(Request $request, ChangeRequest $changeRequest): JsonResponse
    {
        try {
            if ($changeRequest->status !== 'draft') {
                throw new \Exception('Only draft requests can be updated');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            $changeRequest->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data']
            ]);

            return response()->json([
                'message' => 'Draft updated successfully',
                'redirect' => route('change-requests.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating draft: ' . $e->getMessage()
            ], 500);
        }
    }
}
