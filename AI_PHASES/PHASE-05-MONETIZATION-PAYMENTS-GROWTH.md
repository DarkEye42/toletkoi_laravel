# ToLetKoi AI Agent Build Prompt
## Phase 05: Monetization, Payments, Credits, Subscriptions, Promotions & Growth

Implement the commercial engine.

## Credit Packages

Initial configuration:

| Package | Credits | Price |
|---|---:|---:|
| Starter | 50 | ৳49 |
| Standard | 150 | ৳129 |
| Plus | 400 | ৳299 |
| Pro | 1,000 | ৳649 |
| Business | 3,000 | ৳1,499 |

Target contact unlock:
**5 credits**

Purchased credits: suggested 12-month validity.
Promotional/free credits may have shorter expiry.

Recommended registration bonus:
**50 credits**

Avoid unlimited monthly free credits at launch.

## Tenant Plans

| Plan | Monthly | Annual |
|---|---:|---:|
| Free | ৳0 | ৳0 |
| Basic | ৳99 | ৳999 |
| Pro | ৳199 | ৳1,999 |
| Pro+ | ৳399 | ৳3,999 |

## Owner Plans

| Plan | Monthly |
|---|---:|
| Free | ৳0 |
| Plus | ৳299 |
| Pro | ৳799 |
| Property Manager | ৳1,999 |
| Enterprise | ৳4,999+ |

## Sponsored Listings

Boost:
- 3 days: ৳49
- 7 days: ৳99
- 14 days: ৳169
- 30 days: ৳299

Featured:
- 7 days: ৳149
- 14 days: ৳249
- 30 days: ৳449

Premium:
- 7 days: ৳299
- 14 days: ৳499
- 30 days: ৳799

Do not let paid ranking destroy search relevance.

## Verification Pricing

- Phone: Free
- Owner: ৳149
- Property: ৳399
- Physical: ৳699-999

## Payment Architecture

Build a gateway abstraction.

Potential providers:
- SSLCOMMERZ
- bKash/Nagad integrations where contractually and technically supported

Business logic must not be hardcoded to one gateway.

## Payment States

- CREATED
- PENDING
- PROCESSING
- SUCCESS
- FAILED
- CANCELLED
- REFUNDED
- PARTIALLY_REFUNDED

## Idempotency

A duplicate callback must never:
- add credits twice
- activate a subscription twice
- create duplicate promotions
- duplicate a financial record

## Revenue Sources

Track separately:
- CREDIT
- SUBSCRIPTION
- PROMOTION
- VERIFICATION
- KOIFIND
- AGREEMENT
- RENT_COLLECTION
- SERVICE
- SAAS
- ADVERTISING

## Commission Targets

Starting targets:
- maintenance: 10-20%
- moving: 10-15%
- cleaning: 10-20%
- ISP referral: 5-15%
- furniture: 3-10%
- appliances: 2-8%
- rent collection: 0.3-0.5% platform fee, subject to gateway economics

Gateway fees must be modeled separately.

## Growth

Implement:
- referral codes
- referral rewards
- promo codes
- campaign attribution
- UTM/source tracking
- conversion analytics

## Critical Funnel

Track:
Visitor
-> Registration
-> Profile completion
-> Search
-> Listing view
-> Favorite
-> Contact attempt
-> Contact unlock
-> Owner response
-> Visit
-> Rental
-> Repeat engagement

## Financial Dashboard

Show:
- gross payment volume
- gateway fees
- refunds
- net revenue
- revenue by source
- contribution margin
- trends
