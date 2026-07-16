# Host ownership verification

When a `site` surface host is created (or manually re-verified), WebHemi proves ownership by:

1. Writing a short-lived token file under `public/`
2. Fetching that file via the submitted hostname
3. Matching the response body to `webhemi-host-verification:<token>`
4. Setting status to `verified` on match, otherwise `pending`
5. Always deleting the temporary file

Admin and API surface hosts skip the HTTP probe and start as `active`.

See `App\SiteHost\Verification\HostOwnershipVerifier`.
