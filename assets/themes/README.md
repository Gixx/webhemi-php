# Frontend themes (AssetMapper)

Shipped theme static assets live here:

```text
assets/themes/<theme-id>/
```

Runtime package contract (manifest + Twig):

- Manifest: `themes/<theme-id>/theme.json` (shipped) or `var/themes/<theme-id>/theme.json` (uploaded)
- Twig: `templates/themes/<theme-id>/` (shipped) or `var/themes/<theme-id>/templates/` (uploaded)

First shipped theme id: **`default`** (Phase 9 Hello world).

Admin Theme graphics and CSS live under `assets/admin/` (not here). Shared package JS is `assets/webhemi-ui/`.
