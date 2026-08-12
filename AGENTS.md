# Laravel and NativePHP Boilerplate Agent Guide

This repository is a reusable Laravel and NativePHP Mobile application
boilerplate. Act as a senior Laravel engineer who is comfortable with the
versions and tools declared in `composer.json` and `package.json`, including
Laravel, Inertia.js, React, TypeScript, Tailwind CSS, Vite, Pest, Larastan, and
NativePHP Mobile.

Prefer small, conventional, tested changes. Keep this repository generic:
application-specific behaviour belongs in the consuming project and its
documentation, not in the boilerplate unless it is broadly reusable.

## Sources of truth

Read the relevant source before changing behaviour:

1. `development/scope.md` defines product behaviour and boundaries once a
   consuming project has populated it.
2. `composer.json` and `package.json` define the installed stack and supported
   quality commands.
3. `bootstrap/app.php`, `bootstrap/providers.php`, and the route files define
   the Laravel application structure.
4. `config/nativephp.php`, `app/Providers/NativeServiceProvider.php`,
   `app/NativeComponents`, and `resources/views/native` define the mobile
   application structure.
5. `.env.example` documents safe environment configuration.

When implementation and documented product behaviour disagree, stop and
resolve the disagreement rather than silently choosing a new behaviour. Update
the relevant documentation when changing architecture, setup, configuration,
mobile behaviour, or user-visible conventions.

## Scope validation before implementation

Before implementing a feature, review the request, `development/scope.md`, and
the relevant existing behaviour. Identify conflicting requirements, missing
acceptance criteria, and assumptions that could affect the implementation.

- For minor ambiguities, choose the safest conventional interpretation,
  document the assumption, and continue.
- For major ambiguities, pause the affected implementation and request
  clarification. An ambiguity is major when plausible answers would materially
  change the architecture, data model, public API, authentication or
  authorization boundary, canonical data ownership, dependency choices,
  platform support, or user-visible behaviour.
- Do not block on details that can be discovered from the repository or handled
  through a small, reversible implementation choice.

## Baseline stack

Use the versions constrained by the dependency manifests. The current baseline
is:

- PHP `8.4+` and Laravel `13.x`
- Inertia.js `3.x`
- React `19.x` with TypeScript
- Tailwind CSS `4.x`
- Vite `8.x`
- NativePHP Mobile `4.x` and NativePHP Mobile UI
- Pest `5.x`
- Larastan/PHPStan for static analysis
- Laravel Wayfinder for typed route integration

Do not add dependencies, replace framework choices, or create new top-level
directories without a demonstrated need. Obtain explicit approval for changes
that materially expand the boilerplate's stack or maintenance burden.

## Application and mobile boundaries

- Browser pages use Inertia and live under `resources/js/pages`.
- Native screens use `Route::native()`, component classes under
  `app/NativeComponents`, and views under `resources/views/native`.
- Build new mobile screens as NativePHP 4 native components and EDGE views.
  Treat the existing embedded web experience as a deliberate compatibility
  boundary, not the default foundation for new mobile screens. Shared web
  experiences may remain embedded when the product scope justifies them, but
  platform-native interactions belong behind focused NativePHP components,
  services, or explicitly allowlisted plugins.
- Keep platform-independent domain logic outside NativePHP components so it can
  be tested without an emulator.
- Decide and document whether server data or device data is canonical before
  adding synchronization, caching, or offline mutations. Do not create two
  sources of truth accidentally.
- When the server is canonical, choose explicitly between online-required
  behaviour and non-authoritative read snapshots. Cached snapshots must have a
  documented payload contract, freshness metadata, fallback order, and stale
  UI; they must not silently accept offline mutations.

### API-first multi-platform contract

- Treat the remote Laravel server as canonical for shared web and mobile data
  unless `development/scope.md` explicitly chooses a different ownership model.
- Build shared capabilities contract-first: stable versioned API Resources,
  policies, actions, and query objects form one application contract. Native
  clients consume it over HTTPS; remote browser Inertia controllers reuse it
  in-process. Never make the Laravel server call its own API over loopback HTTP.
- Browser and mobile share application and serialization layers, not transport
  authentication. Browser Inertia uses the `web` session guard and CSRF;
  native clients use per-device Sanctum bearer tokens with no cookies or CSRF.
- An Inertia experience running inside the on-device Laravel runtime is a
  mobile client, not the remote server. It must use the configured remote API
  client for server-canonical data rather than querying device SQLite.
- Every new server-canonical payload used by both platforms needs an explicit
  API Resource (or typed serializer), API feature tests, and synchronized
  TypeScript/native client types. Keep browser redirects and API JSON in
  separate controllers.

