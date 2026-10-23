# Product Specification for the Events Application

## Overview

This specification defines the minimum end-to-end product slice for a public events application. A visitor can discover an event, review its details, register once, and receive an on-screen confirmation. An administrator can create and edit events and inspect their registrants without signing in.

The slice is intended to prove the event and registration lifecycle with the smallest coherent implementation. It is not a production-complete event-management platform.

### Users and outcomes

- **Attendee:** finds an event, understands when and where it occurs, submits valid registration details, and receives a clear confirmation.
- **Administrator:** creates or corrects event information and reviews the current registrant list.
- **Project owner:** can verify every promised behavior through the acceptance criteria in this document.

### Technical baseline

The implementation must use the versions and conventions already present in the repository:

- PHP 8.4 runtime with Laravel 13 (`laravel/framework` 13.38.0 installed).
- Inertia Laravel 3.5 with Inertia React 3, React 19, and TypeScript 5.7.
- Tailwind CSS 4 and the existing Radix-based UI components.
- Laravel Wayfinder for typed route/controller imports.
- Pest 5 with the Laravel Pest plugin for automated tests.
- Vite Plus/Vite 8 for frontend development and builds.

The AI Orchestrator and specialist agents described in `AGENTS.md` and `.agents/` are development-time collaborators. They are not runtime services or product features.

## Scope Boundaries: In-Scope & Explicitly Out-Of-Scope

### In scope

#### Public event experience

- A public event listing showing every event in deterministic chronological order by start time, with a stable ID tie-breaker.
- Each listing item exposes at least the title, start/end date and time, and location, and links to its event details page.
- A public event details page showing the title, description, start/end date and time, and location.
- Clear empty-state content when no events exist.
- Dates and times displayed in the application's configured timezone with a visible timezone label.

#### Attendee registration

- A registration form associated with exactly one event.
- Required attendee fields: full name and email address.
- Server-side validation with field-level errors returned to the form; client-side affordances may supplement but never replace it.
- Successful persistence followed by an on-screen confirmation identifying the event and submitted attendee name.
- Post/Redirect/Get behavior, or the Inertia equivalent, so refreshing the confirmation does not create another registration.
- Duplicate prevention for the same normalized email address on the same event.
- A database uniqueness constraint plus graceful application handling so concurrent submissions cannot create duplicates.
- The same email address may register for different events.

#### Administration without login

- An unauthenticated admin event index.
- An unauthenticated form to create an event.
- An unauthenticated form to edit an event.
- An unauthenticated event registrant view showing attendee name, email, and registration timestamp.
- Validation for event data and clear success/error feedback.

#### Quality baseline

- Responsive, keyboard-usable pages built with existing components where available.
- Pest feature coverage for the happy paths, validation failures, missing records, and duplicate-registration invariant.
- Frontend type checking and production build verification.

### Explicitly out of scope

- Application authentication, authorization, roles, or permissions, including protection of admin routes.
- Event deletion, cancellation, draft/publish workflows, moderation, or approval.
- Registration editing, cancellation, transfer, check-in, or attendance tracking.
- Capacity limits, inventory, waitlists, ticket classes, payments, refunds, coupons, or invoices.
- Email, SMS, calendar invitations, webhooks, or any other outbound notification. “Confirmation” means an in-app confirmation only.
- Event images, attachments, maps, geocoding, recurrence, virtual-meeting integrations, or multi-venue support.
- Search, filters, categories, tags, recommendations, pagination, or analytics.
- User accounts, attendee profiles, organizer profiles, or multi-tenancy.
- A public API, mobile application, offline support, localization, or custom timezone selection.
- Deployment implementation or infrastructure configuration; deployment remains human-owned.
- Adding or upgrading dependencies unless separately approved through a human gate.

Retention of unrelated starter-kit functionality is permitted, but it is not part of this slice and must not be required to use the event or admin flows.

## Data & Domain Requirements

### Event

| Attribute                  | Requirement                                                                                                                    |
| -------------------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| `id`                       | Database-generated primary key.                                                                                                |
| `title`                    | Required string, trimmed, maximum 255 characters.                                                                              |
| `description`              | Required text suitable for plain-text rendering.                                                                               |
| `location`                 | Required string, trimmed, maximum 255 characters.                                                                              |
| `starts_at`                | Required date/time. Admin input is interpreted in the configured application timezone, converted to UTC, and persisted in UTC. |
| `ends_at`                  | Required date/time strictly after `starts_at`, converted and persisted using the same timezone rule as `starts_at`.            |
| `created_at`, `updated_at` | Framework-managed timestamps.                                                                                                  |

All created events are publicly visible immediately. No publication-state field is required for this slice.

### Registration

| Attribute                  | Requirement                                                                                                      |
| -------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| `id`                       | Database-generated primary key.                                                                                  |
| `event_id`                 | Required foreign key to an existing event. Registrations must not exist without an event.                        |
| `name`                     | Required string, trimmed, maximum 255 characters.                                                                |
| `email`                    | Required valid email address, trimmed and canonicalized to lowercase before persistence, maximum 255 characters. |
| `created_at`, `updated_at` | Framework-managed timestamps; `created_at` is the registration time displayed to administrators.                 |

