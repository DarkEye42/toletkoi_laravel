# ToLetKoi - Project Decisions

**Last Updated:** 2026-09-15

---

## DEC-001: Laravel as Primary Framework

- **Date:** 2026-09-14
- **Decision:** Use Laravel 12 as the primary backend and web frontend framework.
- **Context:** Need a mature PHP framework with authentication, ORM, queues, testing, and API support for a property marketplace.
- **Options Considered:** Laravel, Symfony, CodeIgniter, Raw PHP
- **Selected Solution:** Laravel 12
- **Reason:** Mature ecosystem, excellent ORM, authentication, authorization, queues, testing, API support, and suitability for the Bangladesh-focused ToLetKoi platform.
- **Status:** Active

---

## DEC-002: MySQL/MariaDB Database via XAMPP

- **Date:** 2026-09-14
- **Decision:** Use MySQL/MariaDB as the application database, with XAMPP providing the local development database environment.
- **Context:** Need a reliable relational database with JSON support, indexing, relational integrity, and strong Laravel compatibility.
- **Options Considered:** MySQL/MariaDB, PostgreSQL, SQLite
- **Selected Solution:** MySQL/MariaDB
- **Reason:** Strong Laravel support, common Bangladesh hosting availability, compatibility with the existing projects, and convenient local development through XAMPP.
- **Local Development:** XAMPP MySQL/MariaDB
- **Database Administration:** phpMyAdmin
- **Status:** Active

The actual database engine and version used by the development
environment must be verified rather than assumed.

---

## DEC-003: Spatie Permission for RBAC

- **Date:** 2026-09-14
- **Decision:** Use `spatie/laravel-permission` v6.25.0 for role-based access control.
- **Context:** ToLetKoi requires multiple consumer, specialized, staff, and administrative roles.
- **Options Considered:** Spatie Permission, Bouncer, Sentinel, Custom RBAC
- **Selected Solution:** Spatie Permission v6.25.0
- **Reason:** Mature Laravel RBAC ecosystem with role/permission management and strong maintainability.
- **Status:** Active

### Switchable Roles

Only the following roles may be freely switched by eligible users:

- Landlord
- Renter
- Tenant

### Fixed/Specialized Roles

The following roles are not freely switchable:

- Property Manager
- Service Provider
- Agent / Scout
- Corporate / Organization
- Staff
- Admin
- Super Admin
- Other future privileged/specialized roles

Role switching must always be:

- server-side authorized
- permission-aware
- auditable
- session/token aware
- protected against privilege escalation

Public users must never be able to promote themselves to
administrative or privileged roles.

---

## DEC-004: Blade + Alpine.js + Tailwind CSS Frontend

- **Date:** 2026-09-14
- **Decision:** Use Laravel Blade with Alpine.js and Tailwind CSS for the web frontend.
- **Context:** Need modern interactive interfaces while maintaining server-side rendering and SEO-friendly public property pages.
- **Options Considered:** Blade+jQuery, Blade+Alpine.js, Vue SPA, React SPA, Livewire
- **Selected Solution:** Blade + Alpine.js + Tailwind CSS
- **Reason:** Provides server-rendered pages, strong SEO characteristics, lightweight interactivity, reusable components, and a modern styling system.
- **Status:** Active

### UI/UX Source of Truth

The existing Node.js + Next.js project is the **PRIMARY UI/UX
REFERENCE** for the new Laravel ToLetKoi web platform.

**Primary UI/UX Reference:**

`C:\Antigravity_New_Apps\toletkoi_v3`

The existing Raw PHP project is a **SECONDARY FUNCTIONAL AND
LEGACY REFERENCE**.

**Secondary Reference:**

`C:\xampp\htdocs\toletkoi_web\toletkoi_web`

The new Laravel application must NOT blindly merge or copy the
implementation of either project.

The Laravel application is a new implementation that recreates
and improves the approved ToLetKoi product experience.

### UI/UX Priority

The UI/UX implementation priority is:

1. Current ToLetKoi product requirements
2. Next.js project's UI/UX
3. Next.js project's design system
4. Accessibility and responsive-design improvements
5. Performance/usability improvements
6. Functional insights from the Raw PHP project

The Raw PHP project's visual design must not override the
Next.js visual direction unless explicitly approved.

---

## DEC-005: Next.js Project as Primary UI/UX Design Blueprint

- **Date:** 2026-09-15
- **Decision:** Use the Node.js + Next.js ToLetKoi project as the primary visual design and user-experience blueprint for the new Laravel web platform.
- **Reference Path:** `C:\Antigravity_New_Apps\toletkoi_v3`
- **Context:** The Next.js project contains the preferred ToLetKoi visual language, page structures, components, interactions, and responsive experience.
- **Selected Solution:** Recreate the Next.js UI/UX using Laravel-native technologies.
- **Reason:** Preserve the established ToLetKoi product experience while replacing the underlying technology stack with Laravel.
- **Status:** Active

