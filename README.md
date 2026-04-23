# InterCourse - Workshop Management System

InterCourse is a robust workshop registration and management platform built with Laravel, Vue.js, and Inertia.js. It features real-time updates, automated reminders, and a comprehensive registration service with waitlisting and overlap protection.

## Quickstart Guide

### Requirements
- **PHP 8.3+**
- **Node.js 20+**
- **Composer**
- **SQLite** (or your preferred database)

### One-Liner Setup
Run the following command to initialize the project (assuming SQLite):
```bash
cp .env.example .env && touch database/database.sqlite && composer install && npm install && php artisan key:generate && php artisan migrate --seed && npm run build
```

---

## Command Reference

### Automated Reminders
The system includes an Artisan command to notify confirmed attendees of workshops starting within the next 24 hours.
```bash
# Send reminders
php artisan app:remind-users-of-workshops

# Dry run (list recipients without sending)
php artisan app:remind-users-of-workshops --dry-run
```

### Test Suite
We use **Pest** for our testing suite. To run all tests:
```bash
./vendor/bin/pest
```

---

## Architecture ADR (Architectural Decision Records)

### 1. Service Layer Pattern
**Decision**: Business logic for registration is encapsulated within `App\Services\RegistrationService`.
**Rationale**:
- **Thin Controllers**: Keeps controllers focused on request/response handling.
- **Isolation**: Registration logic (overlap checks, FIFO promotion) is complex and needs to be tested in isolation from the HTTP layer.
- **Reusability**: Logic can be reused in Artisan commands or other parts of the application without duplicating code.

### 2. Real-time Infrastructure with Laravel Reverb
**Decision**: Used Laravel Reverb for WebSocket broadcasting.
**Rationale**:
- **First-party Support**: Reverb is a first-party Laravel package, ensuring seamless integration.
- **Performance**: Provides immediate UI updates when registrations occur, improving the user experience for waitlisted employees.
- **Simplicity**: No external dependencies like Pusher or Soketi required for local development.

### 3. Testing with Pest
**Decision**: Pest was chosen over PHPUnit as the primary testing framework.
**Rationale**:
- **Developer Experience**: Offers a more expressive and readable syntax.
- **Built-in Features**: Includes excellent support for architecture testing and snapshot testing.
- **Modern Standards**: Aligns with modern PHP development practices while maintaining full compatibility with PHPUnit's underlying engine.

---

## Core Features
- **No-Ubiquity Rule**: Prevents users from registering for overlapping workshops.
- **FIFO Waitlisting**: Automatically promotes the first waitlisted user when a confirmed spot is cancelled.
- **Real-time Dashboards**: Live updates of workshop capacity and registration status using WebSockets.
- **Admin Stats**: Aggregate metrics for workshop popularity and fill rates.
