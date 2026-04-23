<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    /**
     * Display the specified workshop for the employee.
     */
    public function show(Request $request, Workshop $workshop): \Inertia\Response
    {
        $workshop->load(['users' => function ($query) {
            $query->withPivot('status', 'position', 'created_at');
        }]);

        $userRegistration = $workshop->users()
            ->where('user_id', $request->user()->id)
            ->first();

        return \Inertia\Inertia::render('Employee/WorkshopDetails', [
            'workshop' => [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'description' => $workshop->description,
                'starts_at' => $workshop->starts_at->format('M d, Y H:i'),
                'duration_minutes' => $workshop->duration_minutes,
                'capacity' => $workshop->capacity,
                'cover_photo_url' => $workshop->cover_photo_url,
                'speaker_name' => $workshop->speaker_name,
                'speaker_bio' => $workshop->speaker_bio,
                'speaker_photo_url' => $workshop->speaker_photo_url,
                'remaining_seats' => $workshop->availableSeats(),
                'confirmed_count' => $workshop->confirmedUsers()->count(),
                'user_registration' => $userRegistration ? [
                    'status' => $userRegistration->pivot->status,
                    'position' => $userRegistration->pivot->position,
                ] : null,
            ],
        ]);
    }

    /**
     * Register the authenticated user for a workshop.
     */
    public function store(Request $request, Workshop $workshop): RedirectResponse
    {
        try {
            $registration = $this->registrationService->register($request->user(), $workshop);

            $message = $registration->status === 'confirmed'
                ? 'Successfully registered for the workshop.'
                : 'Workshop is full. You have been added to the waitlist.';

            return back()->with('message', $message);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Cancel the authenticated user's registration for a workshop.
     */
    public function destroy(Request $request, Workshop $workshop): RedirectResponse
    {
        $this->registrationService->cancel($request->user(), $workshop);

        return back()->with('message', 'Registration cancelled successfully.');
    }
}
