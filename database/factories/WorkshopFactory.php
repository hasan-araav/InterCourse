<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workshops = [
            ['title' => 'Mastering Laravel 11', 'description' => 'Deep dive into the latest features of Laravel 11, including the streamlined directory structure and new testing tools.'],
            ['title' => 'Vue 3 Composition API', 'description' => 'Learn how to build scalable frontend applications using Vue 3 and the Composition API.'],
            ['title' => 'Advanced CSS Grid & Flexbox', 'description' => 'Master modern CSS layouts and build complex, responsive user interfaces with ease.'],
            ['title' => 'Node.js Performance Tuning', 'description' => 'Techniques for profiling and optimizing your Node.js applications for production environments.'],
            ['title' => 'Docker for Developers', 'description' => 'Everything you need to know about containerizing your development workflow and deploying with Docker.'],
            ['title' => 'Agile Project Management', 'description' => 'Practical strategies for implementing Agile and Scrum in your development team.'],
            ['title' => 'Public Speaking for Engineers', 'description' => 'Improve your communication skills and learn how to present technical topics to non-technical audiences.'],
            ['title' => 'Cybersecurity Best Practices', 'description' => 'Essential security concepts every developer should know to protect their applications from common threats.'],
            ['title' => 'Building Real-time Apps with WebSockets', 'description' => 'A hands-on guide to implementing real-time features using Laravel Reverb and Echo.'],
            ['title' => 'Microservices Architecture', 'description' => 'Understanding the pros and cons of microservices and how to design distributed systems.'],
        ];

        $workshop = fake()->randomElement($workshops);

        $coverImages = [
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1200&auto=format&fit=crop', // Code
            'https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=1200&auto=format&fit=crop', // Laptop
            'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1200&auto=format&fit=crop', // Workspace
            'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1200&auto=format&fit=crop', // Code on screen
            'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop', // Digital
            'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=1200&auto=format&fit=crop', // Team
        ];

        return [
            'title' => $workshop['title'],
            'description' => $workshop['description'],
            'starts_at' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'duration_minutes' => fake()->randomElement([60, 90, 120, 180, 240]),
            'capacity' => fake()->numberBetween(10, 50),
            'speaker_name' => fake()->name(),
            'speaker_bio' => fake()->paragraph(),
            'cover_photo_path' => fake()->randomElement($coverImages),
            'speaker_photo_path' => 'https://i.pravatar.cc/300?u=' . fake()->unique()->safeEmail(),
        ];
    }
}
