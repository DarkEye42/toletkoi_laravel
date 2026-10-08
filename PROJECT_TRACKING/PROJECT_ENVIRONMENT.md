# ToLetKoi - Environment

**Last Updated:** 2026-09-15

## Runtime Environment

| Component | Version | Notes |
|-----------|---------|-------|
| PHP | 8.2.12 | XAMPP, Windows |
| Laravel | 12.69.2 | Latest stable |
| MySQL/MariaDB | via XAMPP | localhost:3306 |
| Node.js | (Vite bundled) | For asset compilation |
| OS | Windows | Development |

## Composer Packages (Production)

| Package | Version | Purpose |
|---------|---------|---------|
| laravel/framework | ^12.0 | Core framework |
| spatie/laravel-permission | ^6.25 | Role-based access control |
| livewire/livewire | ^4.4 | Reactive Blade components |
| barryvdh/laravel-debugbar | ^4.4 | Debug toolbar (dev) |
| laravel/tinker | ^2.10.1 | REPL |

## Composer Packages (Dev)

| Package | Version | Purpose |
|---------|---------|---------|
| phpunit/phpunit | ^11.5.50 | Testing framework |
| fakerphp/faker | ^1.23 | Test data generation |
| mockery/mockery | ^1.6 | Mocking |
| laravel/pint | ^1.24 | Code style fixer |
| nunomaduro/collision | ^8.6 | Error reporting |

## Frontend Assets

| Asset | Version | Purpose |
|-------|---------|---------|
| Tailwind CSS | v4 | CSS framework (via @tailwindcss/vite) |
| Alpine.js | v3.x | Reactive UI (CDN) |
| Leaflet.js | v1.9.4 | Interactive maps (CDN) |

## Environment Variables (.env)

```
APP_NAME=ToLetKoi
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://toletkoi.local
APP_LOCALE=en
APP_FALLBACK_LOCALE=bn

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toletkoi_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

## Required External Credentials (NOT YET CONFIGURED)

| Service | .env Keys | Status |
|---------|-----------|--------|
| Google OAuth | GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET | ⬜ Missing |
| Facebook OAuth | FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET | ⬜ Missing |
| Barikoi Maps | BARIKOI_API_KEY | ✅ Configured and verified live |
| bKash Payment | BKASH_APP_KEY, BKASH_APP_SECRET | ⬜ Missing |

### Frontend Build (2026-09-16)

- Node.js **v26** at `C:\Program Files\nodejs` — **not on the default PATH**. Prefix commands with:
  `$env:Path = "C:\Program Files\nodejs;" + $env:Path`
- Build command: `npm run build` (Vite → `public/build/`).
- Tailwind v4 discovers utility classes by **scanning template files at build time** (`@source` directives in `resources/css/app.css`).

> **Important:** `php artisan view:cache` compiles Blade to PHP only — it does **not** regenerate CSS.
> After editing any `.blade.php` file you must run `npm run build`, or newly used utility classes
> will be missing from the shipped stylesheet and the page will render unstyled while still
> returning HTTP 200. This caused ISSUE-020.

Current assets (2026-09-16): `app-BcuOOgWx.css` (93.19 kB), `app-DKL-jiOM.js`.
Stale asset from before: `app-CB3oYNNr.css` (has been removed by the rebuild).

### Barikoi Configuration (2026-09-16) — VERIFIED WORKING

The Barikoi integration is complete and verified against the live API.

**Correct API details** (three earlier mistakes are documented in ISSUE-003):

| Item | Correct value |
|---|---|
| API host | `https://barikoi.xyz/v2/api` (**not** `barikoi.com`) |
| Credential parameter | `api_key` (**not** `key`) |
| Autocomplete | `GET /search/autocomplete/place?api_key=&q=` |
| Reverse geocode | `GET /search/reverse/geocode?api_key=&longitude=&latitude=` |
| Coordinate order | **longitude first**, then latitude |

**Forward geocoding:** Barikoi v2 has no dedicated forward-geocode endpoint, so `BarikoiService::geocode()` uses autocomplete and returns the best match — the same approach the official package takes.

- Config binding: `config/services.php` → `'barikoi' => ['key' => env('BARIKOI_API_KEY')]`
- Service: `app/Services/BarikoiService.php` (autocomplete, geocode, reverse-geocode) with 5–10 minute caching
- Proxy routes: 6 throttled `/api/maps/*` endpoints (`config`, `autocomplete`, `geocode`, `reverse-geocode`)
- Map rendering: Leaflet with Barikoi raster tiles, falling back to OpenStreetMap when no key is present (pages still render 200)

**To configure**, add to `.env`:

```
BARIKOI_API_KEY=REDACTED
```

Per Phase 00 rules, the real key must never be written into `PROJECT_TRACKING/`, documentation, or version control.

**When the key is absent:** `BarikoiService::isConfigured()` returns false, `getTileUrl()` returns null, pages still return 200, and the map views fall back to OpenStreetMap tiles. `tests/Feature/BarikoiServiceTest.php` covers this path.

**Official package not installable:** `barikoi/barikoiapis` caps at `illuminate/support ^11.0`; this project runs Laravel 12. Installing it would require a Laravel downgrade that breaks Spatie Permission v6, Livewire 4 and the test suite. The endpoints are called directly via the HTTP client instead.
| Nagad Payment | NAGAD_MERCHANT_ID, NAGAD_SECRET_KEY | ⬜ Missing |
| SMTP Email | MAIL_MAILER, MAIL_HOST, etc. | ⬜ Not configured |
| Firebase | FIREBASE_* | ⬜ Missing |

## Database

- **Name:** toletkoi_db
- **Host:** localhost:3306
- **User:** root
- **Password:** (empty)
- **Tables:** 65+ tables across 11 migration files
- **Seeders:** 8 seeders with real Bangladesh data
