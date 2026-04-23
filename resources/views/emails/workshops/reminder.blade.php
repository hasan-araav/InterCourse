<x-mail::message>
# 🗓️ Workshop Reminder

Hello!

This is a friendly reminder that your upcoming workshop is just around the corner. We're excited to have you join us for this session!

<x-mail::panel>
### **{{ $workshop->title }}**
---
**📅 Date & Time:** {{ $workshop->starts_at->format('M d, Y @ H:i') }}
**⏳ Duration:** {{ $workshop->duration_minutes }} minutes
</x-mail::panel>

### **What to Expect**
{{ Str::limit($workshop->description, 150) }}

<x-mail::button :url="config('app.url') . '/dashboard'" color="primary">
Access My Dashboard
</x-mail::button>

If you have any questions before the session, feel free to reach out to the workshop coordinator or your manager.

See you there!

Regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
