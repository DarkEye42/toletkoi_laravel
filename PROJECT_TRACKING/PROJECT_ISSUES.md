# ToLetKoi - Issues

**Last Updated:** 2026-09-16 (Barikoi complete)
## Resolved in 2026-09-16 (Barikoi integration)
### ISSUE-022: Map Config Passed `null` to Leaflet When Unconfigured
- **Severity:** Medium / **Priority:** Medium / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** `resources/views/search/map.blade.php` branched on `BARIKOI_CONFIG.tile_url` but the fallback path also read `config.tile_url`, so with no API key it called `L.tileLayer(null, ...)`. Leaflet throws on a null URL and the map rendered blank with the loading overlay stuck.
- **Resolution:** Extracted an `applyTiles(config)` helper that selects Barikoi tiles when a URL exists and OpenStreetMap tiles otherwise. The layout now also renders `window.BARIKOI_CONFIG` server-side, so the map no longer needs a fetch before it can draw.

### ISSUE-023: `env()` Called Outside Config Broke Testability
- **Severity:** Low / **Priority:** Medium / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** `BarikoiService` fell back to `env('BARIKOI_API_KEY')` when config was absent. Calling `env()` outside a config file is an anti-pattern — it returns `null` once `config:cache` has run, so behaviour would differ between development and production. It also made the service untestable, since `config()` could not override the key.
- **Resolution:** The service reads `config('services.barikoi.key')` only; the `env()` call lives where it belongs, in `config/services.php`.

## Resolved in 2026-09-16 (continued)
### ISSUE-020: Stale Compiled Assets Made Redesigned Pages Render Unstyled
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** `public/build/manifest.json` referenced `app-CB3oYNNr.css`, built before the UI redesigns. Tailwind v4 discovers utility classes by scanning template files at build time, so none of the classes introduced by the new landing page, search modal, dashboard or admin views were present in the shipped stylesheet. The pages returned HTTP 200 and their templates compiled cleanly, so nothing in the test suite or the request probes could detect it — but in a browser the redesigned pages would appear largely unstyled.
- **Impact:** Any visual verification or user-facing demo would have shown broken layout.
- **Resolution:** Ran `npm run build`. New assets: `app-BcuOOgWx.css` (93.19 kB, up from ~68 kB) and `app-DKL-jiOM.js`.
- **Prevention:** **Always run `npm run build` after editing Blade views.** `php artisan view:cache` only compiles Blade to PHP — it does not regenerate CSS, so it cannot catch missing utility classes. Node is at `C:\Program Files\nodejs` and is not on the default PATH.

### ISSUE-021: Homepage Category Rails Always Rendered Empty
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** `resources/views/home.blade.php` built its Family/Bachelor/Office/Sublet/Hostel rails by comparing `propertyType.name` against those words. Real property type names are `Apartment`, `House`, `Room`, `Office Space`, `Shop` and `Land`, and there is no category column anywhere in the schema, so every comparison failed and all five rails silently rendered nothing. The surrounding heading still displayed, making the page look intentionally empty.
- **Resolution:** Rails are now driven by real property-type slugs: Family = `house`+`apartment`, Bachelor = `room`, Office = `office-space`, Shop = `shop`, Land = `land`.
- **Verification:** Homepage renders 20 real listing links (was 0); Family and Bachelor rails populate from the demo dataset.

## Resolved in 2026-09-16 (later session)
### ISSUE-002: Image Upload Processing Not Implemented
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Root cause:** `ListingManageController@store` passed `$request->validated()` straight to the service, but `validated()` returns scalar input only — uploaded files were never included, so `$validated['images']` was always absent and `ListingService::uploadImages()` never ran.
- **Resolution:** Added an `uploadedImages()` helper that pulls and filters files from the request, merged into the payload for both `store()` and `update()`. Built `ImageService` (GD) to validate, rotate by EXIF, resize to a 1600px longest edge, re-encode to WebP and generate a 480px thumbnail. Added `thumbnail_path`/`width`/`height`/`size` columns and a migration.
- **Verification:** `tests/Feature/ListingImageUploadTest.php` (4 tests) plus a live probe decoding stored bytes: 2400x1600 JPEG → 1600x1067 WebP + 480x320 WebP.

