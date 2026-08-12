# Frontend and lifecycle patterns

## NativePHP 4 placement

New mobile screens belong in NativePHP 4 `NativeComponent` classes and EDGE views. Keep platform-independent domain behavior in actions, services, repositories, query objects, and serializers that can also serve HTTP or browser interfaces.

Use Inertia and React for the browser. An embedded web experience is a deliberate compatibility or content boundary, not a substitute for new native screens.

## Reusable Inertia and TypeScript structure

For browser code and an existing embedded web experience:

- pages select and compose features; they should not own transport details;
- layouts own stable shared providers and shell composition;
- feature components own presentation and local interaction;
- focused hooks own state transitions and lifecycle subscriptions;
- a feature `lib/` module owns API paths, fetch/client behavior, cache adapters, mapping, and explicit response types;
- shared primitives stay under `components/ui`; and
- generated Wayfinder actions and routes replace hardcoded backend URLs where they support the runtime.

Wayfinder describes routes in the current Laravel runtime. In a remote browser
that is the canonical server; in an on-device embedded web runtime it is local
and must not be mistaken for the remote API origin. Keep absolute remote API
URL construction in the shared mobile client boundary.

Keep backend payload names at the transport boundary. Map once into frontend-friendly state rather than spreading snake-case response handling through components.

Generate Wayfinder before type checking and production builds whenever backend routes affect frontend types. Do not add Ziggy or another route package when Wayfinder already covers the need.

## State and cache flow

A resilient read flow can be:

1. initialize from the best allowed local cache;
2. render a stable loading, empty, or stale state;
3. fetch one boot-critical aggregate payload;
4. map and validate it at the boundary;
5. update memory and the approved cache;
6. refresh in the background on resume or reconnect; and
7. retain usable stale data when refresh fails, with timestamp and retry UI.

Use an `AbortController` or runtime equivalent to cancel obsolete requests. Keep `isBootstrapping`, `isRefreshing`, `isShowingStaleData`, `lastUpdatedAt`, and `error` distinct; one `loading` boolean cannot describe the user experience accurately.

Do not put sensitive data or credentials in browser storage. Public read snapshots may use durable storage only after the product has defined retention and staleness.

## Navigation and lifecycle

- Use NativePHP 4 navigation and native chrome for new screens so system back gestures, safe areas, keyboards, and materials work normally.
- Use `mount()` for first entry and `onResume()` for returning to a native screen.
- Refresh only when visible and online; deduplicate concurrent refreshes.
- Preserve route identity and parameters across relaunches and deep links.
- Open external destinations through the supported browser/plugin boundary, not an accidental in-app navigation.
- Sanitize return paths and never accept protocol-relative, absolute, control-character, or backslash-based redirects as internal paths.

When maintaining a legacy embedded web experience, isolate platform-specific URL/protocol behavior in one link or navigation adapter. Do not scatter `php://`, loopback hosts, or platform checks throughout components.

## UI states worth testing

- first launch with no data;
- warm launch with fresh data;
- warm launch with stale data and no connection;
- background-to-foreground refresh;
- reconnect refresh;
- request cancellation during navigation;
- authentication expiry;
- permission denial and recovery;
- keyboard and safe-area behavior;
- external link and deep-link handling; and
- reduced motion, screen reader labels, contrast, focus, and touch target size.
