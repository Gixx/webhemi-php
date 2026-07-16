#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
UI_DIST="${WEBHEMI_UI_DIST:-$ROOT/../webhemi-ui/dist}"
TARGET="$ROOT/assets/webhemi-ui"

if [[ ! -f "$UI_DIST/index.js" || ! -f "$UI_DIST/index.css" ]]; then
  echo "Missing @webhemi/ui build at $UI_DIST — run: (cd ../webhemi-ui && npm run build)" >&2
  exit 1
fi

mkdir -p "$TARGET"
cp "$UI_DIST/index.js" "$TARGET/index.js"
cp "$UI_DIST/index.css" "$TARGET/index.css"
echo "Synced @webhemi/ui from $UI_DIST -> $TARGET"
