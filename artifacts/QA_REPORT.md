# Events Vertical Slice QA Report

Run ID: `events-vslice-20261023-001`

Role: QA / Reviewer

Status: `READY`

Repository commit or working-tree identifier: branch `codex/events-vslice-20261023-001`, baseline commit `af3e21308985f713c9b2221b5aa09b78c97c5c89`, with the uncommitted integrated candidate identified by Backend handoff SHA-256 `50f8898d4b95b93f6b139c349502e6fa83549bf2b91446d9271872ecad038727` and Frontend handoff SHA-256 `28fb20cd2402951a1b20bb16db2fae94fd836f71121ea44d39387b5fb7d75212`

Acceptance criteria: `EVT-001`–`EVT-004`, `REG-001`–`REG-006`, `ADM-001`–`ADM-005`, `QUA-001`–`QUA-003`

Input artifacts: `AGENTS.md`, `EVENT_SPEC.md`, `HUMAN_GATES_AND_GUARDRAILS.md`, `artifacts/WORK_PLAN.md`, `artifacts/ARCH_SPEC.md`, `artifacts/BACKEND_HANDOFF.md`, `artifacts/FRONTEND_HANDOFF.md`, Backend and Frontend checkpoints, `.agents/test.md`, implementation source, generated Wayfinder output, and focused tests

Output artifacts: `artifacts/QA_REPORT.md`, `artifacts/checkpoints/events-vslice-20261023-001/qa.md`

## Recommendation

**GO for Orchestrator consolidation and presentation of the exact candidate at G3.** All 18 acceptance criteria pass independent QA. No actionable product defect, architecture deviation, test gap that blocks an approved criterion, or G2-triggering variance was found.

This recommendation is not human acceptance and does not authorize a commit, merge, deployment, destructive migration rollback, or shared/internet release. Before G3, the human should run the complete repository suite with `php artisan test --compact`, as required by the project testing guidance and the QA assignment's prohibition on QA running that complete suite.

## Candidate reconciliation and review scope

- Both implementation handoff hashes match their recorded values exactly.
- Every Backend and Frontend implementation source hash matches the corresponding handoff manifest.
- Regenerated Wayfinder controller and route modules match all eight recorded generated-output hashes.
- `composer.json`, `composer.lock`, `package.json`, and known JavaScript lockfiles have no diff from baseline; no dependency was added, removed, or upgraded.
- The implementation adds only the approved Event/Registration domain, nine event routes including `/`, six Inertia pages, one guest-safe layout, focused components/types/tests, generated Wayfinder output, and the approved database binding-mask setting.
- There is no event deletion route, authentication addition, public API, notification, payment, search/filter, upload, capacity, pagination, or deployment implementation.
- Human-owned governance changes outside Orchestrator-owned decision/trace updates were inspected as formatter-only Markdown/JSON normalization authorized by the human.

## Acceptance-criteria results