Everything bundled into a native application must be treated as public. Never
bundle production database credentials, mail credentials, object-storage
credentials, private keys, signing secrets, or other server-only secrets.
Native access tokens and sensitive device values belong in platform secure
storage, not browser local storage, plain files, bundled environment files, or
SQLite.

NativePHP plugins must be installed deliberately and explicitly allowlisted in
`App\Providers\NativeServiceProvider`. Review every plugin's permissions,
native manifest, data collection, and platform behaviour before enabling it.
Keep `config/nativephp.php` cleanup rules and permission descriptions aligned
with the capabilities that ship in the app.

The `nativephp/` directory contains generated platform projects. Regenerate it
through NativePHP tooling when necessary; do not hand-edit generated files to
implement lasting application behaviour. Prefer supported configuration, then
an allowlisted plugin with declarative assets or lifecycle hooks. A generated
source patch is a last-resort compatibility measure and must be version-pinned,
idempotent, baseline-checked, regeneration-safe, and fail closed when upstream
files change. Do not commit generated build products, signing credentials,
provisioning profiles, keystores, or local IDE state.

When changing mobile behaviour:

- preserve back navigation and deep-link behaviour;
- account for safe areas, keyboards, app lifecycle changes, and intermittent
  connectivity;
- request the narrowest platform permissions at the point of use;
- provide clear denial and recovery states;
- verify on the relevant iOS and/or Android simulator or device; and
- keep native route names and navigation paths stable unless the change is
  intentional and tested.

## Laravel conventions

- Inspect sibling files and follow established conventions before creating or
  editing code. Reuse existing application code before introducing another
  abstraction.
- Use descriptive names for classes, methods, variables, routes, and tests.
- Use explicit PHP parameter and return types, constructor property promotion,
  and useful array-shape PHPDoc for structured arrays. Do not add empty
  constructors. Name enum cases in TitleCase.
- Use environment variables only in configuration files. Read values elsewhere
  through `config()`.
- Generate application links with named routes and `route()`. Keep Wayfinder
  route types synchronized with backend route changes.
- Follow the current streamlined Laravel structure: register middleware,
  exception handling, and routing in `bootstrap/app.php`; register application
  providers in `bootstrap/providers.php`; put console routes and scheduling in
  `routes/console.php`; and rely on command auto-discovery.
- When a migration modifies a column, repeat every existing attribute that
  must be retained so the schema does not silently lose it.
- Prefer Eloquent models and explicit relationships over raw SQL. Eager load
  relationships deliberately to prevent N+1 queries.
- Keep controllers and native components thin. Controllers contain no Eloquent,
  query-builder, facade, relationship-loading, or other database queries.
  Delegate reads and payload aggregation to focused repositories or query
  objects, and writes or multi-step workflows to focused action or service
  classes.
- Use form requests for non-trivial validation, policies for authorization,
  and API resources for stable JSON payloads.
- Enforce important uniqueness and integrity rules in the database as well as
  in validation.
- Wrap multi-record writes and destructive workflows in transactions. Recheck
  destructive preconditions inside the transaction rather than trusting an
  earlier UI confirmation.
- Use soft deletion only when product and retention requirements call for it.
  Every destructive user action should have an explicit confirmation and a
  documented recovery or permanence model.

## Authentication and API rules

Do not add an authentication scheme by implication. Document the browser,
mobile, and API trust boundaries when authentication is introduced.

- First-party browser requests should use Laravel's stateful session
  authentication and CSRF protection.
- A remote API used by native clients should use a supported token mechanism,
  such as per-device Sanctum tokens, and protected routes should use the
  appropriate authentication middleware.
- Do not send CSRF tokens with bearer-token API requests, and do not use API
  tokens for the first-party browser Inertia application.
- Store mobile tokens in device secure storage, scope abilities narrowly where
  applicable, rotate credentials safely, and revoke the current device token
  on logout.
- Apply authorization on the server for every protected resource. Hidden UI is
  not an authorization boundary.
- Version externally consumed APIs, keep payloads explicit, and return
  consistent validation and error shapes.
- Give native tokens an explicit ability and expiration policy. Ordinary
  device logout revokes only the current token; all-device revocation is a
  separately named product workflow.
- Rate-limit authentication, verification, password reset, support, upload,
  and other abuse-sensitive endpoints as appropriate.

## Data, files, and queued work

- Keep database and filesystem choices configurable unless the product scope
  explicitly standardizes them.
- Validate uploads on the server by size and actual file type; never trust an
  extension or client-provided MIME type alone.
- Store sensitive user files on a private disk and return authorized or
  temporary URLs rather than exposing a public bucket.
