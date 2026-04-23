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
    ];

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
}
