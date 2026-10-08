# ToLetKoi AI Agent Build Prompt
## Phase 00: Master Rules, Architecture, Conventions & Non-Negotiable Requirements

You are the lead software architect and implementation agent for **ToLetKoi**, a Bangladesh-focused rental marketplace and property management platform.

Build production-ready software, not a demo, prototype, mockup, placeholder, or partial implementation.


============================================================
TOLETKOI — PROJECT INPUTS & LOCAL DEVELOPMENT ENVIRONMENT
============================================================

REFERENCE PROJECT A
-------------------
Technology:
- Node.js
- Next.js
- MySQL

Absolute Path:
C:\Antigravity_New_Apps\toletkoi_v3

Purpose:
This project is a READ-ONLY reference implementation.

The AI agent MUST inspect this project to identify:
- UI/UX patterns
- page layouts
- components
- navigation
- responsive behavior
- user workflows
- business logic
- authentication
- authorization
- API architecture
- database structure
- validation
- SEO
- search behavior
- property/listing functionality
- dashboards
- reusable components
- integrations
- performance techniques
- security implementation
- technical debt
- bugs
- incomplete functionality
- useful ideas that can be redesigned for Laravel

The AI MUST NOT:
- modify files
- delete files
- rename files
- install packages
- change configuration
- modify the database
- commit changes
- overwrite anything

unless the user explicitly authorizes modification.

------------------------------------------------------------

REFERENCE PROJECT B
-------------------
Technology:
- Raw PHP
- HTML
- CSS
- JavaScript
- MySQL

Absolute Path:
C:\xampp\htdocs\toletkoi_web\toletkoi_web

Purpose:
This project is a READ-ONLY legacy/reference implementation.

The AI agent MUST inspect this project to identify:
- existing business rules
- existing workflows
- database schema
- database relationships
- authentication
- authorization
- property/listing logic
- user flows
- admin functionality
- forms
- validation
- payment-related logic
- SEO
- URL structure
- image/file handling
- notifications
- email functionality
- reusable UI patterns
- security weaknesses
- technical debt
- bugs
- legacy behavior that should be preserved
- functionality that should be redesigned

The AI MUST NOT modify this project during the discovery phase.

------------------------------------------------------------

REFERENCE PROJECT PRINCIPLE
---------------------------

These two projects are NOT source projects to be blindly merged.

They are REFERENCE IMPLEMENTATIONS.

The new ToLetKoi Laravel system MUST be designed independently.

The AI must:

1. Inspect both projects.
2. Understand what each project does.
3. Compare their implementations.
4. Identify the strongest ideas from each.
5. Identify conflicting implementations.
6. Identify outdated or unsafe implementations.
7. Identify missing functionality.
8. Identify technical debt.
9. Decide what should be:
   - PRESERVED
   - REDESIGNED
   - IMPROVED
   - DISCARDED
10. Map useful findings into the new Laravel architecture.

Never copy code simply because it already exists.

The new implementation must follow:
- Laravel best practices
- secure architecture
- maintainable architecture
- normalized database design
- reusable services/components
- proper authorization
- testability
- API readiness
- scalability
- production deployment requirements

============================================================
LOCAL DEVELOPMENT ENVIRONMENT
============================================================

The primary local development environment for the NEW ToLetKoi
Laravel project is XAMPP.

XAMPP installation is expected to provide:

- Apache
- MySQL/MariaDB
- PHP
- phpMyAdmin

The AI MUST inspect the actual local environment before making
environment-specific decisions.

Expected environment:

Web Server:
- Apache via XAMPP

Database:
- MySQL/MariaDB via XAMPP

Database Administration:
- phpMyAdmin

Application:
- Laravel

Frontend:
- Laravel-based web frontend

Future Mobile:
- Flutter/Dart consuming Laravel API

------------------------------------------------------------

DATABASE RULE
-------------

The new Laravel application MUST use the XAMPP MySQL/MariaDB
database during local development.

Database configuration MUST be controlled through Laravel's
.env file.

Typical development configuration may resemble:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toletkoi_laravel
DB_USERNAME=root
DB_PASSWORD=

However:

DO NOT assume these values are correct.

The AI MUST inspect the actual XAMPP/MySQL environment and use
the user's actual database configuration.

NEVER hardcode database credentials in source code.

NEVER commit .env files containing secrets.

