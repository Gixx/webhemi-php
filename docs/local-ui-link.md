# Local @webhemi/ui link (AssetMapper)

WebHemi.PHP does not require Node.js in production. During development, sync the built design system into AssetMapper:

```bash
# from webhemi-ui
npm run build

# from webhemi-php
composer run sync-ui
# or: WEBHEMI_UI_DIST=/absolute/path/to/webhemi-ui/dist bash bin/sync-ui.sh
```

## What sync copies

| Source | Destination | Role |
|--------|-------------|------|
| `webhemi-ui/dist/index.js` | `assets/webhemi-ui/index.js` | Shared React package (`@webhemi/ui`) |
| `webhemi-ui/dist/index.css` | `assets/admin/index.css` | Admin Theme stylesheet |
| `webhemi-ui/src/admin/assets/**` | `assets/admin/**` | Admin graphics (stable names: `system/`, `icons/`, `fonts/`, `logo/`, `chrome/`) |

Frontend themes later sync under `assets/themes/<theme-id>/` (installable / removable per site).

Graphics are **never** base64-inlined into CSS/JS.

| Environment | How graphics resolve |
|-------------|----------------------|
| Storybook | Absolute `/assets/admin/...` via `staticDirs` |
| PHP CSS | Built `index.css` uses **relative** `url(./…)` so AssetMapper digests them |
| PHP React (`<img>`) | Pass Twig `asset('admin/…')` as a prop (e.g. login `bannerUrl`) |

`importmap.php` maps `@webhemi/ui` to `assets/webhemi-ui/index.js`. React controllers under `assets/react/controllers/` import named exports from `@webhemi/ui`. Twig loads Admin CSS via `asset('admin/index.css')`.

## npm link (optional JS tooling)

If you temporarily use a Node-based bundler:

```bash
cd ../webhemi-ui && npm link
cd ../webhemi-php && npm link @webhemi/ui
```

Prefer `sync-ui` for the zero-Node AssetMapper path.
