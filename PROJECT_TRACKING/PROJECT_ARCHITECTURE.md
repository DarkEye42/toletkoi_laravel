# ToLetKoi - Architecture

**Last Updated:** 2026-09-15

## Application Architecture

```
ToLetKoi.com
├── Laravel 12 Backend (MVC)
│   ├── Routes (web.php) - 87+ routes
│   ├── Controllers (20+)
│   │   ├── Auth (4): Register, Login, ForgotPassword, ResetPassword, Social
│   │   ├── Public (6): Home, Search, MapSearch, Listing, Page, Contact
│   │   └── User Dashboard (8): Dashboard, Profile, ListingManage, Favorite,
│   │       SavedSearch, Credit, Subscription, KoiFind, Verification
│   ├── Models (52) with Eloquent relationships
│   ├── Services (4): CreditService, ListingService, SearchService, BarikoiService
│   ├── Migrations (11) creating 65+ tables
│   └── Seeders (8) with real Bangladesh data
├── Laravel Blade Frontend (UI Redesigned 2026-09-15)
│   ├── Layouts (3): app, guest, dashboard
│   ├── Views (38+): Auth, Public, Dashboard, Pages, Components
│   ├── Components: nav (Barikoi search modal), footer (dark theme), listing card
│   └── Assets: Tailwind CSS v4, Alpine.js 3.x, Leaflet.js 1.9.4, Material Icons
├── Barikoi Map Integration
│   ├── Service: BarikoiService (autocomplete, geocode, reverse geocode, tiles)
│   ├── API Proxy: /api/maps/* routes with throttle middleware
│   ├── Tiles: tile.barikoi.com/styles/osm-liberty/{z}/{x}/{y}.png
│   └── Custom Markers: Blue circle (#00a1ff) with rotate(-45deg) + icon
├── Database: MySQL (toletkoi_db)
├── Cache: file-based
├── Session: file-based
└── Storage: local filesystem (public disk)
```

## Backend Architecture

### MVC Pattern
- **Models**: Eloquent ORM with relationships, scopes, casts
- **Views**: Blade templates with layouts, components, sections
- **Controllers**: Thin controllers, validation in-form (Phase 02 pending: Form Requests)

### Authentication
- Laravel Breeze-like auth (manual implementation)
- Email/password registration with role selection (landlord/tenant/agent)
- Social login: Google, Facebook (via Laravel Socialite - configured but needs credentials)
- Password reset via email
- Session-based authentication

### Authorization
- Spatie Laravel Permission v6.25.0
- Roles: super_admin, admin, landlord, tenant, agent, property_manager
- Role-based access via Blade `@role` directives and route middleware

### Service Layer
- **Phase 02 Remaining**: Business logic currently in controllers
- Planned: CreditService, ListingService, SearchService
- helpers.php: `setting()` function for SiteSetting lookup

## Frontend Architecture (UI Redesigned 2026-09-15)

### CSS Framework
- Tailwind CSS v4 via Vite + @tailwindcss/vite plugin
- Custom theme with primary `#00a1ff`, secondary, accent colors
- Responsive design (mobile-first)
- Design language: `rounded-2xl` cards, small/medium text sizes, Material Icons

### JavaScript
- Alpine.js v3 (CDN) for reactive UI components
- Leaflet.js v1.9.4 for interactive map search with Barikoi tiles
- Barikoi autocomplete in search modal
- Custom interactive.js for:
  - Cascading dropdowns (Division → District → Area)
  - AJAX favorite toggle
  - AJAX contact unlock
- Vite bundling for app.js + app.css

### Barikoi Map Integration
- **Service**: `app/Services/BarikoiService.php` - PHP proxy for Barikoi API
  - `autocomplete($query)` - Location search with caching
  - `geocode($address)` - Address to coordinates
  - `reverseGeocode($lat, $lon)` - Coordinates to address
  - `getTileUrl()` - Map tile URL
  - `getApiKey()` - API key getter
