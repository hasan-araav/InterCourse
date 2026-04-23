<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RegistrationService();
    }

    public function test_user_can_register_for_workshop(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->create(['capacity' => 5]);

        $registration = $this->service->register($user, $workshop);

        $this->assertEquals('confirmed', $registration->status);
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_user_is_waitlisted_when_capacity_is_full(): void
    {
        $workshop = Workshop::factory()->create(['capacity' => 1]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->service->register($user1, $workshop);
        $registration2 = $this->service->register($user2, $workshop);

        $this->assertEquals('waitlisted', $registration2->status);
        $this->assertEquals(1, $registration2->position);
    }

    public function test_user_cannot_register_for_overlapping_workshops(): void
    {
        $user = User::factory()->create();
        $workshop1 = Workshop::factory()->create([
            'starts_at' => now()->addDay()->setTime(10, 0),
            'duration_minutes' => 60,
        ]);
        $workshop2 = Workshop::factory()->create([
            'starts_at' => now()->addDay()->setTime(10, 30),
            'duration_minutes' => 60,
        ]);

        $this->service->register($user, $workshop1);

        $this->expectException(ValidationException::class);
        $this->service->register($user, $workshop2);
    }

    public function test_cancelling_confirmed_registration_promotes_waitlisted_user(): void
    {
        $workshop = Workshop::factory()->create(['capacity' => 1]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->service->register($user1, $workshop);
        $this->service->register($user2, $workshop);

        $this->service->cancel($user1, $workshop);

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user2->id,
            'workshop_id' => $workshop->id,
            'status' => 'confirmed',
            'position' => null,
        ]);
    }

    public function test_cancelling_waitlisted_registration_reorders_remaining_waitlist(): void
    {
        $workshop = Workshop::factory()->create(['capacity' => 1]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();

        $this->service->register($user1, $workshop); // confirmed
        $this->service->register($user2, $workshop); // waitlisted 1
        $this->service->register($user3, $workshop); // waitlisted 2
        $this->service->register($user4, $workshop); // waitlisted 3

        $this->service->cancel($user2, $workshop);

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user3->id,
            'workshop_id' => $workshop->id,
            'status' => 'waitlisted',
            'position' => 1,
        ]);

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user4->id,
            'workshop_id' => $workshop->id,
            'status' => 'waitlisted',
            'position' => 2,
        ]);
    }
}
