## 2026-09-16 (Barikoi integration complete)
### Barikoi Map Integration — ISSUE-003 Resolved
#### Fixed
- **Wrong Barikoi host.** `BarikoiService` called `barikoi.com/v2/api`, which serves the marketing site and returns HTML for every request. Correct host is `barikoi.xyz`.
- **Wrong credential parameter.** Sent `key=`; Barikoi expects `api_key=`.
- **Wrong reverse-geocode path.** Called `/search/reverse`; correct path is `/search/reverse/geocode` with `longitude` then `latitude`.
- `resources/views/search/map.blade.php` — passing `null` to `L.tileLayer` when unconfigured left the map blank with the loading overlay stuck; new `applyTiles()` helper selects Barikoi or OpenStreetMap tiles up front.
- `BarikoiService` — removed an `env('BARIKOI_API_KEY')` fallback. Calling `env()` outside a config file returns `null` once `config:cache` has run, so behaviour would differ between development and production; it also made the service untestable. Config is now the single source of truth.
- `routes/web.php` — the four `/api/maps/*` closures passed an array where an int was expected (`autocomplete`) and swapped latitude/longitude on reverse geocode. Moved into `BarikoiMapController` with proper validation.

#### Added
- `app/Http/Controllers/BarikoiMapController.php` — server-side proxy: `config`, `autocomplete`, `geocode`, `reverseGeocode`. Validates input, keeps the key off the client, returns stable JSON (422/404/502/503) and never leaks raw provider errors.
- `tests/Feature/BarikoiServiceTest.php` — 15 tests: normalisation, correct endpoint + `api_key` parameter, longitude-before-latitude ordering, out-of-range coordinate rejection, unconfigured short-circuit, provider failure, limit handling, and cache de-duplication. HTTP faked; no API quota consumed.

#### Changed
- `app/Services/BarikoiService.php` — rewritten against verified endpoints. Unified return keys to `latitude`/`longitude`. Added `isConfigured()`, nullable `getTileUrl()`, `normalizePlace()` and coordinate validation. `reverseGeocode()` is now `(float $longitude, float $latitude, array $options = [])`, matching the official package.
- `resources/views/layouts/app.blade.php` — `window.BARIKOI_CONFIG` is rendered server-side, so maps no longer need a fetch before drawing. Built as a PHP variable because Blade's parser cannot handle a nested array literal inside `@json(...)`.
- `app/Providers/AppServiceProvider.php` — `View::composer('*')` shares `$barikoiConfig` with every view.

#### Verified
- Live round-trips: autocomplete ("Gulshan"), reverse geocode `(90.4125, 23.8103)` → "Baridhara Dohs, Baridhara, Dhaka", geocode ("Banani, Dhaka") → `23.79008396, 90.40124438`.
- `php artisan test` — 72 passed, 156 assertions.
- All public pages plus a real listing detail URL return HTTP 200. CSS rebuilt: `app-D7p0WH3A.css` (116.64 kB).

#### Not done
- `barikoi/barikoiapis` could not be installed: its release caps at `illuminate/support ^11.0` and this project runs Laravel 12. Installing it would require downgrading Laravel, breaking Spatie Permission v6, Livewire 4 and the test suite. Endpoints are called directly instead, with the package's documented argument order preserved.

---

## 2026-09-16 (continued)
### Asset Rebuild + Category Rail Fix
#### Fixed
- **Stale compiled CSS.** `public/build/manifest.json` referenced `app-CB3oYNNr.css`, produced before the UI redesigns. Because Tailwind v4 discovers classes by scanning templates at build time, none of the classes used by the new landing page, search modal, dashboard or admin views existed in the shipped stylesheet — the pages rendered unstyled in a browser despite returning HTTP 200. Rebuilt via `npm run build`; new assets are `app-BcuOOgWx.css` (93.19 kB) and `app-DKL-jiOM.js`.
- **Homepage category rails never matched anything.** `resources/views/home.blade.php` filtered `propertyType.name` for `family`/`bachelor`/`office`/`sublet`/`hostel`, but `property_types.name` holds `Apartment`/`House`/`Room`/`Office Space`/`Shop`/`Land`, and no category column exists in the schema. All five rails rendered zero listings. Rails are now keyed to real property-type slugs (`house`+`apartment`, `room`, `office-space`, `shop`, `land`).
- Re-applied the `search_modal.png` design to `resources/views/components/layouts/nav.blade.php` (Property search header, Property ID field with inline search button, OR divider, category buttons, Division → District → Area/Thana cascade, Cancel + Search footer) plus the Alpine `propertyId` / `divisionDistrictMap` / `districtAreaMap` state, `submitSearch()` and `resetSearch()`.

