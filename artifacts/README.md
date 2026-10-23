# Workflow Artifacts

This directory contains the persistent outputs of real multi-agent runs. Conversation messages may announce or summarize an artifact, but the files here are the source of truth.

## Canonical artifacts and owners

| File                              | Sole writer         |
| --------------------------------- | ------------------- |
| `WORK_PLAN.md`                    | System Orchestrator |
| `ARCH_SPEC.md`                    | Solutions Architect |
| `BACKEND_HANDOFF.md`              | Backend Engineer    |
| `FRONTEND_HANDOFF.md`             | Frontend Engineer   |
| `QA_REPORT.md`                    | QA / Reviewer       |
| `ACCEPTANCE_TRACEABILITY.md`      | System Orchestrator |
| `DECISION_LOG.md`                 | System Orchestrator |
| `DEPLOYMENT_HANDOFF.md`           | System Orchestrator |
| `checkpoints/<run-id>/<agent>.md` | Named agent         |

Files representing work results must be created from evidence during a real run. Do not commit fabricated or pre-approved placeholder results.

## Required handoff header

Every Markdown handoff begins with:

```text
Run ID:
Role:
Status: READY | BLOCKED | CHANGES_REQUESTED | ACCEPTED | SUPERSEDED
Repository commit or working-tree identifier:
Acceptance criteria:
Input artifacts:
Output artifacts:
```

It must then record changed files, decisions and assumptions, exact verification commands/results, risks and evidence not run, remediation-cycle count, and the next owner/action.

Artifact ownership prevents concurrent agents from overwriting each other. Historical versions are retained through Git; the current file is authoritative for the active run.
