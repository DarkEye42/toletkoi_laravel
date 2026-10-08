# ToLetKoi Migration Mapping — Legacy to Laravel

## Database Migration Mapping

### Identity & Auth
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `users` (Project A) | `users` | Add phone, nid, avatar, bio, lat/lng, status, verified_at |
| `users_info` (Project B) | `user_profiles` | Normalize into separate table |
| `is_owner` / `adminPower` (Project B) | `roles` + `user_roles` | Replace boolean flags with proper RBAC |
| `verification` (Project B) | `email_verifications` | Laravel default + custom types |
| `reset_pass_verify` (Project B) | `password_resets` | Laravel default |

### New Laravel Tables (Not in Legacy)
| Table | Purpose |
|---|---|
| `roles` | Role definitions (Super Admin, Admin, Landlord, Renter, Tenant, etc.) |
| `user_roles` | User-Role pivot (primary_role, is_active) |
| `role_change_logs` | Audit trail for role switches |
| `user_preferences` | User settings (notifications, language, etc.) |

### Geography
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `divisions` (both) | `divisions` | Add slug, is_active, property_count |
| `districts` (both) | `districts` | Add slug, is_active, property_count |
| `areas` (Project A) / `upazilas` (Project B) | `areas` | Unified with slug, is_popular, property_count |

### Property/Marketplace
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `rentalposts` / `listings` | `listings` | Normalized with proper FKs, bilingual, slug |
| `postimages` / `property_images` | `property_images` | Add ordering, primary flag |
| `views` | `listing_views` | Renamed for clarity |
| `likes` / `favorites` | `favorites` | Unified, unique constraint |
| `saved_properties` | `saved_searches` | Search-based, not property-based |
| `inquiries` / `rental_requests` | `leads` + `contact_unlocks` | Separated lead from contact unlock |
| `rental_requests` | `rental_applications` | Formal application flow |
| `rent_details` | `rental_agreements` | Formal agreement tracking |

### New Laravel Tables (Property)
| Table | Purpose |
|---|---|
| `property_types` | Property categories (Family, Bachelor, etc.) |
| `property_subtypes` | Subcategories within types |
| `listing_features` | Boolean feature flags (parking, lift, etc.) |
| `listing_amenities` | Amenity tags |
| `listing_stats` | Denormalized stats (views, contacts, favorites) |

### Monetization
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `user_credits` | `credit_wallets` | Renamed, add total_earned/spent/purchased |
| (none) | `credit_transactions` | Full ledger (from Project A) |
| (none) | `credit_packages` | Predefined credit bundles |
| (none) | `subscription_plans` | Subscription tier definitions |
| (none) | `subscriptions` | Active user subscriptions |
| (none) | `subscription_transactions` | Subscription payment history |
| `payment_transactions` (Project A) | `payment_transactions` | Unified payment records |

### Verification
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `verification` (Project B) | `verifications` | Multi-type (email, phone, NID, photo, property) |
| (none) | `verification_types` | Type definitions |
| (none) | `verification_documents` | Document uploads |

### Services & Property Management
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `shop` / `categories` (Project B) | `service_categories` + `service_providers` | Service marketplace |
| (none) | `organizations` | Property management companies |
| (none) | `property_buildings` | Buildings within organizations |
| (none) | `property_units` | Units within buildings |
| (none) | `unit_occupancies` | Tenant-unit assignments |
| (none) | `rent_payments` | Rent collection tracking |
| `utility_bills` (Project B) | `property_expenses` | Expense tracking |

### Content & CMS
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `articles` (Project A) | `blog_posts` | EN/BN, SEO metadata |
| `article_comments` (Project A) | `blog_comments` | Nested comments |
| (none) | `cms_pages` | Static pages (About, Contact, FAQ) |
| `sitedata` (Project B) | `site_settings` | Key-value config store |

### Notifications & Communication
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `chats` (Project B) | `conversations` + `messages` | Real-time messaging |
| (none) | `notifications` | Multi-channel notifications |
| (none) | `notification_templates` | Reusable notification templates |

### System
| Legacy Table | Laravel Table | Changes |
|---|---|---|
| `analytics` (Project B) | `activity_logs` | Comprehensive activity tracking |
| (none) | `audit_logs` | Security audit trail |
| (none) | `cache` | Laravel cache table |
| (none) | `jobs` | Laravel queue jobs |
| (none) | `sessions` | Laravel session storage |

## Data Migration Strategy

### Phase 1: Schema Only
- Create all Laravel migrations
- Run migrations to create empty tables
- No data migration yet

### Phase 2: Reference Data
- Seed divisions, districts, areas from Barikoi data
- Seed property types and subtypes
- Seed credit packages
- Seed subscription plans
- Seed roles and permissions
- Seed admin user

### Phase 3: Selective Data Migration
- Migrate active users (with password hashing)
- Migrate active listings (with slug generation)
- Migrate property images (copy files)
- Migrate credit balances (with transaction records)
- Migrate verification status

### Phase 4: Verification
- Compare record counts
- Verify data integrity
- Test all relationships
- Validate foreign keys
- Check indexes
