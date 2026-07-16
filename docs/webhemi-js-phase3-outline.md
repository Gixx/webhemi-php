# WebHemi.JS — Phase 3 outline (not implemented yet)

This document sketches the third repository from the multi-repo architecture. **Do not start implementation until WebHemi.PHP admin shell + domain core are stable.**

## Goals

- Next.js 15 App Router application
- Embedded Payload CMS 3.0 (`@payloadcms/next`) for the JS-first admin
- Consume the **same** `@webhemi/ui` package published from Storybook
- Separate data model (Payload collections) from the Symfony Doctrine model

## Proposed layout

```text
webhemi-js/
├── app/                 # Next.js App Router + Payload admin routes
├── collections/         # Payload schemas (sites, hosts, users, …)
├── package.json         # depends on @webhemi/ui
└── payload.config.ts
```

## Bootstrap steps (later)

1. `npx create-next-app@latest webhemi-js`
2. `npm install @webhemi/ui @payloadcms/next`
3. Configure Payload collections mirroring multi-tenant concepts (sites, hosts/surfaces, RBAC)
4. Render public/admin views with `import { Button, AdminLayout, ... } from '@webhemi/ui'`
5. Local `npm link @webhemi/ui` during design-system work

## Out of scope for Phase 1–2

- No Next.js code in this repository
- No Payload schemas yet
- Blog/content modeling waits until both engines share stable UI primitives
