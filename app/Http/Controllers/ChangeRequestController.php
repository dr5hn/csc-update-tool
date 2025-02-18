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
                'fields' => ['id', 'name', 'iso3', 'numeric_code', 'iso2', 'phonecode', 'capital', 'currency', 'currency_name', 'currency_symbol', 'tld', 'native', 'region', 'region_id', 'subregion', 'subregion_id', 'nationality', 'timezones', 'translations', 'latitude', 'longitude', 'emoji', 'emojiU', 'wikiDataId'],
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
            $fieldId = 'null';

            if ($relationField) {
                $fieldId = $request->input($relationField);
            }

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
            $data = $query->get();

            // Get view data
            $viewName = strtolower(class_basename($modelClass)) . 's';
            $viewName = $viewName === 'citys' ? 'cities' : $viewName;
            $viewName = $viewName === 'countrys' ? 'countries' : $viewName;
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
     * Store or update a draft change request.
     */
    public function storeDraft(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'sometimes|exists:change_requests,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            // If ID exists, update existing draft
            if (!empty($validated['id'])) {
                return $this->updateDraft($request, $validated['id']);
            }

            // Create new draft
            $changeRequest = ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data'],
                'status' => 'draft'
            ]);

            return response()->json([
                'message' => 'Draft saved successfully',
                'redirect' => route('change-requests.index'),
                'draft_id' => $changeRequest->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Protected method to update existing draft
     */
    protected function updateDraft(Request $request, int $id): JsonResponse
    {
        try {
            // Find the draft and verify ownership
            $draft = ChangeRequest::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$draft) {
                return response()->json([
                    'message' => 'Draft not found or unauthorized'
                ], 404);
            }

            // Verify draft status
            if ($draft->status !== 'draft') {
                return response()->json([
                    'message' => 'This request is no longer a draft'
                ], 422);
            }

            // Update the draft
            $draft->update([
                'title' => $request->title,
                'description' => $request->description,
                'new_data' => $request->new_data
            ]);

            return response()->json([
                'message' => 'Draft updated successfully',
                'redirect' => route('change-requests.index'),
                'draft_id' => $draft->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a change request.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'sometimes|exists:change_requests,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            // If ID exists, update existing request
            if (!empty($validated['id'])) {
                return $this->update($request, $validated['id']);
            }

            // Create new change request
            ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data'],
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Request submitted successfully',
                'redirect' => route('change-requests.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error submitting change request: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function update(Request $request, int $id): JsonResponse
    {
        try {
            // Find the change request and verify ownership
            $changeRequest = ChangeRequest::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$changeRequest) {
                return response()->json([
                    'message' => 'Change request not found or unauthorized'
                ], 404);
            }

            // Check if request can be updated
            if ($changeRequest->status !== 'draft') {
                return response()->json([
                    'message' => 'Cannot update request - current status: ' . $changeRequest->status
                ], 422);
            }

            // Update the change request
            $changeRequest->update([
                'title' => $request->title,
                'description' => $request->description,
                'new_data' => $request->new_data,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Request updated successfully',
                'redirect' => route('change-requests.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating change request: ' . $e->getMessage()
            ], 500);
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
            $user = Auth::user();

            $query = ChangeRequest::with('user')
                ->orderBy('created_at', 'desc');

            // If not admin, only show user's own requests
            if (!$user->is_admin) {
                $query->where('user_id', $user->id);
            }

            $changeRequests = $query->paginate(10);

            return view('change-requests.index', compact('changeRequests'));
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
}
