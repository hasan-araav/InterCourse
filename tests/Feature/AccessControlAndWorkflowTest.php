<?php

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Employees receive a 403 Forbidden when hitting admin routes', function () {
    $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);

    $this->actingAs($employee)
        ->get(route('admin.dashboard'))
        ->assertStatus(403);

    $this->actingAs($employee)
        ->get(route('admin.workshops.index'))
        ->assertStatus(403);
});

test('Employee can register for a workshop and see DB changes', function () {
    $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
    $workshop = Workshop::factory()->create(['capacity' => 10]);

    $this->actingAs($employee)
        ->post(route('workshops.register', $workshop))
        ->assertRedirect();

    $this->assertDatabaseHas('registrations', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
        'status' => 'confirmed',
    ]);
});

test('Employee can cancel a registration and see DB changes', function () {
    $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
    $workshop = Workshop::factory()->create();

    // Register first
    $employee->workshops()->attach($workshop->id, ['status' => 'confirmed']);

    $this->actingAs($employee)
        ->delete(route('workshops.cancel', $workshop))
        ->assertRedirect();

    $this->assertDatabaseMissing('registrations', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
    ]);
});

test('Registration returns 422 error when trying to double-book overlapping workshops', function () {
    $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
    $startTime = now()->addDay()->setTime(10, 0, 0);

    $workshop1 = Workshop::factory()->create([
        'starts_at' => $startTime,
        'duration_minutes' => 60,
    ]);

    $workshop2 = Workshop::factory()->create([
        'starts_at' => $startTime->copy()->addMinutes(30), // Overlaps
        'duration_minutes' => 60,
    ]);

    // Register for the first one
    $employee->workshops()->attach($workshop1->id, ['status' => 'confirmed']);

    // Attempt to register for the overlapping one via HTTP
    $this->actingAs($employee)
        ->post(route('workshops.register', $workshop2))
        ->assertSessionHasErrors();
});
