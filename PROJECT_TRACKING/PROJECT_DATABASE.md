# ToLetKoi - Database

**Last Updated:** 2026-09-15

## Demo Seed Verification [VERIFIED]
- Seeder: `database/seeders/ToLetKoiDemoSeeder.php`, registered by `DatabaseSeeder.php`.
- No migration was added or altered. All 11 existing migrations reported `Ran`.
- Stable counts after two complete `php artisan db:seed` runs: users 25, listings 30, divisions 8, districts 63, areas 40, property types 10, property images 60, listing features 30, listing stats 30, favorites 10, saved searches 10, leads 10, listing views 10, wallets 19, credit transactions 45, CMS pages 2, blog posts 5, organizations 2, tenants 2, service providers 2.
- Credit invariant verified: `balance = total_earned + total_purchased - total_spent` for demo wallets.
- Demo users use password `demo-password` with `demo-*@toletkoi.test` addresses; development-only credentials.

## Database: MySQL (toletkoi_db)

## Migration Files (11)

| File | Purpose |
|------|---------|
| 0001_01_01_000000_create_users_table | Users, password_reset_tokens, sessions |
| 0001_01_01_000001_create_cache_table | Cache, cache_locks |
| 0001_01_01_000002_create_jobs_table | Jobs, job_batches, failed_jobs |
| 2026_09_14_222952_create_permission_tables | Spatie roles, permissions, model_has_roles, model_has_permissions |
| 2026_09_15_000001_create_geography_tables | Divisions, districts, areas |
| 2026_09_15_000002_create_identity_tables | User profiles, preferences, role change logs, user roles |
| 2026_09_15_000003_customize_roles_table | Extends Spatie roles with extra columns |
| 2026_09_15_000004_create_marketplace_tables | Listings, property types, images, features, stats, views, favorites, saved searches |
| 2026_09_15_000005_create_monetization_tables | Credit wallets, transactions, packages, subscriptions, plans, payments, leads |
| 2026_09_15_000006_create_services_rental_tables | Service providers, KoiFind, rental apps/agreements/payments, property management |
| 2026_09_15_000007_create_system_content_tables | CMS pages, blog, site settings, activity/audit logs, verification, contact unlocks |

## Table Groups

### 1. Identity (5 tables)
- **users** - id, name, email, password, phone, role, email_verified_at, remember_token, timestamps
- **user_profiles** - user_id, avatar, nid_front/back, bio, company_name, license_number, social links
- **user_preferences** - user_id, email_notifications, sms_notifications, language, currency, search_radius
- **role_change_logs** - user_id, old_role, new_role, changed_by, reason
- **user_roles** - (Spatie pivot) user_id, role_id

### 2. Geography (3 tables)
- **divisions** - id, name, name_bn, slug, latitude, longitude, sort_order
- **districts** - id, division_id, name, name_bn, slug, latitude, longitude, sort_order
- **areas** - id, district_id, name, name_bn, slug, latitude, longitude, sort_order

### 3. Listings & Property (8 tables)
- **property_types** - id, name, name_bn, slug, icon, description, sort_order
- **property_subtypes** - id, property_type_id, name, name_bn, slug, sort_order
- **listings** - id, user_id, property_type_id, subtype_id, division_id, district_id, area_id, title, slug, description, listing_type, price, price_type, bedrooms, bathrooms, balconies, floor_level, total_floors, area_sqft, furnishing_status, address, lat, lng, contact_name, contact_phone, contact_email, available_from, is_immediately_available, status, is_featured, featured_until, is_promoted, promoted_until, views_count, expires_at, seo_title, seo_description, timestamps, soft_deletes
- **property_images** - id, listing_id, path, alt_text, sort_order, is_primary, file_size, timestamps
- **listing_features** - id, listing_id, has_parking, has_lift, has_generator, has_cctv, has_security, has_swimming_pool, has_gym, has_rooftop, has_balcony, has_garden, has_internet, has_gas, has_water_supply, pet_friendly
- **listing_stats** - id, listing_id, views_today, views_this_week, views_this_month, favorites_count, inquiries_count
- **listing_views** - id, listing_id, user_id, ip_address, user_agent, timestamps
- **favorites** - id, user_id, listing_id, timestamps (unique: user_id + listing_id)

### 4. Search (1 table)
- **saved_searches** - id, user_id, name, filters (JSON), timestamps

### 5. Monetization (6 tables)
- **credit_wallets** - id, user_id, balance, total_earned, total_spent, timestamps
- **credit_transactions** - id, wallet_id, user_id, type, amount, balance_after, description, reference_type, reference_id, timestamps
- **credit_packages** - id, name, credits, bonus_credits, price, is_active, sort_order, timestamps
- **subscriptions** - id, user_id, plan_id, starts_at, ends_at, status, cancelled_at, timestamps
- **subscription_plans** - id, name, price, duration_days, features (JSON), max_listings, is_active, sort_order, timestamps
- **payment_transactions** - id, user_id, subscription_id, amount, currency, payment_method, gateway, gateway_tx_id, status, metadata (JSON), timestamps

### 6. Leads (1 table)
- **leads** - id, listing_id, user_id, name, email, phone, message, source, status, timestamps

