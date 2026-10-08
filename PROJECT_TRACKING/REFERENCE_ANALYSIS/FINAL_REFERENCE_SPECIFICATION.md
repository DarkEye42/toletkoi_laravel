# ToLetKoi — Final Reference Specification

## Executive Summary

ToLetKoi is a Bangladesh-focused rental marketplace and property management platform. This specification synthesizes findings from two reference projects (Node.js/Next.js and Raw PHP) into a production-ready Laravel implementation.

## Technology Stack

### Backend
- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL/MariaDB (via XAMPP)
- **Cache**: Redis
- **Queue**: Laravel Database Queue (upgrade to Redis in production)
- **Auth**: Laravel Sanctum
- **Mail**: Laravel Mail (SMTP)

### Frontend
- **Template Engine**: Blade
- **CSS**: Tailwind CSS 3.4
- **JS**: Alpine.js + Livewire
- **Icons**: Heroicons
- **Fonts**: Inter + Noto Sans Bengali

### External Services
- **Maps**: Barikoi API (geocoding, reverse geocoding, autocomplete)
- **Payment**: SSL Commerz + bKash + Nagad
- **Storage**: Laravel Local Storage (S3 in production)
- **Email**: SMTP (production: Mailgun/SES)

### Mobile (Future - Phase 08)
- **Framework**: Flutter + Dart
- **API**: Laravel Sanctum tokens

## Database Schema

### Identity & Auth
- `users` — All accounts (name, email, phone, avatar, status)
- `roles` — Role definitions (Super Admin, Admin, Landlord, Renter, Tenant, etc.)
- `user_roles` — User-Role pivot (primary_role, is_active)
- `role_change_logs` — Audit trail for role switches
- `user_profiles` — Extended profile (NID, address, bio, preferences)
- `user_preferences` — Notification and display settings

### Geography
- `divisions` — 8 Bangladesh divisions
- `districts` — 64 districts
- `areas` — 360+ areas/neighborhoods

### Marketplace
- `property_types` — Property categories (10 types)
- `property_subtypes` — Subcategories within types
- `listings` — Property listings (bilingual, slug, amenities)
- `property_images` — Property images with ordering
- `listing_features` — Boolean feature flags (14 flags)
- `listing_stats` — Denormalized stats (views, contacts, favorites)
- `favorites` — User-Listing favorites
- `saved_searches` — Saved search criteria
- `listing_views` — View tracking

### Monetization
- `credit_wallets` — Credit balance per user
- `credit_transactions` — Full ledger (immutable audit trail)
- `credit_packages` — Predefined credit bundles
- `subscription_plans` — Subscription tier definitions
- `subscriptions` — Active user subscriptions
- `subscription_transactions` — Subscription payment history
- `payment_transactions` — Unified payment records

### Verification
- `verification_types` — Type definitions (email, phone, NID, photo, property)
- `verifications` — Verification requests
- `verification_documents` — Document uploads

### Leads & Contact
- `leads` — Property inquiries
- `contact_unlocks` — Contact reveal records
- `lead_events` — Lead lifecycle events

### KoiFind
- `koifind_requests` — Assisted search requests
- `koifind_candidates` — Agent-found properties
- `koifind_shortlists` — Curated shortlists
- `koifind_events` — Request lifecycle events

### Rental
- `rental_applications` — Tenant applications
- `rental_agreements` — Formal rental agreements
- `agreement_participants` — Agreement parties
- `rent_payments` — Rent collection tracking
- `rent_payment_items` — Individual payment line items

### Property Management
- `organizations` — Property management companies
- `property_buildings` — Buildings within organizations
- `property_units` — Units within buildings
- `unit_occupancies` — Tenant-unit assignments
- `tenants` — Tenant records
- `tenant_notes` — Tenant notes
- `maintenance_requests` — Maintenance requests
- `maintenance_assignments` — Staff assignments
- `property_expenses` — Expense tracking
- `organization_staff` — Staff members

### Services
- `service_categories` — Service types
- `service_providers` — Provider profiles

### Content & CMS
- `cms_pages` — Static pages (About, Contact, FAQ)
- `blog_posts` — Blog articles (EN/BN)
- `blog_comments` — Nested comments
- `site_settings` — Key-value config store

