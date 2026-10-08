# ToLetKoi Raw PHP — Legacy Reference Project Analysis

## Project Overview

- **Technology**: Raw PHP 8.2 + jQuery 3.5.1 + Bootstrap 5
- **Database**: MariaDB 10.4 (mysqli + PDO mixed)
- **Template**: Majestic Admin dashboard
- **Location**: `C:\xampp\htdocs\toletkoi_web\toletkoi_web`

## Architecture

```
toletkoi_web/
├── *.php              # Frontend pages (flat, no MVC)
├── admin/             # Admin panel (Majestic template)
├── admin/include/     # Config, DB, helpers, PHPMailer
├── admin/ajax/        # Admin AJAX endpoints
├── app/               # Mobile API layer (separate auth!)
├── include/           # Shared frontend includes
├── assets/            # CSS, JS, fonts, images
├── files/             # User uploads + config.php
├── roomOwner/         # HTML admin template (not PHP)
└── toletkoi_old.sql   # Full database dump (~12,000 lines)
```

## Database Tables (27)

### Core
- `users` — 40+ columns (name, email, phone, NID, address, lat/lng, role flags, balance)
- `users_info` — Extended profile (father name, marital status, emergency contacts)
- `verification` — Email verification tokens (2hr expiry)
- `reset_pass_verify` — Password reset tokens

### Property
- `rentalposts` — 40+ columns (owner, category, full address, cost, 14 facility flags)
- `postimages` — Up to 6 images per listing (WebP)
- `views` — View tracking (user_id/IP + timestamp)
- `likes` — Property likes
- `rental_requests` — Booking requests
- `rent_details` — Active rental agreements
- `utility_bills` — Monthly utility billing
- `reviews` / `ratings` — Property reviews

### Geography
- `divisions` — 8 divisions (with lat/lng)
- `districts` — 64 districts (with lat/lng)
- `upazilas` — 500+ upazilas/thanas (with lat/lng)

### Other
- `chats` — User-to-user messaging
- `user_activity` — Online/typing status
- `shop` / `categories` — E-commerce (NOT FUNCTIONAL)
- `analytics` — Page view tracking
- `sitedata` — Site-wide configuration
- `company_shares` / `shareholders` — Equity (experimental)

## API Architecture

### Mobile API (`app/` directory)
- Action-based routing via `?action=signIn|signUp`
- ⚠️ Uses raw mysqli + MD5 passwords + SQL interpolation (INSECURE)

### Web AJAX (`include/` and root)
- CSRF-protected AJAX endpoints
- PDO prepared statements (newer pages)

## Authentication

- `password_hash()`/`password_verify()` with auto-rehash
- Legacy MD5 migration support
- Session with httponly, samesite=Lax, secure cookies
- CSRF tokens on all forms
- Email verification with 2hr expiry
- Rate limiting on password reset (5 min cooldown)

## Business Logic

### Property Listings
- Categories: family, bachelor, sublet, office, hostel
- Image processing: GD resize to 300×300 WebP
- Geolocation: Barikoi API integration
- Search: Category + area filtering with pagination
- View tracking: user_id/IP + timestamp
- Like/Unlike: AJAX-based

### Rental Flow
```
Renter browses → Booking request → Owner manages → Rent details tracked
```

### Chat System
- AJAX-based messaging
- Load history + send messages + seen status
- Online/typing status tracking

## Security

### ✅ Good
- password_hash()/password_verify()
- CSRF tokens
- PDO prepared statements (newer pages)
- Secure session settings
- .htaccess blocking sensitive files
- Security headers

### ❌ Critical
- **SQL Injection in `app/` layer** — Direct string interpolation
- **MD5 passwords in mobile API** (no salt)
- **Hardcoded Barikoi API key** in source
- **No rate limiting on login attempts**
- **No CORS restrictions** on API endpoints

## Technical Debt

1. No MVC, no routing, no dependency injection
2. Two separate codebases (app/ vs root) with different standards
3. No Composer, no autoloading
4. Mixed mysqli and PDO
5. Global variables everywhere
6. Test files committed (test.php, test2.php, etc.)
7. No database indexing beyond primary keys
8. No caching layer
9. Shop system marked "NOT FUNCTIONAL"
10. No payment integration

## Reusable for Laravel

- **Property categories** — family, bachelor, sublet, office, hostel
- **Location hierarchy** — Division → District → Upazila/Thana
- **Facility flags** — 14 boolean property features
- **Rental booking flow** — Request → Accept/Reject → Agreement
- **Chat system concept** — Real-time messaging pattern
- **Admin settings pattern** — Site-wide configuration
- **Image processing** — WebP conversion concept
- **Rating/Reviews concept** — Property review system

## Discard for Laravel

- Raw PHP (use Laravel MVC)
- mysqli (use Eloquent)
- MD5 passwords (use bcrypt via Laravel)
- Manual session management (use Laravel Auth)
- jQuery AJAX (use Livewire/Inertia or Blade)
- Global variables (use dependency injection)
- Hardcoded config (use .env)
- No CSRF protection (use Laravel CSRF)

## Critical Security Fixes Needed

1. **Eliminate SQL injection** — Use Eloquent/Query Builder
2. **Unify API layers** — Single Laravel API
3. **Replace MD5** — Use bcrypt via Laravel
4. **Remove hardcoded secrets** — Use .env
5. **Add CORS** — Use Laravel CORS middleware
6. **Add rate limiting** — Use Laravel rate limiter