### 7. Services (3 tables)
- **service_categories** - id, name, name_bn, slug, icon, description, is_active, sort_order
- **service_providers** - id, user_id, category_id, business_name, description, license_number, is_verified, is_active, timestamps
- **contact_unlocks** - id, user_id, listing_id, credits_spent, timestamps (unique: user_id + listing_id)

### 8. KoiFind (3 tables)
- **koifind_requests** - id, user_id, title, property_type_id, listing_type, budget_min, budget_max, bedrooms, preferred_areas, description, status, timestamps
- **koifind_candidates** - id, request_id, service_provider_id, notes, status, timestamps
- **koifind_shortlists** - id, request_id, candidate_id, user_id, timestamps

### 9. Rental (3 tables)
- **rental_applications** - id, listing_id, user_id, move_in_date, lease_duration, monthly_income, employer, references (JSON), status, timestamps
- **rental_agreements** - id, listing_id, landlord_id, tenant_id, start_date, end_date, monthly_rent, security_deposit, terms, status, timestamps
- **rent_payments** - id, agreement_id, amount, due_date, paid_date, payment_method, transaction_id, status, timestamps

### 10. Property Management (7 tables)
- **organizations** - id, user_id, name, slug, description, logo, address, phone, email, website, is_active, timestamps
- **property_buildings** - id, organization_id, name, address, division_id, district_id, area_id, lat, lng, total_units, description, timestamps
- **property_units** - id, building_id, unit_number, floor, bedrooms, bathrooms, area_sqft, rent_amount, status, description, timestamps
- **unit_occupancies** - id, unit_id, tenant_id, start_date, end_date, rent_amount, status, timestamps
- **tenants** - id, user_id, organization_id, nid_number, emergency_contact, move_in_date, timestamps
- **tenant_notes** - id, tenant_id, user_id, note, timestamps
- **maintenance_requests** - id, unit_id, tenant_id, title, description, priority, status, timestamps
- **maintenance_assignments** - id, request_id, assigned_to, notes, status, timestamps
- **property_expenses** - id, building_id, category, amount, description, date, receipt_path, timestamps

### 11. Content & System (6 tables)
- **cms_pages** - id, title, slug, content, content_bn, meta_title, meta_description, is_active, timestamps
- **blog_posts** - id, user_id, title, title_bn, slug, content, content_bn, excerpt, featured_image, meta_title, meta_description, status (`draft`, `published`, `archived`), published_at, view_count, soft deletes, timestamps
- **blog_comments** - id, post_id, user_id, body, is_approved, timestamps
- **site_settings** - id, key, value, type, timestamps (unique: key)
- **activity_logs** - id, user_id, action, description, properties (JSON), ip_address, timestamps
- **audit_logs** - id, user_id, auditable_type, auditable_id, event, old_values, new_values, ip_address, timestamps

### CMS Schema Verification (2026-09-15)
- Migration: `2026_09_15_000007_create_system_content_tables` is applied.
- Live `cms_pages` columns: `id`, `title`, `slug`, `content`, `content_bn`, `meta_title`, `meta_description`, `is_active`, `created_at`, `updated_at`.
- Live `cms_pages` row count after demo seed: 2; live `blog_posts` row count: 5.
- No migration was added or run because the existing schema is correct; the controller was using the wrong table and columns for blog content.

### 12. Verification (3 tables)
- **verification_types** - id, name, description, is_required, timestamps
- **verifications** - id, user_id, verification_type_id, status, reviewed_by, reviewed_at, notes, timestamps
- **verification_documents** - id, verification_id, document_type, file_path, file_size, mime_type, timestamps

### 13. Auth System (3 tables, Laravel default)
- **password_reset_tokens** - email, token, created_at
- **sessions** - id, user_id, ip_address, user_agent, payload, last_activity
- **cache** - key, value, expiration
- **cache_locks** - key, owner, expiration

### 14. Jobs System (3 tables, Laravel default)
- **jobs** - queue, payload, attempts, reserved_at, available_at, created_at
- **job_batches** - id, name, total_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at
- **failed_jobs** - id, uuid, connection, queue, payload, exception, failed_at

## Seeders (8)

| Seeder | Records | Data |
|--------|---------|------|
| RoleSeeder | 6 | super_admin, admin, landlord, tenant, agent, property_manager |
| PropertyTypeSeeder | 8+ | Apartment, House, Office, Shop, Warehouse, Land, Room, Sublet |
| GeographySeeder | 8+ | All 8 divisions of Bangladesh |
| CreditPackageSeeder | 4+ | Starter, Basic, Professional, Premium credit packs |
| SubscriptionPlanSeeder | 3+ | Free, Basic, Premium subscription plans |
| AdminUserSeeder | 4 | admin, landlord, tenant, agent demo users (password: password) |
| SiteSettingSeeder | 10+ | Site name, contact info, SEO defaults, social links |
| DatabaseSeeder | Calls all | Master seeder that runs all above |
| ToLetKoiDemoSeeder | 30 listings plus related records | Idempotent synthetic users, marketplace, activity, credits, CMS/blog, services, organizations, tenants, and subscription data |
