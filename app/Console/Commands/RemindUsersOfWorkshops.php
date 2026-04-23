<?php

namespace App\Console\Commands;

use App\Mail\WorkshopReminder;
use App\Models\Workshop;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[Signature('app:remind-users-of-workshops {--dry-run : Print list of recipients without sending emails}')]
#[Description('Identify upcoming workshops within the next 24 hours and notify confirmed users.')]
class RemindUsersOfWorkshops extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        Log::info('Reminder command started' . ($dryRun ? ' (DRY RUN)' : ''));
        
        $tomorrowWindow = now()->addHours(24);
        
        $workshops = Workshop::with(['users' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->where('starts_at', '>', now())
            ->where('starts_at', '<=', $tomorrowWindow)
            ->get();

        if ($workshops->isEmpty()) {
            $this->info('No workshops starting within the next 24 hours.');
            Log::info('Reminder command ended: No workshops found.');
            return;
        }

        $results = [];

        foreach ($workshops as $workshop) {
            $attendeeCount = $workshop->users->count();
            $status = 'Success';

            if ($attendeeCount > 0) {
                try {
                    foreach ($workshop->users as $user) {
                        if ($dryRun) {
                            $this->line("Dry run: Would send reminder to {$user->email} for '{$workshop->title}'");
                        } else {
                            Mail::to($user->email)->send(new WorkshopReminder($workshop));
                            Log::info("Reminder sent to {$user->email} for workshop: {$workshop->title}");
                        }
                    }
                    if ($dryRun) {
                        $status = 'Success (Dry Run)';
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send reminders for workshop {$workshop->id}: " . $e->getMessage());
                    $status = 'Failed';
                }
            } else {
                $status = 'No attendees';
            }

            $results[] = [
                'Workshop Title' => $workshop->title,
                'Attendee Count' => $attendeeCount,
                'Status' => $status,
            ];
        }

        $this->table(
            ['Workshop Title', 'Attendee Count', 'Status'],
            $results
        );

        Log::info('Reminder command finished successfully' . ($dryRun ? ' (DRY RUN)' : ''));
    }
}
