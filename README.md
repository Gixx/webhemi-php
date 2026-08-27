# WebHemi.PHP

Greenfield Symfony 8 CMS engine for PHP-first deployments. **No Node.js / npm / `node_modules` in this repository** — not for production and not for local PHP work. Composer + AssetMapper only.

UI (TypeScript/React, including admin pages) is built in [`webhemi-ui`](../webhemi-ui) and synced or pulled via NPM as `@webhemi/ui`. Controllers under `assets/react/controllers/` are plain JS re-exports.

[![Minimum PHP Version](https://img.shields.io/badge/PHP->%3D8.4-blue.svg)](https://php.net/)
[![Email](https://img.shields.io/badge/email-navig80@gmail.com-blue.svg?style=flat-square)](mailto:navig80@gmail.com)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

![Build Status](https://github.com/Gixx/webhemi-php/actions/workflows/ci.yml/badge.svg)
[![PHPCS](https://github.com/Gixx/webhemi-php/actions/workflows/badge-phpcs.yml/badge.svg)](https://github.com/Gixx/webhemi-php/actions/workflows/badge-phpcs.yml)
[![PHPStan](https://github.com/Gixx/webhemi-php/actions/workflows/badge-phpstan.yml/badge.svg)](https://github.com/Gixx/webhemi-php/actions/workflows/badge-phpstan.yml)
[![Deptrac](https://github.com/Gixx/webhemi-php/actions/workflows/badge-deptrac.yml/badge.svg)](https://github.com/Gixx/webhemi-php/actions/workflows/badge-deptrac.yml)
[![PHPUnit](https://github.com/Gixx/webhemi-php/actions/workflows/badge-phpunit.yml/badge.svg)](https://github.com/Gixx/webhemi-php/actions/workflows/badge-phpunit.yml)
[![codecov](https://codecov.io/gh/Gixx/webhemi-php/branch/main/graph/badge.svg)](https://codecov.io/gh/Gixx/webhemi-php)

## Requirements

- PHP >= 8.4
- Composer 2
- MariaDB/MySQL (default local: `webhemi_dev`) or SQLite/PostgreSQL

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

### PHP upload limits (media library)

Default PHP often caps uploads at **2M**. Media library video needs more.

**Preferred (Symfony local server):** project-root [`php.ini`](php.ini) sets `upload_max_filesize` / `post_max_size` to **64M**. Restart after changing it:

```bash
symfony server:stop
symfony serve -d --port=8000
```

Confirm via `/_profiler/phpinfo` (dev) or:

```bash
# should show 64M under the running server’s PHP, not only CLI
symfony php -r 'echo ini_get("upload_max_filesize"), PHP_EOL;'
```

Optional CLI / system `conf.d` copy: [`config/php/99-uploads.ini`](config/php/99-uploads.ini).

Default seed:

- Admin: `admin@webhemi.local` / `admin`
- Site: `main` / Main site
- Hosts: `admin.webhemi.local` (admin surface), `www.webhemi.local` (site surface) — both **already `active` and assigned** to `main` (probe skipped for local fixtures)
- If missing, writes `var/config/webhemi.yaml` with `access.admin: domain` (template: `config/webhemi.yaml.dist`)

Map those hosts to `127.0.0.1` in `/etc/hosts` (or Windows hosts file) for multi-domain smoke tests.

### Install config (`var/config/webhemi.yaml`)

Gitignored runtime settings (defaults if the file is absent: `access.admin: path`). See hub [Admin_API_Access_Mode.md](../docs/plan/Admin_API_Access_Mode.md). Copy from `config/webhemi.yaml.dist` or rely on `app:seed`.

Operator-created hosts in admin follow pending → verify → assign → active ([hub plan](../docs/plan/Host_Ownership_Verification.md)); see [probe mechanics](docs/host-ownership-verification-flow.md).

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
| `admin` | Dedicated admin host (domain mode); UI at `/` → `/admin` |
| `site` | Public site + `/login` (frontend auth); path-mode admin at `/admin` |

Host → surface resolution: `App\Routing\HostContextSubscriber`.

## QA

```bash
composer qa
```

Runs lint, PHPCS, PHP CS Fixer (dry-run), Rector (dry-run), PHPStan, PHPCPD, PHPLOC, Deptrac, and PHPUnit.

Optional: `composer run qa:psalm`

### Git hooks

Enable the repository-managed Git hooks (CRLF check + `composer run qa` on pre-commit):

```bash
chmod +x .githooks/pre-commit
git config core.hooksPath .githooks
```

## Docs

- [Host ownership verification](docs/host-ownership-verification-flow.md)
- [Local UI link](docs/local-ui-link.md)
- [WebHemi.JS Phase 3 outline](docs/webhemi-js-phase3-outline.md)
- [Postman collection](docs/postman/webhemi-admin-api.postman_collection.json)