### Promotions
- `promotion_products` — Promotion types (Boost, Featured, Premium)
- `promoted_listings` — Active promotions
- `promotion_orders` — Promotion purchases

### Notifications
- `notifications` — Multi-channel notifications
- `notification_templates` — Reusable templates

### System
- `activity_logs` — User activity tracking
- `audit_logs` — Security audit trail
- `cache` — Laravel cache table
- `jobs` — Laravel queue jobs
- `sessions` — Laravel session storage

## API Design

### Base URL
```
/api/v1/
```

### Authentication
```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/logout
GET  /api/v1/auth/user
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
POST /api/v1/auth/email/verify
POST /api/v1/auth/phone/send-otp
POST /api/v1/auth/phone/verify
```

### Users
```
GET    /api/v1/users/{id}
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}
GET    /api/v1/users/{id}/profile
PUT    /api/v1/users/{id}/profile
POST   /api/v1/users/{id}/avatar
POST   /api/v1/users/switch-role
GET    /api/v1/users/{id}/role-history
```

### Listings
```
GET    /api/v1/listings
POST   /api/v1/listings
GET    /api/v1/listings/{slug}
PUT    /api/v1/listings/{id}
DELETE /api/v1/listings/{id}
GET    /api/v1/listings/{id}/images
POST   /api/v1/listings/{id}/images
DELETE /api/v1/listings/{id}/images/{imageId}
POST   /api/v1/listings/{id}/favorite
DELETE /api/v1/listings/{id}/favorite
GET    /api/v1/listings/{id}/similar
```

### Search
```
GET /api/v1/search
GET /api/v1/search/advanced
GET /api/v1/search/nearby
GET /api/v1/search/saved
POST /api/v1/search/saved
DELETE /api/v1/search/saved/{id}
```

### Locations
```
GET /api/v1/locations/divisions
GET /api/v1/locations/divisions/{slug}
GET /api/v1/locations/divisions/{id}/districts
GET /api/v1/locations/districts/{slug}
GET /api/v1/locations/districts/{id}/areas
GET /api/v1/locations/areas/{slug}
```

### Maps (Barikoi)
```
GET /api/v1/maps/geocode
GET /api/v1/maps/reverse-geocode
GET /api/v1/maps/autocomplete
GET /api/v1/maps/nearby
GET /api/v1/maps/route
```

### Credits
```
GET  /api/v1/credits/balance
GET  /api/v1/credits/transactions
POST /api/v1/credits/purchase
GET  /api/v1/credits/packages
```

### Subscriptions
```
GET  /api/v1/subscriptions/plans
POST /api/v1/subscriptions/subscribe
GET  /api/v1/subscriptions/current
POST /api/v1/subscriptions/cancel
GET  /api/v1/subscriptions/transactions
```

### Payments
```
POST /api/v1/payments/initiate
POST /api/v1/payments/callback/{gateway}
GET  /api/v1/payments/{id}/status
GET  /api/v1/payments/transactions
```

### Leads
```
GET  /api/v1/leads
POST /api/v1/leads
GET  /api/v1/leads/{id}
PUT  /api/v1/leads/{id}
POST /api/v1/leads/{id}/unlock
```

### KoiFind
```
POST /api/v1/koifind/request
GET  /api/v1/koifind/requests
GET  /api/v1/koifind/{id}
PUT  /api/v1/koifind/{id}/status
GET  /api/v1/koifind/{id}/candidates
```

### Admin
```
GET    /api/v1/admin/dashboard
GET    /api/v1/admin/users
PUT    /api/v1/admin/users/{id}
GET    /api/v1/admin/listings
PUT    /api/v1/admin/listings/{id}
GET    /api/v1/admin/verifications
PUT    /api/v1/admin/verifications/{id}
GET    /api/v1/admin/credits
POST   /api/v1/admin/credits/adjust
GET    /api/v1/admin/subscriptions
GET    /api/v1/admin/analytics
GET    /api/v1/admin/audit-logs
```

### Content
```
GET  /api/v1/content/pages/{slug}
GET  /api/v1/content/blog
GET  /api/v1/content/blog/{slug}
GET  /api/v1/content/faqs
```

