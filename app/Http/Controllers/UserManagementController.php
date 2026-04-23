<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with stats and filters.
     */
    public function index(Request $request): Response
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(10)->withQueryString()->through(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'photo' => $user->photo_url,
            'created_at' => $user->created_at->format('M d, Y'),
        ]);

        // Stats
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'employees' => User::where('role', User::ROLE_EMPLOYEE)->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'sort', 'direction']),
            'stats' => $stats,
        ]);
    }

    /**
     * Display user details.
     */
    public function show(User $user): Response
    {
        $user->load(['workshops' => function ($query) {
            $query->withCount(['users as confirmed_count' => function ($q) {
                $q->where('status', 'confirmed');
            }]);
        }]);

        $registrations = $user->workshops->map(function ($workshop) {
            return [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'starts_at' => $workshop->starts_at->format('M d, Y H:i'),
                'status' => $workshop->pivot->status,
                'position' => $workshop->pivot->position,
                'is_past' => $workshop->starts_at->isPast(),
            ];
        });

        $metrics = [
            'total_registrations' => $user->workshops()->count(),
            'confirmed_workshops' => $user->workshops()->wherePivot('status', 'confirmed')->count(),
            'waitlisted_workshops' => $user->workshops()->wherePivot('status', 'waitlisted')->count(),
            'attendance_rate' => $user->workshops()->where('starts_at', '<', now())->count() > 0
                ? round(($user->workshops()->where('starts_at', '<', now())->wherePivot('status', 'confirmed')->count() / $user->workshops()->where('starts_at', '<', now())->count()) * 100, 2)
                : 0,
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'photo' => $user->photo_url,
                'created_at' => $user->created_at->format('M d, Y'),
            ],
            'registrations' => $registrations,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,employee',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user-photos', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'photo_path' => $photoPath,
        ]);

        event(new Registered($user));

        return redirect()->back()->with('message', 'User created successfully.');
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class.',email,'.$user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,employee',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('message', 'User updated successfully.');
    }

    /**
     * Remove a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('message', 'User deleted successfully.');
    }
}
