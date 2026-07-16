# Local @webhemi/ui link (AssetMapper)

WebHemi.PHP does not require Node.js in production. During development, sync the built design system into AssetMapper:

```bash
# from webhemi-ui
npm run build

# from webhemi-php
composer run sync-ui
# or: WEBHEMI_UI_DIST=/absolute/path/to/webhemi-ui/dist bash bin/sync-ui.sh
```

This copies:

- `dist/index.js` → `assets/webhemi-ui/index.js`
- `dist/index.css` → `assets/webhemi-ui/index.css`

`importmap.php` maps `@webhemi/ui` to that local path. React controllers under `assets/react/controllers/` import named exports from `@webhemi/ui`.

## npm link (optional JS tooling)

If you temporarily use a Node-based bundler:

```bash
cd ../webhemi-ui && npm link
cd ../webhemi-php && npm link @webhemi/ui
```

Prefer `sync-ui` for the zero-Node AssetMapper path.
