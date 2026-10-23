# Human Gates and Guardrails

## Purpose and authority

This document defines where AI-assisted work must pause for a human decision and the boundaries no agent may cross implicitly. The human owns requirements (`EVENT_SPEC.md`, `AGENTS.md`, this document, and `WORKFLOW_DIAGRAM.md`) and deployment.

When instructions conflict, agents must use this precedence order and escalate unresolved conflicts:

1. Current explicit human instruction.
2. Human-approved requirements and gate decisions.
3. `AGENTS.md` and the applicable role charter.
4. Approved architecture handoff.
5. Existing implementation conventions and agent recommendations.

Silence is not approval for a gated action.

## Required human gates

| Gate | Trigger | Evidence presented by Orchestrator | Human decision | Work permitted after approval |
| --- | --- | --- | --- | --- |
| G0 — Requirements baseline | Before architecture or implementation begins. | Scope, assumptions, acceptance matrix, open questions, and explicit exclusions. | Approve, revise, or reject requirements. | Architecture analysis only. |
| G1 — Implementation plan, architecture, and risk | Before migrations, shared contracts, or feature implementation. | `artifacts/WORK_PLAN.md`, `artifacts/ARCH_SPEC.md`, domain/schema proposal, route/prop contract, duplicate strategy, personal-data handling, unauthenticated-admin risk, alternatives, and reversibility. | Approve the plan, request changes, or stop. | Implementation within the approved plan and design. |
| G2 — Change control | Any material scope, dependency, security boundary, schema-risk, data-loss, or cross-contract change. | Reason, impact, alternatives, affected acceptance criteria/files, migration or rollback implications, and recommendation. | Approve a bounded exception, defer it, or reject it. | Only the specifically approved change. |
| G3 — Merge approval | After integration and independent QA, before merge to the protected branch. | Exact commit or pull request, diff summary, `artifacts/QA_REPORT.md`, `artifacts/ACCEPTANCE_TRACEABILITY.md`, test/build results, unresolved risks, and go/no-go recommendation. | Approve that revision for merge, request remediation, defer, or reject. | Merge of only the identified revision, performed by the human or an explicitly authorized mechanism. |
| G4 — Deployment | Before any production or shared-environment release action. | Release contents, environment assumptions, migration/rollback plan, checks, known risks, and post-deploy verification plan. | Human performs or explicitly authorizes deployment actions. | Only the authorized release actions; default ownership remains human. |

G2 is event-driven and may occur more than once. A rejected or unanswered gate leaves the affected work blocked.

## Project guardrails

### Scope and requirements

- Implement only the approved thin vertical slice and its necessary tests.
- Do not reinterpret “confirmation” as an email/SMS notification or “admin” as an authenticated role.
- Do not modify human-owned governance documents unless the human explicitly requests that edit.
- Do not mark an acceptance criterion complete without reproducible evidence.
- Route all ambiguities that materially change user behavior, data shape, or risk through the Orchestrator and a human gate.

### Architecture and dependencies

- Use the installed Laravel 13, Inertia React 3, React 19, Tailwind 4, Wayfinder, and Pest stack.
- Confirm installed versions before using package-specific APIs and consult version-scoped documentation when behavior matters.
- Do not add, remove, or upgrade Composer or npm dependencies without G2 approval.
- Do not create new top-level application structures or speculative abstractions without G2 approval.
- Prefer reversible, focused changes. Treat already-shared migrations as immutable.

### Data and security

- Enforce duplicate registration with normalized input and a database unique constraint; validation alone is insufficient.
- Treat attendee email addresses as personal data. Never place them in URLs, public event props, logs, fixtures committed with real data, or client telemetry.
- Never commit secrets or populated environment files.
- Preserve CSRF protection and server-side validation for state-changing requests.
- Do not run destructive database commands, rewrite shared migrations, delete data, or change retention behavior without G2 approval and a recovery plan.
- Do not introduce authentication as an unapproved feature. Conversely, do not describe the unauthenticated admin area as secure.

### Unauthenticated-admin risk acceptance

The no-login admin requirement intentionally permits any application visitor who knows or discovers the admin routes to create or edit events and view registrant personal data. This is acceptable only for the approved proof-of-concept context.

