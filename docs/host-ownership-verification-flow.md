# Host ownership verification (probe mechanics)

**Product lifecycle (source of truth):** hub [`docs/plan/Host_Ownership_Verification.md`](../../docs/plan/Host_Ownership_Verification.md) — create without site → `pending` → ownership probe → `verified` → assign to site → `active`. That plan supersedes older “create-with-site / admin·api skip probe” product rules.

This file documents **probe mechanics only**. Status transitions and assign rules live in the hub plan.

## Probe (domain service)

When ownership is checked (`HostOwnershipVerifier`), WebHemi proves the hostname resolves to this install by:

1. Writing a short-lived token file under `public/`
2. Fetching that file via the submitted hostname (same port as the admin request; HTTP and HTTPS)
3. Matching the response body to `webhemi-host-verification:<token>`
4. Always deleting the temporary file

TLS certificate name checks are **not** required for the probe: a new hostname is often verified before it appears on the cert. The probe only needs the Host to reach this install and return the token body.

See `App\SiteHost\Verification\HostOwnershipVerifier`. Operator-triggered API: `POST /admin/api/hosts/{id}/verify`.

## Dev seed (`app:seed`)

Local fixtures **skip the ownership probe**. `app:seed` upserts `admin.webhemi.local` (admin surface) and `www.webhemi.local` (site surface) already bound to the `main` site with status `active` and `is_active=true`, so routing and login work immediately after migrate + seed.

That is **dev-only convenience**. Production / UI-created hosts must follow pending → verify → assign → active.
