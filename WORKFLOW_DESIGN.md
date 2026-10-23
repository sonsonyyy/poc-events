# Multi-Agent Workflow Design

## Purpose

This document defines how the events application is planned, implemented, reviewed, merged, and prepared for deployment by a human-governed AI Orchestrator and specialist agents. It is the detailed companion to the one-page `WORKFLOW_DIAGRAM.md`.

The agents are development-time collaborators. They are not application runtime services. The human owns requirements, approval gates, merging to the protected branch, and deployment.

## Governing artifacts

The following artifacts are authoritative in descending order:

1. Current explicit human instructions and recorded gate decisions.
2. `EVENT_SPEC.md`.
3. `HUMAN_GATES_AND_GUARDRAILS.md`.
4. `AGENTS.md` and the relevant `.agents/*.md` role charter.
5. `artifacts/ARCH_SPEC.md` after architecture approval.
6. Specialist handoffs and implementation details.

An agent must escalate a material conflict rather than silently choose one source.

## Roles, tools, and permissions

Each role's detailed permissions are defined in its charter. The summary below is binding:

| Role                | Primary tools                                                                                          | Owned writes                                                                                                                                                                | Prohibited without human approval                                                        |
| ------------------- | ------------------------------------------------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------- |
| System Orchestrator | Repository inspection, Git read operations, task delegation, documentation lookup, artifact validation | `RUN_TRACE.json`, `artifacts/WORK_PLAN.md`, `artifacts/DECISION_LOG.md`, `artifacts/ACCEPTANCE_TRACEABILITY.md`, `artifacts/DEPLOYMENT_HANDOFF.md`, Orchestrator checkpoint | Application implementation, merge, deployment, destructive commands                      |
| Solutions Architect | Repository/package inspection, version-scoped documentation search, read-only schema/route inspection  | `artifacts/ARCH_SPEC.md`, Architect checkpoint                                                                                                                              | Application implementation, dependencies, merge, deployment                              |
| Backend Engineer    | Laravel Boost read tools, Artisan generators, tests, Pint, static analysis, Git diff/status            | Approved backend/database/route/test paths, generated Wayfinder files, `artifacts/BACKEND_HANDOFF.md`, Backend checkpoint                                                   | Frontend product UI, dependency changes, destructive database actions, merge, deployment |
| Frontend Engineer   | Inertia/React/Tailwind guidance, frontend checks, type checking, build, browser/log inspection         | Approved `resources/js/**` and style paths, `artifacts/FRONTEND_HANDOFF.md`, Frontend checkpoint                                                                            | Backend contracts, dependency changes, merge, deployment                                 |
| QA / Reviewer       | Read-only inspection, test/static/build commands, browser checks, Git diff/status                      | `artifacts/QA_REPORT.md`, QA checkpoint; tests only when separately assigned                                                                                                | Silent implementation fixes, weakened tests, merge, deployment                           |

All tools operate with least privilege. A role may read the repository and governing artifacts as needed, but may write only its owned paths and paths explicitly included in the Orchestrator assignment. Permission to inspect is not permission to mutate.

## Persistent artifact contract

Conversation is transport, not the source of truth. Every substantive assignment, decision, handoff, verification result, and approval must be written to a repository artifact.

| Artifact                                    | Owner             | Required contents                                                                                |
| ------------------------------------------- | ----------------- | ------------------------------------------------------------------------------------------------ |
| `artifacts/WORK_PLAN.md`                    | Orchestrator      | Work packages, dependencies, parallelization plan, assigned role, acceptance IDs, gates          |
| `artifacts/ARCH_SPEC.md`                    | Architect         | Domain/schema design, route and prop contracts, validation, risks, decisions, acceptance mapping |
| `artifacts/BACKEND_HANDOFF.md`              | Backend           | Files changed, schema/API behavior, tests/checks, decisions, risks, acceptance IDs               |
| `artifacts/FRONTEND_HANDOFF.md`             | Frontend          | Files changed, UI states, contract use, accessibility evidence, checks/build, acceptance IDs     |
| `artifacts/QA_REPORT.md`                    | QA                | Criterion results, findings, reproductions, commands, evidence, go/no-go recommendation          |
| `artifacts/ACCEPTANCE_TRACEABILITY.md`      | Orchestrator      | One final status and evidence link for every acceptance criterion                                |
| `artifacts/DECISION_LOG.md`                 | Orchestrator      | Human gates, material decisions, superseded decisions, owner, date, rationale                    |
| `artifacts/DEPLOYMENT_HANDOFF.md`           | Orchestrator      | Accepted commit/PR, release contents, migrations, rollback and post-deploy checks                |
| `artifacts/checkpoints/<run-id>/<agent>.md` | Named agent       | Recoverable working state before pause, interruption, compaction, or handoff                     |
| `RUN_TRACE.json`                            | Orchestrator only | Structured per-run and per-agent activity, timing, usage, cost, errors, and outputs              |

Artifact files are created when their producing phase starts and updated by their sole owner. Parallel agents must never write the same artifact. Git history supplies prior versions; the current file is the canonical state for the active run.

Every handoff includes the run ID, repository commit or working-tree identifier, acceptance IDs, artifact paths, decisions, verification results, known risks, and next owner. A chat message may point to the artifact but may not replace it.

## Workflow phases and human gates

### Phase 0 — Requirements and plan

