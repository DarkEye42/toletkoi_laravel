TASK: Build a production-quality Laravel development/demo seed dataset for ToLetKoi.

OBJECTIVE

Populate the current Laravel ToLetKoi development database with realistic synthetic data based on:

1. Existing ToLetKoi Project A:
   C:\Antigravity_New_Apps\toletkoi_v3

2. Existing ToLetKoi Project B:
   C:\xampp\htdocs\toletkoi_web\toletkoi_web

3. Publicly observable product/category/location/rent patterns from:
   https://www.thetolet.com/

IMPORTANT:
The two existing projects are READ-ONLY references.
Do not modify them.

Do not blindly import their SQL dumps.

The NEW Laravel database schema is authoritative.

--------------------------------------------------
PHASE 1 — AUDIT CURRENT LARAVEL DATABASE
--------------------------------------------------

Before writing any seed code:

1. Inspect all current migrations.
2. Inspect all current Eloquent models.
3. Inspect current database/seeders.
4. Inspect DatabaseSeeder.php.
5. Inspect current database schema.
6. Inspect current foreign keys.
7. Inspect enums and status values.
8. Inspect nullable/non-nullable columns.
9. Inspect unique constraints.
10. Inspect relationships.

Pay special attention to:

- users
- roles
- user_roles
- divisions
- districts
- areas
- property_types
- property_subtypes
- listings
- property_images
- favorites
- saved_searches
- inquiries/leads
- CMS/blog tables
- credit wallets
- credit transactions
- payments
- subscriptions
- promotions
- notifications
- verification
- any other currently implemented tables

Do NOT assume that the old ToLetKoi database schema matches the Laravel schema.

--------------------------------------------------
PHASE 2 — AUDIT REFERENCE DATABASES
--------------------------------------------------

Read the available SQL/data from both existing ToLetKoi projects.

Extract only compatible concepts and useful mock-data patterns.

Reference Project A:
C:\Antigravity_New_Apps\toletkoi_v3

Reference Project B:
C:\xampp\htdocs\toletkoi_web\toletkoi_web

DO NOT:

- copy secrets
- copy real passwords
- copy production credentials
- copy private personal information
- blindly copy IDs
- blindly copy foreign keys
- overwrite current Laravel data
- modify either reference project

Convert the reference data into NEW synthetic Laravel-compatible records.

--------------------------------------------------
PHASE 3 — PUBLIC MARKETPLACE RESEARCH
--------------------------------------------------

Review:

https://www.thetolet.com/

Use it only as inspiration for realistic marketplace patterns.

Observe:

- listing categories
- property types
- property subtypes
- locations
- rent ranges
- bedroom/bathroom combinations
- family listings
- bachelor listings
- sublet listings
- office listings
- apartment listings
- room listings
- Bangladesh location patterns

Do NOT copy copyrighted descriptions or exact listing content.

Create original synthetic titles/descriptions.

Examples of realistic patterns:

Family Flat:
- Mirpur 11
- Uttara
- Badda
- Rampura
- Dhanmondi
- Mohammadpur
- Jatrabari

Bachelor:
- Farmgate
- Badda
- Mirpur
- Uttara
- Dhanmondi

Sublet:
- Badda
- Uttara
- Mirpur
- Rampura

Office:
- Banani
- Uttara
- Badda
- Motijheel
- Tejgaon

Regional:
- Rajshahi
- Khulna
- Sylhet
- Chattogram
- Rangpur
- Barishal

--------------------------------------------------
PHASE 4 — CREATE DEMO USERS
--------------------------------------------------

Create synthetic users suitable for development.

Minimum:

1 Super Admin
2 Admin
5 Landlords
5 Renters
5 Tenants
2 Property Managers if their current schema supports them
2 Service Providers if their current schema supports them
2 Agents/Scouts if their current schema supports them

CRITICAL:

Follow the current ToLetKoi role architecture.

Only:
- Landlord
- Renter
- Tenant

are switchable.

Do NOT make privileged roles switchable.

Never create fake public Admin/Super Admin registration flow.

Use Laravel Hash::make().

