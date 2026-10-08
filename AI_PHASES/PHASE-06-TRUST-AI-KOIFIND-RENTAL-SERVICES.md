# ToLetKoi AI Agent Build Prompt
## Phase 06: Trust, AI Matching, KoiFind, Rental Agreements & Services

Build higher-value services on top of the core marketplace.

## Verification

Levels:
- Phone Verified
- Owner Verified
- Property Verified
- Location Verified
- Physical Verification

Implement:
- verification workflow
- document review
- staff assignment
- approval/rejection
- expiry
- audit trail

Never expose private verification documents publicly.

## Owner Trust Score

Potential inputs:
- identity verification
- response rate
- successful transactions/interactions
- complaints
- reports
- account age
- reviews

Keep the scoring explainable and auditable.

## Listing Quality Score

Inputs:
- completeness
- images
- location
- price clarity
- features
- verification
- freshness

Use it in ranking/moderation.

## AI Recommendations

Use relevant non-sensitive product signals:
- searches
- favorites
- budget
- location
- property type
- bedrooms
- furnishing
- engagement
- previous contact behavior

Return:
- match score
- recommendation reasons

## KoiFind

Workflow:
1. customer submits requirements
2. AI searches
3. rank candidates
4. optional human verification
5. shortlist
6. contact coordination
7. completion
8. rating

Starting price configuration:
- AI Shortlist: ৳99
- Standard: ৳299
- Verified Search: ৳499
- Premium: ৳999
- Executive: ৳1,999+

Do not hardcode pricing in business logic.

## Rental Agreement

Support:
- landlord
- tenant
- property
- rent
- deposit
- utilities
- notice
- term
- acknowledgments/signatures as supported

Starting products:
- Basic: ৳99
- Premium: ৳199
- assisted: ৳399+

Clearly distinguish document generation from legal advice.

## Maintenance Marketplace

Flow:
ticket -> category -> location -> provider matching -> quote -> booking -> completion -> payment -> rating

Target commission:
10-20%

## Moving/Relocation

Support:
- origin
- destination
- moving date
- inventory
- provider
- quote
- booking
- commission

Starting commission:
10-15%

## Notifications

Support:
- push
- email
- in-app
- SMS if required

Events:
- new saved-search listing
- price drop
- messages
- contact request
- verification
- payments
- rent due
- maintenance

## Deliverables

- verification
- trust scoring
- listing quality
- recommendation service
- KoiFind
- agreements
- maintenance
- moving
- commissions
- notifications
- tests
