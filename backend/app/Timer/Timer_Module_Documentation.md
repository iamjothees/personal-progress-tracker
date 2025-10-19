# Timer Module Documentation

This document provides an overview and detailed information about the Timer module, which is responsible for managing user-specific timers and their activities.

## 1. Overview

The Timer module encapsulates all logic, API endpoints, and data structures related to creating, managing, and tracking personal timers. It follows a modular design pattern to ensure maintainability and separation of concerns within the application.

## 2. Module Structure

The Timer module's code is organized within the `app/Timer` directory:

```
app/Timer/
├── Exceptions/
│   └── TimerActionException.php    # Custom exception for timer-related business logic errors.
├── Http/
│   ├── Controllers/
│   │   └── TimerController.php     # Handles API requests for timer management.
│   ├── Requests/
│   │   ├── PauseTimerRequest.php   # Form Request for validating pause timer requests.
│   │   ├── ResumeTimerRequest.php  # Form Request for validating resume timer requests.
│   │   └── StopTimerRequest.php    # Form Request for validating stop timer requests.
│   └── Resources/
│       └── TimerResource.php       # Transforms Timer model data into API-friendly JSON.
├── Models/
│   ├── Timer.php                   # Eloquent model for a user's timer.
│   └── TimerActivity.php           # Eloquent model for individual pause/resume activities within a timer.
├── Policies/
│   └── TimerPolicy.php             # Defines authorization rules for Timer models.
├── Services/
│   └── TimerService.php            # Contains the core business logic for timer operations.
└── routes.php                      # Defines API routes specific to the Timer module.
```

## 3. Database Schema

The Timer module interacts with the following tables:

### `timers` table
- **Purpose:** Stores main timer records.
- **Columns:**
    - `id`: Primary key.
    - `owner_id`: Foreign key to `users` table (`id`), restricted on delete.
    - `started_at`: Timestamp when the timer was initiated.
    - `stopped_at`: Nullable timestamp when the timer was completed/stopped.
    - `created_at`: Timestamp of record creation.
    - `updated_at`: Timestamp of last record update.

### `timer_activities` table
- **Purpose:** Records pause and resume events for a specific timer.
- **Columns:**
    - `id`: Primary key.
    - `timer_id`: Foreign key to `timers` table (`id`), cascaded on delete.
    - `paused_at`: Timestamp when the timer activity was paused.
    - `resumed_at`: Nullable timestamp when the timer activity was resumed.

## 4. API Endpoints

All Timer module API endpoints are prefixed with `/api/timers` and require authentication (`auth:sanctum`).

| Method | Endpoint                                     | Description                                     | Policy Check        |
| :----- | :------------------------------------------- | :---------------------------------------------- | :------------------ |
| `GET`  | `/api/timers`                                | Retrieve a list of the authenticated user's running timers. | `viewAny` on `Timer` |
| `POST` | `/api/timers/actions/start`                  | Start a new timer for the authenticated user.   | `act` on `Timer`    |
| `GET`  | `/api/timers/{timer}`                        | Retrieve details of a specific timer owned by the user. | `view` on `timer`   |
| `POST` | `/api/timers/{timer}/actions/pause`          | Pause a running timer. Requires `latest_activity_id` for optimistic locking. | `act` on `timer`    |
| `POST` | `/api/timers/{timer}/actions/{timerActivity}/resume` | Resume a paused timer activity.                 | `act` on `timer`    |
| `POST`  | `/api/timers/{timer}/actions/stop`           | Stop a running or paused timer. Requires `latest_activity_id` for optimistic locking. | `act` on `timer`    |

## 5. Core Logic & Error Handling

*   **`TimerService`**: Contains the business rules for timer operations (e.g., starting, pausing, resuming, stopping). It throws `App\Timer\Exceptions\TimerActionException` for business rule violations.
*   **`TimerController`**: Handles incoming requests, delegates to `TimerService`, and returns `ApiResponse` objects. It uses Form Requests for input validation and catches `TimerActionException` to re-throw them as `Illuminate\Validation\ValidationException`, ensuring consistent `422 Unprocessable Entity` API responses for validation and business logic errors.
*   **`TimerPolicy`**: Enforces ownership rules, ensuring users can only interact with their own timers.

## 6. Testing

Feature tests for the Timer module are located at `app/Timer/tests/Feature/TimerTest.php` and cover authentication, authorization, validation, and happy/failure paths for all API endpoints.