### Public Website UX

The Laravel platform should reproduce and evolve the following
Next.js-inspired public website experience:

#### Homepage

- Hero section
- Property search interface
- Location search
- Property type selection
- Price filtering
- Featured/popular listings
- Category-wise listings
- Family
- Bachelor
- Sublet
- Office
- Hostel Seat
- Other approved property categories
- Popular areas
- Property counts
- Platform statistics
- Testimonials
- Blog/articles
- Footer navigation

#### Property Listing/Search

- Property grid
- Property image
- Property title
- Rent/price
- Location
- Verification badges
- Relevant property metadata
- Filter interface
- Property type
- Category
- Price range
- Bedrooms
- Area
- Additional approved filters
- Sort controls
- Newest
- Price
- Popularity
- Pagination
- Map view

Map/location functionality must integrate with the approved
Barikoi/location architecture.

#### Property Details

- Image gallery
- Image carousel/lightbox
- Property title
- Price
- Location
- Amenities
- Property specifications
- Verification information
- Map/location
- Owner information
- Credit-based contact unlocking
- Similar properties
- Contact/inquiry functionality

Private contact information must never be exposed to the
browser before server-side authorization and successful unlock.

#### Location Pages

Support SEO-friendly location landing pages including:

- Division
- District
- Area
- Property counts
- Popular areas
- Relevant listings
- SEO metadata
- Structured data where appropriate

---

## DEC-006: Next.js Design System Adaptation

- **Date:** 2026-09-15
- **Decision:** Extract and adapt the Next.js project's design system for Laravel rather than creating an unrelated visual system.
- **Status:** Active

### Project A — Next.js Design Characteristics

The current analysis identifies:

#### Colors

- Primary blue
- Danger red
- Muted gray
- Light backgrounds

Exact production color tokens must be extracted from the actual
Next.js implementation before finalizing the Laravel design tokens.

#### Typography

- Inter / equivalent existing Next.js typography

The Laravel implementation should preserve the visual typography
of Project A where practical.

#### Icons

Project A uses:

- Google Material Icons
- outlined icons
- filled icons

The Laravel implementation should preserve these icons where
they are important to visual consistency.

Heroicons or Lucide may be considered only where they provide a
clear implementation or accessibility benefit without causing
unnecessary visual divergence.

#### Cards

Project A uses:

- rounded cards
- material-style shadows
- structured content hierarchy
- image-driven property cards

The Laravel property-card implementation should preserve this
visual language.

#### Responsive Design

Project A uses a:

- mobile-first approach
- Tailwind-style responsive grid
- responsive component behavior

The Laravel frontend must maintain:

- mobile usability
- tablet usability
- desktop usability
- responsive navigation
- responsive property grids
- responsive filters
- responsive dashboards

#### Loading States

Preserve the existing Project A approach where appropriate:

- skeleton loaders
- shimmer/loading states
- loading feedback

Also implement consistent:

- empty states
- error states
- retry states
- disabled states

---

## DEC-007: Laravel Design System

- **Date:** 2026-09-15
- **Decision:** Implement a reusable Laravel design system based primarily on Project A's Next.js visual language.
- **Status:** Active

### Framework

- Tailwind CSS
- Blade components
- Alpine.js
- Livewire only where later justified by interaction requirements

### Typography

Preferred:

- Inter for primary Latin UI
- Noto Sans Bengali for Bangla UI/content where required

Poppins may be used only when justified by the extracted design
system or a specific product area.

### Semantic Colors

The application should support semantic tokens for:

- Primary
- Secondary
- Accent
- Danger
- Success
- Warning
- Muted
- Background
- Surface
- Border
- Text

The initial proposed semantic palette may use:

- Primary: `#2563EB`
- Secondary: `#0D9488`
- Accent: `#F59E0B`
- Danger: `#DC2626`
- Success: `#16A34A`
- Muted: `#6B7280`

However, these values are NOT automatically authoritative.

The actual Next.js design tokens must be inspected first.

### Components

Build reusable Blade components for:

- Property cards
- Search bars
- Location autocomplete
- Filter panels
- Gallery/carousel
- Badges
- Stats cards
- Data tables
- Modals
- Toasts
- Alerts
- Forms
- Inputs
- Selects
- Checkboxes
- Radios
- Textareas
- Buttons
- Pagination
- Breadcrumbs
- Navigation
- Skeleton loaders
- Empty states
- Error states

---

## DEC-008: Admin Dashboard UX

- **Date:** 2026-09-15
- **Decision:** Maintain a separate Admin Dashboard with a professional dashboard UX informed by the reference projects while remaining consistent with the primary ToLetKoi design language.
- **Status:** Active

### Layout

