<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    /**
     * Display a listing of workshop statistics.
     */
    public function index(): Response
    {
        $workshops = Workshop::withCount([
            'users as confirmed_count' => function ($query) {
                $query->where('status', 'confirmed');
            },
            'users as waitlist_count' => function ($query) {
                $query->where('status', 'waitlisted');
            }
        ])
        ->get()
        ->map(function ($workshop) {
            $fillPercentage = $workshop->capacity > 0
                ? round(($workshop->confirmed_count / $workshop->capacity) * 100, 2)
                : 0;

            return [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'starts_at' => $workshop->starts_at,
                'capacity' => $workshop->capacity,
                'confirmed_count' => $workshop->confirmed_count,
                'waitlist_count' => $workshop->waitlist_count,
                'fill_percentage' => $fillPercentage,
                'is_past' => $workshop->starts_at->isPast(),
            ];
        });

        // Most Popular Logic: Top 5 by confirmed + waitlist count
        $mostPopular = $workshops->sortByDesc(function ($workshop) {
            return $workshop['confirmed_count'] + $workshop['waitlist_count'];
        })->take(5)->values();

        // Focus on upcoming or recent trends
        $upcomingStats = $workshops->where('is_past', false)->values();
        $pastStats = $workshops->where('is_past', true)->values();

        return Inertia::render('Admin/Stats/Index', [
            'mostPopular' => $mostPopular,
            'upcomingStats' => $upcomingStats,
            'pastStats' => $pastStats,
            'totalRegistrations' => $workshops->sum('confirmed_count'),
            'totalWaitlist' => $workshops->sum('waitlist_count'),
        ]);
    }
}
