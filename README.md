# InterCourse - Workshop Management System

InterCourse is a professional workshop registration and management platform built with a modern stack focusing on performance, real-time updates, and a premium user experience.

## Tech Stack
- **Backend**: PHP 8.5 / Laravel 13
- **Frontend**: Vue.js 3 with Inertia.js (Composition API)
- **Real-time**: Laravel Reverb & Echo (WebSockets)
- **Styling**: Tailwind CSS (Indigo/Emerald/Amber palette)
- **Icons**: Lucide Vue Next
- **Visuals**: Chart.js & UI-Avatars
- **Testing**: Pest

## Quickstart Guide

### Requirements
- **PHP 8.5+**
- **Node.js 20+**
- **Composer**
- **SQLite**

### One-Liner Setup
Run the following command to initialize the project:
```bash
cp .env.example .env && touch database/database.sqlite && composer install && npm install && php artisan key:generate && php artisan migrate --seed && php artisan storage:link && npm run build
```

### Environment Configuration
Ensure your `.env` is configured for local development:
- **Broadcasting**: Set `BROADCAST_CONNECTION=reverb`.
- **Reverb**: Use `127.0.0.1` for `REVERB_HOST` to ensure local connectivity.
- **WebPush (VAPID)**: Generate your VAPID keys by running:
  ```bash
  php artisan webpush:vapid
  ```
- **Storage**: Run `php artisan storage:link` to serve uploaded profile photos.

## Access Credentials

### Admin Dashboard
- **Username**: `admin@intercourse.com`
- **Password**: `password`

### Employee Portal
- **Username**: `employee@intercourse.com`
- **Password**: `password`

## Core Features

### 🏢 Advanced Workshop Management
- **Full CRUD**: Manage workshops with cover photos and speaker profiles.
- **Speaker Spotlights**: Dedicated sections for speaker bios and professional headshots.
- **Metrics**: Executive overview of total events, upcoming workshops, and fill rates.

### 👥 Intelligent User Directory
- **Employee Profiles**: Searchable, sortable table for user management.
- **Photo Uploads**: Support for profile photos with high-quality UI-Avatar fallbacks.
- **Engagement Tracking**: View individual workshop attendance and engagement rates.

### ⚡ Real-time & Automation
- **WebSocket Integration**: Live updates of workshop capacity and registration status.
- **No-Ubiquity Rule**: Automated prevention of overlapping workshop bookings.
- **FIFO Waitlisting**: Automatic promotion of waitlisted users upon cancellation.
- **Automated WebPush Notification**: Real-time notifications for waitlisted users about workshop changes.
- **Automated Reminders**: Daily notifications for workshops starting within 24 hours.

### 🎨 Premium UI/UX
- **Branded Design System**: High-contrast indigo/emerald palette with `rounded-xl` (12px) tokens.
- **Split-Screen Auth**: Informational branding panels for login and registration.
- **Unified Error Handling**: Branded SPA error pages for 403, 404, and 500 status codes.
- **Professional Emails**: Customized Laravel Mail templates matching the project's identity.

## Command Reference

### Artisan Commands
```bash
# Send workshop reminders (Daily 08:00 AM)
php artisan app:remind-users-of-workshops

# Start the WebSocket server
php artisan reverb:start
```

### Testing Suite
```bash
# Run all Pest tests
./vendor/bin/pest
```

## Architectural Decision Records (ADR)

### 1. Service Layer Pattern
**Decision**: Business logic for registration is encapsulated within `App\Services\RegistrationService`.
**Rationale**: Keeps controllers thin, ensures isolation for complex logic (overlap checks, FIFO promotion), and enables reusability across HTTP and CLI layers.

### 2. Real-time Infrastructure with Laravel Reverb
**Decision**: Used Laravel Reverb for WebSocket broadcasting.
**Rationale**: Provides a first-party, high-performance solution for immediate UI updates without external dependencies, improving the user experience for waitlisted employees.

## 3. Real-time WebPush Notification
**Decision**: Integrated WebPush notifications for waitlisted users about workshop changes.
**Rationale**: Enhances user engagement by providing immediate feedback on workshop status changes, reducing the need for manual checks.

### 3. Testing with Pest
**Decision**: Pest is the primary testing framework.
**Rationale**: Offers an expressive, readable syntax that aligns with modern PHP standards while maintaining full compatibility with PHPUnit.
