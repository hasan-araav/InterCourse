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
        // Define some specific workshops to ensure variety
        $specificWorkshops = [
            [
                'title' => 'Mastering Laravel 11',
                'description' => 'Deep dive into the latest features of Laravel 11, including the streamlined directory structure and new testing tools.',
                'capacity' => 20,
                'speaker_name' => 'Taylor Otwell',
                'speaker_bio' => 'The creator of the Laravel framework.',
                'cover_photo_path' => 'https://images.unsplash.com/photo-1599507593499-a3f7f7d9a2cc?q=80&w=1200&auto=format&fit=crop',
                'speaker_photo_path' => 'https://unavatar.io/twitter/taylorotwell',
            ],
            [
                'title' => 'Vue 3 Composition API',
                'description' => 'Learn how to build scalable frontend applications using Vue 3 and the Composition API.',
                'capacity' => 15,
                'speaker_name' => 'Evan You',
                'speaker_bio' => 'The creator of the Vue.js framework.',
                'cover_photo_path' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1200&auto=format&fit=crop',
                'speaker_photo_path' => 'https://unavatar.io/twitter/youyuxi',
            ],
            [
                'title' => 'Advanced CSS Grid & Flexbox',
                'description' => 'Master modern CSS layouts and build complex, responsive user interfaces with ease.',
                'capacity' => 25,
                'speaker_name' => 'Jen Simmons',
                'speaker_bio' => 'Graphic designer, web developer, and educator.',
                'cover_photo_path' => 'https://images.unsplash.com/photo-1507721999472-8ed4421c4af2?q=80&w=1200&auto=format&fit=crop',
                'speaker_photo_path' => 'https://unavatar.io/twitter/jensimmons',
            ],
        ];

        foreach ($specificWorkshops as $workshop) {
            \App\Models\Workshop::factory()->create(array_merge($workshop, [
                'starts_at' => now()->addDays(rand(1, 14)),
            ]));
        }

        // 5 workshops starting in the next 24 hours
        \App\Models\Workshop::factory(5)->create([
            'starts_at' => now()->addHours(rand(1, 23)),
        ]);

        // 10 workshops starting later
        \App\Models\Workshop::factory(10)->create([
            'starts_at' => now()->addDays(rand(15, 60)),
        ]);
    }
}