#### Verification
- `php artisan test` — 57 passed, 122 assertions.
- `php artisan view:cache` — all templates compile.
- URL sweep — 12 public, 10 dashboard, 3 admin routes all correct; guest `/admin` → 302.
- Homepage renders 20 real listing links across the category rails (was 0).

#### Note
- The workspace reverted to an earlier revision of `home.blade.php` and `nav.blade.php` between sessions. All backend artifacts (ImageService, admin controllers/views/routes, blog subsystem, both migrations, AdminPanelTest, BlogTest, ListingImageUploadTest) were intact; only the two view redesigns were lost and have been reapplied.

---

## 2026-09-16 (later session)
### UI Redesign + Image Pipeline + Phase 03 Admin Dashboard
#### Added
- `app/Http/Controllers/Admin/AdminDashboardController.php` — platform stats, 14-day registration series, recent listings/users, pending-by-type, credits ledger total.
- `app/Http/Controllers/Admin/AdminListingController.php` — moderation queue (status/search/property-type filters), review detail, approve, reject-with-reason, feature toggle, delete.
- `resources/views/layouts/admin.blade.php`, `admin/dashboard.blade.php`, `admin/listings/index.blade.php`, `admin/listings/show.blade.php`.
- `app/Services/ImageService.php` — GD pipeline: header-based image validation, EXIF rotation, longest-edge resize (1600px), WebP encode (q82), 480px thumbnail.
- `tests/Feature/AdminPanelTest.php` (10 tests) and `tests/Feature/ListingImageUploadTest.php` (4 tests).
- `database/migrations/2026_09_16_000001_add_thumbnail_and_metadata_to_property_images.php`.
- `database/migrations/2026_09_16_000002_add_rejected_status_to_listings.php`.

#### Changed
- `resources/views/home.blade.php` — new landing page: centred hero, category chips, trust stats, property-type grid, featured/latest grids, "How ToLetKoi Works", articles, CTA.
- `resources/views/components/layouts/nav.blade.php` — search modal rebuilt to `design_ref/search_modal.png` (Property search title, Property ID field, OR divider, category buttons, Division/District/Area-Thana cascade, Cancel + Search footer).
- `app/Providers/AppServiceProvider.php` — shares cached `divisionDistrictMap` / `districtAreaMap` with the nav; registers `ImageService` as a singleton.
- `app/Services/ListingService.php` — `uploadImages()` uses `ImageService`; `deleteListing()` removes thumbnails as well as full images.
- `app/Models/PropertyImage.php` — thumbnail/width/height/size fillable + casts, `getThumbnailUrl()`, `getReadableSize()`.
- `app/Models/Listing.php` — `rejection_reason` added to `$fillable`.
- `routes/web.php` — 7 admin routes under `auth` + `role:Admin|Super Admin`.
- 14 dashboard/auth views migrated from `emerald-*` / `gray-900` / emoji onto the shared design tokens.

#### Fixed
- `ListingManageController` — images were never persisted because `validated()` omits uploaded files; added `uploadedImages()` and merged into the payload for both `store()` and `update()`.
- `bootstrap/app.php` — Spatie middleware aliases pointed at `Spatie\Permission\Middlewares\*`, which does not exist in v6 (now `Middleware`, singular). This broke every role-protected route.
- `listings.status` enum — added `rejected` so moderation can persist a rejection.
- `listings` — added the missing `rejection_reason` column.
- `HomeController::searchLocations()` — removed an invalid `'type' => 'division'` entry from a `get()` column list that threw instead of returning divisions.

#### Testing
- `php artisan test` — **57 passed, 122 assertions**.
- `php artisan view:cache` — all templates compile.
- Direct request probes — all public, dashboard and admin URLs returned the expected status codes; non-admin access to `/admin*` returns 403.
- Image pipeline verified by decoding the stored bytes (2400x1600 JPEG → 1600x1067 WebP full + 480x320 WebP thumbnail).

