<?php

namespace App\Services;

use App\Events\RegistrationUpdated;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    /**
     * Register a user for a workshop.
     */
    public function register(User $user, Workshop $workshop): Registration
    {
        $registration = DB::transaction(function () use ($user, $workshop) {
            // 1. Check for existing registration
            $existing = Registration::where('user_id', $user->id)
                ->where('workshop_id', $workshop->id)
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'workshop' => 'You are already registered for this workshop.',
                ]);
            }

            // 2. Check for time overlaps (No-Ubiquity Rule)
            $this->checkTimeOverlap($user, $workshop);

            // 3. Determine status based on current capacity
            $confirmedCount = Registration::where('workshop_id', $workshop->id)
                ->confirmed()
                ->count();

            $status = 'confirmed';
            $position = null;

            if ($confirmedCount >= $workshop->capacity) {
                $status = 'waitlisted';
                // Position is based on current waitlist count
                $position = Registration::where('workshop_id', $workshop->id)
                    ->waitlisted()
                    ->count() + 1;
            }

            return Registration::create([
                'user_id' => $user->id,
                'workshop_id' => $workshop->id,
                'status' => $status,
                'position' => $position,
            ]);
        });

        RegistrationUpdated::dispatch($workshop);

        return $registration;
    }

    /**
     * Cancel a user's registration and promote the next person in waitlist.
     */
    public function cancel(User $user, Workshop $workshop): void
    {
        DB::transaction(function () use ($user, $workshop) {
            $registration = Registration::where('user_id', $user->id)
                ->where('workshop_id', $workshop->id)
                ->first();

            if (!$registration) {
                return;
            }

            $wasConfirmed = $registration->status === 'confirmed';
            $registration->delete();

            // If a confirmed registration was cancelled, promote the first person in waitlist
            if ($wasConfirmed) {
                $this->promoteNextWaitlistedUser($workshop);
            } else {
                // If a waitlisted registration was cancelled, reorder positions
                $this->reorderWaitlist($workshop);
            }
        });

        RegistrationUpdated::dispatch($workshop);
    }

    /**
     * Check if the user has any overlapping workshops.
     */
    protected function checkTimeOverlap(User $user, Workshop $workshop): void
    {
        $workshopStart = $workshop->starts_at;
        $workshopEnd = $workshop->starts_at->copy()->addMinutes($workshop->duration_minutes);

        $overlap = Registration::where('user_id', $user->id)
            ->whereHas('workshop', function ($query) use ($workshopStart, $workshopEnd) {
                $query->where(function ($q) use ($workshopStart, $workshopEnd) {
                    // New workshop starts during an existing one
                    $q->where('starts_at', '<', $workshopEnd)
                      ->whereRaw('datetime(starts_at, "+" || duration_minutes || " minutes") > ?', [$workshopStart->toDateTimeString()]);
                });
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'workshop' => 'This workshop overlaps with another one you are registered for.',
            ]);
        }
    }

    /**
     * Promote the first waitlisted user to confirmed status.
     */
    protected function promoteNextWaitlistedUser(Workshop $workshop): void
    {
        $nextInLine = Registration::where('workshop_id', $workshop->id)
            ->waitlisted()
            ->orderBy('position')
            ->first();

        if ($nextInLine) {
            $nextInLine->update([
                'status' => 'confirmed',
                'position' => null,
            ]);

            // After promoting, reorder the remaining waitlist
            $this->reorderWaitlist($workshop);
        }
    }

    /**
     * Reorder waitlist positions to ensure they are sequential (FIFO).
     */
    protected function reorderWaitlist(Workshop $workshop): void
    {
        $waitlisted = Registration::where('workshop_id', $workshop->id)
            ->waitlisted()
            ->orderBy('position')
            ->get();

        foreach ($waitlisted as $index => $registration) {
            $registration->update(['position' => $index + 1]);
        }
    }
}
