# PingPing

PingPing is a bilingual uptime monitor for site owners and small teams. It checks public HTTP/HTTPS websites on a schedule, records a rolling 30-day uptime and response-time history, verifies TLS, and emails the owner on an initial failure or later status change.

The supported product is intentionally focused: public targets on ports 80 and 443, 5/15/30/60-minute intervals, one monitoring location, and email alerts. Private-network monitoring, one-minute checks, SMS, Slack, and webhooks are not implemented.

## Stack and requirements

- PHP 8.2+ with cURL, OpenSSL, PDO, and the extensions required by Laravel
- Composer 2
- Node.js 20+ and npm
- Laravel 12, Inertia 2, Vue 3, Vite 7, Tailwind CSS 3
- A supported SQL database, cache with atomic locks, queue backend, scheduler, and mail transport in production
- Chrome/Chromium for Laravel Dusk

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
```

Start the complete development stack:

```bash
composer run dev
```

That command starts the web server, Vite, a queue listener, and application logs. To run services separately:

```bash
php artisan serve
npm run dev
php artisan queue:work --tries=2 --timeout=30
php artisan schedule:work
```

Mail uses the configured Laravel mailer. The default local configuration writes mail to the log; configure SMTP or another production transport before enabling alerts.

## Tests and quality gates

```bash
composer test
vendor/bin/pint --test
npm run build
composer audit --locked
npm audit
php artisan schedule:list
```

Dusk uses the isolated `database/dusk.sqlite` file configured in `.env.dusk`:

```bash
php artisan dusk:chrome-driver --detect
php artisan serve --host=127.0.0.1 --port=8000
php artisan dusk --without-tty
```

The browser journeys cover landing/authentication, verification, language switching, dashboard search/filter/pagination, monitor validation and CRUD, account updates, password changes, account deletion, and axe-core serious/critical accessibility checks.

## Production operation

Point the web server document root at `public/`, configure HTTPS, and set production database, cache, queue, mail, and trusted proxy values in the environment. Deploy in this order:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
```

Run one scheduler process and one or more queue workers under a process supervisor:

```bash
php artisan schedule:run
php artisan queue:work --tries=2 --timeout=30 --max-time=3600
```

The scheduler command must run every minute (normally from cron). Queue workers must be restarted after each deployment. Keep `APP_URL`, `APP_NAME=PingPing`, `APP_LOCALE`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` accurate so signed links and notification branding are correct.

Before the monitoring schema migration, back up the database and stop old scheduler/queue processes. Historical ping logs are preserved; PingPing does not automatically delete them.

## Monitoring safety model

Every submitted URL and redirect hop is resolved again at check time. PingPing rejects credentials, unsupported schemes/ports, localhost, unresolved hosts, and private/reserved/link-local IPv4 or IPv6 addresses. It pins an approved public DNS address, disables automatic redirects, caps redirects at five, uses short connection/request timeouts, retries one network/timeout/server failure, and verifies TLS trust and hostname.

See [`docs/specs/2026-08-24-pingping-audit-redesign.md`](docs/specs/2026-08-24-pingping-audit-redesign.md) for the product contract and [`docs/verification/2026-08-24-pingping-verification.md`](docs/verification/2026-08-24-pingping-verification.md) for current release evidence.
