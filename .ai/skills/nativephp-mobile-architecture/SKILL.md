---
name: nativephp-mobile-architecture
description: Architecture and production-readiness workflow for NativePHP Mobile applications. Use when deciding server versus device data ownership, online or offline behavior, API and authentication boundaries, native and Inertia coexistence, device persistence, lifecycle refresh, plugin or build customization, generated native projects, App Store or Play Store compliance, or when reviewing a mobile architecture. Complements the installed nativephp-mobile skill, which remains the authority for current NativePHP APIs and commands.
---

# NativePHP Mobile Architecture

Use this skill to make the product and system decisions around a NativePHP app. Use the installed `nativephp-mobile` skill and the installed package documentation for NativePHP 4 API syntax, EDGE components, plugins, commands, and tests.

Do not copy NativePHP 3 implementation details into a NativePHP 4 project. Extract the architectural intent, then implement it with the installed v4 extension points.

## Workflow

### 1. Read the project contract

Before proposing or changing mobile behavior, inspect:

- `development/scope.md` and relevant architecture, privacy, support, and release documentation;
- `composer.json`, `composer.lock`, `package.json`, and `nativephp.lock` when present;
- `config/nativephp.php`, `App\Providers\NativeServiceProvider`, native routes, components, and views;
- `bootstrap/app.php`, browser and API routes, authentication middleware, and API resources;
- the shared frontend entry point, API client, types, and Vite configuration; and
- existing mobile, API-contract, authentication, lifecycle, and release tests.

State the installed NativePHP major version and target platforms. Treat manifests and local vendor documentation as more authoritative than examples from another app.

### 2. Draw the trust and data boundaries

Name each participating runtime and its responsibility:

- remote Laravel server and canonical database;
- browser session client;
- on-device Laravel runtime and SQLite database;
- native UI screens and device APIs; and
- an embedded web experience, if one is already justified.

For a multi-platform web and mobile application, default to a contract-first,
server-canonical API unless the product scope explicitly requires local-first
ownership. Choose exactly one canonical-data strategy before implementing
persistence:

1. **Server-canonical, online required** — device data is ephemeral except for protected credentials and harmless preferences.
2. **Server-canonical with read snapshots** — device SQLite may cache explicit, versioned, non-authoritative snapshots; mutations still require the server.
3. **Device-canonical** — local SQLite owns the domain; remote synchronization requires a documented conflict and recovery model before it is added.

Do not let an implementation silently create two sources of truth. Read [references/data-and-runtime.md](references/data-and-runtime.md) for the decision criteria and runtime flow.

### Contract-first reuse rule

- Expose every shared server-canonical capability through a versioned remote
  API and an explicit API Resource or typed serializer.
- Reuse the same actions, query objects, policies, and Resources from remote
  browser Inertia controllers in-process. Do not make the Laravel server call
  its own API over HTTP.
- Treat native components and any Inertia/webview code running inside the
  on-device Laravel runtime as remote clients. They use one configured HTTPS
  API client and never query device SQLite for server-canonical records.
- Keep browser session/CSRF and native bearer-token authentication separate.
  Sharing a contract does not mean sharing a transport guard.

### 3. Define identity and transport

Document browser and native authentication separately. A first-party browser uses a stateful session and CSRF protection. A remote native client uses per-device bearer credentials over HTTPS and must not send cookies or CSRF tokens with those requests.

Treat the entire mobile bundle as public. Protect device credentials with a platform-backed secure-storage design, not browser storage, plaintext files, bundled configuration, or domain snapshots. Make token persistence and revocation failure-safe.

Read [references/authentication-and-security.md](references/authentication-and-security.md) before adding authentication, sensitive storage, uploads, logging, or third-party SDKs.

### 4. Choose the UI boundary

For NativePHP 4, build new mobile screens as `NativeComponent` classes with EDGE views and `Route::native()`. Reuse domain services, actions, query objects, resources, and explicit payload contracts across web and native interfaces.

Keep Inertia and React for the browser. Maintain an existing embedded web experience only when it has a documented product reason; do not make it the default for new mobile screens. When an embedded web experience remains:

- isolate runtime detection, URL construction, API behavior, and native bridge access;
- preserve platform back behavior, deep links, external-browser exits, safe areas, keyboard behavior, and app lifecycle changes;
- keep response types aligned with the backend contract; and
- present cached data, stale data, loading, offline, denial, and recovery states explicitly.

Read [references/frontend-and-lifecycle.md](references/frontend-and-lifecycle.md) for the reusable Inertia/TypeScript and lifecycle patterns.

### 5. Keep framework layers narrow

- Native components and HTTP controllers orchestrate only; they contain no database queries.
- Query objects or repositories own reads and payload aggregation.
- Focused actions or services own writes, transactions, and multi-step workflows.
- Form Requests own non-trivial HTTP validation; policies and middleware own authorization.
- API Resources or explicit typed serializers own stable remote payloads.
- Native-only storage services own device persistence and expose narrow interfaces.

Do not add an abstraction or package merely because a reference project used one. Follow the current repository structure and installed dependencies.

### 6. Customize generated builds safely

Treat `nativephp/` as generated output. In NativePHP 4, prefer, in order:

1. supported app configuration;
2. an explicitly allowlisted existing plugin;
3. a focused custom plugin with declarative assets and lifecycle hooks; then
4. only when no supported extension point exists, a reproducible generated-source patch.

A fallback patch must be version-pinned, idempotent, baseline-checked, regeneration-safe, reviewable, and tested on every target platform. It must fail closed when the upstream structure changes rather than rewriting an unfamiliar file.

Read [references/builds-compliance-and-testing.md](references/builds-compliance-and-testing.md) before changing plugins, permissions, platform manifests, generated projects, signing, or release workflows.

### 7. Verify the architecture

Add or update proportionate evidence:

- native component tests for rendering, interaction, navigation, accessibility, and bridge events;
- feature tests for runtime gating, API authentication, authorization, contracts, snapshots, and failure paths;
- frontend tests for client selection, bearer/session separation, URL handling, retries, uploads, and stale-data states;
- fresh-install and upgrade migration tests for device SQLite;
- plugin manifest and lifecycle-hook tests; and
- simulator or device checks for navigation, lifecycle, connectivity, permissions, safe areas, keyboards, deep links, and release archives.

Do not automatically run NativePHP build, run, package, or simulator commands. The upstream `nativephp-mobile` skill requires asking the user for the target platform and handing those commands to the user. Safe in-process tests and static checks may be run normally.

## Review output

When reviewing or proposing an architecture, report:

1. canonical data source and offline behavior;
2. browser, native, API, and storage trust boundaries;
3. native UI versus web UI ownership;
4. lifecycle, retry, stale-data, and recovery behavior;
5. plugin, permission, generated-build, and compliance impact;
6. contract and release evidence; and
7. unresolved product decisions or version-specific risks.

Call out copied NativePHP 3 techniques that should instead use NativePHP 4 components, configuration, plugins, hooks, or tests.
