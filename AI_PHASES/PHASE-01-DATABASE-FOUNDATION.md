# ToLetKoi AI Agent Build Prompt
## Phase 01: Laravel/MySQL Business Foundation & Database Architecture

This phase establishes the data model and backend contracts.

Do not build Flutter in this phase.
Do not build Admin Dashboard UI in this phase.

## Required Domains

Create normalized Laravel migrations/models for:

### Identity
- users
- roles
- user_roles
- role_change_logs
- user_profiles
- user_preferences

### Geography
- divisions
- districts
- areas
- area aliases where useful

### Marketplace
- property_types
- property_subtypes
- listings
- property_images
- listing_features
- listing_amenities
- listing_views
- listing_stats
- favorites
- comparisons
- saved_searches

### Credits
- credit_wallets
- credit_transactions
- credit_packages
- credit_orders

### Subscriptions
- subscription_plans
- subscriptions
- subscription_transactions
- subscription_events

### Promotions
- promotion_products
- promotions
- promotion_orders
- promotion_events

### Verification
- verification_types
- verifications
- verification_documents
- verification_logs

### Leads/Contact
- leads
- contact_unlocks
- lead_events

### KoiFind
- koifind_requests
- koifind_candidates
- koifind_shortlists
- koifind_events

### Rental
- rental_applications
- rental_agreements
- agreement_participants
- rent_payments
- rent_payment_items
- payment_orders
- payment_transactions
- payment_callbacks
- refunds

### Property Management
- organizations
- property_buildings
- property_units
- unit_occupancies
- tenants
- tenant_notes
- maintenance_requests
- maintenance_assignments
- property_expenses
- organization_staff

### Services
- service_categories
- service_providers
- service_orders
- service_order_items
- service_commissions
- provider_reviews

### Communication
- conversations
- conversation_participants
- messages
- message_attachments
- notifications
- notification_preferences

### Growth/Governance
- referrals
- referral_rewards
- promo_codes
- analytics_events
- audit_logs
- abuse_reports
- user_blocks

## Role Policy

At registration, require a role.

Only these are switchable:
- Landlord
- Renter
- Tenant

A user can switch only when both current and target roles are in that set.

Fixed roles can never be self-switched.

Implement a service/policy such as:
`RoleSwitchService`

Log every successful/failed role-change attempt where appropriate.

## Listings

Support:
- title
- slug
- description
- property type/subtype
- rent
- security deposit
- service charge
- bedrooms
- bathrooms
- area_sqft
- floor_number
- total_floors
- available_from
- furnishing
- parking
- lift
- generator
- electricity_included
- water_included
- gas_type
- features JSON if needed
- latitude
- longitude
- area_id
- address
- owner_id
- status
- verification status
- published_at
- expires_at

## Indexing

Index common query fields:
- owner_id
- area_id
- status
- rent
- property type/subtype
- published_at
- available_from
- verification status

Use MySQL spatial support where appropriate.

## Financial Integrity

Use decimal types for BDT.
Never use floating-point columns for money.

Credit operations must be transactional and auditable.

## Deliverables

Produce:
1. migrations
2. models
3. relationships
4. enums/value objects where useful
5. factories
6. seeders
7. policies
8. services
9. schema/ERD documentation
10. tests for critical integrity rules