Do not hardcode production credentials.

For development only, document the seed login credentials safely.

--------------------------------------------------
PHASE 5 — GEOGRAPHY
--------------------------------------------------

Use existing geography records whenever possible.

Do not duplicate:

- divisions
- districts
- areas

unless the current schema requires missing demo records.

Prefer locations already present in the database.

Ensure foreign keys are valid.

Prioritize:

Dhaka
Gazipur
Narayanganj
Chattogram
Rajshahi
Khulna
Sylhet
Rangpur
Barishal
Mymensingh

For Dhaka prioritize:

Mirpur
Uttara
Badda
Rampura
Dhanmondi
Mohammadpur
Jatrabari
Farmgate
Banani
Motijheel
Tejgaon
Khilkhet
Vatara
Lalbag
Khilgaon

--------------------------------------------------
PHASE 6 — PROPERTY TYPES
--------------------------------------------------

Use existing property types.

If required and missing, create:

- Flat
- Apartment
- Room
- Seat
- Floor
- Unit
- Office
- Shop
- Hostel

Only create records if the current schema supports them.

Do not duplicate existing unique records.

--------------------------------------------------
PHASE 7 — LISTINGS
--------------------------------------------------

Create at least 30 realistic synthetic listings.

Recommended distribution:

10 Family
5 Bachelor
5 Sublet
4 Office
3 Hostel/Seat
3 Apartment/Unit

Use realistic BDT prices.

Examples:

Family Flat:
৳8,000–৳35,000

Premium Family:
৳35,000–৳70,000

Bachelor Seat:
৳1,500–৳6,000

Bachelor Room:
৳5,000–৳12,000

Sublet:
৳5,000–৳15,000

Office:
৳20,000–৳80,000+

Use realistic:

- bedrooms
- bathrooms
- area_sqft
- floor
- total floors
- furnishing
- parking
- lift
- generator
- gas
- electricity
- water
- features
- available_from
- latitude
- longitude
- address
- slug

Use the current Laravel status values exactly.

Do NOT assume:
Active
active
Published
published

are interchangeable.

Read the actual current schema/model/enum first.

--------------------------------------------------
PHASE 8 — LISTING IMAGES
--------------------------------------------------

If property_images exists:

Create 2–4 image records per listing.

Use safe public placeholder/demo image URLs only if the current implementation expects URLs.

Prefer existing image conventions from the reference project.

Do not download or redistribute copyrighted website images from THE TOLET.

Mark one image as primary.

--------------------------------------------------
PHASE 9 — USER ACTIVITY
--------------------------------------------------

If implemented, create realistic:

- favorites
- saved searches
- listing views
- inquiries
- leads
- comparison records

Use valid foreign keys.

Do not create impossible relationships.

Example:

Tenant -> favorite listing
Tenant -> inquiry to landlord listing
Tenant -> saved search
Listing -> views

--------------------------------------------------
PHASE 10 — CREDITS
--------------------------------------------------

If the current credit system exists:

Create realistic development transactions.

Examples:

Welcome bonus:
+50 credits

Contact unlock:
-5 credits

Purchase:
+50 / +150 / +400 credits

Every credit balance must reconcile with the transaction ledger.

Never directly manufacture an inconsistent balance.

--------------------------------------------------
PHASE 11 — CMS / BLOG
--------------------------------------------------

IMPORTANT:

Inspect the CURRENT Laravel CMS schema first.

The current homepage is querying:

cms_pages.status = 'published'

and:

type IN ('article', 'blog_post')

Do not assume the old `articles` table can be inserted into `cms_pages`.

Create 3–6 synthetic published articles/blog posts compatible with the ACTUAL current Laravel schema.

Examples:

- How to Find the Right Rental Home in Dhaka
- Rental Safety Checklist for Tenants
- How to Verify a Rental Property Before Paying
- Family Flat vs Bachelor Seat: Which Is Right for You?
- How to Choose a Good Location in Dhaka
- Things to Check Before Moving Into a New Flat

Create Bangla versions where supported.

Ensure the values match the current status/type columns exactly.

This must also fix/verify the current homepage CMS query failure.

