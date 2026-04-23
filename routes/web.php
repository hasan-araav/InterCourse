<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\WorkshopController;
use App\Models\Workshop;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => false,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function (Request $request) {
    $workshops = Workshop::withCount(['users as confirmed_count' => function ($query) {
        $query->where('status', 'confirmed');
    }])
    ->where('starts_at', '>', now())
    ->orderBy('starts_at')
    ->get()
    ->map(function ($workshop) use ($request) {
        $registration = $request->user()->workshops()->where('workshop_id', $workshop->id)->first();

        return [
            'id' => $workshop->id,
            'title' => $workshop->title,
            'description' => $workshop->description,
            'starts_at' => $workshop->starts_at,
            'duration_minutes' => $workshop->duration_minutes,
            'capacity' => $workshop->capacity,
            'confirmed_count' => $workshop->confirmed_count,
            'remaining_seats' => max(0, $workshop->capacity - $workshop->confirmed_count),
            'user_registration' => $registration ? [
                'status' => $registration->pivot->status,
                'position' => $registration->pivot->position,
            ] : null,
        ];
    });

    return Inertia::render('Dashboard', [
        'workshops' => $workshops,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/my-schedule', function (Request $request) {
    $registrations = $request->user()->workshops()
        ->withCount(['users as confirmed_count' => function ($query) {
            $query->where('status', 'confirmed');
        }])
        ->orderBy('starts_at')
        ->get()
        ->map(function ($workshop) {
            return [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'description' => $workshop->description,
                'starts_at' => $workshop->starts_at,
                'duration_minutes' => $workshop->duration_minutes,
                'capacity' => $workshop->capacity,
                'confirmed_count' => $workshop->confirmed_count,
                'status' => $workshop->pivot->status,
                'position' => $workshop->pivot->position,
            ];
        });

    return Inertia::render('Employee/MySchedule', [
        'registrations' => $registrations,
    ]);
})->middleware(['auth', 'verified'])->name('my-schedule');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('workshops', WorkshopController::class);
});

Route::middleware('auth')->group(function () {
    Route::post('/workshops/{workshop}/register', [RegistrationController::class, 'store'])->name('workshops.register');
    Route::delete('/workshops/{workshop}/cancel', [RegistrationController::class, 'destroy'])->name('workshops.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
