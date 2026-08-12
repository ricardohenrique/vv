# Laravel Sanctum implementation patterns

Use these patterns as a checklist, not as code to copy blindly. Match the target repository's Laravel version and conventions.

## Server structure

- Register `routes/api.php` through the routing bootstrap.
- Add `Laravel\\Sanctum\\HasApiTokens` to the authenticatable user model.
- Keep public authentication routes separate from `auth:sanctum` routes.
- Prefer `/api/v1/auth/login`, `/api/v1/auth/register`, `/api/v1/auth/logout`, and `/api/v1/user` for a small first-party API.
- Name tokens after the device and assign application-specific abilities when useful.
- Use API resources for authentication, users, and domain models.
- Return 201 for created resources and registrations, 204 for logout and deletion, 401 for missing or invalid credentials, 403 for known-but-forbidden resources, 404 when route-model scoping intentionally conceals a resource, and 422 for validation failures.

## Logout invariant

Device logout must revoke only the token used for the request:

```php
$token = $request->user()->currentAccessToken();

if ($token instanceof PersonalAccessToken) {
    $token->delete();
}

return response()->noContent();
```

Do not call `$request->user()->tokens()->delete()` for ordinary logout; reserve that behavior for a clearly named “log out all devices” workflow.

## Native client boundary

Maintain one API client with two modes:

- Browser mode uses same-origin URLs, cookies, and XSRF protection.
- Native mode resolves an absolute remote API URL, attaches the bearer token, and disables credentials and XSRF handling.

An Inertia experience running inside NativePHP's on-device Laravel runtime is
native mode for server-canonical data. Do not let a local Wayfinder route or
loopback host accidentally query the device database instead of the remote API.

On native authentication:

1. Resolve a useful device name.
2. Exchange email/password/device name for a token.
3. Persist the token before navigating into the authenticated experience.
4. If persistence fails, immediately revoke the just-issued token. Report failure rather than leaving an unprotected valid credential.
5. On a 401, clear the stored token and return to login.

## NativePHP encrypted-file fallback

Use this only when the product has explicitly rejected a platform secure-storage plugin and the NativePHP runtime supplies a unique per-install application key.

- Store `Crypt::encryptString($token)` on the local disk at one fixed, non-public path.
- Read with `Crypt::decryptString()` and delete ciphertext that cannot be decrypted.
- Gate local token endpoints with middleware that verifies the NativePHP runtime and supported platform.
- Validate a maximum token length on write.
- Never return the token endpoint to ordinary browser or remote API clients.
- Test that the persisted file does not contain the plaintext token.
- Document the residual risk and prefer Keychain/Keystore storage if a suitable free or approved implementation becomes available.

## Minimum feature-test matrix

Authentication:

- valid login returns token type, token, and user
- invalid credentials return 422 without leaking which field was wrong
- device name is required and bounded
- repeated failures are rate-limited
- logout revokes the current token and leaves another device token active
- an unauthenticated request returns JSON 401

Domain endpoints:

- user sees only owned resources
- create/update payload validation matches product rules
- update/delete enforce policies
- serialization keys and timestamp formats are explicit
- soft-deleted resources are absent where required

Native persistence:

- endpoints are 404 outside the native runtime
- plaintext is absent from the stored value
- invalid ciphertext is discarded
- delete clears the stored credential

## Documentation decisions

Record all of the following:

- canonical data source
- browser and native trust boundaries
- API base URL configuration
- token storage and revocation behavior
- token abilities and expiration policy
- offline/caching behavior, even when the decision is “none”
- simulator/device verification performed
