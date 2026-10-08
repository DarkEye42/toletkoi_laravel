## 2026-09-16 (Barikoi integration) — VERIFIED
### Commands
- `php artisan testfilter=BarikoiServiceTest` — **15 passed, 34 assertions** (HTTP faked; no API quota spent).
- `php artisan test` — **72 passed, 156 assertions**.
- `php artisan view:cache` — all templates compile.
- `npm run build` — CSS `app-D7p0WH3A.css` (116.64 kB).

### Live API round-trips (real network, real key)
- **Autocomplete** `GET https://barikoi.xyz/v2/api/search/autocomplete/place?api_key=&q=Gulshan` → returned real places with `longitude`/`latitude`/`area`/`district`.
- **Reverse geocode** `reverseGeocode(90.4125, 23.8103)` → "Hospital, Lane 11 West, Baridhara Dohs, Baridhara, Dhaka". Confirms longitude-before-latitude ordering.
- **Geocode** `geocode("Banani, Dhaka")` → `23.79008396, 90.40124438` via the autocomplete endpoint.

### Proxy endpoint probes
- `GET /api/maps/config` → 200 with `configured: true` and a live tile URL
- `GET /api/maps/autocomplete?q=Gulshan` → 200 with real places
- `GET /api/maps/reverse-geocode?lat=23.8103&lng=90.4125` → 200 with a real address
- `GET /api/maps/geocode?address=Banani,Dhaka` → 200 with coordinates
- Negative: `?q=a` (too short) and missing coordinates → **422**, not 500
### Page sweep
- `/`, `/search`, `/search/map`, `/listings`, `/blog`, `/pricing`, `/about`, `/faq`, `/contact`, `/login`, `/register`, `/admin` and a real nested listing detail URL → all correct status codes, no 500s.
- `window.BARIKOI_CONFIG` confirmed rendering server-side on `/search/map` with `configured: true`.

### Not Verified
- No browser rendering: map tiles are confirmed only as a correct URL in the page source, not as drawn pixels. Visual confirmation of the rendered map is still outstanding.

## 2026-09-16 (later session) UI Redesign + Image Pipeline + Admin
### Commands
- `php artisan test` — **57 passed, 122 assertions**. (Start of this session: 5 passed / 16 errored; after the migration fix: 36/65.)
- `php artisan test --filter=AdminPanelTest` — **10 passed**.
- `php artisan test --filter=ListingImageUploadTest` — **4 passed**.
- `php artisan view:cache` — all Blade templates compiled.
- `php artisan migrate --force` — both new migrations applied.
- `php artisan optimize:clear` — caches rebuilt after adding routes/providers.

### Request Probes (HTTP status, no browser)
- Public: `/`, `/search`, `/search/map`, `/pricing`, `/listings`, `/blog`, `/blog/{slug}`, `/about`, `/faq`, `/contact`, `/login`, `/register` → all 200.
- Authenticated: `/dashboard`, `/dashboard/profile`, `/dashboard/credits`, `/dashboard/subscription`, `/dashboard/saved-searches`, `/dashboard/favorites`, `/dashboard/koifind`, `/dashboard/verification`, `/dashboard/listings`, `/dashboard/listings/create` → all 200.
- Admin: guest `/admin` → 302 (login redirect); Admin `/admin`, `/admin/listings`, `/admin/listings?status=pending`, `/admin/listings/{id}` → 200; non-admin `/admin` and `/admin/listings` → **403**.

### Image Pipeline
- Generated a real 2400x1600 JPEG, processed it, then decoded the stored bytes to confirm: full image = **1600x1067 WebP**, thumbnail = **480x320 WebP**, both present on the `public` disk. Cleanup on listing delete confirmed by the test suite.

### Not Verified
- No browser rendering — correctness is based on status codes and compiled templates, not visual inspection.
- Barikoi live API calls were not exercised successfully (ISSUE-003); the OSM fallback is what renders.

