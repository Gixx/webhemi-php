# Host ownership verification (probe mechanics)

**Product lifecycle (source of truth):** [`docs/plan/Host_Ownership_Verification.md`](../../docs/plan/Host_Ownership_Verification.md) — pending → verified → assign to site → active. That plan supersedes older “create-with-site / admin·api skip probe” product rules.

## Probe (domain service)

When ownership is checked (`HostOwnershipVerifier`), WebHemi proves the hostname resolves to this install by:

1. Writing a short-lived token file under `public/`
2. Fetching that file via the submitted hostname (same port as the admin request; HTTP and HTTPS)
3. Matching the response body to `webhemi-host-verification:<token>`
4. Always deleting the temporary file

TLS certificate name checks are **not** required for the probe: a new hostname is often verified before it appears on the cert. The probe only needs the Host to reach this install and return the token body.

See `App\SiteHost\Verification\HostOwnershipVerifier`.

Status transitions and “only verified hosts may be assigned to a site” are defined in the hub plan above — not in this file.