### ISSUE-017: Spatie Role Middleware Alias Pointed at a Missing Class
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** `bootstrap/app.php` aliased `role`, `permission` and `role_or_permission` to `Spatie\Permission\Middlewares\*`. In spatie/laravel-permission v6 those classes live in `Middleware` (singular) — the `Middlewares` namespace does not exist. Any route using the `role:` middleware threw a BindingResolutionException.
- **Resolution:** Corrected the three aliases to `Spatie\Permission\Middleware\*`.
- **Verification:** Admin routes now resolve; non-admin users receive 403 and Admin users 200.

### ISSUE-018: `listings.status` Enum Rejected the `rejected` Value
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** The enum allowed only `pending/active/inactive/rented/sold`. Admin moderation could not set `rejected`; MySQL raised a CHECK constraint violation.
- **Resolution:** Migration widens the enum (MySQL `MODIFY`; SQLite via `writable_schema`, avoiding a table rename that would have broken foreign keys referencing `listings`).
- **Verification:** `AdminPanelTest::test_admin_can_reject_a_listing_with_reason` passes.

### ISSUE-019: `listings.rejection_reason` Column Did Not Exist
- **Severity:** Medium / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** The moderation reason had nowhere to be stored, and `rejection_reason` was also missing from `Listing::$fillable`, so it was silently discarded even if the column had existed.
- **Resolution:** Added the nullable `text` column and the `$fillable` entry.
- **Verification:** Rejection reason round-trips through the database in tests.

## Resolved in 2026-09-16 Sprint
### ISSUE-013: Duplicate `areas` Migration Broke Fresh Migrate
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** `2026_09_15_022025_recreate_areas_table` re-created `areas`, which `2026_09_15_000001_create_geography_tables` already created identically. Every fresh `migrate` failed and 16 tests errored.
- **Resolution:** Guarded with `Schema::hasTable('areas')` and emptied `down()` so ownership stays with the geography migration.
- **Verification:** Full suite now passes 43 tests / 78 assertions.

### ISSUE-014: Dead Blog Links (No Blog Route)
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** Homepage "Blog & Articles" cards and the "View All" link pointed to `/blog` and `/blog/{slug}`; neither route was registered, so every article link 404ed.
- **Resolution:** Added `BlogController` (index/show), both routes, two views, and `tests/Feature/BlogTest.php`.
- **Verification:** `/blog` and `/blog/{slug}` return HTTP 200; 7 new tests pass.

### ISSUE-015: HTTP 500 on `/search`, `/pricing`, `/dashboard`, `/dashboard/subscription`
- **Severity:** High / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** Four pages threw 500s: `/search` referenced missing route `map-search`; `/pricing` and `/dashboard/subscription` ordered `subscription_plans` by a non-existent `price` column; `/dashboard` called an undefined `User::getFirstRole()`.
- **Resolution:** Corrected the route name, switched ordering to `price_monthly`, and made `getPrimaryRole()` fall back to the user's first role.
- **Verification:** All four URLs verified HTTP 200 via direct request probes.

### ISSUE-016: Feature Tests Failing with HTTP 419
- **Severity:** Medium / **Priority:** High / **Status:** Resolved
- **Discovered:** 2026-09-16
- **Description:** POST feature tests received 419 CSRF failures. `tests/TestCase.php` did not exclude CSRF middleware, and the obvious class name `VerifyCsrfToken` no longer exists in Laravel 12 (renamed `ValidateCsrfToken`).
- **Resolution:** Exclude `ValidateCsrfToken` in `setUp()`.
- **Verification:** AuthenticationTest and all POST-based tests pass.

