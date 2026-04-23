<?php

use App\Models\User;
use App\Models\Workshop;
use App\Notifications\PromotedFromWaitlist;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new RegistrationService();
});

test('Happy Path: Employee joins available workshop', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['capacity' => 10]);

    $registration = $this->service->register($user, $workshop);

    expect($registration->status)->toBe('confirmed');
    $this->assertDatabaseHas('registrations', [
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
        'status' => 'confirmed',
    ]);
});

test('Full Capacity: Ensure user is placed on waitlist', function () {
    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->service->register($user1, $workshop); // Occupy the only spot
    $registration2 = $this->service->register($user2, $workshop);

    expect($registration2->status)->toBe('waitlisted');
    expect($registration2->position)->toBe(1);
    $this->assertDatabaseHas('registrations', [
        'user_id' => $user2->id,
        'workshop_id' => $workshop->id,
        'status' => 'waitlisted',
        'position' => 1,
    ]);
});

test('No-Ubiquity Rule: Ensure overlap throws exception', function () {
    $user = User::factory()->create();
    $startTime = now()->addDay()->setTime(14, 0);

    $workshop1 = Workshop::factory()->create([
        'starts_at' => $startTime,
        'duration_minutes' => 60, // Ends at 15:00
    ]);

    $workshop2 = Workshop::factory()->create([
        'starts_at' => $startTime->copy()->addMinutes(30), // Starts at 14:30
        'duration_minutes' => 60,
    ]);

    $this->service->register($user, $workshop1);

    expect(fn() => $this->service->register($user, $workshop2))
        ->toThrow(ValidationException::class);
});

test('FIFO Promotion sends WebPush notification', function () {
    Notification::fake();

    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $userConfirmed = User::factory()->create();
    $userWaitlisted = User::factory()->create();

    $this->service->register($userConfirmed, $workshop);
    $this->service->register($userWaitlisted, $workshop);

    $this->service->cancel($userConfirmed, $workshop);

    Notification::assertSentTo(
        $userWaitlisted,
        PromotedFromWaitlist::class,
        function ($notification) use ($workshop) {
            return $notification->workshop->id === $workshop->id;
        }
    );
});

test('FIFO Promotion: Canceling a spot moves the first waitlisted user to confirmed', function () {
    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $userConfirmed = User::factory()->create();
    $userWaitlisted = User::factory()->create();

    $this->service->register($userConfirmed, $workshop);
    $this->service->register($userWaitlisted, $workshop); // Should be position 1

    $this->service->cancel($userConfirmed, $workshop);

    $this->assertDatabaseHas('registrations', [
        'user_id' => $userWaitlisted->id,
        'workshop_id' => $workshop->id,
        'status' => 'confirmed',
        'position' => null,
    ]);
});
