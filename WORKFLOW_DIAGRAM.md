# One-Page Multi-Agent Workflow

The agents are development-time collaborators; no agent runs in the Laravel application's request path. Detailed rules are in `WORKFLOW_DESIGN.md`.

```mermaid
flowchart TD
    H[Human-owned requirements]
    G0{G0 Requirements baseline}
    O[Orchestrator\nWORK_PLAN + RUN_TRACE]
    A[Architect\nARCH_SPEC]
    G1{G1 Plan and architecture approval}
    B[Backend Engineer\nCode/tests + BACKEND_HANDOFF]
    F[Frontend Engineer\nUI/types + FRONTEND_HANDOFF]
    I[Integrated candidate]
    Q[Independent QA\nQA_REPORT]
    R{Acceptance failures?}
    C{Material change?}
    G2{G2 Change control}
    T[Orchestrator\nACCEPTANCE_TRACEABILITY]
    G3{G3 Human merge approval\nexact commit or PR}
    M[Human-authorized merge]
    P[DEPLOYMENT_HANDOFF]
    G4{G4 Human deployment approval}
    D[Human-owned deployment\nand post-deploy verification]
    S[Stop, defer, or revise]

    H --> G0
    G0 -- revise or reject --> S
    G0 -- approve --> O --> A --> G1
    G1 -- changes --> A
    G1 -- reject or defer --> S
    G1 -- approve --> B
    G1 -- approve --> F
    B --> I
    F --> I
    I --> Q --> R
    R -- no --> T --> G3
    R -- yes --> C
    C -- no: cycle 1 or 2 --> O
    O --> B
    O --> F
    C -- yes, or third recurrence --> G2
    G2 -- bounded approval --> A
    G2 -- reject or defer --> S
    G3 -- changes --> O
    G3 -- reject or defer --> S
    G3 -- approve --> M --> P --> G4
    G4 -- not authorized --> S
    G4 -- authorize --> D
```

## Control legend

- Backend and frontend may run in parallel only after G1 and against the same approved contract.
- Every arrow between roles carries a persistent repository artifact; conversation only announces or summarizes it.
- Each artifact has one writer. Canonical names and owners are defined in `artifacts/README.md`.
- QA independently verifies every changed acceptance criterion. A repeated underlying failure is limited to two remediation cycles before human escalation.
- Agents checkpoint under `artifacts/checkpoints/<run-id>/` before pause, interruption, reassignment, or context compaction.
- The Orchestrator alone writes `RUN_TRACE.json`, including activity, decisions, elapsed time, usage/cost availability, errors, and retries.
- G3 authorizes only the identified merge. G4 separately authorizes deployment.