- Collapsible sidebar
- Top navigation
- Search
- Notifications
- User profile
- Breadcrumbs
- Main content area

### Modules

- Dashboard overview
- Statistics
- Charts
- User management
- Listing moderation
- Verification
- Credits/finance
- Subscriptions
- Promotions
- CMS
- Analytics
- Support
- Abuse reports
- Audit history

Admin UI must remain visually consistent with the overall
ToLetKoi design system while clearly communicating privileged
administrative functionality.

---

## DEC-009: User Dashboard UX

- **Date:** 2026-09-15
- **Decision:** Use a dedicated user dashboard with role-aware navigation and the same core visual language as the primary Next.js UI.
- **Status:** Active

### Layout

- Responsive sidebar
- Avatar/profile section
- Welcome section
- Role-aware navigation
- Main content area

### Modules

- My Listings
- Saved Properties
- My Inquiries
- Credits
- Credit history
- Profile
- Preferences
- Notifications
- Relevant role-specific functionality

Only Landlord, Renter, and Tenant users may receive the normal
role-switching interface.

---

## DEC-010: Leaflet.js for Interactive Maps + Barikoi for Location Services

- **Date:** 2026-09-14
- **Decision:** Use Leaflet.js as the interactive map-rendering layer and Barikoi as the primary location/geocoding service.
- **Context:** Need property maps, geocoding, reverse geocoding, location search, GPS search, and Bangladesh-specific location data.
- **Options Considered:** Google Maps, Leaflet + OSM, Mapbox, Barikoi JS SDK
- **Selected Solution:** Leaflet.js + configurable map tiles + Barikoi location services
- **Reason:** Leaflet provides a flexible map-rendering layer while Barikoi is preferred for Bangladesh-focused location services.
- **Status:** Active

### Separation of Responsibilities

**Leaflet:**
- map rendering
- markers
- map interactions
- viewport
- clustering where required

**Barikoi:**
- geocoding
- reverse geocoding
- location search
- administrative hierarchy
- location normalization

Do not tightly couple core business logic to Leaflet.

Do not use Google Maps unless explicitly approved.

---

## DEC-011: File-Based Cache/Session for Development

- **Date:** 2026-09-14
- **Decision:** Use file-based cache and session during local development.
- **Context:** Keep XAMPP development simple and avoid unnecessary external services.
- **Options Considered:** File, Database, Redis, Memcached
- **Selected Solution:** File-based
- **Reason:** Zero/minimal local configuration.
- **Production:** Redis should be evaluated and configured where appropriate.
- **Status:** Active

---

## DEC-012: Credit-Based Monetization

- **Date:** 2026-09-14
- **Decision:** Implement credit-based monetization combined with subscription tiers.
- **Context:** Need flexible monetization for contact unlocking and premium features.
- **Options Considered:** Credit-only, subscription-only, per-listing, freemium, credit + subscription
- **Selected Solution:** Credit-based + subscription tiers
- **Reason:** Supports pay-per-use and recurring monetization and is suitable for Bangladesh payment methods.
- **Status:** Active

### Credit Rules

Every credit movement must create an auditable ledger entry.

Examples:

- registration bonus
- credit purchase
- contact unlock
- promotion
- premium service
- refund
- administrative adjustment
- expiry where applicable

Frontend values must never be trusted for:

- balance
- price
- credit cost
- eligibility
- transaction status

---

## DEC-013: Division → District → Area Geography

- **Date:** 2026-09-14
- **Decision:** Use Division → District → Area as the primary user-facing ToLetKoi geography hierarchy.
- **Context:** Need a simple location hierarchy for property search and SEO.
- **Options Considered:** Division→District→Thana→Area, Division→District→Area
- **Selected Solution:** Division → District → Area
- **Reason:** Simple user experience and suitable for marketplace search and SEO.
- **Status:** Active

Additional location metadata may be stored when useful, including:

- Thana/Upazila
- Union
- Ward
- Postal code
- Locality
- Neighborhood
- Road
- Landmark
- Latitude
- Longitude

These additional fields must not unnecessarily complicate the
primary user-facing hierarchy.

---

## DEC-014: XAMPP as Local Development Environment

- **Date:** 2026-09-15
- **Decision:** Use XAMPP as the primary local development environment.
- **Status:** Active

### Local Stack

- XAMPP
- Apache
- PHP
- MySQL/MariaDB
- phpMyAdmin
- Composer
- Node.js/npm where required for frontend asset tooling

Laravel Apache configuration should point to:

`<Laravel Project>/public`

The Laravel project root must not be exposed directly as the
Apache document root.

Database configuration must use Laravel `.env`.

Secrets must never be stored in Git or project documentation.

---

## DEC-015: Barikoi API Proxy Pattern

