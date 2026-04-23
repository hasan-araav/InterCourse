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