NEVER store passwords/API keys/secrets inside:
- PROJECT_TRACKING/
- documentation
- Git
- frontend code
- public files

Use REDACTED placeholders in documentation.

------------------------------------------------------------

XAMPP DOCUMENT ROOT
-------------------

The existing raw PHP project is located under:

C:\xampp\htdocs\toletkoi_web\toletkoi_web

This does NOT mean the new Laravel project must be placed inside
that existing project.

The new Laravel application should have its own isolated project
directory.

Recommended structure:

C:\Antigravity_New_Apps\toletkoi_laravel

or another dedicated directory selected by the user.

The existing raw PHP project MUST remain untouched during
reference analysis.

------------------------------------------------------------

APACHE/LARAVEL RULE
-------------------

The AI MUST configure Apache/XAMPP correctly for Laravel.

Laravel's public directory MUST be the web-accessible document root.

Do NOT expose the Laravel project root directly through Apache.

Correct conceptual structure:

C:\Antigravity_New_Apps\toletkoi_laravel
    app\
    bootstrap\
    config\
    database\
    public\       <-- Apache document root
    resources\
    routes\
    storage\
    tests\
    vendor\
    .env

Apache should serve:

C:\Antigravity_New_Apps\toletkoi_laravel\public

NOT:

C:\Antigravity_New_Apps\toletkoi_laravel

------------------------------------------------------------

LOCAL URL
---------

The AI should configure a convenient local development URL.

Preferred approach:

toletkoi.local

or another suitable local hostname.

If Apache VirtualHost configuration is used, configure:

- Apache VirtualHost
- Windows hosts file
- Laravel public directory as DocumentRoot
- correct Rewrite/AllowOverride configuration
- HTTPS locally only if required

Do not modify unrelated XAMPP VirtualHosts.

If VirtualHost configuration is not practical, a localhost
subdirectory setup may be used temporarily, but the preferred
development setup is a dedicated VirtualHost pointing directly
to Laravel's /public directory.

------------------------------------------------------------

MYSQL RULES
-----------

All database structure changes for the NEW Laravel application
MUST use Laravel migrations.

Do NOT manually modify production database structure through
phpMyAdmin and leave the migration system out of sync.

phpMyAdmin may be used for:
- inspection
- debugging
- data viewing
- controlled development administration
- importing/exporting approved seed/test data

But Laravel migrations remain the source of truth for schema.

Seed data MUST use Laravel seeders/factories where appropriate.

------------------------------------------------------------

REFERENCE DATABASE RULE
-----------------------

The AI may inspect the MySQL databases belonging to the
reference projects if the databases are available locally.

Before doing so:

1. Identify the database used by the project.
2. Inspect its schema.
3. Inspect relationships.
4. Inspect indexes.
5. Inspect important data structures.
6. Identify duplicated/legacy structures.
7. Identify security problems.
8. Document useful concepts.

The AI MUST NOT modify the reference databases.

If database modification is ever required for analysis, create
a separate copy first and work only on the copy after explicit
authorization.

============================================================
REFERENCE ANALYSIS OUTPUT
============================================================

The AI MUST create:

PROJECT_TRACKING/
└── REFERENCE_ANALYSIS/
    ├── PROJECT_A_ANALYSIS.md
    ├── PROJECT_B_ANALYSIS.md
    ├── UI_UX_ANALYSIS.md
    ├── BUSINESS_LOGIC_ANALYSIS.md
    ├── DATABASE_ANALYSIS.md
    ├── FEATURE_COMPARISON.md
    ├── MIGRATION_MAPPING.md
    ├── REUSABLE_COMPONENTS.md
    ├── TECHNICAL_DEBT.md
    └── FINAL_REFERENCE_SPECIFICATION.md

PROJECT_A = Node.js + Next.js project
PROJECT_B = Raw PHP project

The final reference specification MUST explain exactly how
findings from both projects influence the new Laravel system.

============================================================
MANDATORY FIRST ACTION
============================================================

Before writing Laravel application code, the AI MUST:

1. Read TOLETKOI_MASTER_AGENT_PROMPT.md.
2. Inspect the current repository.
3. Inspect PROJECT_TRACKING/.
4. Inspect Project A:
   C:\Antigravity_New_Apps\toletkoi_v3
5. Inspect Project B:
   C:\xampp\htdocs\toletkoi_web\toletkoi_web
