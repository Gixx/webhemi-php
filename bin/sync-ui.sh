#!/usr/bin/env bash
# Sync @webhemi/ui build + Admin Theme graphics into AssetMapper paths.
#
# Layout (theme-installable later):
#   assets/webhemi-ui/     shared package JS (all consumers)
#   assets/admin/          Admin Theme CSS + graphics (from src/admin/assets)
#   assets/themes/<id>/    frontend themes (synced when those builds exist)
#
# Never relies on base64-inlined images in CSS/JS.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
UI_ROOT="${WEBHEMI_UI_ROOT:-$ROOT/../webhemi-ui}"
UI_DIST="${WEBHEMI_UI_DIST:-$UI_ROOT/dist}"
UI_ADMIN_ASSETS="${UI_ROOT}/src/admin/assets"

TARGET_JS="$ROOT/assets/webhemi-ui"
TARGET_ADMIN="$ROOT/assets/admin"
TARGET_THEMES="$ROOT/assets/themes"

if [[ ! -f "$UI_DIST/index.js" || ! -f "$UI_DIST/index.css" ]]; then
  echo "Missing @webhemi/ui build at $UI_DIST — run: (cd $UI_ROOT && npm run build)" >&2
  exit 1
fi

if [[ ! -d "$UI_ADMIN_ASSETS" ]]; then
  echo "Missing Admin assets at $UI_ADMIN_ASSETS" >&2
  exit 1
fi

mkdir -p "$TARGET_JS" "$TARGET_ADMIN" "$TARGET_THEMES"

# Shared React package
cp "$UI_DIST/index.js" "$TARGET_JS/index.js"

# Admin Theme: stable graphics (no hash) + built CSS
if command -v rsync >/dev/null 2>&1; then
  rsync -a --delete \
    "$UI_ADMIN_ASSETS/" "$TARGET_ADMIN/"
else
  # Fallback without rsync (still wipe graphics trees we own)
  rm -rf "$TARGET_ADMIN/system" "$TARGET_ADMIN/icons" "$TARGET_ADMIN/fonts" \
    "$TARGET_ADMIN/logo" "$TARGET_ADMIN/chrome"
  cp -a "$UI_ADMIN_ASSETS/." "$TARGET_ADMIN/"
fi

cp "$UI_DIST/index.css" "$TARGET_ADMIN/index.css"

# Drop legacy CSS next to the JS package if present
rm -f "$TARGET_JS/index.css"

echo "Synced @webhemi/ui JS  → $TARGET_JS/index.js"
echo "Synced Admin Theme     → $TARGET_ADMIN/ (index.css + graphics from src/admin/assets)"