| Criterion | Status | Independent evidence                                                                                                                                                                                                                                                                                                       |
| --------- | ------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `EVT-001` | PASS   | Focused Pest proves all events are returned in `starts_at`, then `id`, order with exact summary props. Source and a populated `390 × 844` browser review confirm title, explicit-timezone range, location, and generated-Wayfinder details link.                                                                           |
| `EVT-002` | PASS   | Focused Pest proves an empty successful response; source review confirms the clear `No events scheduled` state.                                                                                                                                                                                                            |
| `EVT-003` | PASS   | Focused Pest verifies exact public detail props without registration data. Populated mobile browser review confirms the title, plain-text description, explicit-timezone range, location, and labeled registration form.                                                                                                   |
| `EVT-004` | PASS   | Focused Pest proves unknown public event route-model bindings return the normal 404 response.                                                                                                                                                                                                                              |
| `REG-001` | PASS   | Mobile accessibility tree and keyboard review confirm associated Name and Email labels, required controls, email semantics, an actionable submit button, focus visibility, and native required-field focus behavior. Source confirms the disabled spinner/text processing state.                                           |
| `REG-002` | PASS   | Focused Pest proves missing/invalid input produces field errors and no row. Inertia `<Form>` keeps safe DOM form state across validation redirects; errors are field-keyed and associated through `aria-invalid`/`aria-describedby`. No sensitive value is placed in a URL or flash.                                       |
| `REG-003` | PASS   | Focused Pest proves exactly one normalized row, PRG redirect, and one-time confirmation containing event title and attendee name. Source confirms a polite live region and excludes email.                                                                                                                                 |
| `REG-004` | PASS   | Focused Pest submits a case/whitespace variant, proves one row remains, and asserts the exact user-readable email error.                                                                                                                                                                                                   |
| `REG-005` | PASS   | Focused Pest proves the same normalized email can register successfully for two different events.                                                                                                                                                                                                                          |
| `REG-006` | PASS   | Fresh isolated schema inspection confirms `registrations_event_id_email_unique`. Focused tests prove a direct competing insert leaves one row and the targeted loser path becomes a controlled field validation error with masked bindings.                                                                                |
| `ADM-001` | PASS   | All admin routes use only `web` middleware. Focused Pest and an unauthenticated browser session confirm index, create, edit, and registrant pages do not redirect to login. The UI accurately warns that the POC admin area is unauthenticated.                                                                            |
| `ADM-002` | PASS   | Focused Pest proves a guest creates one trimmed event, local input is persisted in UTC, success flash is set, and the response redirects to edit. Mobile review confirms the complete labeled form.                                                                                                                        |
| `ADM-003` | PASS   | Focused Pest covers missing, malformed, non-increasing, and equal-date create/update failures with no invalid persistence. Source/UI review confirms field-associated errors and required controls.                                                                                                                        |
| `ADM-004` | PASS   | Focused Pest proves valid changes persist, the registration remains unchanged, and success feedback/redirect occur. Source review confirms generated update form integration.                                                                                                                                              |
| `ADM-005` | PASS   | Focused Pest proves `created_at`, then `id`, ordering, exact name/email/time props, the empty collection, and 404. Populated mobile review confirms semantic table/caption/headers, horizontal responsiveness, explicit timezone, and PII notice.                                                                          |
| `QUA-001` | PASS   | Regenerated Wayfinder outputs match handoff hashes. Every event/admin navigation uses Inertia `Link` plus generated `@/routes`; all writes use Inertia `Form` plus generated controller `.form()` helpers. Scoped search finds no hand-authored URL/method, Axios, `fetch`, or `mailto:` substitute. Type checking passes. |
| `QUA-002` | PASS   | Independent focused Pest, Pint-with-no-change hash check, exact PHPStan wrapper, repository check, TypeScript check, and production build all pass in the current candidate.                                                                                                                                               |
| `QUA-003` | PASS   | Baseline diff, dependency manifests, routes, props, schema, and source were reviewed against scope. No unapproved dependency or out-of-scope capability was introduced.                                                                                                                                                    |

## Verification commands and results