### Notifications
```
GET  /api/v1/notifications
PUT  /api/v1/notifications/{id}/read
PUT  /api/v1/notifications/read-all
GET  /api/v1/notifications/settings
PUT  /api/v1/notifications/settings
```

## Role System

### Fixed Roles (Cannot Switch)
- Super Admin
- Admin
- Moderator
- Finance
- Support
- Verification Staff
- Property Manager
- Service Provider
- Agent/Scout
- Corporate/Organization
- Staff

### Switchable Roles (Can Switch)
- Landlord
- Renter
- Tenant

### Role Switch Rules
1. Only Landlord ↔ Renter ↔ Tenant can switch
2. Server-side authorization required
3. Every role change logged in `role_change_logs`
4. Primary role determines default dashboard
5. API checks primary role for authorization

## Business Rules

### Property Listings
- Max 10 images per listing
- Auto-generate SEO slugs
- Bilingual support (EN/BN)
- Status workflow: Pending → Active → Inactive/Sold/Rented
- Featured listings require subscription or credit purchase
- View tracking (user_id/IP + timestamp)

### Credit System
- Purchase credits via payment gateway
- Earn credits via referrals (50 per referral)
- Spend credits on contact unlock, boosting
- Full ledger with immutable transactions
- Balance must match transaction history

### Payment System
- SSL Commerz for card/bank
- bKash for mobile payment
- Nagad for mobile payment
- Idempotent callbacks (no double credit)
- Server-side verification required
- Status transitions validated

### Subscription System
- Free tier (1 listing, basic features)
- Basic tier (5 listings, more features)
- Premium tier (unlimited, all features)
- Auto-renewal reminders
- Grace period on expiry
- Downgrade on cancellation

### Verification System
- Email verification (token-based, 24hr expiry)
- Phone verification (OTP, 5min expiry)
- NID verification (document upload, staff review)
- Photo verification (selfie, staff review)
- Property verification (physical visit)

### KoiFind
- Tenant submits search request
- Agent assigned from queue
- Agent finds candidates
- Shortlist presented to tenant
- Fee charged on completion (100 BDT)

### Property Management SaaS
- Organization owns buildings
- Buildings contain units
- Units assigned to tenants
- Rent collected per unit
- Maintenance requests tracked
- Expenses tracked

## Frontend Architecture

### Public Website
- Homepage (hero, featured, popular, stats)
- Property listings (grid, filters, map)
- Property detail (gallery, info, map, contact)
- Location pages (SEO landing)
- Blog/articles
- Static pages (About, Contact, FAQ)

### Admin Dashboard
- Overview (stats, charts)
- User management
- Listing moderation
- Verification review
- Credits/Finance
- Subscriptions
- Promotions
- Content/CMS
- Analytics
- Support
- Settings

### User Dashboard
- Overview (stats, recent activity)
- My listings (CRUD)
- Saved properties
- My inquiries
- Credits (balance, history)
- Subscriptions
- Profile
- Settings

## Security Requirements

### Authentication
- bcrypt passwords (12 rounds)
- Laravel Sanctum tokens
- Session with httponly, secure, samesite cookies
- CSRF protection on all forms
- Email verification required
- Phone verification optional
- 2FA-ready design

### Authorization
- Role-based access control
- Policy-based authorization
- Middleware for route protection
- Server-side validation
- Input sanitization
- SQL injection prevention (Eloquent)

### Rate Limiting
- Auth routes: 10/min
- API routes: 100/min
- Upload routes: 20/min
- Payment routes: 10/min

### Data Protection
- Encrypt sensitive data at rest
- HTTPS in production
- No secrets in code
- Environment-based configuration
- Audit logging for sensitive operations
- GDPR-ready data handling

## Deployment

### Local Development
- XAMPP (Apache + MySQL)
- Laravel Dev Server
- Vite for asset compilation

### Production
- Laravel Forge or similar
- MySQL 8.0+
- Redis
- Nginx or Apache
- SSL certificate
- CDN for assets
- Queue workers
- Scheduler

### Monitoring
- Laravel Telescope (debugging)
- Laravel Horizon (queues)
- Error tracking (Sentry/Bugsnag)
- Performance monitoring
- Uptime monitoring
