<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profilePhotos = [
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1554151228-14d9def656e4?q=80&w=256&h=256&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1527980965255-d3b416303d12?q=80&w=256&h=256&auto=format&fit=crop',
        ];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_EMPLOYEE,
            'photo_path' => fake()->randomElement($profilePhotos),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Indicate that the user is an employee.
     */
    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_EMPLOYEE,
        ]);
    }
}
