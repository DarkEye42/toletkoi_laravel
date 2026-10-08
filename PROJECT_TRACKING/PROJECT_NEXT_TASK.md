# ToLetKoi - Next Task

**Last Updated:** 2026-09-16 (Barikoi complete)
## Latest: Barikoi Map Integration [COMPLETE]

### Delivered
- **Barikoi live geocoding works.** Three mistakes were fixed: host `barikoi.com` → **`barikoi.xyz`**, parameter `key` → **`api_key`**, reverse path `/search/reverse` → **`/search/reverse/geocode`** with `longitude` before `latitude`.
- `BarikoiMapController` — server-side proxy for `config`, `autocomplete`, `geocode`, `reverseGeocode` with validation and stable error codes.
- `BarikoiService` rewritten; return keys unified to `latitude`/`longitude`; `isConfigured()` + nullable `getTileUrl()` so maps degrade to OpenStreetMap without a key.
- `window.BARIKOI_CONFIG` now rendered server-side (no fetch before drawing); `$barikoiConfig` shared with all views.
- `tests/Feature/BarikoiServiceTest.php` — 15 tests, HTTP faked.

### Verification
- Live round-trips succeeded for autocomplete, geocode and reverse geocode.
- All four `/api/maps/*` endpoints return correct JSON; invalid input returns 422.
- **72 tests / 156 assertions passing.** All pages 200. CSS rebuilt (`app-D7p0WH3A.css`).

### Blockers
- **None for maps.** The Barikoi integration is complete and verified.
- Google/Facebook OAuth credentials still absent (ISSUE-001), so social login is non-functional.

### Next Tasks
1. **Visual confirmation of the map + redesigned UI** — no browser is available in this environment; map tiles are verified as a correct URL in the page source, not as rendered pixels
2. **Extend Phase 03 admin**: user management, CMS/blog management, reports/analytics, file manager
3. **Set up notifications/email** (ISSUE-004) — password reset and moderation emails
4. **Phase 04** — User dashboards & role switching
5. **Phase 05** — Monetization, payments & growth
6. **Phase 06–09** — Trust/AI/KoiFind, Property Management SaaS, Flutter, QA & deployment
## Previous: UI Redesign + Image Pipeline + Phase 03 Admin [COMPLETE]

### Deliverables
- **Landing page redesigned** — centred hero, category chips, trust stats, property-type grid, featured/latest grids, popular areas, "How ToLetKoi Works", articles, CTA (`resources/views/home.blade.php`).
- **Search modal rebuilt** to match `design_ref/search_modal.png` — "Property search" header, Property ID field with inline search button, OR divider, category buttons, Division → District → Area/Thana cascade, Cancel + Search footer. Cascade data comes from the cached `nav_geo_cascade` in `AppServiceProvider`.
- **Image pipeline implemented** (ISSUE-002 resolved) — `ImageService` uses GD to validate, EXIF-rotate, resize to a 1600px longest edge, re-encode to WebP (q82) and generate a 480px thumbnail. `ListingManageController` now actually passes the uploaded files to the service.
- **Dashboard UI sweep complete** — 14 views migrated off `emerald-*`/`gray-900`/emoji onto the shared tokens.
- **Phase 03 Admin Dashboard started** — admin shell, overview dashboard, listing moderation queue, review detail, approve/reject/feature/delete, all behind `role:Admin|Super Admin`.

### Verification
- `php artisan test` — **57 passed, 122 assertions** (was 36/65).
- `php artisan view:cache` — all templates compile.
- All public, dashboard and admin URLs return the expected status codes; non-admin → 403 on `/admin*`.
- Image pipeline confirmed by decoding stored bytes (2400x1600 JPEG → 1600x1067 WebP + 480x320 WebP thumbnail).

### Blockers
- **Barikoi endpoints unverified** (ISSUE-003). The key loads, but every documented endpoint returns HTML and `api.barikoi.com` does not resolve from this environment. Maps fall back to OpenStreetMap, so pages render, but live geocoding/autocomplete need the correct endpoint paths confirmed from the Barikoi dashboard.
- Google/Facebook OAuth credentials still absent (ISSUE-001).

