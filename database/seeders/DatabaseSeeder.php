<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin Users
        User::factory()->admin()->create([
            'name' => 'Hasan Uj Jaman',
            'email' => 'admin@intercourse.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->admin()->create([
            'name' => 'System Administrator',
            'email' => 'system@intercourse.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->employee()->create([
            'name' => 'Employee',
            'email' => 'employee@intercourse.com',
            'password' => bcrypt('password'),
        ]);

        // Create Employee Users
        $employees = [
            ['name' => 'Alice Johnson', 'email' => 'alice@intercourse.com'],
            ['name' => 'Bob Smith', 'email' => 'bob@intercourse.com'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@intercourse.com'],
            ['name' => 'Diana Prince', 'email' => 'diana@intercourse.com'],
            ['name' => 'Ethan Hunt', 'email' => 'ethant@intercourse.com'],
            ['name' => 'Fiona Gallagher', 'email' => 'fiona@intercourse.com'],
            ['name' => 'George Costanza', 'email' => 'george@intercourse.com'],
            ['name' => 'Hannah Baker', 'email' => 'hannah@intercourse.com'],
            ['name' => 'Ian Malcolm', 'email' => 'ian@intercourse.com'],
            ['name' => 'Jane Doe', 'email' => 'jane@intercourse.com'],
        ];

        foreach ($employees as $employee) {
            User::factory()->employee()->create($employee);
        }

        // Create more random employees
        User::factory(10)->employee()->create();

        // Seed Workshops
        $this->call([
            WorkshopSeeder::class,
        ]);

        // Randomly register employees for workshops
        $workshops = \App\Models\Workshop::all();
        $allEmployees = User::where('role', 'employee')->get();
        $registrationService = app(\App\Services\RegistrationService::class);

        foreach ($allEmployees as $employee) {
            // Each employee registers for 2-5 random workshops
            $randomWorkshops = $workshops->random(rand(2, 5));

            foreach ($randomWorkshops as $workshop) {
                try {
                    $registrationService->register($employee, $workshop);
                } catch (\Exception $e) {
                    // Skip if registration fails (e.g., overlapping workshops)
                }
            }
        }
    }
}
