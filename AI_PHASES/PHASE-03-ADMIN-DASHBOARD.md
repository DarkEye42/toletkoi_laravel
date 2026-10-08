# ToLetKoi AI Agent Build Prompt
## Phase 03: Separate Admin Dashboard & Operations Console

This phase is exclusively for the Admin Dashboard.

Admin UI must be separated from the public User Dashboard.

## Admin Roles

Support fixed privileged roles such as:
- Super Admin
- Admin
- Moderator
- Finance
- Support
- Verification Staff

These roles are not user-switchable.

## Modules

### Overview
Show:
- total users
- active users
- active listings
- new listings
- verified listings
- contacts
- revenue by source
- refunds
- subscriptions
- pending verifications
- pending payments
- complaints

### User Management
- search
- filter
- profile
- verification
- suspend/reactivate
- authorized role administration
- audit history
- abuse history

### Listing Moderation
- pending
- active
- rejected
- expired
- reported
- duplicate
- edit
- suspend
- restore
- feature

### Verification
- review documents
- approve/reject
- assign physical verification
- verification expiry
- history

### Credits/Finance
- credit packages
- orders
- transactions
- manual adjustment
- refunds
- revenue
- reconciliation

Manual wallet adjustments require:
- reason
- staff ID
- timestamp
- audit record

### Subscriptions
- plans
- active subscriptions
- failed payments
- cancellations
- upgrades/downgrades

### Promotions
- Boost
- Featured
- Premium
- placement control
- schedules
- campaign pricing

### KoiFind
- requests
- queue
- assignments
- candidates
- shortlist
- status
- customer communication

### Services
- providers
- orders
- commissions
- disputes
- reviews

### Property Management SaaS
- organizations
- plans
- unit counts
- usage
- billing

### Content/SEO
- pages
- FAQs
- area content
- blog/articles
- banners

### Analytics
Track:
- DAU/WAU/MAU
- registration
- activation
- conversion funnel
- revenue by source
- CAC
- LTV
- churn
- retention
- geographic performance
- listing liquidity

### Support
- tickets
- abuse reports
- disputes
- blocks

## Security

Use:
- dedicated admin guard/auth architecture where appropriate
- RBAC/permissions
- 2FA-ready design
- audit logs
- session controls
- login monitoring

Never rely on normal consumer-role authorization for admin access.

## Deliverables

- admin authentication
- admin layout
- permissions
- dashboards
- management tables
- filters
- forms
- audit logs
- tests
- admin documentation
