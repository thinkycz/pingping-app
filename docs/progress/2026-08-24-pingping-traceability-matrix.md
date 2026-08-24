# PingPing traceability matrix

| Requirement | Implementation surface | Primary verification |
| --- | --- | --- |
| Verified monitoring access | `User`, web routes, auth controllers | Authentication and verification feature tests pass |
| Public targets only | Monitor request, target resolver, probe redirect loop | IPv4/IPv6, DNS, credential, scheme, and port tests |
| Pinned DNS and safe redirects | HTTP probe adapter | Redirect, pinning, and hop-limit tests |
| Verified TLS | TLS inspector and probe result | TLS success/failure/expiry tests |
| Retry transient/server failure once | HTTP probe | Retry-count and final-result tests |
| Accurate states and 30-day uptime | Monitor model, check runner, controller payloads | State, aggregation, and first-check tests |
| No duplicate checks | Unique job and overlap middleware | Queue uniqueness and dispatcher tests |
| Transition mail in user locale | Check runner, user locale contract, notification | Notification and locale tests |
| Shaped dashboard/detail data | Monitor controller/resources | Inertia payload feature tests |
| Central authorization | Monitor policy | Cross-user CRUD/toggle/show tests |
| Calm responsive UI | tokens, primitives, all Vue pages | Build, 3 Chrome journeys, axe checks, and exact 390/768/1440 captures pass |
| Complete English/Czech | JSON and Laravel language catalogs | locale feature test and bilingual browser review |
| Operations and safe rollout | README and docs | command review, fresh migration/seed, scheduler inspection |
| Advisory cleanup | Composer/npm lockfiles | Composer reports no advisories; npm reports 0 vulnerabilities |
