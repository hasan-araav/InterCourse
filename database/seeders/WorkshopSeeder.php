<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 5 workshops starting in the next 24 hours
        \App\Models\Workshop::factory(5)->create([
            'starts_at' => now()->addHours(rand(1, 23)),
        ]);

        // 5 workshops starting later
        \App\Models\Workshop::factory(5)->create([
            'starts_at' => now()->addDays(rand(2, 30)),
        ]);
    }
}
