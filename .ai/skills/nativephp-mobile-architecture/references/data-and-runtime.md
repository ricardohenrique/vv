# Data and runtime architecture

## Start with ownership

For a boilerplate intended to serve browser, iOS, and Android clients, begin
with a server-canonical, versioned API contract. Browser Inertia may reuse the
same application and Resource layers in the server process; it should not make
a loopback HTTP request merely to prove that an API exists. Native UI and any
embedded web code executing on-device are remote clients and must use the API.

Record a decision table before adding caching or synchronization:

| Concern | Remote server | Device runtime | Recovery authority |
| --- | --- | --- | --- |
| Domain records | canonical, snapshot, or none | canonical, snapshot, or none | exactly one owner |
| Credentials | issues and revokes | protects the current device credential | server revocation plus local clearing |
| Preferences | optionally synced | local when harmless | documented reset behavior |
| Files | canonical private storage or none | temporary capture/cache | server or explicit local export |

If both server and device can mutate the same domain record, stop and define identifiers, versions, conflict resolution, deletion semantics, retry idempotency, and partial-failure recovery. “Sync later” is not an architecture.

## Strategy A: server-canonical and online required

Use this when every useful operation requires current remote state.

- The on-device Laravel runtime is a UI host and API client, not a second application server.
- Persist only protected device credentials, harmless preferences, and short-lived caches.
- Show actionable connectivity failures; do not imply offline writes were accepted.
- Keep the remote API versioned and its payloads explicit.
- Do not bundle remote database credentials. The device talks to HTTPS endpoints only.

This is the simplest strategy for authenticated mutable applications.

## Strategy B: server-canonical with read snapshots

Use this for read-heavy experiences that should boot quickly or remain readable during brief outages.

- Device SQLite stores versioned payload snapshots, not an independent domain model.
- Snapshots contain the minimum public/non-sensitive data required by the UI.
- A bootstrap endpoint aggregates boot-critical data to reduce round trips.
- Boot reads the latest usable snapshot first, then refreshes in the background when visible and online.
- Remote requests use short connection timeouts and bounded total timeouts for boot-critical paths.
- Cache writes occur only after a successful, schema-valid response.
- The UI carries `synced_at` or equivalent metadata and explicitly labels stale content.
- Server mutations remain online-only unless a full synchronization design is approved.

Avoid stacking several caches with unclear ownership. If PHP response cache, SQLite snapshots, and frontend memory/storage all exist, document each TTL, invalidation rule, and fallback order.

## Strategy C: device-canonical

Use this only when the product is intentionally local-first.

- SQLite migrations run safely on fresh installs and upgrades.
- Initial device data is installed through migrations when required by NativePHP, not an assumed CLI seeder.
- Exports, backups, device replacement, and deletion behavior are product requirements.
- Remote sync is a separate feature with a conflict protocol, not a transparent repository substitution.

## Runtime detection

Centralize runtime and platform detection. Application code should not scatter environment reads, loopback-host checks, protocol checks, or user-agent conditions.

- Prefer installed NativePHP 4 runtime APIs and configuration.
- Read environment variables only from configuration files unless the installed runtime has a documented boot-time exception.
- Give tests a supported way to set the runtime without mutating global process state where possible.
- Keep browser, native UI, and embedded-web routing decisions explicit.

## Remote client behavior

Centralize base URL selection, absolute URL construction, TLS policy, auth headers, timeouts, response normalization, upload behavior, and logging redaction.

- Never disable TLS verification in a production client.
- Abort obsolete requests when screens change or components unmount.
- Retry only idempotent or explicitly retry-safe operations.
- Use bounded backoff for transient statuses; do not retry validation or authorization failures blindly.
- Treat malformed or non-JSON API responses as transport failures, not valid empty data.
- Preserve the original actionable error when cleanup or fallback also fails.