The domain uniqueness rule is `(event_id, email)`. It must be enforced by both the request workflow and a composite unique database index. A duplicate attempt returns a user-readable validation error and must not alter the existing registration.

### Relationships and lifecycle rules

- An event has many registrations; a registration belongs to exactly one event.
- Registering against an unknown event returns the application's normal not-found response.
- Editing an event must not remove or rewrite its registrations.
- Event deletion is not exposed in this slice; cascade behavior is therefore an implementation decision that the architect must document before migrations are approved.
- Public event ordering must be explicit and deterministic: ascending `starts_at`, then ascending `id`.
- Admin registrant ordering must be explicit and deterministic: ascending `created_at`, then ascending `id`.
- Registration emails are personal data. They appear only in the deliberately unauthenticated admin view and must not be logged, embedded in URLs, or exposed on public event pages.

## Acceptance Criteria Matrix

| ID      | Capability               | Acceptance criterion                                                                                                                                                                                                                             | Verification evidence                                               |
| ------- | ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------- |
| EVT-001 | Event listing            | Given multiple events, when a visitor opens the public listing, then every event is shown in ascending start-time order with title, time range, location, and a details link.                                                                    | Pest feature test plus responsive UI review.                        |
| EVT-002 | Empty listing            | Given no events, when a visitor opens the public listing, then a clear empty state appears without an application error.                                                                                                                         | Pest feature test.                                                  |
| EVT-003 | Event details            | Given an existing event, when a visitor opens its details route, then the title, description, time range, location, and registration form are visible.                                                                                           | Pest feature test plus UI review.                                   |
| EVT-004 | Missing event            | Given an unknown event identifier, when its details route is requested, then the application returns its normal 404 response.                                                                                                                    | Pest feature test.                                                  |
| REG-001 | Registration form        | Given an event details page, when the page renders, then name and email inputs have labels, required state, and an actionable submit control.                                                                                                    | UI/accessibility review.                                            |
| REG-002 | Registration validation  | Given missing or invalid registration data, when the form is submitted, then nothing is persisted and field-level errors are displayed with the submitted non-sensitive values preserved.                                                        | Pest feature tests plus UI review.                                  |
| REG-003 | Registration success     | Given valid name and email data, when the form is submitted, then one registration is stored for that event and an in-app confirmation identifies the event and attendee.                                                                        | Pest feature test asserting response and database state.            |
| REG-004 | Duplicate prevention     | Given an email already registered for an event, when the same email is submitted again with different casing or surrounding whitespace, then no second row is created and a clear duplicate message is shown.                                    | Pest feature test asserting message and row count.                  |
| REG-005 | Cross-event registration | Given an email registered for one event, when it is submitted for another event, then the second registration succeeds.                                                                                                                          | Pest feature test.                                                  |
| REG-006 | Concurrent safety        | Given competing submissions for the same event and normalized email, when persistence is attempted, then the database invariant permits at most one record and the losing request receives a controlled response rather than an unhandled error. | Unique-index inspection and focused automated test where practical. |
| ADM-001 | Admin access             | Given no authenticated session, when `/admin/events` or its create/edit/registrant routes are requested, then the pages are accessible without a login redirect.                                                                                 | Pest feature tests.                                                 |
| ADM-002 | Create event             | Given valid event data, when an administrator submits the create form, then one event is stored and success feedback is shown.                                                                                                                   | Pest feature test asserting response and database state.            |
| ADM-003 | Event validation         | Given missing data or an end time not after the start time, when an event form is submitted, then no invalid change is persisted and field-level errors are shown.                                                                               | Pest feature tests for create and update boundaries.                |
| ADM-004 | Edit event               | Given an existing event, when an administrator submits valid changed data, then that event is updated, its registrations are unchanged, and success feedback is shown.                                                                           | Pest feature test asserting response and database state.            |
| ADM-005 | Registrant view          | Given an event with registrations, when its admin registrant view opens, then each attendee's name, email, and registration time are shown in deterministic order; an event with none shows an empty state.                                      | Pest feature tests plus UI review.                                  |
| QUA-001 | Route integration        | Given navigation or form actions in React, then they use Inertia and generated Wayfinder routes/actions rather than duplicated hard-coded route contracts.                                                                                       | Code review and frontend type check.                                |
| QUA-002 | Regression quality       | Given the completed slice, when focused Pest tests, `npm run check`, `npm run types:check`, and `npm run build` execute, then all commands pass.                                                                                                 | Command output recorded in the QA handoff.                          |
| QUA-003 | Scope compliance         | Given the completed slice, when reviewed against this specification, then no out-of-scope capability or unapproved dependency has been introduced.                                                                                               | QA traceability report and human acceptance.                        |
