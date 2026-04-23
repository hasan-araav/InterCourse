<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'photo_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_EMPLOYEE = 'employee';

    /**
     * Get the user's photo URL.
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo_path) {
            return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=6366f1&color=fff";
        }

        return str_starts_with($this->photo_path, 'http')
            ? $this->photo_path
            : asset('storage/' . $this->photo_path);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['photo_url'];

    public function workshops()
    {
        return $this->belongsToMany(Workshop::class, 'registrations')
            ->withPivot('status', 'position')
            ->withTimestamps();
    }
}
