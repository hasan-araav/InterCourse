<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Workshop::query()->withCount([
            'users as confirmed_count' => fn($q) => $q->where('status', 'confirmed'),
            'users as waitlist_count' => fn($q) => $q->where('status', 'waitlisted'),
        ]);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortField = $request->input('sort', 'starts_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $workshops = $query->paginate(10)->withQueryString()->through(fn ($workshop) => [
            'id' => $workshop->id,
            'title' => $workshop->title,
            'description' => $workshop->description,
            'starts_at' => $workshop->starts_at->format('M d, Y H:i'),
            'duration_minutes' => $workshop->duration_minutes,
            'capacity' => $workshop->capacity,
            'confirmed_count' => $workshop->confirmed_count,
            'waitlist_count' => $workshop->waitlist_count,
            'cover_photo' => $workshop->cover_photo_url,
            'is_past' => $workshop->starts_at->isPast(),
        ]);

        // Stats
        $stats = [
            'total_workshops' => Workshop::count(),
            'upcoming' => Workshop::where('starts_at', '>', now())->count(),
            'total_confirmed' => \App\Models\Registration::where('status', 'confirmed')->count(),
            'avg_fill_rate' => Workshop::count() > 0
                ? round(Workshop::all()->avg(fn($w) => ($w->confirmedUsers()->count() / max(1, $w->capacity)) * 100), 1)
                : 0,
        ];

        return Inertia::render('Workshops/Index', [
            'workshops' => $workshops,
            'filters' => $request->only(['search', 'sort', 'direction']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.workshops.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo_path'] = $request->file('cover_photo')->store('workshop-covers', 'public');
        }

        if ($request->hasFile('speaker_photo')) {
            $validated['speaker_photo_path'] = $request->file('speaker_photo')->store('speaker-photos', 'public');
        }

        Workshop::create($validated);

        return redirect()->route('admin.workshops.index')
            ->with('message', 'Workshop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop): Response
    {
        $workshop->load(['users' => function ($query) {
            $query->withPivot('status', 'position', 'created_at')
                  ->orderBy('registrations.created_at');
        }]);

        return Inertia::render('Workshops/Show', [
            'workshop' => [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'description' => $workshop->description,
                'starts_at' => $workshop->starts_at->format('M d, Y H:i'),
                'duration_minutes' => $workshop->duration_minutes,
                'capacity' => $workshop->capacity,
                'cover_photo' => $workshop->cover_photo_url,
                'speaker_name' => $workshop->speaker_name,
                'speaker_bio' => $workshop->speaker_bio,
                'speaker_photo' => $workshop->speaker_photo_url,
                'attendees' => $workshop->users->where('pivot.status', 'confirmed')->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'photo' => $user->photo_url,
                        'registered_at' => $user->pivot->created_at->format('M d, Y H:i'),
                    ];
                })->values(),
                'waitlist' => $workshop->users->where('pivot.status', 'waitlisted')->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'photo' => $user->photo_url,
                        'position' => $user->pivot->position,
                        'registered_at' => $user->pivot->created_at->format('M d, Y H:i'),
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workshop $workshop): RedirectResponse
    {
        return redirect()->route('admin.workshops.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_photo')) {
            if ($workshop->cover_photo_path) {
                Storage::disk('public')->delete($workshop->cover_photo_path);
            }
            $validated['cover_photo_path'] = $request->file('cover_photo')->store('workshop-covers', 'public');
        }

        if ($request->hasFile('speaker_photo')) {
            if ($workshop->speaker_photo_path) {
                Storage::disk('public')->delete($workshop->speaker_photo_path);
            }
            $validated['speaker_photo_path'] = $request->file('speaker_photo')->store('speaker-photos', 'public');
        }

        $workshop->update($validated);

        return redirect()->route('admin.workshops.index')
            ->with('message', 'Workshop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop): RedirectResponse
    {
        if ($workshop->cover_photo_path) {
            Storage::disk('public')->delete($workshop->cover_photo_path);
        }

        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('message', 'Workshop deleted successfully.');
    }
}
