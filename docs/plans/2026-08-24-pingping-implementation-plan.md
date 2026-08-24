# PingPing implementation plan

Date: 2026-08-24
Source: `docs/specs/2026-08-24-pingping-audit-redesign.md`
Status: Complete

## Delivery sequence

1. Establish traceability, schema contract, and rollback notes.
2. Add regression tests for verification, authorization, URL safety, probing, state transitions, aggregation, locale, and CRUD behavior.
3. Add compatibility-safe schema columns, backfill historical data, move application reads/writes, then remove ambiguous legacy columns in the same migration boundary.
4. Extract monitoring into target resolution, HTTP/TLS probing, a `ProbeResult`, and a persistence/notification runner. Keep queue jobs thin and unique.
5. Shape all Inertia payloads and centralize authorization in a monitor policy.
6. Establish design tokens and shared accessible components, then rebuild landing/auth, dashboard, create/detail, profile, and error surfaces.
7. Complete English and Czech UI/server/mail translations.
8. Update dependencies, branding, operations documentation, Dusk journeys, and accessibility coverage.
9. Run the complete verification matrix and record exact evidence.

## Rollout and rollback

- Back up the production database before migration.
- Stop the scheduler and queue workers before deployment so old workers cannot write legacy fields while migrations run.
- Deploy code and migration together, run `php artisan migrate --force`, restart workers, then verify `/up`, `schedule:list`, a safe test monitor, and mail delivery.
- The migration copies old uptime/response values before removing their columns. Its rollback recreates and backfills those columns from the new fields before dropping the new contract.
- Application rollback therefore requires rolling back the database migration before starting the old workers.
- No migration deletes monitor or ping-log rows.

## Risk controls

- DNS, HTTP, TLS, time, notification, and queue boundaries are injectable or fakeable in tests.
- Shared schema/model consumers are located before field replacement: models, factories, seeders, controllers, jobs, views, and tests.
- Unsafe targets are checked during form validation and again at every probe/redirect hop to defend against DNS rebinding.
- Browser verification uses an isolated database and does not alter production-like workspace data.
