# ToLetKoi Database Analysis — From Both Reference Projects

## Project A (Node.js) — 18+ Tables

### Schema Quality: ⭐⭐⭐ (Good)
- Consistent naming conventions
- Proper foreign keys (some)
- Timestamps on most tables
- Soft deletes on users, listings, articles

### Key Patterns
- `id` (auto-increment) as primary key
- `created_at` / `updated_at` timestamps
- `deleted_at` for soft deletes
- `status` enum fields (active, inactive, pending)
- `is_featured` boolean flags
- `lat` / `lon` decimal fields for geolocation
- `slug` for SEO-friendly URLs
- JSON columns for flexible data (images, amenities)

### Strengths
- Separate `property_images` table (normalized)
- `credit_transactions` ledger (immutable audit trail)
- `user_credits` wallet (denormalized balance)
- `divisions` → `districts` → `areas` hierarchy
- `saved_properties` with notes
- `inquiries` with status workflow

### Weaknesses
- No foreign key cascades
- `images_json` duplicated in listings AND property_images
- No proper indexes beyond PKs
- No migration system (manual SQL)

## Project B (PHP) — 27 Tables

### Schema Quality: ⭐⭐ (Fair)
- Flat structure
- Mixed naming conventions
- No foreign keys
- Some redundant tables

### Key Patterns
- `id` (auto-increment) as primary key
- `created_at` / `updated_at` on some tables
- No soft deletes (hard deletes)
- Boolean role flags (`is_owner`, `adminPower`)
- `lat` / `lng` decimal fields
- 14 boolean facility flags on `rentalposts`
- WebP image references

### Strengths
- Comprehensive `users` table (40+ columns)
- `users_info` extended profile (separation of concerns)
- `divisions` → `districts` → `upazilas` hierarchy
- `rental_requests` → `rent_details` workflow
- `chats` with message history
- `utility_bills` for property management

### Weaknesses
- No foreign keys at all
- No indexes beyond PKs
- Mixed data types
- Redundant data across tables
- No migration system
- SQL dump in repo (not migrations)

## Laravel Database Design — Recommended

### Principles
1. Use Laravel migrations (version-controlled schema)
2. Use Eloquent models with relationships
3. Proper foreign key constraints with cascades
4. Indexes on frequently queried columns
5. Soft deletes where data retention matters
6. Timestamps on all tables
7. JSON columns only for truly flexible data
8. Separate pivot tables for many-to-many

### Table Naming Conventions
- Plural snake_case for tables (e.g., `property_listings`)
- Singular snake_case for columns (e.g., `user_id`)
- Foreign keys: `{related_table_singular}_id` (e.g., `user_id`)
- Pivot tables: `{table1}_{table2}` (e.g., `listing_amenities`)
- Status columns: enum with clear values
- Boolean columns: `is_` prefix (e.g., `is_featured`)

### Index Strategy
- Primary key on `id`
- Foreign key indexes on all `_id` columns
- Composite indexes for common filter combinations
- Fulltext indexes for search columns
- Unique indexes for unique constraints (slug, email, etc.)

### Migration Order (dependency-safe)
1. divisions
2. districts (FK → divisions)
3. areas (FK → districts)
4. users
5. roles
6. user_roles (pivot)
7. user_profiles (FK → users)
8. role_change_logs (FK → users, roles)
9. property_types
10. property_subtypes (FK → property_types)
11. listings (FK → users, areas, property_types)
12. property_images (FK → listings)
13. listing_features (FK → listings)
14. listing_amenities (FK → listings)
15. listing_views (FK → listings, users)
16. favorites (FK → users, listings)
17. saved_searches (FK → users)
18. credit_wallets (FK → users)
19. credit_transactions (FK → credit_wallets, users)
20. credit_packages
21. subscription_plans
22. subscriptions (FK → users, subscription_plans)
23. subscription_transactions (FK → subscriptions)
24. payment_transactions
25. verification_types
26. verifications (FK → users, verification_types)
27. verification_documents (FK → verifications)
28. leads (FK → listings, users)
29. contact_unlocks (FK → leads, users)
30. koifind_requests (FK → users)
31. koifind_candidates (FK → koifind_requests, listings)
32. koifind_shortlists (FK → koifind_requests, users)
33. rental_applications (FK → listings, users)
34. rental_agreements (FK → listings, users)
35. rent_payments (FK → rental_agreements)
36. organizations
37. property_buildings (FK → organizations)
38. property_units (FK → property_buildings)
39. service_categories
40. service_providers (FK → users, service_categories)
41. notifications
42. audit_logs
43. cms_pages
44. blog_posts
45. advertisements
46. promoted_listings (FK → listings, promotion_products)
