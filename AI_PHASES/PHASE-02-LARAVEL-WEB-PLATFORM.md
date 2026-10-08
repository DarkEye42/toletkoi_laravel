# ToLetKoi AI Agent Build Prompt
## Phase 02: Laravel + MySQL Customer Web Platform

This phase is WEB ONLY.
Do not implement Flutter here.
Do not implement Admin Dashboard here.

## Public Website

Build:
- homepage
- property search
- area search
- map search
- listing details
- gallery
- favorites
- compare
- saved searches
- registration
- login
- password reset
- profile completion
- pricing
- credit wallet
- subscription plans
- verification
- KoiFind
- legal pages
- help/contact

## Search

Support:
- division/district/area
- radius search
- current location
- price range
- property type
- subtype
- bedrooms
- bathrooms
- furnishing
- parking
- lift
- utilities
- available date
- verified only
- sponsored/featured ordering

Use pagination and optimized queries.

## Listing Detail

Show:
- rent
- deposit
- service charge
- property details
- amenities
- photos
- map/location
- availability
- verification badges
- owner trust information
- listing freshness
- contact CTA
- favorite
- share

Do not reveal private contact information before authentication and successful authorization.

## Contact Unlock

Flow:
1. authenticate
2. verify user
3. check credits/subscription allowance
4. transactionally deduct credits
5. create `contact_unlocks`
6. reveal permitted contact information
7. log analytics event

Duplicate requests must not cause double deduction.

## Registration and Roles

Registration requires role selection.

Only:
- Landlord
- Renter
- Tenant

can later switch between those three.

All other roles:
- no role-switch UI
- server/API rejects switching

A role switch must preserve previous role data.

## SEO

Implement:
- clean URLs
- slugs
- canonical URLs
- meta title/description
- Open Graph
- structured data where appropriate
- sitemap
- robots.txt
- SEO area pages
- property landing pages

## Performance

Use:
- caching
- eager loading
- optimized queries
- pagination
- image compression
- WebP
- lazy loading
- CDN-ready assets

## Security

Implement:
- authentication
- policies/gates
- rate limits
- secure uploads
- MIME validation
- safe filenames
- CSRF
- XSS protection
- abuse reporting

## Deliverables

Provide a working production web platform with:
- routes
- controllers
- requests
- models
- services
- authorization
- views/frontend
- tests
- docs

Admin Dashboard is excluded from this phase.
