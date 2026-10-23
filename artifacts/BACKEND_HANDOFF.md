# Backend Implementation Handoff

Status: READY

Run ID: `events-vslice-20261023-001`

Repository commit or working-tree identifier: baseline `af3e21308985f713c9b2221b5aa09b78c97c5c89` on `codex/events-vslice-20261023-001`, plus the uncommitted Backend and concurrent Frontend/Orchestrator changes identified by `git status --short` at handoff time

Scope and AC IDs: WP-02A/WP-02B; `EVT-001`–`EVT-004`, `REG-002`–`REG-006`, `ADM-001`–`ADM-005`, `QUA-001`–`QUA-003`; server props supplied for `REG-001`

Artifacts changed or produced:

- Laravel domain and persistence: `app/Models/Event.php`, `app/Models/Registration.php`, `app/Actions/CreateRegistration.php`, Event/Registration factories, and the two new create-table migrations.
- HTTP/Inertia boundary: four approved controllers, two approved Form Requests, and `routes/web.php`.
- Privacy configuration: `config/database.php` only adds `mask_bindings_in_exception_messages => true` to the five configured SQL connections.
- Generated integration seam: ignored/generated files under `resources/js/actions/**`, `resources/js/routes/**`, and `resources/js/wayfinder/**` were regenerated with form variants. No hand-authored frontend file was changed by Backend.
- Focused verification: five Pest files under `tests/Feature/{Actions,Admin,Events}`.
- Backend-owned artifacts: this handoff and `artifacts/checkpoints/events-vslice-20261023-001/backend.md`.

Decisions and assumptions:

- The implementation follows `artifacts/ARCH_SPEC.md` without route, prop, validation, schema, timezone, privacy, or security-boundary variance.
- Event date-time strings are interpreted in `config('app.timezone')`, persisted as UTC offset-free database values, and mapped to UTC ISO-8601 values with a `Z` suffix. Admin edit props additionally expose `Y-m-dTHH:mm` values in the application timezone.
- Registration name is trimmed; email is trimmed and lowercased before validation and persistence. The route-bound Event is authoritative; no request `event_id` is accepted.
- Friendly scoped uniqueness validation is backed by `registrations_event_id_email_unique`. `CreateRegistration` converts only that index, or parsed `event_id,email` columns, into the documented `email` validation error; other database exceptions are rethrown.
- Registration email is hidden from default model serialization, mapped only by the unauthenticated admin registrant controller, omitted from public props/flash/URLs, and masked from unexpected SQL exception messages.
- Event deletion remains unexposed. The registration foreign key uses `RESTRICT`; the focused suite proves a registered event cannot be deleted.
- The deliberately unauthenticated admin area and SQLite concurrency-test limitation remain the G1-accepted residual risks. No real simultaneous-request claim is made.

## Contract and acceptance evidence

| Criteria            | Backend evidence                                                                                                                                                            |
| ------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `EVT-001`–`EVT-004` | Public index/detail controllers, explicit `starts_at,id` ordering, exact props, empty collection, privacy assertion, and route-binding 404 tests.                           |
| `REG-002`–`REG-005` | Normalizing Form Request, field errors/no-write tests, PRG plus one-time typed confirmation, same-event normalized duplicate rejection, and cross-event success.            |
| `REG-006`           | Named database unique index, direct competing insert, masked exception-message assertion, and deterministic `CreateRegistration` loser-path validation test.                |
| `ADM-001`–`ADM-004` | Admin routes outside auth middleware, guest page/action tests, exact event validation, local-to-UTC create test, update/flash tests, and unchanged registration assertions. |
| `ADM-005`           | Explicit admin-only registrant prop, `created_at,id` ordering, empty state, and 404 tests.                                                                                  |
| `QUA-001`           | Generated named routes/actions and form variants published at the agreed import paths.                                                                                      |
| `QUA-002`           | Focused Pest and PHP static analysis evidence below. Frontend npm checks remain Frontend/QA-owned.                                                                          |
| `QUA-003`           | No dependency/lockfile, auth, deletion route, API, notification, capacity, deployment, or other out-of-scope change introduced by Backend.                                  |

## Verification commands/checks and results

- `composer show --direct` — passed; confirmed Laravel `13.38.0`, Inertia Laravel `3.5.2`, Wayfinder `0.1.23`, Pest `5.3.3`, and the installed direct package set.
- Narrow test runs after each increment:
    - `php artisan test --compact tests/Feature/Events/EventBrowsingTest.php` — passed, 4 tests / 59 assertions.
    - `php artisan test --compact tests/Feature/Events/EventRegistrationTest.php` — passed, 6 tests / 52 assertions.
    - `php artisan test --compact tests/Feature/Actions/CreateRegistrationTest.php` — passed after each change; final narrow run passed, 4 tests / 13 assertions.
    - `php artisan test --compact tests/Feature/Admin/EventManagementTest.php` — passed, 6 tests / 72 assertions.
    - `php artisan test --compact tests/Feature/Admin/EventRegistrationsTest.php` — initial run exposed an overly narrow timestamp type assertion; after the in-scope correction it passed, 3 tests / 35 assertions.