- `composer show --direct --format=json` — **passed**; confirmed Laravel `13.38.0`, Inertia Laravel `3.5.2`, Wayfinder `0.1.23`, Pest `5.3.3`, and the direct PHP package set.
- `php artisan test --compact tests/Feature/Events tests/Feature/Admin tests/Feature/Actions/CreateRegistrationTest.php` — **passed**, 23 tests / 231 assertions.
- `php artisan route:list --except-vendor` and JSON middleware inspection — **passed**, 16 application routes; exactly nine approved event routes including `/`; every event route has `web` middleware only; no event delete route.
- Disposable SQLite migration plus `php artisan db:table events` and `php artisan db:table registrations` — **passed**; both tables, required columns, `events_starts_at_id_index`, `registrations_event_id_email_unique`, `registrations_event_id_created_at_id_index`, and `ON DELETE RESTRICT` are present. The temporary database was removed.
- `php artisan wayfinder:generate --with-form --no-interaction` — **passed**; all regenerated event controller/route module hashes match the Backend handoff.
- Scoped source searches for hard-coded route/form contracts, unsafe raw HTML, external HTTP clients, `mailto:`, and application logging — **passed**, with no prohibited implementation match.
- `vendor/bin/pint --dirty --format agent` with full PHP hash comparison before/after — **passed** and changed no PHP file.
- `composer run types:check` — **passed**, PHPStan reported 0 errors. The Backend's earlier environment-only wrapper failure did not reproduce.
- `npm run check` — **passed** on the final QA artifact state, all 104 files formatted; no warnings or lint errors in 74 files.
- `npm run types:check` — **passed**, `tsc --noEmit` exit 0.
- `npm run build` — **passed**, 2328 modules transformed and production assets emitted. The existing optional `fontaine` optimization notice is non-fatal and requires no dependency change.
- `git diff --check` — **passed**.
- Background local-browser review at default and `390 × 844` viewports — **passed** for populated public list/detail, registration labels/required behavior/keyboard focus, unauthenticated admin warning/list, populated registrant table, PII notice, create form, responsive wrapping/overflow, headings, timezone labels, and empty-state source contract. The viewport override was reset; QA made no browser data mutation.

## Findings

No actionable findings.

## Privacy and security review

- Public controllers use explicit prop maps and do not load or expose registrations. Focused Pest proves a stored attendee email is absent from public Inertia props.
- `Registration` hides `email` by default; only the deliberately unauthenticated admin registrant controller explicitly selects and maps it.
- Registration confirmation contains only event title and attendee name. No email or name is placed in a URL.
- Event descriptions render as escaped React text with preserved line breaks; no raw HTML API is used.
- All five configured SQL connections mask bindings in exception messages; the constraint test proves the attempted email is absent from the SQLite exception message.
- State-changing routes remain under Laravel's `web` middleware and retain CSRF protection.
- The unauthenticated admin mutation and registrant-PII exposure is an accepted POC risk, not a security success. Shared/internet deployment remains blocked pending G4 risk acceptance or infrastructure restriction.

## Test review

The five focused Pest files exercise observable request/domain behavior with isolated factory data and database assertions. Coverage distinguishes public browse/detail/404, registration validation/success/duplicate/cross-event/unknown-event, schema race controls, admin guest access/order/create/update/validation, and registrant order/empty/404. Assertions cover response plus persistence on writes and no-change behavior on failures. No test was weakened, deleted, or edited by QA.

## Known residual risks and evidence not run

- A genuinely simultaneous multi-request race was not run on SQLite. This is the G1-accepted deterministic-testing limitation; the portable database unique constraint, direct competing insert, and controlled loser path are independently verified.
- Only the configured SQLite driver received live migration/schema and persistence verification. Other supported SQL drivers were reviewed through portable Laravel definitions and driver-aware exception metadata, but were not provisioned.
- QA did not run the complete repository Pest suite because WP-05 explicitly prohibits it. The human should run `php artisan test --compact` before G3 and report any failure to the Orchestrator.
- Browser review used existing synthetic local preview rows for populated states and did not create, edit, submit, or delete data. Empty states were verified through focused response tests and source review.
- The POC-only unauthenticated admin exposure remains accepted for this run and blocks shared/internet deployment absent the separate G4 decision.

Retry and remediation-cycle count: transient/environment retry `0`; QA remediation cycles `0`.

Usage/cost availability summary: token and monetary cost telemetry are unavailable to this QA execution and must be recorded as `null` with that reason by the Orchestrator. Elapsed time should use Orchestrator timestamps rather than an estimate.

Checkpoint path: `artifacts/checkpoints/events-vslice-20261023-001/qa.md`

Next owner and requested action: System Orchestrator validates this report, produces `artifacts/ACCEPTANCE_TRACEABILITY.md`, updates its trace/decision/checkpoint artifacts, identifies the exact revision, and presents the G3 package to the human. No merge or deployment is authorized.
