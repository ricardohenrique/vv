---
name: laravel-sanctum
description: Implement or refactor Laravel applications to expose versioned APIs authenticated with Laravel Sanctum, especially for first-party iOS and Android clients. Use when adding per-device login, registration, logout, bearer-token protection, API resources, authorization, native token persistence, API tests, or when separating browser session authentication from mobile API authentication.
---

# Laravel Sanctum

## Overview

Build a server-canonical Laravel API for native clients while preserving the browser application's session and CSRF boundary. Treat bearer tokens as device credentials, keep API payloads explicit, and verify authentication, authorization, validation, and revocation with feature tests.

## Workflow

1. Read the repository instructions, product scope, dependency manifests, routing bootstrap, authentication code, policies, resources, and native client structure.
2. Search for an existing API client, token store, domain actions, requests, resources, and tests before adding abstractions.
3. Resolve the trust boundaries in writing before implementation:
   - Browser: Laravel `web` guard, session cookie, and CSRF protection.
   - Native: versioned HTTPS API, per-device Sanctum bearer token, and no CSRF token.
   - Server: canonical data and authorization for every protected resource.
4. Install Sanctum only when absent and authorized. Follow the repository's Laravel version and existing conventions; do not assume a particular installer command or migration filename.
5. Add versioned routes under `/api/v1`. Keep authentication endpoints abuse-limited and protect application endpoints with `auth:sanctum` plus policies or `can` middleware.
6. Implement per-device authentication:
   - Accept validated `email`, `password`, and `device_name` values.
   - Verify credentials without creating a browser session.
   - Create a token with narrowly scoped abilities when the application benefits from them.
   - Return the plaintext token once, inside an explicit authentication resource.
   - Revoke only `currentAccessToken()` on device logout.
7. Reuse domain actions, query objects, policies, and API resources across browser and API controllers. Remote browser Inertia controllers call those boundaries in-process rather than making loopback HTTP requests. Keep Inertia redirects out of API controllers and JSON responses out of browser controllers.
8. Put all remote client calls behind one configured API client. For native requests, attach `Authorization: Bearer ...`, disable cookies/XSRF behavior, and clear local credentials on a 401 response.
9. Store mobile tokens in platform secure storage. If a paid plugin is unavailable and the application uses NativePHP's per-install application key, an encrypted app-private file may be used only as a documented fallback: encrypt with Laravel `Crypt`, expose it only through native-runtime-gated local endpoints, delete unreadable ciphertext, and never persist plaintext.
10. Add focused feature tests before running the full repository quality gates. Test login failure and rate limiting, device-name validation, token abilities, protected access, ownership boundaries, consistent validation JSON, logout revocation, browser/native boundary separation, and encrypted token persistence.
11. Update architecture, scope, safe environment examples, and setup documentation. State where the API base URL comes from and which data source is canonical.

## Guardrails

- Do not replace first-party browser session authentication with bearer tokens.
- Do not send CSRF tokens with bearer-token requests.
- Do not bundle server credentials, production secrets, or a shared production database connection in a native build.
- Do not put access tokens in `localStorage`, `sessionStorage`, SQLite, logs, URLs, plain files, or bundled environment files.
- Do not trust hidden UI for authorization; enforce ownership on the server.
- Do not issue a new token on every request or leave superseded device tokens active without an explicit product decision.
- Do not expose model internals accidentally. Use API resources and stable validation/error shapes.
- Do not run destructive migrations or database reset commands against an unidentified database.

## Repository-Specific Reference

Read [references/implementation-patterns.md](references/implementation-patterns.md) when implementing a NativePHP client, adapting an existing Inertia application, or reviewing an API-first refactor.
