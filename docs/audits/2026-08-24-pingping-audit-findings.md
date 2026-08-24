# PingPing full audit findings

Date: 2026-08-24

## Critical and high findings addressed

- Unsafe target access: monitor URLs and redirects could reach local/private targets. Added public-only IPv4/IPv6 resolution, restricted ports, per-hop validation, DNS pinning, disabled automatic redirects, and a five-hop limit.
- TLS verification: certificate inspection disabled peer and hostname checks. Replaced it with verified HTTP TLS plus a pinned OpenSSL certificate inspection boundary.
- Monitoring job coupling: networking, persistence, metrics, and notifications lived in one job. Extracted target resolution, probing, immutable results, and the check runner; jobs now remain thin and uniquely scheduled.
- Dependency exposure: Composer reported 34 advisories and npm reported high vulnerabilities. Lockfiles were upgraded within the existing majors; both final audits report no advisories.

## Correctness findings addressed

- New monitors falsely began as up. They now render as pending and queue a first check immediately.
- Dashboard counts mixed page-local and global values. Counts are now global and filters/search/pagination are server-side.
- Initial success could trigger noise while initial failure handling was ambiguous. Initial success is silent; initial down and later transitions notify.
- Response-time units and uptime naming were ambiguous. Data now uses integer milliseconds and `uptime_30d`, with an additive backfill migration that preserves logs.
- Scheduler scans loaded the full monitor set and allowed overlap. Dispatch now processes chunks and uses unique/overlap locks.
- Authorization checks were repeated in controllers. Ownership is centralized in `MonitorPolicy`, and verified email is enforced by the user contract.

## Experience findings addressed

- Unsupported multi-region, one-minute, SMS, Slack, and webhook claims were removed.
- Page-only status metrics, hidden mobile pagination/language controls, raw tables on phones, hidden form errors, duplicate detail metrics, settings-before-history, and native delete confirmation were replaced.
- All primary surfaces now share a light calm-technical design system, visible focus states, Heroicons, non-color state labels, responsive cards/tables, accessible dialogs, toasts, reduced motion, and chart alternatives.
- English and Czech now cover UI, validation, authentication, passwords, status/failure explanations, and monitor notification mail.
- Branding is consistently PingPing across HTML metadata, favicon, UI, configuration, and mail.

## Out-of-scope observations

- Log retention remains a product decision; no automatic deletion was added.
- Multi-region checks, one-minute intervals, SMS, Slack, webhooks, and dark mode remain intentionally unsupported.
