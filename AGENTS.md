<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:

- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
    - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>

# AI Multi-Agent System Architecture & Operating Guidelines

# Executive Overview

This repository delivers a thin vertical slice of an events application through a human-governed, multi-agent development workflow. The human owns the product requirements and deployment. A System Orchestrator coordinates the Solutions Architect, Backend Engineer, Frontend Engineer, and QA / Reviewer against the acceptance criteria in `EVENT_SPEC.md`.

The agents are development-time collaborators, not runtime application services. They must preserve the installed Laravel 13, Inertia React 3, React 19, Tailwind CSS 4, Wayfinder, and Pest stack unless a human explicitly approves a dependency change.

The authoritative project artifacts are:

- `EVENT_SPEC.md` — product scope, domain requirements, and acceptance criteria.
- `AGENTS.md` — repository-wide engineering and collaboration rules.
- `.agents/*.md` — specialist role charters.
- `HUMAN_GATES_AND_GUARDRAILS.md` — human approval boundaries and stop conditions.
- `WORKFLOW_DESIGN.md` — detailed execution model, permissions, artifacts, error handling, context recovery, and cost tracking.
- `WORKFLOW_DIAGRAM.md` — one-page delivery sequence and artifact flow.
- `artifacts/README.md` — canonical artifact names, ownership, and handoff header.
- `RUN_TRACE.schema.json` and `RUN_TRACE.json` — structured trace contract and Orchestrator-owned run ledger.

The human owns these governance artifacts. Agents may propose changes but must not edit them unless the human explicitly requests the edit.

# System Topology

## Delivery topology

```text
Human requirements and gates
            |
            v
    System Orchestrator
       /      |       \
Architect  Backend  Frontend
       \      |       /
        Integrated candidate
                |
          QA / Reviewer
                |
      Human acceptance and deployment
```

- The Orchestrator is the coordination and traceability hub.
- The Architect establishes the approved domain, schema, route, prop, validation, and risk contracts.
- Backend and Frontend may work in parallel only after the shared contract is approved.
- QA independently verifies the integrated candidate and routes findings through the Orchestrator.
- Human approval is required for the implementation plan, merge, and deployment at the gates defined in `HUMAN_GATES_AND_GUARDRAILS.md`.

## Application runtime topology

```text
Browser (public attendee or unauthenticated admin)
    <-> Inertia React 3 + React 19 + TypeScript + Wayfinder
    <-> Laravel 13 web routes, controllers, and validation
    <-> Eloquent Event/Registration domain and database constraints
```

No AI agent or Orchestrator participates in application requests. Registration uniqueness must be protected by the database as well as the request workflow.

# Agent Roster & Core Responsibilities

| Role                  | Charter                         | Core responsibility                                                                                                   | Must not own                                                   |
| --------------------- | ------------------------------- | --------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------- |
| Human / Project Owner | `HUMAN_GATES_AND_GUARDRAILS.md` | Requirements, risk decisions, acceptance, and deployment.                                                             | Routine specialist implementation.                             |
| System Orchestrator   | `.agents/orchestrator.md`       | Work breakdown, sequencing, assignment, traceability, gates, and integrated readiness.                                | Product decisions, specialist design decisions, or deployment. |
| Solutions Architect   | `.agents/architect.md`          | Minimal technical design, contracts, invariants, data/privacy decisions, and architecture risks.                      | Unapproved requirements or deployment.                         |
| Backend Engineer      | `.agents/backend.md`            | Laravel domain, persistence, validation, routes/controllers, server-side Inertia contract, and focused Pest coverage. | Frontend design, scope changes, or deployment.                 |
| Frontend Engineer     | `.agents/frontend.md`           | Inertia React pages, typed integration, UI states, responsiveness, and accessibility.                                 | Backend contract changes, scope changes, or deployment.        |
| QA / Reviewer         | `.agents/test.md`               | Independent acceptance traceability, regression/security/accessibility review, and go/no-go recommendation.           | Human acceptance, silent remediation, or deployment.           |

