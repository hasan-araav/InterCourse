<?php

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('availableSeats returns correct integer', function () {
    $workshop = Workshop::factory()->create(['capacity' => 5]);
    $users = User::factory(2)->create();

    foreach ($users as $user) {
        $workshop->users()->attach($user->id, ['status' => 'confirmed']);
    }

    expect($workshop->availableSeats())->toBe(3);
});

test('isFull returns true when capacity is reached', function () {
    $workshop = Workshop::factory()->create(['capacity' => 2]);
    $users = User::factory(2)->create();

    foreach ($users as $user) {
        $workshop->users()->attach($user->id, ['status' => 'confirmed']);
    }

    expect($workshop->isFull())->toBeTrue();
});

test('isFull returns false when capacity is not reached', function () {
    $workshop = Workshop::factory()->create(['capacity' => 5]);
    $user = User::factory()->create();

    $workshop->users()->attach($user->id, ['status' => 'confirmed']);

    expect($workshop->isFull())->toBeFalse();
});
