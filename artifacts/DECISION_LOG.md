# Decision Log

Run ID: `events-vslice-20261023-001`

Role: System Orchestrator

Status: `ACCEPTED`

Repository commit or working-tree identifier: `39037c4577cd78abe2df0fd5a5cafe7ae0589750` plus the uncommitted G1 planning artifacts for this run

Acceptance criteria: Governance Gates G0 and G1; `EVT-001`–`EVT-004`, `REG-001`–`REG-006`, `ADM-001`–`ADM-005`, `QUA-001`–`QUA-003`

Input artifacts: `EVENT_SPEC.md`, `AGENTS.md`, `HUMAN_GATES_AND_GUARDRAILS.md`, `WORKFLOW_DESIGN.md`, `WORKFLOW_DIAGRAM.md`, `.agents/*.md`, `artifacts/README.md`, `artifacts/WORK_PLAN.md`, `artifacts/ARCH_SPEC.md`

Output artifacts: `artifacts/DECISION_LOG.md`, `RUN_TRACE.json`

## G0 — Requirements Baseline

| Field | Recorded decision |
| --- | --- |
| Decision | Approved |
| Decision owner | Erickson Salangsang |
| Decision date | 2026-10-23 |
| Recorded at | 2026-10-23T09:36:41Z |
| Approved revision | `869e7fbe7ddf35d5440aa99a61c8f43f183cc3cf` |
| Run ID | `events-vslice-20261023-001` |

### Approved scope

- `EVENT_SPEC.md`
- `AGENTS.md`
- `HUMAN_GATES_AND_GUARDRAILS.md`
- `WORKFLOW_DESIGN.md`
- `WORKFLOW_DIAGRAM.md`
- `RUN_TRACE.schema.json`
- `.agents/*.md`
- `artifacts/README.md`

### Conditions

- No application implementation may begin before G1 approval.
- The Orchestrator and Architect may produce planning and architecture artifacts.
- Material requirement, dependency, security, privacy, or schema-risk changes require G2 approval.
- Merge and deployment remain subject to separate G3 and G4 approvals.

### Risks accepted

- The unauthenticated admin area is acceptable only for this proof-of-concept scope.
- No production or shared-environment deployment is authorized by this approval.

### Evidence

- Governance baseline commit: `869e7fbe7ddf35d5440aa99a61c8f43f183cc3cf`
- Human approval: “I approve G0 as written Erickson Salangsang.”

### Verification

- The approved revision exists in the local Git history.
- The working tree was clean immediately before this decision was recorded.
- No application implementation was authorized or performed by this gate decision.

### Next owner and action

The System Orchestrator may create `artifacts/WORK_PLAN.md`, initialize the governed run, and assign the Solutions Architect to create `artifacts/ARCH_SPEC.md`. Work must stop for G1 approval before Backend or Frontend implementation begins.

## G1 — Implementation Plan, Architecture, and Risk

| Field | Recorded decision |
| --- | --- |
| Decision | Approved |
| Decision owner | Erickson Salangsang |
| Decision date | 2026-10-23 |
| Recorded at | 2026-10-23T10:22:44Z |
| Package prepared | 2026-10-23 |
| Run ID | `events-vslice-20261023-001` |
| Base repository revision | `39037c4577cd78abe2df0fd5a5cafe7ae0589750` |
| Approved requirements baseline | `869e7fbe7ddf35d5440aa99a61c8f43f183cc3cf` |
| Scope | All 18 product acceptance criteria mapped in `artifacts/WORK_PLAN.md` and `artifacts/ARCH_SPEC.md`. |
| Work authorized | Implementation and verification within the exact approved work-plan and architecture contracts. |
| Work not authorized | Any material variance without G2, merge without G3, or deployment without G4. |

### Approved implementation decisions

- Add `events` and `registrations` with UTC event datetimes, deterministic ordering indexes, a restrictive event foreign key, and a named composite unique constraint on normalized (`event_id`, `email`).
- Use route-model binding, explicit public/admin controllers and Form Requests, generated Wayfinder contracts, six Inertia React pages, and a guest-safe events layout.
- Normalize registration email before validation; use a friendly scoped uniqueness rule plus the database constraint; convert only the expected `UniqueConstraintViolationException` into an `email` validation error.
- Redirect successful registration to the event GET and provide a one-time Inertia flash naming the event and attendee; no email or attendee identity is placed in a URL.
- Parse admin `datetime-local` values in `config('app.timezone')`, persist UTC, send UTC ISO values, render with an explicit configured timezone, and show its label.
- Expose attendee name/email only on the deliberately unauthenticated admin registrant page; use explicit props, hidden default email serialization, no payload logging, and database exception-binding masking.
- Permit a post-G1 Backend edit to `config/database.php` only to set `mask_bindings_in_exception_messages => true` for configured SQL connections. Any broader configuration change requires G2.
- Add model factories for test data; omit dedicated demo seeders as unnecessary for the acceptance scope.

### Alternatives not selected

- Authentication was not added because it is explicitly out of scope.
- Cascade deletion was rejected in favor of `RESTRICT` / `NO ACTION`; there is no deletion route and retention changes require G2.
- Validation-only duplicate protection was rejected because it races; application locks were rejected as unnecessary for one atomic insert.
- A permanent confirmation record/URL was rejected because it adds schema and privacy exposure; a one-time session flash satisfies the required post/redirect/get behavior.
- Browser-local timezone interpretation, hard-coded route URLs, new dependencies, speculative services/repositories, APIs, queues, and notifications were rejected as inconsistent with the approved thin slice.

### Accepted residual risks

1. Anyone who discovers the unauthenticated admin routes can create/edit events and view registrant names and email addresses. This is acceptable only for the POC; shared/internet deployment still requires an explicit G4 exposure decision or human-owned infrastructure restriction.
2. The configured SQLite test database can deterministically prove the composite unique index and controlled losing-insert path but may not support a reliable genuinely simultaneous request test. The invariant and exception path remain mandatory evidence.
3. A populated-table rollback would be destructive. Fresh migration reversal is defined, but any shared-environment rollback requires G2, a backup, and explicit human authorization.

### G1 evidence

- `artifacts/WORK_PLAN.md`
- `artifacts/ARCH_SPEC.md`
- `artifacts/checkpoints/events-vslice-20261023-001/orchestrator.md`
- `artifacts/checkpoints/events-vslice-20261023-001/architect.md`
- `RUN_TRACE.json`

### Human approval evidence

“I approve G1 for run `events-vslice-20261023-001` as written. I accept the documented residual risks and authorize implementation and verification within the approved contracts. This does not authorize merge or deployment. — Erickson Salangsang, 2026-10-23”

### Conditions

- Implementation must remain within `artifacts/WORK_PLAN.md` and `artifacts/ARCH_SPEC.md` as approved.
- Material scope, dependency, security, privacy, schema-risk, data-loss, or cross-contract changes require G2 before affected work proceeds.
- This decision does not authorize merge; the exact integrated revision remains subject to G3.
- This decision does not authorize deployment or shared/internet exposure; those remain subject to G4.

Remediation-cycle count: `0`

Next owner and action: System Orchestrator may issue the bounded Backend and Frontend assignments defined in `artifacts/WORK_PLAN.md`. G3 merge and G4 deployment remain separately blocked.