Before deployment to any internet-accessible or shared environment, the human must explicitly accept the exposure or apply an infrastructure-level access restriction as part of the human-owned deployment plan. Adding application login remains out of scope unless requirements are revised at G2.

### Quality and repository safety

- Preserve unrelated user changes and do not rewrite, reset, or discard them.
- Do not delete tests or weaken assertions without explicit approval.
- Add meaningful regression coverage for behavior and domain changes; pure documentation, copy, and layout-only work does not require new tests.
- Run focused checks before broader checks and report failures honestly, including environmental failures.
- Do not claim a formatter, test, type check, build, or review passed unless it was run successfully in the current repository state.
- Use generated Wayfinder routes/actions and existing UI components rather than creating duplicate contracts.

### Agent operations

- The Orchestrator assigns bounded work and is the routing point for contract changes and blockers.
- Specialist agents must not expand their own scope or delegate approval authority.
- QA remains independent by default and reports defects before remediation.
- Every handoff must be persisted at its fixed path under `artifacts/`; conversation alone is not an artifact.
- Every handoff must include the run ID, repository state, acceptance-criteria IDs, changed artifacts, decisions/assumptions, verification evidence, risks, remediation-cycle count, and a status.
- An agent that encounters a gate must pause only the affected work; independent approved work may continue.
- Agents may write only the paths owned by their role charter or explicitly granted in the approved work plan.
- The Orchestrator is the sole writer of `RUN_TRACE.json`; agents provide structured usage and activity summaries for it.

### Errors, loops, and context limits

- A transient tool or environment failure may be retried once when safe. A repeated failure is recorded and escalated rather than hidden.
- A criterion may receive no more than two remediation cycles after its initial failure. A third recurrence of the same underlying defect, or two fixes that create new regressions, requires human escalation.
- Permission, privacy, security-boundary, and destructive-action failures are never retried without human approval.
- Before pause, interruption, reassignment, handoff, or context compaction, an agent must write its checkpoint under `artifacts/checkpoints/<run-id>/`.
- A checkpoint must record repository state, artifact references, decisions, criteria status, checks, remediation counts, blockers, and the exact next action.
- A resumed or replacement agent must re-read authoritative artifacts and verify the recorded repository state. Stale checkpoints are reconciled through the Orchestrator before work continues.
- Conversation memory must never be the only record of a decision, approval, failure, or unfinished task.

### Deployment

- Agents may prepare release notes, migration notes, rollback steps, and verification checklists.
- Agents must not deploy, change DNS, provision resources, rotate production secrets, or mutate production/shared data without an explicit deployment instruction and approval.
- The human owns the final environment choice, credentials, backups, deployment execution, and release acceptance.

## Stop-and-escalate conditions

Agents must stop the affected work and request a human decision when any of the following occurs:

- The requested behavior conflicts with an approved acceptance criterion or explicit exclusion.
- A new dependency, external service, paid resource, or credential is needed.
- A schema or command may destroy, irreversibly transform, or expose data.
- The backend/frontend contract must change after implementation has begun.
- Authentication, authorization, privacy exposure, or another security boundary changes.
- A test can pass only by weakening the documented contract.
- A deployment or shared-environment mutation is required.
- The same underlying acceptance failure reaches a third occurrence or remediation creates repeated regressions.
- A checkpoint cannot be reconciled with the current repository or approved contract.
- Material repository changes cannot be attributed safely to the current task.
- Two specialist recommendations conflict and the architecture handoff does not resolve them.

## Gate decision record

Each decision should be recorded in the work handoff using this minimum shape:

```text
Gate: G0 | G1 | G2 | G3 | G4
Decision: approved | approved-with-conditions | changes-requested | rejected | deferred
Run ID: trace identifier in RUN_TRACE.json
Scope: exact artifacts, actions, and acceptance criteria covered
Revision: commit SHA or pull request when G3/G4 applies
Conditions: constraints or required follow-up
Risks accepted: explicit residual risks
Evidence: repository artifact paths
Decision owner: human name or identifier
Date: YYYY-MM-DD
```

Approval applies only to the stated scope. Any material variance triggers a new gate.
