<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    /** @use HasFactory<\Database\Factories\WorkshopFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'duration_minutes',
        'capacity',
        'cover_photo_path',
        'speaker_name',
        'speaker_bio',
        'speaker_photo_path',
    ];

    /**
     * Get the workshop's cover photo URL.
     *
     * @return string
     */
    public function getCoverPhotoUrlAttribute(): string
    {
        if (!$this->cover_photo_path) {
            return "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070&auto=format&fit=crop";
        }

        return str_starts_with($this->cover_photo_path, 'http')
            ? $this->cover_photo_path
            : asset('storage/' . $this->cover_photo_path);
    }

    /**
     * Get the speaker's photo URL.
     *
     * @return string
     */
    public function getSpeakerPhotoUrlAttribute(): string
    {
        if (!$this->speaker_photo_path) {
            return "https://ui-avatars.com/api/?name=" . urlencode($this->speaker_name ?? 'Speaker') . "&background=6366f1&color=fff";
        }

        return str_starts_with($this->speaker_photo_path, 'http')
            ? $this->speaker_photo_path
            : asset('storage/' . $this->speaker_photo_path);
    }

    protected $appends = ['cover_photo_url', 'speaker_photo_url'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'duration_minutes' => 'integer',
            'capacity' => 'integer',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'registrations')
            ->withPivot('status', 'position')
            ->withTimestamps();
    }

    public function confirmedUsers()
    {
        return $this->users()->wherePivot('status', 'confirmed');
    }

    public function availableSeats(): int
    {
        return max(0, $this->capacity - $this->confirmedUsers()->count());
    }

    public function isFull(): bool
    {
        return $this->availableSeats() <= 0;
    }
}
