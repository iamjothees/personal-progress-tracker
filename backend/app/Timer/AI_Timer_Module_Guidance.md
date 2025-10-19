# AI Model Guidance: Interacting with the Timer Module

This document provides specific instructions and context for AI models on how to understand, analyze, and interact with the Timer module within this Laravel application.

## 1. Module Location and Scope

*   **Location:** All core PHP code for the Timer feature is located under `backend/app/Timer/`.
*   **Purpose:** This module manages user-specific timers, including starting, pausing, resuming, and stopping them, along with tracking activity history.
*   **Database:** The module interacts with the `timers` and `timer_activities` tables. Migrations for these tables are in `backend/database/migrations/`.

## 2. Key Components and Their Roles

*   **`App\Timer\Models\Timer`**: Represents a main timer. Key attributes: `id`, `owner_id`, `started_at`, `stopped_at` (nullable).
*   **`App\Timer\Models\TimerActivity`**: Represents a pause/resume event within a timer. Key attributes: `id`, `timer_id`, `paused_at`, `resumed_at` (nullable).
*   **`App\Timer\Services\TimerService`**: Contains all business logic. **Always interact with timers through this service.** Do not directly manipulate `Timer` or `TimerActivity` models from controllers or other services unless absolutely necessary and justified.
*   **`App\Timer\Http\Controllers\TimerController`**: Exposes the API endpoints. It uses Form Requests for validation and `TimerService` for business operations.
*   **`App\Timer\Http\Requests\*Request`**: Dedicated classes for validating incoming API requests. When modifying validation rules, update these files.
*   **`App\Timer\Policies\TimerPolicy`**: Defines authorization rules. When modifying access control, update this policy.
*   **`App\Timer\Exceptions\TimerActionException`**: Custom exception for service-level business rule violations. When a service operation fails due to a business rule, this exception is thrown.
*   **`App\Timer\Http\Resources\TimerResource`**: Defines the structure of JSON responses for `Timer` models.

## 3. Interaction Guidelines

*   **API Interaction:** All external interactions with the Timer feature should go through the defined API routes in `backend/app/Timer/routes.php`.
*   **Business Logic:** When implementing new timer-related features or modifying existing ones, the core logic should reside within `App\Timer\Services\TimerService`.
*   **Validation:** All request validation for API endpoints should be handled by the corresponding Form Request classes in `App\Timer\Http\Requests/`.
*   **Error Handling:**
    *   Service methods will throw `App\Timer\Exceptions\TimerActionException` for business rule failures.
    *   Controllers will catch `TimerActionException` and re-throw them as `Illuminate\Validation\ValidationException` to ensure a consistent `422 Unprocessable Entity` API response.
    *   When adding new error conditions in the service, ensure `TimerActionException` is used.
*   **Authorization:** Access control is managed by `App\Timer\Policies\TimerPolicy`. Any changes to who can perform what action on a timer should be reflected here.
*   **Database Interactions:** Eloquent models (`App\Timer\Models\Timer`, `App\Timer\Models\TimerActivity`) should be used for all database operations. Avoid raw SQL queries unless absolutely necessary.
*   **Testing:** All feature tests for the Timer module are located in `backend/app/Timer/tests/Feature/TimerTest.php`. When adding new functionality or fixing bugs, ensure adequate test coverage is provided in this file.

## 4. Key Considerations for AI Models

*   **Contextual Awareness:** Always consider the context of the authenticated user (`auth()->user()`) when performing timer operations, as timers are user-specific.
*   **Optimistic Locking:** `pause` and `stop` actions use `latest_activity_id` for optimistic locking. Ensure this parameter is handled correctly in requests.
*   **State Management:** Timers have states (running, paused, stopped). Ensure actions are only performed when the timer is in an appropriate state (e.g., cannot pause a stopped timer). The `TimerService` handles these state transitions and validations.
*   **Relationships:** `Timer` has `owner`, `activities`, and `latestActivity` relationships. `TimerActivity` belongs to a `Timer`. Leverage these Eloquent relationships.

By adhering to these guidelines, AI models can effectively understand, modify, and extend the Timer module while maintaining the application's architectural integrity.