### ISSUE-003: Barikoi Live API Unreachable / Endpoints Unverified
- **Severity:** Medium / **Priority:** Medium / **Status:** **RESOLVED**
- **Discovered:** 2026-09-16
- **Resolved:** 2026-09-16
- **Root cause (three separate mistakes, all now fixed):**
  1. **Wrong host** — the service used `barikoi.com/v2/api`, which serves the marketing site and returns HTML for every request. The real API host is **`barikoi.xyz`**.
  2. **Wrong credential parameter** — the service sent `key=`. Barikoi expects **`api_key=`**.
  3. **Wrong reverse-geocode path** — the service called `/search/reverse`. The correct path is **`/search/reverse/geocode`**, taking `longitude` then `latitude`.
- **Correct endpoints (verified against the live API):**
  - `GET https://barikoi.xyz/v2/api/search/autocomplete/place?api_key=&q=`
  - `GET https://barikoi.xyz/v2/api/search/reverse/geocode?api_key=&longitude=&latitude=`
- **Note on forward geocoding:** Barikoi v2 exposes no dedicated forward-geocode endpoint, so `geocode()` uses the autocomplete endpoint and returns the best match — the same approach the official package takes.
- **Note on the official package:** `barikoi/barikoiapis` could **not** be installed. Its current release caps at `illuminate/support ^11.0` while this project runs **Laravel 12**. Installing it would require downgrading Laravel, which would break Spatie Permission v6, Livewire 4 and the test suite. The same endpoints are therefore called directly through the HTTP client, with the package's documented argument order (`longitude, latitude`) preserved.
- **Verification:** Live round-trips succeeded for all three operations — autocomplete for "Gulshan" returned real places; `reverseGeocode(90.4125, 23.8103)` returned "Hospital, Lane 11 West, Baridhara Dohs, Baridhara, Dhaka"; `geocode("Banani, Dhaka")` returned coordinates `23.79008396, 90.40124438`. Covered by `tests/Feature/BarikoiServiceTest.php` (15 tests, HTTP faked so the suite spends no API quota).

### ISSUE-009: Listing Model Missing slug Route Key
- **Severity:** Low / **Priority:** Low / **Status:** Resolved (documentation was stale)
- **Description:** Listing detail URLs use the nested `district/area/type/subtype/slug` form and were verified returning HTTP 200 for active listings and 404 for pending ones.

### ISSUE-011 / ISSUE-012: Demo Dataset + Navigation Route Names
- **Status:** Resolved (superseded by the 2026-09-16 sprint verification).

## Open Issues
### ISSUE-001: Social Login Missing Credentials
- **Severity:** Medium
- **Priority:** Low
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** SocialLoginController references Google/Facebook OAuth but no API credentials are configured in .env
- **Affected:** Social login functionality
- **Resolution:** Add GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET to .env
- **Resolution Date:** N/A

### ISSUE-002: Image Upload Processing Not Implemented
- **Severity:** High
- **Priority:** High
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** ListingManageController@store does not handle file uploads. Images are not processed, resized, or stored.
- **Affected:** Listing creation with images
- **Resolution:** Implement Intervention Image or similar for upload processing, resize, WebP conversion
- **Resolution Date:** N/A

### ISSUE-003: Barikoi API Not Configured
- **Severity:** Medium
- **Priority:** Medium
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** Map search uses Leaflet with OpenStreetMap tiles. Barikoi geocoding/reverse geocoding not yet integrated.
- **Affected:** Map search, address resolution, coordinate lookup
- **Resolution:** Add BARIKOI_API_KEY to .env and implement LocationService
- **Resolution Date:** N/A