6. Determine the technologies and versions actually used.
7. Inspect their database configuration.
8. Inspect available database schemas.
9. Analyze UI/UX.
10. Analyze business logic.
11. Analyze workflows.
12. Analyze database architecture.
13. Analyze APIs.
14. Analyze authentication/authorization.
15. Analyze SEO.
16. Analyze security.
17. Analyze performance.
18. Record technical debt.
19. Create REFERENCE_ANALYSIS documentation.
20. Produce FINAL_REFERENCE_SPECIFICATION.md.
21. Update PROJECT_STATUS.md.
22. Update PROJECT_NEXT_TASK.md.
23. Only after this analysis is complete, proceed to product
    architecture and Laravel implementation.

DO NOT START BUILDING THE NEW LARAVEL APPLICATION BEFORE
REFERENCE ANALYSIS IS COMPLETE.

## Product Positioning

ToLetKoi is a:
> Bangladesh Rental & Property Management Platform

Core journey:
**Find -> Match -> Verify -> Contact -> Rent -> Pay -> Manage -> Live**

Primary business engines:
1. Rental marketplace
2. Credits/contact unlock
3. Tenant subscriptions
4. Landlord subscriptions
5. Sponsored listings
6. Property/owner verification
7. KoiFind assisted property search
8. Rental agreements
9. Rent collection
10. Maintenance/service marketplace
11. Property management SaaS
12. Future enterprise/data products

## Technology Separation

Develop the project in clearly separated technical phases:

- **Web Platform:** Laravel + MySQL
- **Mobile App:** Flutter + Dart
- Admin Dashboard is separate from User Dashboard.
- Laravel backend/API is the shared business/data layer for Flutter.

Do not merge the Laravel web phase and Flutter phase.

## Role Rules: NON-NEGOTIABLE

At registration, every account must define a primary role.

### Switchable roles

Only these three roles may be changed later from the User Dashboard:
- Landlord
- Renter
- Tenant

These three may switch between one another.

### Fixed roles

All other roles are fixed after registration and MUST NOT be switchable by the user.

Examples:
- Property Manager
- Service Provider
- Agent / Scout
- Corporate / Organization
- Staff
- Admin / Super Admin
- any future privileged or specialized role

For fixed roles:
- role is selected during registration where public registration is permitted
- no role-switching UI is shown
- API/server rejects unauthorized role changes
- every role change is auditable

Never allow a normal user to switch into Admin, Staff, Super Admin, Property Manager, Service Provider, Corporate, or another restricted role.

## Role Model

Use a robust role model such as:
- `users`
- `roles`
- `user_roles`
- `primary_role`
- `role_change_logs`

Support:
- one primary role
- controlled switching between the three switchable roles
- fixed roles
- server-side authorization
- auditability

## Quality Rules

- production-ready code
- strong validation
- authorization at server/API level
- CSRF protection where applicable
- rate limiting
- SQL injection protection
- XSS protection
- secure file uploads
- audit logs
- transaction-safe financial operations
- idempotent payment callbacks
- no hardcoded secrets
- environment-based config
- proper indexes and foreign keys
- migrations and seeders
- meaningful error handling
- logging
- API versioning
- responsive web UI
- mobile-first Flutter UX
- Bangla + English localization readiness
- Bangladesh currency: BDT / ৳
- timezone: Asia/Dhaka

## Product Context

Support:
- Bangladesh districts/areas
- GPS/radius search
- Barikoi geocoding/reverse-geocoding/navigation where appropriate
- property images
- favorites
- saved searches
- recommendations
- messaging/contact
- notifications
- payment providers such as SSLCOMMERZ
- Firebase push notifications if selected
- WebSockets/realtime messaging if selected

## Money and Credit Rules

Keep these separate:
1. Payment ledger
2. Revenue ledger
3. Credit wallet/ledger

Never mutate credit balances without an auditable transaction.

Payment callbacks must be verified server-side and idempotent.

## AI Agent Working Rules

Before modifying code:
1. inspect existing project
2. understand architecture
3. identify reusable code
4. create implementation plan
5. implement incrementally
6. run tests/validation
7. report changed files
8. report database changes
9. report API changes
10. report unresolved issues

Never silently delete working functionality.
Never replace working logic with placeholders.

## Definition of Done

A phase is complete only when:
- implementation exists
- migrations are complete
- validation exists
- authorization exists
- tests or meaningful verification exists
- documentation exists
- no obvious placeholder functionality remains
- next phase can safely build on the result
