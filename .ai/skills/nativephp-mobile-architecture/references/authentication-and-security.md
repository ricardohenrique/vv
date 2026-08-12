# Authentication and security boundaries

## Separate browser and native trust models

Browser requests:

- use the first-party Laravel session guard;
- send cookies and CSRF protection to the same origin; and
- do not use personal or device API tokens for ordinary Inertia navigation.

Remote native requests:

- use a per-device, revocable bearer token over HTTPS;
- do not send browser cookies or CSRF tokens;
- send a useful device label and record token purpose/abilities where supported;
- revoke the current device token on logout and offer all-device revocation when the product needs it; and
- handle `401` by clearing the local credential and returning to authentication without an unsafe redirect.

Apply authorization on every protected server resource. Hiding a native or React control is not authorization.

## Protect the device credential

Prefer the current NativePHP secure-storage capability when its plugin is installed, reviewed, and allowlisted. A facade existing in the core package does not prove the native plugin is present. If a project uses encrypted private app storage, verify that the encryption key is per installation and protected by the platform keystore/keychain rather than shipped in the bundle.

Credential persistence must be failure-safe:

1. obtain a device-specific token;
2. protect it locally;
3. if protection fails, attempt immediate server revocation;
4. never continue as authenticated with an unprotected token; and
5. expose a clear recovery path if storage or revocation fails.

Do not store tokens in localStorage, sessionStorage, plaintext SQLite, logs, crash metadata, or frontend state that outlives the minimum required request flow.

## Treat the bundle as public

Before every release, inspect both configuration and the compiled archive.

Remove or reject:

- database, mail, object-storage, deployment, signing, and service credentials;
- Composer authentication and local tool configuration;
- production `.env` content and private application keys;
- deployment scripts, private operational docs, test fixtures with personal data, and development artifacts; and
- unused SDKs, permissions, entitlements, background modes, and transport exceptions.

`cleanup_env_keys` and `cleanup_exclude_files` are defense-in-depth, not proof. Add an archive verifier for high-risk apps.

## API and files

- Version APIs used by shipped clients.
- Return stable resource shapes and consistent validation errors.
- Rate-limit authentication, verification, reset, support, upload, and abuse-sensitive endpoints.
- Validate uploads by actual content, size, and authorization.
- Keep sensitive files private and return authorized or temporary URLs.
- Make upload retries and background processing idempotent.
- Redact authorization headers, credentials, personal content, and signed URLs from logs.

## Plugins and SDKs

Every plugin is an executable supply-chain and privacy decision.

- Install deliberately and explicitly allowlist its provider.
- Review `nativephp.json`, native dependencies, permissions, secrets, data collection, network destinations, and platform minimums.
- Request the narrowest permission at the point of use with a precise user-facing reason.
- Provide denial and settings/recovery states.
- Update the privacy policy, store disclosures, data-retention documentation, and release tests when collection changes.
