<x-mail::message>
# Workshop Reminder: {{ $workshop->title }}

Hello,

This is a friendly reminder that the workshop **{{ $workshop->title }}** is starting tomorrow.

**Details:**
- **Date & Time:** {{ $workshop->starts_at->format('M d, Y @ H:i') }}
- **Duration:** {{ $workshop->duration_minutes }} minutes

<x-mail::button :url="config('app.url') . '/dashboard'">
View Dashboard
</x-mail::button>

We look forward to seeing you there!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