## 2026-09-16 Bug Fix + URL Reliability Sprint
### Commands Executed
- `php artisan test` — **43 passed, 78 assertions** (2.0s+). Baseline before this sprint was 36 passed / 65 assertions; the earlier `areas` migration conflict had reduced it to 5 passed / 16 errored.
- `php artisan test --filter=BlogTest` — **7 passed**.
- `php artisan view:cache` — all Blade templates compiled (exit 0).
- `php artisan route:list --except-vendor` — 76 routes registered, including the 2 new blog routes.
- `php artisan db:seed --force` — demo dataset loaded successfully.
- `php -l` on `BlogController.php`, `routes/web.php`, `User.php`, `PageController.php`, `SubscriptionController.php`, `BlogPost.php` — no syntax errors.

### Request Probes (HTTP status, no browser)
Direct kernel probes with an authenticated session confirmed:
- Public: `/` 200, `/blog` 200, `/blog/{slug}` 200, `/search` 200, `/search/map` 200, `/pricing` 200, `/listings` 200, `/about` 200, `/faq` 200, `/contact` 200, `/login` 200, `/register` 200, `/sitemap.xml` 200.
- Authenticated: `/dashboard` 200, `/dashboard/profile` 200, `/dashboard/subscription` 200, `/dashboard/saved-searches` 200, `/dashboard/favorites` 200, `/dashboard/credits` 200.
- Negative: `/blog/{draft-slug}` 404, `/blog/{unknown-slug}` 404 (asserted in `BlogTest`).

### New Tests
- `tests/Feature/BlogTest.php` (7 tests): index loads; index shows published only; show loads for published; show increments `view_count`; show 404s for drafts; show 404s for unknown slugs; homepage blog links resolve.

### Not Verified
- No browser rendering was performed; correctness is based on HTTP status codes and compiled templates, not visual inspection.
- Barikoi live API calls were not exercised (no `BARIKOI_API_KEY` in `.env`); the OSM fallback path is what renders.

## 2026-09-15 Homepage CMS Fix Verification

- `php artisan migrate:status`: all 11 migrations, including `2026_09_15_000007_create_system_content_tables`, reported `Ran`.
- Live schema check: `cms_pages` contains `is_active`, not `status`, `type`, or `is_published`; row count was 0.
- `php artisan test --filter="test_homepage_loads|test_homepage_shows_published_blog_posts_only"`: **2 passed, 4 assertions**.
- Regression test confirms published `BlogPost` records render and draft records do not.
- `GET /`: verified successfully with HTTP 200 after the fix.
- N+1 check: the homepage article query loads scalar `BlogPost` data only; no article relationships are accessed in the view. Existing listing relationships remain eager-loaded in `HomeController`.
- Broader `PublicPagesTest`: 11 passed and 1 unrelated existing failure in `resources/views/listings/show.blade.php` due to an unclosed Blade conditional.

## 2026-09-15 Demo Seed Verification [VERIFIED]

- `php artisan migrate:status`: all 11 migrations reported `Ran`.
- `php artisan db:seed`: completed successfully after adding missing canonical demo areas.
- `php artisan optimize:clear`: completed successfully.
- Second `php artisan db:seed`: completed successfully with stable counts, proving idempotency.
- Public HTTP checks: `/`, `/search`, `/listing/demo-family-mirpur-3br`, `/property-type/apartment`, `/division/dhaka`, `/division/dhaka/district/dhaka-district/area/mirpur`, and `/page/about-toletkoi` all returned HTTP 200.
- `php artisan test --filter=PublicPagesTest`: 12 passed, 14 assertions.
- `php artisan test`: 36 passed, 65 assertions.
- CMS root cause: publication state belongs to `blog_posts.status`; `cms_pages` uses `is_active` and has no `status` or `type` columns. No migration was needed.
- Reference audit: both legacy projects were read-only; no compatible SQL dump was imported. Public site fetch was blocked by network policy.
