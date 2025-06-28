<?php

namespace App\Http\Controllers;

use App\Models\{City, Country, Region, State, Subregion, ChangeRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminChangeRequestNotification;
use App\Notifications\ChangeRequestStatusNotification;
use App\Services\SQLGeneratorService;
use App\Services\CacheService;
use App\Services\ChangeRequestIncorporationService;
use App\Http\Requests\ChangeRequestStoreRequest;
use App\Models\User;
use Exception;

class ChangeRequestController extends Controller
{

    protected $sqlGenerator;
    protected $cache;

    public function __construct(SQLGeneratorService $sqlGenerator, CacheService $cache)
    {
        $this->sqlGenerator = $sqlGenerator;
        $this->cache = $cache;
    }

    /**
     * Display the dashboard with stats and recent activity
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Get stats
        $stats = [
            'total_requests' => ChangeRequest::count(),
            'pending_requests' => ChangeRequest::where('status', 'pending')->count(),
            'approved_requests' => ChangeRequest::where('status', 'approved')->count(),
            'user_requests' => ChangeRequest::where('user_id', $user->id)->count(),
        ];

        // Get recent requests (last 5) - only for admin users
        $recentRequests = [];
        if ($user->is_admin) {
            $recentRequests = ChangeRequest::with('user')
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentRequests'));
    }

    public function exportSQL(ChangeRequest $changeRequest)
    {
        $changes = json_decode($changeRequest->new_data, true);

        $result = $this->sqlGenerator->generate($changes);

        if (!$result['success']) {
            return back()->with('error', 'Failed to generate SQL: ' . $result['error']);
        }

        return view('change-requests.sql', [
            'changeRequest' => $changeRequest,
            'forwardSQL' => $result['data']['up'],
            'rollbackSQL' => $result['data']['down']
        ]);
    }

    public function downloadSQL(ChangeRequest $changeRequest)
    {
        $changes = json_decode($changeRequest->new_data, true);

        $result = $this->sqlGenerator->generate($changes);

        if (!$result['success']) {
            return back()->with('error', 'Failed to generate SQL: ' . $result['error']);
        }

        $sql = "-- Forward Migration\n\n";
        $sql .= $result['data']['up'];
        $sql .= "\n\n-- Rollback Migration\n\n";
        $sql .= $result['data']['down'];

        $filename = sprintf(
            'change_request_%d_%s.sql',
            $changeRequest->id,
            date('Y_m_d_His')
        );

        return response($sql)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }


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
    public function store(ChangeRequestStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Add ID validation for updates
            if ($request->has('id')) {
                $request->validate(['id' => 'exists:change_requests,id']);
                $validated['id'] = $request->id;
            }

            // If ID exists, update existing request
            if (!empty($validated['id'])) {
                return $this->update($request, $validated['id']);
            }

            // Create new change request
            $changeRequest = ChangeRequest::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'new_data' => $validated['new_data'],
                'status' => 'pending'
            ]);

            // Send notification to all admin users
            $adminUsers = User::where('is_admin', true)->get();
            Notification::send($adminUsers, new AdminChangeRequestNotification($changeRequest));

            // Clear relevant caches
            $this->cache->forgetChangeRequestCaches();

            return response()->json([
                'success' => true,
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
        // Check if user has permission to view this request
        if (!Auth::user()->is_admin && $changeRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('change-requests.show', compact('changeRequest'));
    }

    public function index(Request $request): View
    {
        try {
            $user = Auth::user();

            // Cache key based on user and filters
            $cacheKey = sprintf(
                'change_requests_user_%d_status_%s_search_%s_page_%s',
                $user->id,
                $request->input('status', 'all'),
                md5($request->input('search', '')),
                $request->input('page', 1)
            );

            // Build base query with eager loading
            $query = ChangeRequest::with(['user', 'comments'])
                ->withCount('comments')
                ->orderBy('created_at', 'desc');

            // If not admin, only show user's own requests
            if (!$user->is_admin) {
                $query->where('user_id', $user->id);
            }

            // Apply status filter
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Apply search
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Handle per page setting
            $perPage = $request->input('per_page', 10);
            if (!in_array($perPage, [10, 25, 50])) {
                $perPage = 10;
            }

            // Get paginated results
            $changeRequests = $query->paginate($perPage)->withQueryString();

            // Process tables affected for each request
            $changeRequests->each(function ($request) {
                $request->affected_tables = $this->getAffectedTables($request->new_data);
            });

            // Get incorporation summary for admin users
            $incorporationSummary = null;
            if ($user->is_admin) {
                $incorporationService = app(ChangeRequestIncorporationService::class);
                $incorporationSummary = $incorporationService->getIncorporationSummary();
            }

            return view('change-requests.index', [
                'changeRequests' => $changeRequests,
                'filters' => [
                    'status' => $request->status ?? 'all',
                    'search' => $request->search ?? '',
                    'per_page' => $perPage
                ],
                'incorporationSummary' => $incorporationSummary
            ]);
        } catch (\Exception $e) {
            Log::error('Error in change requests index: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get list of tables affected by a change request
     */
    private function getAffectedTables(?string $newData): array
    {
        try {
            if (empty($newData)) {
                return [];
            }

            $changes = json_decode($newData, true);
            $tables = [];

            // Check modifications
            if (!empty($changes['modifications'])) {
                $tables = array_merge($tables, array_keys($changes['modifications']));
            }

            // Check additions
            if (!empty($changes['additions'])) {
                foreach ($changes['additions'] as $key => $value) {
                    $table = explode('-', $key)[1] ?? '';
                    $table = explode('_', $table)[0];
                    if (!empty($table) && !in_array($table, $tables)) {
                        $tables[] = $table;
                    }
                }
            }

            // Check deletions
            if (!empty($changes['deletions'])) {
                foreach ($changes['deletions'] as $deletion) {
                    $table = explode('_', $deletion)[0];
                    if (!empty($table) && !in_array($table, $tables)) {
                        $tables[] = $table;
                    }
                }
            }

            return array_unique($tables);
        } catch (\Exception $e) {
            Log::error('Error parsing affected tables: ' . $e->getMessage());
            return [];
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

    public function storeComment(Request $request, ChangeRequest $changeRequest): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        try {
            $changeRequest->comments()->create([
                'content' => $validated['content'],
                'user_id' => Auth::id(),
            ]);

            return back()->with('status', 'Comment added successfully');
        } catch (Exception $e) {
            Log::error('Error saving comment: ' . $e->getMessage());
            return back()->with('error', 'Error adding comment');
        }
    }

    /**
     * Approve a change request
     */
    public function approve(Request $request, ChangeRequest $changeRequest): JsonResponse
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            return response()->json([
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Check if request can be approved
        if ($changeRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending requests can be approved.'
            ], 422);
        }

        try {
            // DB::transaction(function () use ($changeRequest) {
            $changeRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            //Send notification to the user
            $changeRequest->user->notify(new ChangeRequestStatusNotification(
                $changeRequest,
                'approved'
            ));
         // });



            return response()->json([
                'message' => 'Change request approved successfully.',
                'redirect' => route('change-requests.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error approving request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a change request
     */
    public function reject(Request $request, ChangeRequest $changeRequest): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'rejection_reason' => 'required|string|max:1000'
            ]);

            // Update change request
            $changeRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejectedby' => auth()->user()->name,
                'rejection_reason' => $request->rejection_reason
            ]);

            // Send notification to the creator
            $changeRequest->user->notify(new ChangeRequestStatusNotification($changeRequest));

            Log::info('Change request rejected', [
                'change_request_id' => $changeRequest->id,
                'rejected_by' => auth()->user()->name,
                'reason' => $request->rejection_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Change request rejected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting change request: ' . $e->getMessage(), [
                'change_request_id' => $changeRequest->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject change request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a change request as incorporated
     */
    public function markIncorporated(Request $request, ChangeRequest $changeRequest): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'incorporation_status' => 'required|in:pending,incorporated',
                'incorporated_by' => 'required_if:incorporation_status,incorporated|string|max:255',
                'incorporation_notes' => 'nullable|string|max:2000'
            ]);

        // Check if user is admin
            if (!auth()->user()->is_admin) {
            return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
            ], 403);
        }

            // Check if change request is approved
            if ($changeRequest->status !== 'approved') {
            return response()->json([
                    'success' => false,
                    'message' => 'Only approved change requests can be marked as incorporated'
                ], 400);
            }

            $updateData = [
                'incorporation_status' => $request->incorporation_status,
            ];

            if ($request->incorporation_status === 'incorporated') {
                $updateData['incorporated_at'] = now();
                $updateData['incorporated_by'] = $request->incorporated_by;
                $updateData['incorporation_notes'] = $request->incorporation_notes;
                $updateData['incorporation_details'] = [
                    'marked_via_ui' => true,
                    'timestamp' => now()->toISOString(),
                    'user_id' => auth()->id()
                ];
            } else {
                // Reset to pending
                $updateData['incorporated_at'] = null;
                $updateData['incorporated_by'] = null;
                $updateData['incorporation_notes'] = $request->incorporation_notes ?: 'Reset to pending';
                $updateData['incorporation_details'] = null;
            }

            $changeRequest->update($updateData);

            Log::info('Change request incorporation status updated', [
                'change_request_id' => $changeRequest->id,
                'status' => $request->incorporation_status,
                'updated_by' => auth()->user()->name
            ]);

            $message = $request->incorporation_status === 'incorporated'
                ? 'Change request marked as incorporated successfully'
                : 'Change request reset to pending incorporation';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating incorporation status: ' . $e->getMessage(), [
                'change_request_id' => $changeRequest->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update incorporation status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a single change request
     */
    public function verifyChanges(Request $request, ChangeRequest $changeRequest): JsonResponse
    {
        try {
            // Check if user is admin
            if (!auth()->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Check if change request is approved and incorporated
            if ($changeRequest->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved change requests can be verified'
                ], 400);
            }

            if ($changeRequest->incorporation_status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Change request must be marked as incorporated before verification'
                ], 400);
            }

            // Use the incorporation service to verify this specific change request
            $incorporationService = app(ChangeRequestIncorporationService::class);

            // Create a temporary collection with just this change request for verification
            $tempCollection = collect([$changeRequest]);

            $results = [
                'verified' => [],
                'missing' => [],
                'conflicted' => [],
                'total_checked' => 1
            ];

            try {
                $verificationResult = $this->verifyChangeRequest($changeRequest);

            $changeRequest->update([
                    'incorporation_status' => $verificationResult['status'],
                    'last_sync_verified_at' => now(),
                    'sync_verification_details' => $verificationResult['details']
                ]);

                $results[$verificationResult['status']][] = $changeRequest->id;

                Log::info('Change request verified', [
                    'change_request_id' => $changeRequest->id,
                    'verification_status' => $verificationResult['status'],
                    'verified_by' => auth()->user()->name
                ]);

            return response()->json([
                    'success' => true,
                    'message' => 'Verification completed successfully',
                    'verification_result' => [
                        'status' => $verificationResult['status'],
                        'details' => $verificationResult['details'],
                        'message' => $this->getVerificationMessage($verificationResult['status'])
                    ]
                ]);

        } catch (\Exception $e) {
                Log::error('Failed to verify change request', [
                    'change_request_id' => $changeRequest->id,
                    'error' => $e->getMessage()
                ]);

            return response()->json([
                    'success' => false,
                    'message' => 'Verification failed: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error in verify changes: ' . $e->getMessage(), [
                'change_request_id' => $changeRequest->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify changes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a single change request against current database
     */
    private function verifyChangeRequest(ChangeRequest $request): array
    {
        $changes = json_decode($request->new_data, true);
        $details = [
            'verification_date' => now()->toISOString(),
            'changes_verified' => [],
            'issues' => []
        ];

        $allVerified = true;
        $hasConflicts = false;

        // Verify modifications
        if (!empty($changes['modifications'])) {
            foreach ($changes['modifications'] as $table => $modifications) {
                foreach ($modifications as $recordKey => $newData) {
                    $verification = $this->verifyModification($table, $recordKey, $newData);
                    $details['changes_verified'][] = $verification;

                    if (!$verification['verified']) {
                        $allVerified = false;
                        if ($verification['type'] === 'conflict') {
                            $hasConflicts = true;
                        }
                    }
                }
            }
        }

        // Verify additions
        if (!empty($changes['additions'])) {
            foreach ($changes['additions'] as $recordKey => $newData) {
                $verification = $this->verifyAddition($recordKey, $newData);
                $details['changes_verified'][] = $verification;

                if (!$verification['verified']) {
                    $allVerified = false;
                }
            }
        }

        // Verify deletions
        if (!empty($changes['deletions'])) {
            foreach ($changes['deletions'] as $recordKey) {
                $verification = $this->verifyDeletion($recordKey);
                $details['changes_verified'][] = $verification;

                if (!$verification['verified']) {
                    $allVerified = false;
                }
            }
        }

        // Determine status
        $status = 'verified';
        if ($hasConflicts) {
            $status = 'conflicted';
        } elseif (!$allVerified) {
            $status = 'missing';
        }

        return [
            'status' => $status,
            'details' => $details
        ];
    }

    /**
     * Helper methods for verification
     */
    private function verifyModification(string $table, string $recordKey, array $newData): array
    {
        // Implementation similar to ChangeRequestIncorporationService
        $parts = explode('_', $recordKey);
        $tableName = $parts[0];
        $id = $parts[1] ?? null;

        if (!$id) {
            return [
                'type' => 'modification',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Invalid record key format'
            ];
        }

        try {
            $record = \DB::table($tableName)->where('id', $id)->first();

            if (!$record) {
                return [
                    'type' => 'modification',
                    'table' => $tableName,
                    'record_key' => $recordKey,
                    'verified' => false,
                    'reason' => 'Record not found'
                ];
            }

            // Check if all modified fields match expected values
            $mismatches = [];
            foreach ($newData as $field => $expectedValue) {
                $actualValue = $record->$field ?? null;

                if ((string)$actualValue !== (string)$expectedValue) {
                    $mismatches[] = [
                        'field' => $field,
                        'expected' => $expectedValue,
                        'actual' => $actualValue
                    ];
                }
            }

            return [
                'type' => 'modification',
                'table' => $tableName,
                'record_key' => $recordKey,
                'verified' => empty($mismatches),
                'reason' => empty($mismatches) ? 'All fields match expected values' : 'Field mismatches found',
                'mismatches' => $mismatches
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'modification',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    private function verifyAddition(string $recordKey, array $newData): array
    {
        // Extract table from record key
        $parts = explode('-', $recordKey);
        $table = $parts[1] ?? '';

        if (!$table) {
            return [
                'type' => 'addition',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Invalid record key format'
            ];
        }

        try {
            // Look for a record with matching data
            $query = \DB::table($table);

            foreach ($newData as $field => $value) {
                if ($field !== 'id') { // Skip ID field for additions
                    $query->where($field, $value);
                }
            }

            $exists = $query->exists();

            return [
                'type' => 'addition',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => $exists,
                'reason' => $exists ? 'Record found with matching data' : 'Record not found'
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'addition',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    private function verifyDeletion(string $recordKey): array
    {
        $parts = explode('_', $recordKey);
        $table = $parts[0];
        $id = $parts[1] ?? null;

        if (!$id) {
            return [
                'type' => 'deletion',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Invalid record key format'
            ];
        }

        try {
            $exists = \DB::table($table)->where('id', $id)->exists();

            return [
                'type' => 'deletion',
                'table' => $table,
                'record_key' => $recordKey,
                'verified' => !$exists, // Verified if record does NOT exist
                'reason' => $exists ? 'Record still exists (deletion not applied)' : 'Record successfully deleted'
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'deletion',
                'record_key' => $recordKey,
                'verified' => false,
                'reason' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    private function getVerificationMessage(string $status): string
    {
        return match($status) {
            'verified' => 'All changes have been successfully verified in the database.',
            'missing' => 'Some changes are missing from the database. You may need to re-incorporate them.',
            'conflicted' => 'Some changes conflict with current database values. Manual review required.',
            default => 'Verification completed with unknown status.'
        };
    }

    /**
     * Bulk mark all pending approved changes as incorporated
     */
    public function bulkMarkIncorporated(Request $request): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'incorporated_by' => 'required|string|max:255'
            ]);

            // Check if user is admin
            if (!auth()->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Get all pending approved change requests
            $pendingRequests = ChangeRequest::where('status', 'approved')
                ->where('incorporation_status', 'pending')
                ->get();

            if ($pendingRequests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending approved change requests found'
                ], 400);
            }

            $successCount = 0;
            $errors = [];

            foreach ($pendingRequests as $changeRequest) {
                try {
                    $changeRequest->update([
                        'incorporation_status' => 'incorporated',
                        'incorporated_at' => now(),
                        'incorporated_by' => $request->incorporated_by,
                        'incorporation_details' => [
                            'marked_via_bulk_ui' => true,
                            'timestamp' => now()->toISOString(),
                            'user_id' => auth()->id()
                        ]
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update change request #{$changeRequest->id}: " . $e->getMessage();
                }
            }

            Log::info('Bulk mark as incorporated completed', [
                'total_requests' => $pendingRequests->count(),
                'successful' => $successCount,
                'errors' => count($errors),
                'marked_by' => auth()->user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully marked {$successCount} change requests as incorporated",
                'count' => $successCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk mark incorporated: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark changes as incorporated: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk verify all incorporated changes
     */
    public function bulkVerifyChanges(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!auth()->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Use the incorporation service to verify all incorporated changes
            $incorporationService = app(ChangeRequestIncorporationService::class);
            $results = $incorporationService->verifyAgainstSync();

            Log::info('Bulk verification completed', [
                'total_checked' => $results['total_checked'],
                'verified' => count($results['verified']),
                'missing' => count($results['missing']),
                'conflicted' => count($results['conflicted']),
                'verified_by' => auth()->user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bulk verification completed successfully',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk verify changes: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify changes: ' . $e->getMessage()
            ], 500);
        }
    }
}