--------------------------------------------------
PHASE 12 — DASHBOARD DATA
--------------------------------------------------

Where current tables support it, create enough data for dashboards to look realistic:

Admin:
- users
- listings
- pending listings
- published listings
- inquiries
- credits
- payments

Landlord:
- multiple listings
- inquiries
- views
- favorites

Tenant/Renter:
- favorites
- saved searches
- inquiries
- credit balance

--------------------------------------------------
PHASE 13 — SAFE SEEDING
--------------------------------------------------

The seeder MUST be idempotent.

Running:

php artisan db:seed

multiple times must NOT continually duplicate the same demo records.

Prefer:

updateOrCreate()
firstOrCreate()
or deterministic demo identifiers

where appropriate.

Do NOT use destructive:

TRUNCATE
DROP TABLE
delete all users
delete all listings

The existing development database may contain real development work.

--------------------------------------------------
PHASE 14 — IMPLEMENTATION
--------------------------------------------------

Create:

database/seeders/ToLetKoiDemoSeeder.php

If necessary, create smaller seeders:

ToLetKoiDemoUserSeeder
ToLetKoiDemoListingSeeder
ToLetKoiDemoCmsSeeder
ToLetKoiDemoActivitySeeder

Register them through DatabaseSeeder.php.

Keep seeders maintainable.

Use model factories where appropriate.

Use transactions where appropriate.

--------------------------------------------------
PHASE 15 — VALIDATION
--------------------------------------------------

Run:

php artisan migrate:status
php artisan db:seed
php artisan optimize:clear

Then verify:

php artisan tinker

Check:

User::count()
Listing::count()
Division::count()
District::count()
Area::count()

and all relevant CMS/activity tables.

Verify:

GET /

GET /search

listing detail page

category filtering

location filtering

homepage latest articles/blog section

dashboard pages

No SQL exceptions.

--------------------------------------------------
PHASE 16 — HOMEPAGE ERROR
--------------------------------------------------

Specifically verify the previously reported error:

SQLSTATE[42S22]:
Unknown column 'status' in 'where clause'

Query:

select * from `cms_pages`
where `status` = 'published'
and `type` in ('article', 'blog_post')
order by `created_at` desc
limit 3

Determine whether the root cause is:

1. missing migration
2. wrong column
3. wrong status value
4. wrong CMS table
5. incomplete CMS implementation

Fix the root cause according to the actual Laravel architecture.

Do not simply hide the exception.

--------------------------------------------------
PHASE 17 — TESTING
--------------------------------------------------

Run:

php artisan test

and relevant feature tests.

At minimum verify:

- seeding
- homepage
- listings
- search
- listing detail
- CMS/blog
- authentication
- authorization
- dashboard
- database relationships

--------------------------------------------------
PHASE 18 — PROJECT TRACKING
--------------------------------------------------

Update:

PROJECT_TRACKING/PROJECT_STATUS.md
PROJECT_TRACKING/PROJECT_DATABASE.md
PROJECT_TRACKING/PROJECT_CHANGELOG.md
PROJECT_TRACKING/PROJECT_ISSUES.md
PROJECT_TRACKING/PROJECT_TESTS.md
PROJECT_TRACKING/PROJECT_NEXT_TASK.md

Record:

- seeders created
- number of demo users
- number of listings
- CMS records
- geography records used
- activity records
- root cause of cms_pages error
- migration changes, if any
- tests executed
- final verification

Use:

[VERIFIED]
[PARTIAL]
[PLANNED]
[BROKEN]
[BLOCKED]
[UNKNOWN]

Never claim successful seeding unless it was actually executed and verified.

--------------------------------------------------
FINAL REPORT
--------------------------------------------------

Report:

1. Seeders created
2. Tables populated
3. Number of records created
4. Reference data used
5. THE TOLET research patterns used
6. CMS error root cause
7. Database changes
8. Tests run
9. GET / verification
10. Any remaining issues
11. Exact next task

Do not modify either reference project.
Do not import production/private credentials.
Do not scrape or copy copyrighted listing content.
Do not use fake success messages.