- `vendor/bin/pint --dirty --format agent` — passed.
- `php artisan test --compact tests/Feature/Events tests/Feature/Admin tests/Feature/Actions/CreateRegistrationTest.php` — final run passed, 23 tests / 231 assertions.
- `php artisan route:list --except-vendor` — passed; 16 application routes total, including the 9 approved event routes and no event delete route.
- `php artisan wayfinder:generate --with-form --no-interaction` — passed; generated actions and named routes were published.
- Generated-resolution check using `test -f` for all four event controller modules and all four event route modules — passed.
- Temporary isolated schema inspection:
    - `DB_DATABASE=/tmp/poc-events-schema.Dt7d2k php artisan migrate --force` — passed; all migrations ran, with `events` before `registrations`.
    - `DB_DATABASE=/tmp/poc-events-schema.Dt7d2k php artisan db:table events` — passed; required columns and `events_starts_at_id_index` present.
    - `DB_DATABASE=/tmp/poc-events-schema.Dt7d2k php artisan db:table registrations` — passed; required columns, `registrations_event_id_email_unique`, `registrations_event_id_created_at_id_index`, and `events` foreign key with `ON DELETE RESTRICT` present.
    - The isolated temporary database was removed after inspection; no shared or project database was migrated or destroyed.
- `composer run types:check` — executed but did not complete successfully for environmental reasons: the sandbox denied PHPStan's local worker socket. The one permitted elevated retry opened the worker but its child reached the environment's configured 128 MB PHP memory limit. This is not recorded as a pass.
- `php -d memory_limit=512M vendor/bin/phpstan analyse --debug --memory-limit=512M` — passed with 0 errors, providing the equivalent PHPStan analysis serially with a sufficient explicit memory limit.
- `git diff --check` — passed.

Known risks, failures, and evidence not run:

- The exact Composer wrapper remains an environmental failure as documented above; the underlying configured PHPStan analysis passed with 0 errors under the explicit memory/debug invocation. QA should rerun `composer run types:check` in an environment whose PHPStan workers can open loopback sockets with more than 128 MB, or use the recorded equivalent invocation.
- A genuinely simultaneous SQLite request test was not added because it is not deterministic on the configured driver. The portable named unique index, direct competing insert, and loser-path conversion are covered.
- Full repository Pest, npm checks, production build, responsive browser/accessibility review, and independent QA were not run by Backend. They remain WP-03/WP-05 responsibilities.
- The unauthenticated admin area intentionally exposes event mutation and registrant names/emails to anyone who discovers its routes; this remains the explicit G1 risk and blocks shared/internet deployment absent G4 acceptance or an infrastructure restriction.

Retry and remediation-cycle count: transient/environment retry budget used once for `composer run types:check`; implementation remediation cycle count `0`. The one timestamp assertion correction occurred during initial implementation verification before handoff and did not change the approved contract.

Usage/cost availability summary: token and monetary cost telemetry are unavailable to this specialist and must be recorded as `null` with that reason by the Orchestrator.

Checkpoint path: `artifacts/checkpoints/events-vslice-20261023-001/backend.md`

Next owner and requested action: System Orchestrator reconciles this handoff with the Frontend handoff and current working tree, then assigns the integrated candidate to independent QA. QA should repeat the focused suite, PHP/static checks, npm checks/build, schema/route inspection, generated-import review, and responsive accessibility/privacy review. No merge or deployment is authorized.

## Hash manifest

Inputs read at implementation dispatch:

| Path                            | SHA-256                                                            |
| ------------------------------- | ------------------------------------------------------------------ |
| `EVENT_SPEC.md`                 | `652f7384d499839875d6457b6551194b200d023d61c1c9cae45fc79207f9647b` |
| `AGENTS.md`                     | `db9b172e655375a0dcbd52acab49b5c0b8ea52d89b89338a5d144c8f799a6c2f` |
| `HUMAN_GATES_AND_GUARDRAILS.md` | `b6275844677850fa9262280806a992ec3e155a92b1b22e49d599a2ee7a6a124e` |
| `artifacts/WORK_PLAN.md`        | `7144591add50f1c730727ecdcd38bc324527af915adf6bcf3004dc1315ea2150` |
| `artifacts/ARCH_SPEC.md`        | `bbcfaf89cd4feb845d091bb479e587463975d8b006fb49ea7c981a1cf6a807b3` |
| `.agents/backend.md`            | `eace824ae8cd9300871e0669d2a1eb981f5acd7f3ae370d056d389903d9e2bae` |

Backend implementation outputs:

