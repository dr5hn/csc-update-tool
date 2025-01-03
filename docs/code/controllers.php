<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChangeRequest;
use App\Models\ChangeRequestComment;
use App\Services\ChangeRequestService;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChangeRequestController extends Controller
{
    protected $changeRequestService;
    protected $validationService;

    public function __construct(
        ChangeRequestService $changeRequestService,
        ValidationService $validationService
    ) {
        $this->changeRequestService = $changeRequestService;
        $this->validationService = $validationService;
        
        $this->middleware('permission:view-change-requests')->only(['index', 'show']);
        $this->middleware('permission:create-change-requests')->only(['create', 'store']);
        $this->middleware('permission:manage-change-requests')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $query = ChangeRequest::with(['user', 'approver'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->table, function ($query, $table) {
                return $query->where('table_name', $table);
            })
            ->when(!Auth::user()->hasRole('admin'), function ($query) {
                return $query->where('user_id', Auth::id());
            });

        $changeRequests = $query->latest()->paginate(config('change-request.pagination.per_page'));

        return view('change-requests.index', compact('changeRequests'));
    }

    public function create()
    {
        $tables = config('change-request.tables');
        $changeTypes = config('change-request.change_types');
        
        return view('change-requests.create', compact('tables', 'changeTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'table_name' => 'required|string|in:' . implode(',', array_keys(config('change-request.tables'))),
            'change_type' => 'required|string|in:' . implode(',', array_keys(config('change-request.change_types'))),
            'new_data' => 'required|array',
            'original_data' => 'nullable|array'
        ]);

        try {
            // Validate the data structure against the table schema
            $this->validationService->validateChangeRequest($validated['new_data'], $validated['table_name']);
            
            $changeRequest = $this->changeRequestService->create($validated);
            
            return redirect()
                ->route('change-requests.show', $changeRequest)
                ->with('success', 'Change request created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['user', 'approver', 'comments.user']);
        
        return view('change-requests.show', compact('changeRequest'));
    }

    public function approve(ChangeRequest $changeRequest)
    {
        try {
            $this->changeRequestService->approve($changeRequest);
            
            return redirect()
                ->route('change-requests.show', $changeRequest)
                ->with('success', 'Change request approved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, ChangeRequest $changeRequest)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10'
        ]);

        try {
            $this->changeRequestService->reject($changeRequest, $validated['reason']);
            
            return redirect()
                ->route('change-requests.show', $changeRequest)
                ->with('success', 'Change request rejected successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

class ChangeRequestCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:comment-on-change-requests');
    }

    public function store(Request $request, ChangeRequest $changeRequest)
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:5'
        ]);

        $comment = $changeRequest->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment']
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Comment added successfully',
                'comment' => $comment->load('user')
            ]);
        }

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy(ChangeRequest $changeRequest, ChangeRequestComment $comment)
    {
        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Comment deleted successfully']);
        }

        return back()->with('success', 'Comment deleted successfully.');
    }
}

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 3600, function () {
            return [
                'pending_requests' => ChangeRequest::pending()->count(),
                'approved_requests' => ChangeRequest::approved()->count(),
                'rejected_requests' => ChangeRequest::rejected()->count(),
                'total_requests' => ChangeRequest::count(),
                'recent_activities' => ChangeRequest::with(['user'])
                    ->latest()
                    ->take(5)
                    ->get()
            ];
        });

        return view('dashboard', compact('stats'));
    }
}

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $user = Auth::user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (isset($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
