# ToLetKoi Feature Comparison — Project A vs Project B

| Feature | Project A (Node.js) | Project B (PHP) | Laravel Decision |
|---|---|---|---|
| **Auth System** | JWT + bcrypt | password_hash + sessions | Laravel Sanctum + bcrypt |
| **Role System** | 5 roles (string-based) | Boolean flags (is_owner, adminPower) | Proper RBAC with roles table |
| **Role Switching** | Owner ↔ Tenant | None | Landlord ↔ Renter ↔ Tenant |
| **Property CRUD** | ✅ Full | ✅ Full | ✅ Full with validation |
| **Property Search** | 15+ filters | Category + area only | ✅ Advanced search with radius |
| **Property Categories** | 10 categories | 5 categories | 10+ categories |
| **Bilingual Content** | ✅ EN/BN fields | ❌ English only | ✅ EN/BN support |
| **SEO URLs** | ✅ Slug-based | ❌ PHP file-based | ✅ Slug-based |
| **Image Upload** | Cloudinary | GD (WebP) | Laravel Storage + Intervention |
| **Image Count** | 10 max | 6 max | 10 max |
| **Geolocation** | ✅ Barikoi | ✅ Barikoi | ✅ Barikoi |
| **Location Hierarchy** | Division → District → Area | Division → District → Upazila | Division → District → Area |
| **Credits System** | ✅ Full ledger | ❌ Balance only | ✅ Full ledger |
| **Payment Methods** | 7 methods | ❌ None | SSL Commerz + bKash + Nagad |
| **Subscriptions** | ❌ Not implemented | ❌ Not implemented | ✅ Full subscription system |
| **Favorites** | ✅ + Saved properties | ✅ Likes | ✅ Favorites + Saved searches |
| **Inquiries/Leads** | ✅ Full | ✅ Booking requests | ✅ Full lead system |
| **Chat/Messaging** | ❌ Not implemented | ✅ Basic AJAX chat | ✅ Real-time messaging |
| **KoiFind** | ❌ Not implemented | ❌ Not implemented | ✅ Assisted search |
| **Verification** | ❌ Basic | ❌ Email only | ✅ Multi-type verification |
| **Referrals** | ✅ Credit rewards | ❌ Not implemented | ✅ Credit rewards |
| **Admin Dashboard** | ✅ Full | ✅ Template-based | ✅ Custom admin |
| **User Dashboard** | ✅ Full | ✅ Basic | ✅ Full with role switching |
| **Blog/CMS** | ✅ Articles + comments | ❌ Not implemented | ✅ Full CMS |
| **Analytics** | ❌ Basic | ✅ Page views | ✅ Comprehensive analytics |
| **Notifications** | ❌ Not implemented | ❌ Not implemented | ✅ Multi-channel |
| **Testing** | ❌ None | ❌ None | ✅ PHPUnit + Feature tests |
| **API Versioning** | ❌ None | ❌ None | ✅ /api/v1/ |
| **Caching** | In-memory | ❌ None | ✅ Redis |
| **Queue/Jobs** | ❌ None | ❌ None | ✅ Laravel Queues |
| **WebSockets** | ❌ None | ❌ None | ✅ Laravel Reverb/Pusher |
| **Email** | ❌ None | ✅ PHPMailer | ✅ Laravel Mail |
| **File Management** | Cloudinary | GD + local | Laravel Storage (local/S3) |
| **Soft Deletes** | ✅ Some tables | ❌ None | ✅ Where needed |
| **Audit Logs** | ❌ None | ❌ None | ✅ Comprehensive |
| **RBAC Permissions** | ❌ Role only | ❌ Role only | ✅ Spatie Permission or custom |
| **2FA** | ❌ None | ❌ None | ✅ Ready |
| **CSRF Protection** | SameSite cookies | ✅ Token-based | ✅ Laravel CSRF |
| **Rate Limiting** | ✅ Basic | ❌ None | ✅ Laravel rate limiter |
| **Input Validation** | Joi | Custom helpers | ✅ Laravel Validation |
| **Error Handling** | Inconsistent | Mixed | ✅ Laravel exception handler |
| **Logging** | Pino | error_log | ✅ Laravel Log |
| **Database Migrations** | ❌ Manual SQL | ❌ Manual SQL | ✅ Laravel Migrations |
| **ORM** | ❌ Raw SQL | ❌ Raw SQL | ✅ Eloquent ORM |
| **Package Manager** | npm | None | ✅ Composer + npm |
| **Build Tools** | Next.js | None | ✅ Vite |
| **CSS Framework** | Tailwind 3.4 | Bootstrap 5 | ✅ Tailwind CSS |
| **JS Framework** | React (Next.js) | jQuery | ✅ Livewire + Alpine.js |
| **Mobile App** | ❌ None | ❌ None | ✅ Flutter (Phase 08) |

## Summary

### From Project A (Keep)
- 10 property categories
- Bilingual content support
- SEO-friendly slug URLs
- Credit system with full ledger
- Location hierarchy (Division → District → Area)
- Barikoi integration patterns
- Admin dashboard module structure
- API response format (success/data/error)
- Rate limiting differentiation (auth vs API)

### From Project B (Keep)
- 14 facility/amenity flags
- Rental booking workflow (request → accept/reject)
- Chat/messaging concept
- Extended user profile structure
- Property view tracking
- Like/favorite system
- Site-wide configuration pattern
- Image WebP conversion concept
- Rating/review system concept

### New for Laravel
- Laravel Sanctum for API auth
- Proper RBAC with roles/permissions tables
- Role switching with audit logs
- Subscription system
- Payment gateway integration (SSL Commerz)
- Real-time messaging (WebSockets)
- KoiFind assisted search
- Multi-type verification
- Comprehensive analytics
- Multi-channel notifications
- Audit logging
- 2FA-ready design
- API versioning (/api/v1/)
- Redis caching
- Queue system for background jobs
- PHPUnit testing
- Laravel Telescope for debugging