| Path or group                                                          | SHA-256                                                            |
| ---------------------------------------------------------------------- | ------------------------------------------------------------------ |
| `app/Actions/CreateRegistration.php`                                   | `e7c8f037862f48f9a7f3c32d6698393089975f21dd6832a01aceb376d2bb5b36` |
| `app/Http/Controllers/EventController.php`                             | `dcbde999b747c33090536afd4b60858441ad7b9132b666f2fa0ffb0b8f0552e6` |
| `app/Http/Controllers/EventRegistrationController.php`                 | `d9a8379a1c5ee6c5d9c1f166c718117ec3939520984ad108f069bcb5927cdb76` |
| `app/Http/Controllers/Admin/EventController.php`                       | `11065f385434fb3086c2e6eb22b3e46524b31543ec31e06e3a98516e4457e91b` |
| `app/Http/Controllers/Admin/EventRegistrationController.php`           | `1a44d91e5c7b21b4011126107f68729eaf5c93ab9db571bd8b31ffea0ab82a24` |
| `app/Http/Requests/Admin/SaveEventRequest.php`                         | `fbbc8b670009c342bdae7e194616f4721c281dcfe401f7182e27003329a493b0` |
| `app/Http/Requests/StoreRegistrationRequest.php`                       | `e11a4d9da3dbe5d22b60e603622a5e96eaa280e3695cad5501ff5389fd2f49ca` |
| `app/Models/Event.php`                                                 | `893127b5208443d4acee3200cdff1f778276e2210522ccb0baa7d533a0fc2a19` |
| `app/Models/Registration.php`                                          | `e14c123bf79d4b8d93187d972d1c99ada86ae0b2ab8ce25b4c7a979d2d5a9f82` |
| `database/factories/EventFactory.php`                                  | `dd1a8fd3ff0330506879528965b3243cea433c51c13c9c63c55b47ee57e1fd65` |
| `database/factories/RegistrationFactory.php`                           | `067879b421356a22f0f8fc4350a5c9a374f32f9f25af5249a1d1263f3b3728b2` |
| `database/migrations/2026_10_23_103839_create_events_table.php`        | `3824bde56af96b542c6a12c70b23a1f06b8af32911a8ddadab7d995fa95f99ea` |
| `database/migrations/2026_10_23_103840_create_registrations_table.php` | `7792ce6ce3cdf6439fd435dc1c875afdc11105ba66748aaf4f35dbbc50e24161` |
| `routes/web.php`                                                       | `78a42280091d33dc40badf04b59580275b3b6cb87231089980175b1663c7ee1b` |
| `config/database.php`                                                  | `4bde239914dc9d8f6be5a04bcb14d7ff120d59961fd89fcba12664afe8e10ac9` |
| `tests/Feature/Actions/CreateRegistrationTest.php`                     | `eeb15e6fcabe0bea6df521232792c1f6f550f52b600bb6fdfbf0d084c82ea2ef` |
| `tests/Feature/Admin/EventManagementTest.php`                          | `7318790500b28d67ef23db0338566e3624f4e6ee252672844ebbe61f43786ceb` |
| `tests/Feature/Admin/EventRegistrationsTest.php`                       | `e5dd807b34accac2d93cbca237d5926b51110b0bb9c47d1445a782183fcfce74` |
| `tests/Feature/Events/EventBrowsingTest.php`                           | `7a885982abc71e14f0bef3b470557ccff0d85d12564c9385c3a227c500ca669d` |
| `tests/Feature/Events/EventRegistrationTest.php`                       | `9c2acec6996a5818135b1ef27537580560de94465a4eb12cd81ba173b60a1b58` |

Generated Wayfinder seam at handoff time:

| Path                                                                             | SHA-256                                                            |
| -------------------------------------------------------------------------------- | ------------------------------------------------------------------ |
| `resources/js/actions/App/Http/Controllers/EventController.ts`                   | `dd3f59427382981f4a04f8f2384c12b7fed919830e7d7e981a4d753ed9ec5601` |
| `resources/js/actions/App/Http/Controllers/EventRegistrationController.ts`       | `6db0dc1df039a25ec25098509d813dea8692cbcaa352da964e711c6d43811a7c` |
| `resources/js/actions/App/Http/Controllers/Admin/EventController.ts`             | `1d1972acdb08e8b3e93a5cec7abacc003d63e1b4b13be008b88dbdc8064a0198` |
| `resources/js/actions/App/Http/Controllers/Admin/EventRegistrationController.ts` | `080318fe14603bc3f6b143b8de7c86b8d3fe3a27efe92c9b99b71423f001e062` |
| `resources/js/routes/events/index.ts`                                            | `a6ea6a28050efad41130af5b4e46a22c63fcfc1d91fb9c406533015b255f23d3` |
| `resources/js/routes/events/registrations/index.ts`                              | `1a0ab2fa42419a93638f94503eb7744272d079787878522591ad76d10385ba2c` |
| `resources/js/routes/admin/events/index.ts`                                      | `820ff5ee1399835ed2e0397db02e9fcde778ab5dbf2b1258687e221099e71dd4` |
| `resources/js/routes/admin/events/registrations/index.ts`                        | `ebb749813c09b48e79717bcef87b18658acfc01fab712a3eed59fef0ed58583f` |

Generated Wayfinder directories are intentionally ignored by the repository and are reproducible from the committed route/controller sources with `php artisan wayfinder:generate --with-form --no-interaction` or the configured Vite plugin.