### ISSUE-004: No Email/Notification System
- **Severity:** Medium
- **Priority:** Medium
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** No email templates, event listeners, or notification classes exist. Password reset emails cannot be sent.
- **Affected:** Password reset, contact unlock notifications, subscription confirmations
- **Resolution:** Create notification classes, email templates, configure mail driver
- **Resolution Date:** N/A

### ISSUE-005: No Form Request Validation
- **Severity:** Medium
- **Priority:** High
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** Controllers use inline $request->validate() instead of Form Request classes. No reusable validation rules.
- **Affected:** All form submissions
- **Resolution:** Create Form Request classes for all major inputs
- **Resolution Date:** N/A

### ISSUE-006: No Tests Written
- **Severity:** High
- **Priority:** High
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** No Feature or Unit tests exist. phpunit.xml has default config.
- **Affected:** Code quality, regression prevention
- **Resolution:** Create test suite for auth, listings, search, dashboard
- **Resolution Date:** N/A

### ISSUE-007: Production .env Not Configured
- **Severity:** Low
- **Priority:** Low
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** Still using local development .env. No production mail, queue, cache, or session config.
- **Affected:** Deployment readiness
- **Resolution:** Create .env.production with proper settings
- **Resolution Date:** N/A

### ISSUE-008: Rate Limiting Not Applied
- **Severity:** Medium
- **Priority:** Medium
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** No rate limiting middleware on public routes (search, listing views, API endpoints)
- **Affected:** API abuse prevention, DDoS protection
- **Resolution:** Apply Laravel rate limiting middleware to search, auth, and API routes
- **Resolution Date:** N/A

### ISSUE-009: Listing Model Missing slug Route Key
- **Severity:** Low
- **Priority:** Low
- **Status:** Open
- **Discovered:** 2026-09-15
- **Description:** Routes use `{listing:slug}` but Listing model doesn't override `getRouteKeyName()`. Slugs may not resolve correctly.
- **Affected:** Listing detail page URL resolution
- **Resolution:** Add `getRouteKeyName()` returning 'slug' to Listing model, or use `{listing}` with route model binding
- **Resolution Date:** N/A

## Resolved Issues

### ISSUE-011: Demo Dataset Missing
- **Severity:** Medium
- **Priority:** High
- **Status:** Resolved
- **Discovered:** 2026-09-15
- **Description:** The Laravel seed pipeline had no maintainable synthetic marketplace dataset.
- **Resolution:** Added idempotent `ToLetKoiDemoSeeder` with 30 listings and related dashboard, activity, credit, CMS, service, organization, and tenant records.
- **Verification:** Ran `php artisan db:seed` twice with stable counts; full suite passed 36 tests and 65 assertions.
- **Resolution Date:** 2026-09-15

### ISSUE-012: Seeded Public Navigation Used Missing Route/View Names
- **Severity:** Medium
- **Priority:** Medium
- **Status:** Resolved
- **Discovered:** 2026-09-15
- **Description:** Location views referenced `pages.*` route names and search referenced a missing pagination view.
- **Resolution:** Aligned location links with current nested route names and switched search to default pagination.
- **Verification:** Required public requests returned HTTP 200.
- **Resolution Date:** 2026-09-15

### ISSUE-010: Homepage CMS Query Used Nonexistent Columns
- **Severity:** High
- **Priority:** High
- **Status:** Resolved
- **Discovered:** 2026-09-15
- **Description:** Homepage queried `cms_pages.status` and `cms_pages.type`, which are absent from the applied CMS migration and live MySQL schema.
- **Resolution:** Use `BlogPost::published()` for the latest article/blog section; use `is_active` for static `CmsPage` records. No schema change was needed.
- **Affected Files:** `app/Http/Controllers/HomeController.php`, `app/Http/Controllers/PageController.php`, `app/Models/CmsPage.php`, `resources/views/home.blade.php`, `tests/Feature/PublicPagesTest.php`
- **Migration:** None; `2026_09_15_000007_create_system_content_tables` was already applied.
- **Resolution Date:** 2026-09-15
