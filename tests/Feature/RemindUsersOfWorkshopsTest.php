<?php

use App\Models\User;
use App\Models\Workshop;
use App\Mail\WorkshopReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('app:remind-users-of-workshops sends emails to confirmed users for tomorrow\'s workshops', function () {
    Mail::fake();

    // Workshop tomorrow (within 24h)
    $tomorrowWorkshop = Workshop::factory()->create([
        'title' => 'Tomorrow Workshop',
        'starts_at' => now()->addHours(12),
    ]);

    $confirmedUser = User::factory()->create();
    $waitlistedUser = User::factory()->create();

    $tomorrowWorkshop->users()->attach($confirmedUser->id, ['status' => 'confirmed']);
    $tomorrowWorkshop->users()->attach($waitlistedUser->id, ['status' => 'waitlisted']);

    $this->artisan('app:remind-users-of-workshops')
        ->assertExitCode(0);

    Mail::assertSent(WorkshopReminder::class, function ($mail) use ($confirmedUser, $tomorrowWorkshop) {
        return $mail->hasTo($confirmedUser->email) && $mail->workshop->id === $tomorrowWorkshop->id;
    });

    Mail::assertNotSent(WorkshopReminder::class, function ($mail) use ($waitlistedUser) {
        return $mail->hasTo($waitlistedUser->email);
    });
});

test('app:remind-users-of-workshops does not send emails for workshops outside the 24h window', function () {
    Mail::fake();

    // Workshop happening today (in 1 hour - technically within 24h, but we want to check boundaries)
    // Actually the command uses > now() and <= now()->addHours(24)
    
    // Workshop far in the future (2 days from now)
    $futureWorkshop = Workshop::factory()->create([
        'title' => 'Future Workshop',
        'starts_at' => now()->addDays(2),
    ]);

    // Workshop in the past
    $pastWorkshop = Workshop::factory()->create([
        'title' => 'Past Workshop',
        'starts_at' => now()->subHours(1),
    ]);

    $user = User::factory()->create();
    $futureWorkshop->users()->attach($user->id, ['status' => 'confirmed']);
    $pastWorkshop->users()->attach($user->id, ['status' => 'confirmed']);

    $this->artisan('app:remind-users-of-workshops')
        ->expectsOutput('No workshops starting within the next 24 hours.')
        ->assertExitCode(0);

    Mail::assertNothingSent();
});

test('app:remind-users-of-workshops respects dry-run flag', function () {
    Mail::fake();

    $workshop = Workshop::factory()->create([
        'starts_at' => now()->addHours(10),
    ]);
    $user = User::factory()->create();
    $workshop->users()->attach($user->id, ['status' => 'confirmed']);

    $this->artisan('app:remind-users-of-workshops --dry-run')
        ->expectsOutputToContain("Dry run: Would send reminder to {$user->email}")
        ->assertExitCode(0);

    Mail::assertNothingSent();
});