### Next Tasks
1. **Confirm Barikoi base URL + endpoint paths** and complete a real geocode round-trip (ISSUE-003)
2. **Extend Phase 03 admin**: user management, CMS/blog management, reports/analytics, file manager
3. **Build AdminListingController coverage** for bulk actions and expiry handling
4. **Set up notifications/email** (ISSUE-004) — password reset and moderation emails
5. **Phase 04** — User dashboards & role switching
6. **Phase 05** — Monetization, payments & growth
7. **Phase 06–09** — Trust/AI/KoiFind, Property Management SaaS, Flutter, QA & deployment
## Previous: Bug Fix + URL Reliability Sprint [COMPLETE]
- Fixed duplicate `areas` migration that failed every fresh migrate and errored 16 tests
- Fixed HTTP 500s on `/search`, `/pricing`, `/dashboard`, `/dashboard/subscription`
- Built the missing blog subsystem (controller, 2 routes, 2 views, 7 tests) — homepage article links previously 404ed
- Restyled `dashboard/index`, `dashboard/saved-searches`, `dashboard/subscription` onto the shared design tokens
- Verified every public URL: `/`, `/blog`, `/blog/{slug}`, `/search`, `/search/map`, `/pricing`, `/listings`, `/about`, `/faq`, `/contact`, `/login`, `/register` → all HTTP 200
- Verified every authenticated URL: `/dashboard`, `/dashboard/profile`, `/dashboard/subscription`, `/dashboard/saved-searches`, `/dashboard/favorites`, `/dashboard/credits` → all HTTP 200
- Suite: **43 tests / 78 assertions passing**

## Previous: UI Redesign & Barikoi Integration [COMPLETE]
- Full UI redesign completed across 11 files (nav, footer, homepage, listing detail, search pages, map page, card component)
- Barikoi map integration: `BarikoiService` + API proxy routes + tile rendering + custom markers
- All maps now use Barikoi tiles with OSM fallback
- Search modal with Barikoi location autocomplete in navigation
- Mobile-responsive design throughout
- All PHP syntax checks passed, caches cleared and warmed
- 87+ routes registered

## Current Phase
Phase 02 - Laravel Web Platform ✅ COMPLETE (including UI redesign)

## Current Objective
Phase 02 is fully complete. Ready to proceed to Phase 03 - Admin Dashboard.

## Last Completed Task
UI Redesign & Barikoi Map Integration: Redesigned nav, footer, homepage, listing detail, search pages, map search page, and card component. Created `BarikoiService` for API proxying. Added 6 Barikoi map proxy routes. Integrated Barikoi tiles into Leaflet maps with custom blue markers. All syntax checks passed, caches warmed.

## Next Tasks
1. **Set `BARIKOI_API_KEY` in `.env`** and verify live geocoding, reverse-geocoding and autocomplete against the Barikoi API
2. **Image upload processing** in `ListingManageController` (ISSUE-002) — still unimplemented; validate MIME, resize, WebP-convert, thumbnails
3. **Finish dashboard UI pass** — `profile`, `credits`, `favorites`, and the listings CRUD views still use the legacy palette/emoji styling
4. **Phase 03 - Admin Dashboard** (CRUD for listings, users, CMS, reports, file management)
5. **Phase 04 - User Dashboards & Role Switching** (landlord/tenant/agent perspectives)
6. **Phase 05 - Monetization, Payments & Growth** (bKash/Nagad integration, advertising)
7. **Phase 06 - Trust, AI, KoiFind & Rental Services** (verification, AI matching, agreements)
8. **Phase 07 - Property Management SaaS** (buildings, units, tenants, maintenance)
9. **Phase 08 - Flutter/Dart Mobile App** (MUST NOT start until web platform done)
10. **Phase 09 - QA, Security & Deployment**

## Blockers (superseded — see Blockers in the Latest section above)

## Expected Result
Phase 03 Admin Dashboard complete with full CRUD for all entities.

## Definition of Done
- Admin can manage all listings (approve, reject, feature, delete)
- Admin can manage users and roles
- Admin can manage CMS pages and blog posts
- Admin can view reports and analytics
- Admin can manage file uploads
- All admin routes protected by role middleware
- Tests passing for all admin features