Every specialist must read this file, its own charter, the current Orchestrator assignment, and all applicable human-owned requirements before acting.

Detailed tool access and path-level write permissions are defined in each role charter. Read access does not grant write access, and a specialist must not write another role's artifact.

# Handoff Protocols & Artifact Standards

## Authority and routing

1. Current explicit human instructions and approved gate decisions take precedence.
2. `EVENT_SPEC.md` defines product behavior; this file and the role charters define execution behavior.
3. The approved architecture handoff defines implementation contracts but cannot override the product specification.
4. Contract changes, cross-role blockers, and requirement ambiguity route through the Orchestrator.
5. Material scope, dependency, security, schema-risk, or deployment decisions route to the human under the applicable gate.

Agents must not resolve a substantive conflict by silently choosing one source. Record the conflict, affected criteria, options, and recommendation, then escalate.

## Assignment standard

Every assignment from the Orchestrator must state:

- Run ID and current repository commit or working-tree identifier.
- The objective and bounded scope.
- Acceptance-criteria IDs.
- Required inputs and expected output artifacts.
- Files or layers in scope and explicit exclusions.
- Allowed tools, commands, and write paths for that assignment.
- Dependencies on other agents or approved contracts.
- Verification required for completion.
- Retry/remediation budget and checkpoint path.
- Applicable human gate or stop conditions.

## Handoff standard

Every handoff must contain:

```text
Status: READY | BLOCKED | CHANGES_REQUESTED | ACCEPTED
Run ID:
Repository commit or working-tree identifier:
Scope and AC IDs:
Artifacts changed or produced:
Decisions and assumptions:
Verification commands/checks and results:
Known risks, failures, and evidence not run:
Retry and remediation-cycle count:
Usage/cost availability summary:
Checkpoint path:
Next owner and requested action:
```

Use `ACCEPTED` only for acceptance by the receiving workflow stage; it never substitutes for human acceptance at G3. Do not report `READY` when required verification is failing or has not run without clearly qualifying the status.

## Artifact standards

- Use repository-relative paths and stable acceptance-criteria IDs.
- Persist work at the canonical paths in `artifacts/README.md`; conversation may summarize or link but cannot replace a repository artifact.
- Maintain one sole writer per artifact so parallel agents never overwrite each other.
- Keep requirement, architecture, implementation, verification, and deployment artifacts distinct.
- Make assumptions explicit and label recommendations separately from approved decisions.
- Record exact commands and meaningful results; never claim a check passed if it was not executed successfully.
- Keep changes focused, preserve unrelated work, and identify generated files.
- Do not include secrets, populated environment values, production personal data, or private credentials.
- Record prompt/tool summaries and redacted artifact references in `RUN_TRACE.json`, not unnecessary raw content.
- Record unavailable token or cost fields as `null` with a reason; never estimate them.
- Use Mermaid or compact tables when they make system relationships or traceability materially clearer.
- Do not create additional documentation files unless the human explicitly requests them.

## Completion and feedback

- An implementation task is complete only when its scoped criteria have evidence, relevant focused tests pass, and the handoff is accepted by the Orchestrator.
- A QA finding returns to the accountable specialist through the Orchestrator with severity, evidence, affected criterion, and expected outcome.
- Remediation that stays inside the approved design does not need a new architecture gate. A material contract or risk change triggers G2.
- The same underlying criterion may receive at most two remediation cycles before human escalation.
- Before pause, interruption, reassignment, or context compaction, every agent writes a checkpoint containing recoverable state and the exact next action.
- Resumed agents rehydrate from governing artifacts, the work plan, handoffs, and checkpoint, then verify the recorded repository state before continuing.
- G3 human approval is required before merging the identified revision; implementation or QA completion is not merge authorization.
- The workflow ends with a separate G4 human deployment decision; merge approval does not authorize deployment.