- **API Routes**: `/api/maps/*` with throttle:120,1
  - `GET /api/maps/config` - Returns tile URL and API key
  - `GET /api/maps/autocomplete?q=` - Location autocomplete
  - `GET /api/maps/geocode?address=` - Geocode address
  - `GET /api/maps/reverse-geocode?lat=&lon=` - Reverse geocode
- **Tile URL**: `https://tile.barikoi.com/styles/osm-liberty/{z}/{x}/{y}.png?key={apiKey}`
- **Custom Marker**: Blue circle with `rotate(-45deg)` + home icon, matching primary `#00a1ff`
- **Fallback**: OSM tiles if Barikoi config fails

### Blade Components
- `<x-layouts.nav>` - Single-row header with Barikoi search modal, category bar, mobile menu
- `<x-layouts.footer>` - Dark theme `bg-gray-900`, 4-column grid, social links
- `<x-listings.card>` - Listing card with image, badges, price, details (grid + compact views)

### Page Views
- **Homepage** (`home.blade.php`): Hero, search form, property types, featured/recent, areas, blog, CTA
- **Listing Detail** (`listings/show.blade.php`): Gallery, Barikoi map, features, contact sidebar
- **Search** (`search/index.blade.php`): Filter sidebar, sort controls, results grid
- **Map Search** (`search/map.blade.php`): Full-screen Barikoi map, Alpine.js reactive, mobile filters

## Database Architecture

See PROJECT_DATABASE.md for full table documentation.

### Key Table Groups
1. **Identity**: users, user_profiles, user_preferences, roles, permissions
2. **Geography**: divisions, districts, areas (Bangladesh admin hierarchy)
3. **Listings**: listings, listing_features, listing_stats, listing_views, property_images
4. **Property Types**: property_types, property_subtypes
5. **Monetization**: credit_wallets, credit_transactions, credit_packages, subscriptions, subscription_plans, payment_transactions
6. **Services**: service_categories, service_providers, koifind_requests, koifind_candidates
7. **Rental**: rental_applications, rental_agreements, rent_payments
8. **Property Management**: organizations, property_buildings, property_units, tenants, maintenance
9. **Content**: cms_pages, blog_posts, blog_comments, site_settings
10. **System**: activity_logs, audit_logs, verification, favorites, saved_searches

## API Architecture

### Internal AJAX Routes (prefixed /api/)
- `GET /api/search` - Search listings with filters (JSON)
- `GET /api/areas` - List areas (JSON)
- `GET /api/districts/{divisionId}` - Cascading dropdown data
- `GET /api/areas/{districtId}` - Cascading dropdown data
- `POST /api/favorites/{listing}` - Toggle favorite (auth required)
- `POST /api/listings/{listing}/unlock-contact` - Unlock contact (auth required, credits)

### External API Integrations (Planned)
- **Barikoi**: Geocoding, reverse geocoding, map tiles (API key needed)
- **bKash/Nagad**: Mobile payment (credentials needed)
- **Google/Facebook**: Social login (credentials needed)
- **SMTP/SMTP**: Email notifications (configured)

## SEO Architecture
- Dynamic sitemap.xml route
- robots.txt configured
- Open Graph meta tags on all pages
- JSON-LD structured data on listing pages
- Semantic HTML with proper heading hierarchy
- URL slugs for listings, divisions, districts, areas, property types

## File Management
- Laravel public disk storage
- Symlink created (public/storage → storage/app/public)
- Images stored in: storage/app/public/listings/{user_id}/
- Max 6 images per listing (enforced in view, pending controller validation)

## Caching
- File-based cache (default Laravel)
- View cache (php artisan view:cache)
- No Redis/Memcached configured (development mode)

## Queue System
- Database queue driver configured
- No queues active (development mode)
- Laravel jobs table created

## Logging
- Stack channel (single driver)
- Daily log rotation
- Debugbar enabled (development)