1. The human supplies or revises the governing requirements.
2. The Orchestrator writes `artifacts/WORK_PLAN.md` and opens the run in `RUN_TRACE.json`.
3. G0 records human approval of the requirements baseline.

### Phase 1 — Architecture

1. The Architect writes `artifacts/ARCH_SPEC.md` from the approved requirements.
2. The Orchestrator checks completeness and traceability.
3. G1 records human approval of the implementation plan, architecture, and risks.

### Phase 2 — Implementation

1. The Orchestrator assigns narrow backend and frontend work packages.
2. Backend and frontend work in parallel only where the approved contract makes them independent.
3. Each specialist writes its handoff artifact and checkpoint.
4. A material scope, dependency, schema-risk, privacy, or contract change triggers G2 change control.

### Phase 3 — Independent QA

1. The Orchestrator assembles the integrated candidate and sends artifact references to QA.
2. QA independently verifies every changed behavior against acceptance criteria and writes `artifacts/QA_REPORT.md`.
3. Failures route through the Orchestrator to the accountable specialist.
4. QA re-verifies affected and regression criteria after remediation.

### Phase 4 — Merge

1. The Orchestrator writes `artifacts/ACCEPTANCE_TRACEABILITY.md` from the specialist and QA evidence.
2. G3 identifies the exact commit or pull request and asks the human to approve, reject, or request changes before merge to the protected branch.
3. Only the human, or an explicitly authorized mechanism, performs the merge.

### Phase 5 — Deployment handoff

1. The Orchestrator prepares `artifacts/DEPLOYMENT_HANDOFF.md` for the accepted merge.
2. G4 asks the human to authorize the exact deployment action and environment.
3. The human owns deployment and post-deployment acceptance.

## Parallel execution rules

- Parallelize tasks only when they have separate write ownership and a stable shared contract.
- Backend and frontend may run concurrently after G1.
- QA begins only after the integrated candidate and both handoffs are available.
- Documentation review, test-plan preparation, and demo planning may run alongside implementation when they do not claim implementation results prematurely.
- Contract changes stop only affected work. Unrelated approved work may continue.
- The Orchestrator records parent/child run IDs so parallel work can be reconstructed.

## Error handling and bounded feedback loops

Errors are classified before retry:

| Error class                                                   | Action                                                                                |
| ------------------------------------------------------------- | ------------------------------------------------------------------------------------- |
| Transient tool/environment failure                            | Retry once when safe, then record and escalate if still failing.                      |
| Implementation or test failure                                | Route to the accountable specialist with evidence and affected acceptance IDs.        |
| Contract or requirement conflict                              | Stop affected work and invoke G2 or another applicable human gate.                    |
| Permission, destructive-action, privacy, or security boundary | Do not retry; stop and request human approval.                                        |
| Agent interruption or context exhaustion                      | Persist a checkpoint, record the interruption, and resume or reassign from artifacts. |

A criterion may go through at most two remediation cycles after its initial failure. A third recurrence of the same underlying failure, or two fixes that create new regressions, requires human escalation. The Orchestrator records each cycle and may not reset the count by rewording the same defect.

Superseded decisions remain in `artifacts/DECISION_LOG.md` and `RUN_TRACE.json`; they are marked superseded rather than deleted.

## Context limits, checkpoints, and recovery

Agents must checkpoint before a planned pause, handoff, reassignment, context compaction, or when they can no longer reliably retain the full task state.

A checkpoint records:

- Run ID, role, objective, and current status.
- Acceptance IDs completed, in progress, blocked, and not started.
- Files changed and the current Git commit or working-tree identifier.
- Decisions, assumptions, unresolved questions, and remediation-cycle counts.
- Commands/checks run and their latest results.
- Authoritative input/output artifact paths and hashes when available.
- The exact safe next action.

On resume, an agent must re-read the governing artifacts, its charter, the current work plan, the latest checkpoint, and relevant handoffs. It verifies the recorded repository state before continuing. If the repository or approved contract has changed, it marks the checkpoint stale and asks the Orchestrator to reconcile it.

Conversation memory is never the only store for a decision or unfinished task.

## Run trace, cost, and privacy

`RUN_TRACE.json` conforms to `RUN_TRACE.schema.json`. The Orchestrator is its sole writer and records one entry per real run plus nested agent executions.

Specialists return a structured usage summary to the Orchestrator. The trace records model, charter version, start/end time, elapsed time, status, parent/child runs, acceptance IDs, artifact references, decisions, tool-call summaries, errors, retries, per-agent usage, and run-level token/cost totals.

Token or cost data that the execution environment does not expose must be `null` with an `unavailable_reason`; it must never be guessed. Cost includes currency and source. Time is measured from recorded timestamps rather than estimated after the run.

The Orchestrator serializes trace updates after specialist handoffs so parallel agents never race to rewrite the ledger. An interrupted update must leave the last valid JSON document intact.

Do not store raw secrets, personal data, environment values, or unnecessary full prompts/tool outputs. Store redacted summaries and artifact references/hashes. Any redaction is recorded in the run entry.

## Completion rules

A run is complete only when:

- Its required artifacts exist and identify the same run and repository state.
- Every scoped acceptance criterion has evidence or an explicit blocked/deferred status.
- QA has independently verified the integrated candidate.
- Human gate decisions are recorded.
- Usage, elapsed time, cost availability, errors, and remediation cycles are captured in `RUN_TRACE.json`.
- The final checkpoint and next action are unambiguous.

Implementation completion does not authorize merge, and merge approval does not authorize deployment.
