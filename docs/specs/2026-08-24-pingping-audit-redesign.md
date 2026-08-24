# PingPing audit, hardening, and redesign specification

Date: 2026-08-24
Status: Implemented and verified

## Product intent

PingPing is a public, multi-user uptime monitor for site owners and small teams. It must feel calm, compact, and trustworthy in English and Czech. The supported product is deliberately narrow: public HTTP/HTTPS monitoring, five-minute-or-longer intervals, and email status alerts.

## Functional requirements

- Require authentication and verified email for every monitoring route.
- Accept only public HTTP/HTTPS targets on ports 80 and 443. Reject credentials, localhost, unsafe or unresolved IPv4/IPv6 addresses, and unsafe redirect destinations.
- Pin each approved DNS result, disable automatic redirects, validate at most five redirect hops, use verified TLS, and retry one transient/network/server failure.
- Record HTTP status, response time in integer milliseconds, TLS state, safe failure code/detail, check time, and rolling 30-day uptime.
- Derive the display states `pending`, `up`, `down`, and `paused` without rewriting historical check logs.
- Run a first check after creation, avoid duplicate/concurrent checks, and notify on an initial failure or later state transition—not on an initial success.
- Show global account counts and server-side search, filtering, and pagination on the dashboard.
- Persist the signed-in user's English or Czech locale and use it for queued mail.
- Preserve all existing monitoring logs. Automatic retention is not part of this change.

## Experience requirements

- Use a light-only, calm technical system: off-white canvas, white surfaces, deep ink text, restrained blue/teal, semantic status colors, Figtree, tabular numerals, modest radii, clear focus states, and reduced-motion support.
- Keep mobile workflows first-class, including language controls, pagination, cards, dialogs, validation, and navigation.
- Use shared UI primitives for actions, fields, errors, badges, headers, pagination, empty states, dialogs, and flash feedback.
- Remove unsupported marketing claims about multi-region checks, one-minute intervals, SMS, Slack, and webhooks.
- Provide meaningful branded authentication, account, and 403/404/419/500 states.
- Provide accessible names, keyboard-safe interactions, non-color status cues, chart alternatives, and representative responsive verification at 390px, 768px, and 1440px.

## Non-goals

- Internal/private network monitoring
- Ports other than 80 and 443
- Multi-region monitoring or one-minute intervals
- SMS, Slack, webhook, or dark-mode support
- Automated log retention or deletion

## Acceptance gates

Fresh migration and seed, PHPUnit, browser journeys, production build, Pint, Composer audit, npm audit, scheduler inspection, browser console review, accessibility smoke checks, keyboard review, responsive visual review, and current verification evidence under `docs/verification`.