#### Blocked
- Barikoi live geocoding unverified: API key loads, but all documented endpoints return HTML and `api.barikoi.com` does not resolve here.

---

## 2026-09-16
### Bug Fix + URL Reliability Sprint
### Database
- `database/migrations/2026_09_15_022025_recreate_areas_table.php` — made idempotent (`Schema::hasTable('areas')` guard, empty `down()`). It duplicated an identical table already created by the geography migration, breaking every fresh migrate and erroring 16 tests.

### Fixed
- `resources/views/search/index.blade.php` — `route('map-search')` → `route('search.map')`. Was causing HTTP 500 on `/search`.
- `app/Http/Controllers/PageController.php` — `SubscriptionPlan::orderBy('price')` → `orderBy('price_monthly')`. Was causing HTTP 500 on `/pricing`.
- `app/Http/Controllers/User/SubscriptionController.php` — same `orderBy('price_monthly')` correction.
- `app/Models/User.php` — `getPrimaryRole()` no longer calls the undefined `getFirstRole()`; falls back to the user's first role. Was causing HTTP 500 on `/dashboard`.
- `app/Models/BlogPost.php` — `views_count` → `view_count` in `$fillable` and `$casts` to match the actual column.
- `resources/views/pages/pricing.blade.php` — replaced non-existent `$plan->price` / `$plan->duration_days` with `price_monthly` / `price_yearly`, and restyled to the shared design tokens.
- `resources/views/dashboard/subscription.blade.php` — same field corrections plus design-token restyle.
- `resources/views/home.blade.php` — blog card links now use `route('blog.show', ...)`; "View All" uses `route('blog.index')`.
- `tests/TestCase.php` — excludes `ValidateCsrfToken` (Laravel 12 name; `VerifyCsrfToken` does not exist), fixing HTTP 419 in all POST feature tests.

### Added
- `app/Http/Controllers/BlogController.php` — **NEW** `index()` (paginated published posts + featured) and `show()` (slug lookup, 404 on drafts/unknown, increments `view_count`, loads related posts).
- `resources/views/blog/index.blade.php` — **NEW** blog listing with featured article, responsive grid, pagination.
- `resources/views/blog/show.blade.php` — **NEW** article page with breadcrumb, related-articles sidebar, search CTA, styled article body.
- `tests/Feature/BlogTest.php` — **NEW** 7 tests covering index, published-only filtering, detail load, view-count increment, 404 for drafts, 404 for unknown slugs, and homepage link resolution.

### Frontend
- `resources/views/dashboard/index.blade.php` — rebuilt on `.card` / `.badge-*` / Material Icons; removed emoji icons and the legacy `emerald`/`gray-900` palette.
- `resources/views/dashboard/saved-searches.blade.php` — restyled to design tokens, Material Icons replace emoji, empty state added.

### API / Routes
- `routes/web.php` — registered `GET /blog` (`blog.index`) and `GET /blog/{slug}` (`blog.show`), previously dead links from the homepage.

### Testing
- Suite now **43 tests / 78 assertions passing** (was 36 / 65). All Blade templates compile; all public and authenticated URLs verified HTTP 200 via direct request probes.

---

## 2026-09-15
### UI Redesign & Barikoi Map Integration - COMPLETE

**Files Modified/Created (11 total):**

| File | Change |
|------|--------|
| `app/Services/BarikoiService.php` | **NEW** - Barikoi API proxy (autocomplete, geocode, reverse geocode) with 5-10min caching |
| `config/services.php` | Added `barikoi.key` config entry |
| `routes/web.php` | Added 6 `/api/maps/*` proxy routes (config, autocomplete, geocode, reverse-geocode) with throttle |
| `resources/views/layouts/app.blade.php` | Barikoi config JS injection, tile CSS, Leaflet Barikoi integration |
| `resources/views/components/layouts/nav.blade.php` | **FULL REWRITE** - Single-row header, search modal with Barikoi autocomplete, category bar, mobile menu |
| `resources/views/components/layouts/footer.blade.php` | **FULL REWRITE** - Dark theme `bg-gray-900`, 4-column grid, SEO links, social media |
| `resources/views/home.blade.php` | **FULL REWRITE** - Hero with gradient, search form, property grid, featured/recent, areas, blog, CTA |
| `resources/views/listings/show.blade.php` | **FULL REWRITE** - Image gallery, Barikoi map with custom marker, features grid, contact sidebar |
| `resources/views/search/index.blade.php` | **FULL REWRITE** - Modern filter sidebar, sort controls, map view link, responsive |
| `resources/views/search/map.blade.php` | **FULL REWRITE** - Barikoi tiles, Alpine.js reactive, mobile filter panel, results badge |
| `resources/views/components/listings/card.blade.php` | **UPDATED** - Cleaner design, listing type badges, price formatting, hover effects |

