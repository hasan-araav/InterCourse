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
        return $this->cover_photo_path
            ? asset('storage/' . $this->cover_photo_path)
            : "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070&auto=format&fit=crop";
    }

    /**
     * Get the speaker's photo URL.
     *
     * @return string
     */
    public function getSpeakerPhotoUrlAttribute(): string
    {
        return $this->speaker_photo_path
            ? asset('storage/' . $this->speaker_photo_path)
            : "https://ui-avatars.com/api/?name=" . urlencode($this->speaker_name ?? 'Speaker') . "&background=6366f1&color=fff";
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
