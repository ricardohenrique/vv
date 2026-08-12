# Builds, compliance, and testing

## Generated-project policy

Do not make lasting product changes by editing `nativephp/` directly. Prefer:

1. app configuration supported by the installed NativePHP version;
2. an existing plugin reviewed and allowlisted in `NativeServiceProvider`;
3. a focused custom plugin using declarative manifest assets and lifecycle hooks; or
4. a generated-source patch only as a documented temporary compatibility measure.

Use a custom plugin for privacy manifests, approved Info.plist values, Android manifest entries, entitlements, native assets, build verification, or missing native behavior when NativePHP 4 provides the extension point.

## Fail-closed customizations

Any hook or fallback patch that changes generated metadata must:

- match only the exact upstream structure it was designed for;
- refuse to modify an unfamiliar structure;
- be idempotent and safe after clean regeneration;
- validate both simulator/debug and release/archive outputs where they differ;
- be pinned to and re-audited for NativePHP upgrades; and
- have focused tests that prove accepted and rejected inputs.

Do not remove a broad transport or permission setting by loose text replacement. Parse the platform format, verify the expected shape, change the minimum exact structure, then verify the compiled result.

## Compliance evidence

Keep repository-controlled evidence aligned with the shipped binary:

- privacy manifests and collected-data declarations;
- permission purpose strings and localizations;
- entitlements, capabilities, background modes, deep-link domains, and minimum OS versions;
- transport security settings;
- app name, bundle/application identifier, release version, and monotonically increasing build number;
- support, privacy, account deletion, authentication, and review paths; and
- a list of bundled SDKs and their data collection.

Store signing keys, provisioning profiles, keystores, store API credentials, and review-account passwords outside Git and outside the distributable bundle.

## Test layers

### In-process native tests

Use NativePHP 4's native test harness for component rendering, interactions, navigation, lifecycle-driven state, bridge calls/events, platform variants, chrome, and accessibility. Prefer stable `ref` values for controls without unique visible text.

### Laravel feature and unit tests

Cover runtime gating, API contracts, auth guards, per-device token lifecycle, authorization, rate limits, snapshot selection, remote failures, cache invalidation, and fresh/upgrade SQLite migrations. Fake storage, HTTP, cache, and bridges instead of requiring a simulator for these cases.

### Frontend tests

Test browser/native client selection, absolute URL handling, cookie/CSRF versus bearer separation, response normalization, multipart uploads, retry classification, safe redirects, stale-data rendering, and listener cleanup.

### Platform verification

On each supported target, verify:

- clean install and upgrade from the oldest supported version;
- cold start, resume, reconnect, offline states, and process restart;
- system back, edge/predictive back, deep links, and external links;
- safe areas, keyboard avoidance, dark mode, reduced motion, VoiceOver/TalkBack, and orientation policy;
- real-device-only APIs such as camera or biometrics;
- permission denial and settings recovery; and
- release archive metadata, privacy report, signature, entitlements, forbidden files, and production transport.

Follow the upstream `nativephp-mobile` skill's command policy: ask for the target platform and give build/run/package commands to the user instead of running them automatically.

## Upgrade rule

Both architecture examples that informed this guide used NativePHP 3. This project uses NativePHP 4. Reuse their intent—canonical data, protected credentials, explicit lifecycle behavior, reproducible builds, and executable compliance—but implement it with v4 native components, core facades, plugins, manifests, hooks, and test APIs. Never copy generated Swift, Kotlin, WebView bridges, protocol workarounds, or v3 plugin packages without revalidating the current v4 design.