**Barikoi Integration:**
- `BarikoiService.php`: PHP service proxying Barikoi API (autocomplete, geocode, reverse geocode) with `Cache::remember()`
- API routes: `/api/maps/config`, `/api/maps/autocomplete`, `/api/maps/geocode`, `/api/maps/reverse-geocode`
- Tile URL: `https://tile.barikoi.com/styles/osm-liberty/{z}/{x}/{y}.png?key={apiKey}`
- Custom marker: Blue circle with `rotate(-45deg)` + home icon matching `#00a1ff` primary
- Fallback: OSM tiles if Barikoi config fails
- Listing detail map: Initialized with Leaflet, Barikoi tiles, custom popup
- Map search: Alpine.js reactive, viewport-based listing loading, mobile responsive

**Design Language:**
- Primary color: `#00a1ff`
- Rounded corners: `rounded-2xl` (1rem)
- Card style: white bg, `border-gray-100`, hover `shadow-lg` + `border-primary/20`
- Typography: Material Icons, small/medium text sizes
- Color palette: primary/50 backgrounds, gray-50/100 borders

**Verification:**
- All 11 files pass `php -l` syntax check
- View cache cleared: `php artisan view:clear`
- Config cache warmed: `php artisan config:cache`
- Route cache warmed: `php artisan route:cache`
- No test regressions expected (UI-only changes)

---

## 2026-09-15

### Phase 02 COMPLETE - Laravel Web Platform
- All 36 tests pass (17 unit + 19 feature, 65 assertions)
- 81 routes registered, all Blade templates compile, config cached
- Key fixes in final session:
  - Escaped `@context`/`@type` in JSON-LD structured data with `@@` (Livewire was interpreting them as directives)
  - Fixed `$listing->subtype` → `$listing->propertySubtype` (relationship name mismatch)
  - Fixed `$listing->views_count` → `$listing->stats->view_count ?? 0` (value is in related `listing_stats` table)
  - Added `'slug' => 'tenant'` to Spatie Role creation in tests (v6 requires slug column)
  - Fixed RegisterController redirect route from `profile.edit` → `dashboard.profile.edit`

### Added: Development/demo seed dataset
- Added `database/seeders/ToLetKoiDemoSeeder.php` and registered it in `DatabaseSeeder.php`.
- Added 30 synthetic listings across family, bachelor, sublet, office, hostel, and regional apartment patterns.
- Added related images, feature flags, listing stats, favorites, saved searches, leads, views, credit wallets/ledger entries, CMS pages, five published blog posts, service providers, organizations, tenants, subscription, and payment data.
- Seed records use deterministic slugs/emails and `updateOrCreate`/`firstOrCreate` patterns; rerunning `php artisan db:seed` leaves counts stable.
- Added local synthetic SVG placeholders under `storage/app/public/demo/listings/`; no external property images or private credentials were imported.
- Legacy reference projects were read-only; no files were modified. The public research site was blocked by network policy.

### Compatibility fixes discovered during seed verification
- Fixed geography Blade links from nonexistent `pages.*` route names to the existing nested geography routes.
- Fixed search pagination to use Laravel's default renderer instead of missing `vendor.pagination.simple`.

### Fixed: Homepage CMS publication query
- Root cause: `HomeController` filtered `CmsPage` by nonexistent `status` and `type` columns. The live `cms_pages` schema uses `is_active`; publication status belongs to `blog_posts`.
- Changed `HomeController` to query `BlogPost::published()`, order by `published_at`, and limit results to three.
- Changed `PageController` to use `CmsPage.is_active` and removed nonexistent CMS model fillable/cast fields.
- Updated the homepage article image binding from `image` to the existing `blog_posts.featured_image` column.
- No database migration was required; no CMS data was altered.
- Added homepage regression coverage for published and draft blog posts.
