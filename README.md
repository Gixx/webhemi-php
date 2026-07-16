# WebHemi.PHP

Greenfield Symfony 8 CMS engine for PHP-first deployments. Production installs need **Composer only** (plus AssetMapper CDN assets) — no Node.js runtime.

UI components come from [`@webhemi/ui`](../webhemi-ui) (Storybook design system), synced locally or later published to NPM.

## Requirements

- PHP >= 8.4
- Composer 2
- SQLite (default) or MySQL/PostgreSQL

## Quick start

```bash
composer install
php bin/console importmap:install
composer run sync-ui          # copies ../webhemi-ui/dist → assets/webhemi-ui
php bin/console doctrine:migrations:migrate -n
# or on a fresh DB after dump-schema:
# php bin/console doctrine:schema:create -n
php bin/console app:seed -n
symfony server:start   # or php -S 127.0.0.1:8000 -t public
```

Default seed:

- Admin: `admin@webhemi.local` / `ChangeMe!`
- Hosts: `admin.webhemi.local` (admin), `www.webhemi.local` (site)

Map those hosts to `127.0.0.1` in `/etc/hosts` (or Windows hosts file) for multi-domain smoke tests.

## Local UI workflow

Until `@webhemi/ui` is published to NPM:

1. Develop components in `../webhemi-ui` (`npm run storybook`)
2. `cd ../webhemi-ui && npm run build`
3. `composer run sync-ui` in this repo
4. Refresh the PHP app — AssetMapper serves `assets/webhemi-ui`

Production path (after publish): `php bin/console importmap:require @webhemi/ui` against the registry/CDN, still with **zero Node on the VPS**.

## Surfaces

| Surface | Purpose |
|---------|---------|
| `admin` | Twig mount points + React admin (`/admin`, `/login`) |
| `site` | Public site JSON home for now |
| `api` | JSON admin API under `/admin/api` |

Host → surface resolution: `App\Routing\HostContextSubscriber`.

## QA

```bash
composer qa
```

## Docs

- [Host ownership verification](docs/host-ownership-verification-flow.md)
- [Local UI link](docs/local-ui-link.md)
- [WebHemi.JS Phase 3 outline](docs/webhemi-js-phase3-outline.md)
- [Postman collection](docs/postman/webhemi-admin-api.postman_collection.json)