- Coordinate database and external file changes through idempotent actions or
  retry-safe jobs. Avoid claiming success until required state can be
  recovered after failure.
- Queue slow or failure-prone work such as email delivery, media processing,
  imports, and remote API calls when appropriate.
- Queued jobs must be safe to retry and should avoid duplicate external side
  effects.
- Never log access tokens, passwords, secrets, or sensitive file contents.

## Inertia, React, and Tailwind conventions

- Write frontend code in TypeScript and keep shared types explicit.
- Keep page components under `resources/js/pages`, reusable application
  components under `resources/js/components`, and small reusable primitives
  under `resources/js/components/ui` when those directories are needed.
- For substantial features, keep transport types, API paths, client/fetch
  behaviour, mapping, and cache adapters in a focused `resources/js/lib`
  module; keep lifecycle-driven state in focused hooks; and let pages compose
  the feature rather than owning its transport details.
- Use Laravel controllers and named routes for Inertia navigation.
- Keep remote API access behind a shared client; do not scatter base URLs,
  authentication headers, or environment checks through components.
- Update frontend response types whenever backend resources change.
- Prefer React state, Inertia forms, and focused hooks before adding another
  state-management framework.
- Keep Tailwind CSS 4 configuration CSS-first in `resources/css/app.css`. Use
  `@theme` for design tokens and do not introduce removed Tailwind 3 utilities
  or a JavaScript configuration file without a demonstrated need.
- Build mobile-first and verify responsive layouts in both a browser and the
  NativePHP experience where applicable.
- Respect safe areas, keyboard navigation, screen readers, reduced motion,
  contrast, focus visibility, and touch-target size.
- Preserve matching dark-mode variants when changing an interface that already
  supports dark mode.

Do not add a second component library or frontend state framework without a
demonstrated need and explicit approval.

## Project-local skill precedence

Project conventions and installed dependencies override generic examples in
third-party skills:

- Preserve Laravel's conventional singular controller names and the existing
  `app/Http/Controllers` structure. The project-local `laravel-controllers`
  skill is retained and narrowed to its zero-query, HTTP-only controller rule;
  do not introduce plural controller names, an alternate directory hierarchy,
  Spatie Query Builder, or uninstalled companion patterns.
- Database optimization advice is conditional on the configured database and
  cache drivers. Inspect the query plan and current schema first; do not assume
  Redis, cache tags, PostgreSQL concurrent indexes, Laravel Debugbar, or another
  dependency is available or approved.
- Run PHPStan through `composer types:check`. Fix root causes without weakening
  configuration, adding ignores or baselines, installing IDE Helper, or using
  runtime `assert()` as a substitute for application validation unless the
  user explicitly approves the tradeoff.
- Use `nativephp-mobile` as the authority for installed NativePHP 4 APIs and
  commands. Use the project-local `nativephp-mobile-architecture` skill for
  canonical-data, authentication, lifecycle, generated-build, compliance, and
  production-readiness decisions learned from earlier mobile applications.

## Testing and quality gates

Use Pest and add focused coverage for new behaviour. Prefer feature tests for
HTTP behaviour, native routes, authentication, authorization, validation,
transactions, and database rules. Use unit tests for isolated transformations
and services.

Do not remove or weaken existing tests without explicit approval. Use model
factories and their existing states for setup, prefer specific response
assertions such as `assertForbidden()` and `assertNotFound()`, and use Pest
datasets when they make repeated cases clearer.

Run focused checks while iterating, then run the relevant full quality gates:

```bash
composer test
npm run lint:check
npm run format:check
npm run types:check
npm run build
```

For a focused backend test, use a command such as:

```bash
php artisan test --filter MobileHomeTest
```

Never run `migrate:fresh`, `db:wipe`, destructive SQL, or production migrations
against an unidentified database. Destructive database commands are permitted
only on a confirmed disposable local/test database or when the user explicitly
authorizes the exact target.

## Generated and sensitive files

Do not commit:

- `.env` files or copied production configuration;
- `vendor/`, `node_modules/`, `public/build/`, or generated native build
  products;
- database, mail, object-storage, SSH, API, Apple, Android, or signing
  credentials;
- user uploads or generated private media; or
- local IDE, simulator, emulator, and operating-system state.

Use `.env.example` only for safe examples and blank secret placeholders. When
adding an environment variable, update the example and the relevant
documentation without adding a real credential.

## Documentation expectations

Keep implementation, tests, `AGENTS.md`, `development/scope.md`, dependency
manifests, and environment examples consistent. A consuming application should
add focused architecture, design, deployment, privacy, and operational
documentation as those decisions are made; do not encode product-specific
assumptions in this boilerplate prematurely.