- **Date:** 2026-09-15
- **Decision:** Proxy all Barikoi API calls server-side through `BarikoiService` rather than calling the Barikoi API directly from the browser.
- **Context:** Need to protect the API key and add caching to reduce API calls.
- **Options Considered:** Direct browser → Barikoi, Server-side proxy, Hybrid
- **Selected Solution:** Server-side proxy via `app/Services/BarikoiService.php`
- **Reason:** API key never exposed to client, server-side caching (5-10 min TTL via `Cache::remember()`), single point of configuration, easy to swap providers.
- **Status:** Active

### Proxy Routes

All routes defined under `/api/maps/` prefix with `throttle:120,1`:

| Route | Method | Purpose |
|-------|--------|---------|
| `/api/maps/config` | GET | Tile URL + API key for Leaflet |
| `/api/maps/autocomplete?q=` | GET | Location search (5min cache) |
| `/api/maps/geocode?address=` | GET | Address → coordinates (10min cache) |
| `/api/maps/reverse-geocode?lat=&lon=` | GET | Coordinates → address (10min cache) |

### Key Implementation

- Service: `app/Services/BarikoiService.php`
- Config: `config/services.php` → `barikoi.key`
- Env: `BARIKOI_API_KEY`
- Tile: `https://tile.barikoi.com/styles/osm-liberty/{z}/{x}/{y}.png?key={apiKey}`
- Fallback: Standard OSM tiles if Barikoi config unavailable

---

## DEC-016: UI Design Language (Implemented)

- **Date:** 2026-09-15
- **Decision:** Adopt the finalized ToLetKoi UI design language based on thetolet.com and thetoletkoi.com reference projects.
- **Status:** Active (implemented 2026-09-15)

### Design Tokens

| Token | Value | Usage |
|-------|-------|-------|
| Primary | `#00a1ff` | Buttons, links, icons, accents |
| Danger | `#ef4444` | Delete, error states |
| Success | `#10b981` | Verified badges, success |
| Background | `gray-50` | Page background |
| Surface | `white` | Cards, modals |
| Border | `gray-100` | Card borders, dividers |
| Text primary | `gray-900` | Headings |
| Text secondary | `gray-600` | Body text |
| Text muted | `gray-400` | Labels, hints |

### Component Library

| Component | Style | Location |
|-----------|-------|----------|
| Nav | Single-row, Barikoi search modal, category bar | `components/layouts/nav.blade.php` |
| Footer | Dark `bg-gray-900`, 4-column grid | `components/layouts/footer.blade.php` |
| Listing Card | Full clickable `<a>`, image top, badge overlay | `components/listings/card.blade.php` |
| Homepage | Hero gradient, property grid, featured, areas, blog | `home.blade.php` |
| Listing Detail | Gallery, Barikoi map, features, contact sidebar | `listings/show.blade.php` |
| Search | Filter sidebar, sort, map view link | `search/index.blade.php` |
| Map Search | Full-screen Barikoi map, Alpine.js reactive | `search/map.blade.php` |

### Design Decisions

- `rounded-2xl` (1rem) for all cards and major containers
- Material Icons throughout (filled + outlined)
- Mobile-first responsive design
- Hover effects: `shadow-lg` + `border-primary/20` transition
- Price display: amount + period suffix (e.g., "৳15,000/mo")
- Listing badges: RENT/SALE/FEATURED overlay on images
- Map markers: Custom blue circle with `rotate(-45deg)` + home icon

---

## DEC-015: Existing Projects Are Read-Only Reference Implementations

- **Date:** 2026-09-15
- **Decision:** Existing ToLetKoi projects are reference implementations and must remain read-only during discovery and analysis.
- **Status:** Active

### Project A — Primary UI/UX Reference

`C:\Antigravity_New_Apps\toletkoi_v3`

Technology:

- Node.js
- Next.js
- MySQL

Primary purpose:

- UI/UX
- visual design
- design system
- responsive behavior
- components
- frontend workflows
- product experience

### Project B — Secondary Functional/Legacy Reference

`C:\xampp\htdocs\toletkoi_web\toletkoi_web`

Technology:

- Raw PHP
- HTML
- CSS
- JavaScript
- MySQL

Primary purpose:

- legacy business logic
- historical workflows
- existing functionality
- database concepts
- admin functionality
- validation
- migration reference
- technical debt discovery
- security analysis

Neither project should be blindly merged into the Laravel
application.

---

## DEC-016: Laravel as the Single Source of Truth

- **Date:** 2026-09-15
- **Decision:** Laravel will be the authoritative application and API layer.
- **Status:** Active

Architecture:

```text
                    ToLetKoi.com
                         |
              +----------+----------+
              |                     |
       Laravel Web             Laravel API v1
              |                     |
        Blade + Alpine               |
        + Tailwind                   |
              |                     |
              +----------+----------+
                         |
                    MySQL/MariaDB
                         |
                  Future Flutter App
