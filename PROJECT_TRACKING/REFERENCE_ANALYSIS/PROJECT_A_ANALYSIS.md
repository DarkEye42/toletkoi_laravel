# ToLetKoi V3 — Node.js/Next.js Reference Project Analysis

## Project Overview

- **Technology**: Node.js + Express.js 4.18 + Next.js 14 (App Router)
- **Database**: MySQL/MariaDB (mysql2/promise pool, no ORM)
- **Frontend**: Tailwind CSS 3.4 + Recharts + Barikoi Maps
- **Auth**: JWT (7-day expiry) + bcrypt (10 rounds)
- **Location**: `C:\Antigravity_New_Apps\toletkoi_v3`

## Architecture

```
toletkoi_v3/
├── backend/              # Express.js REST API
│   └── src/
│       ├── controllers/  # 14 controllers
│       ├── routes/       # 14 route files
│       ├── middleware/    # auth.js, adminAuth.js, validator.js
│       ├── services/     # jwt.service.js
│       └── utils/        # cache, logger, sanitize, slug
├── web-admin/            # Next.js 14 (triple duty: public + admin + user)
├── scripts/              # SQL schemas, seeds, migrations
└── bd_geolocation_db/    # Barikoi geolocation SQL dump
```

## Database Tables (18+)

### Core
- `users` — All accounts (role, referral_code, is_verified, deleted_at)
- `listings` — Property listings (10 categories, slug, bilingual, amenities JSON)
- `divisions` — 8 Bangladesh divisions
- `districts` — 64 districts
- `areas` — 360+ areas with is_popular flag

### Interactions
- `favorites` — User→Listing favorites
- `saved_properties` — Saved with notes
- `inquiries` — Tenant→Owner messages
- `property_views` — View tracking
- `property_images` — Separate images with ordering

### Monetization
- `user_credits` — Credit balance per user
- `credit_transactions` — Full ledger (6 transaction types)
- `payment_transactions` — 7 payment methods (bkash, nagad, rocket, card, bank, SSL Commerz, order_home)
- `order_home_requests` — Personalized search (100 BDT)
- `referrals` — Referral tracking with credit rewards

### Content
- `articles` — Blog/CMS with bilingual support
- `article_comments` — Nested comments
- `ai_logs`, `automation_logs`, `job_feed_tasks`

## API Patterns

- RESTful with consistent JSON responses: `{ success, data, error }`
- Pagination: `?limit=50&offset=0`
- Filtering via query params (15+ parameters)
- Slug-based URLs for SEO
- Soft deletes via `deleted_at`
- In-memory caching with `X-Cache` header
- Rate limiting: 10/min (auth), 100/min (API)

## Role System (5 roles)

| Role | Access |
|---|---|
| Admin | Full admin panel |
| Owner | Create/edit own listings |
| Tenant | Browse, save, inquire |
| Agent | (Limited implementation) |
| Field_Executive | (Limited implementation) |

## Business Logic

### Property Listings
- 10 categories: Family, Bachelor, Office, Sublet, Hostel, Shop, Factory Place, Ready Shed, Open Land
- 2 listing types: Rent, Sale
- 3 price types: Monthly, Weekly, Daily
- Bilingual support (EN/BN)
- Cloudinary image storage (auto-resize to 1200x900)
- SEO slugs auto-generated

### Credits & Payments
- Credit economy: purchase/earn/spend/refund/bonus/admin_adjustment
- Order Home: 100 BDT personalized search
- Contact reveal consumes credits

### Location
- Division → District → Area (3-level hierarchy)
- Barikoi API integration (geocode, reverse geocode, autocomplete, nearby, route)
- Property counts per location for SEO

## Security

✅ bcrypt passwords, JWT with HTTP-only cookies, Helmet.js, CORS whitelist, rate limiting, Joi validation, parameterized SQL, soft deletes

⚠️ Issues: No CSRF beyond SameSite, no per-user rate limiting, JWT secret in .env

## Technical Debt

1. No ORM — Raw SQL queries everywhere
2. No TypeScript
3. No tests (placeholder only)
4. No migration tool (manual SQL files)
5. JSON columns for images (duplicated)
6. No foreign key cascades
7. Incomplete payment gateway integration
8. No WebSocket/real-time features
9. In-memory cache lost on restart
10. No state management on frontend

## Reusable for Laravel

- **Location hierarchy** (Division → District → Area) — Well-designed
- **Credit system design** — Comprehensive ledger model
- **SEO slug generation** — Good pattern
- **Property categories** — 10 categories with bilingual support
- **API response format** — Consistent success/data/error pattern
- **Rate limiting patterns** — Auth vs API differentiation
- **Admin dashboard structure** — Good module organization
- **User role switching** — Owner ↔ Tenant toggle

## Discard for Laravel

- Raw SQL queries (use Eloquent)
- JavaScript (use PHP/Laravel)
- Manual SQL files (use migrations)
- In-memory caching (use Redis)
- Cloudinary (use Laravel Storage)
- JWT auth (use Laravel Sanctum)
- Express middleware (use Laravel middleware)
