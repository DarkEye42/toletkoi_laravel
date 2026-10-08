# ToLetKoi AI Agent Build Prompt
## Phase 09: QA, Security, Performance, CI/CD & Production Hardening

This is mandatory.

## Backend Tests

Cover:
- unit
- feature
- API
- authorization
- database integrity
- payment callbacks
- credit ledger
- subscriptions
- promotions
- role switching

### Required Role Tests

Allowed:
- Landlord -> Tenant
- Landlord -> Renter
- Tenant -> Landlord
- Tenant -> Renter
- Renter -> Landlord
- Renter -> Tenant

Denied:
- Admin -> Tenant
- Property Manager -> Tenant
- Service Provider -> Tenant
- Tenant -> Admin
- Landlord -> Admin
- Renter -> Property Manager

## Flutter Tests

- unit
- widget
- integration
- auth/session
- role switching
- payment states
- error handling

## Security Audit

Check:
- authentication
- authorization
- IDOR
- SQL injection
- XSS
- CSRF
- file uploads
- rate limits
- brute force
- webhook verification
- secrets
- admin isolation
- organization isolation

## Financial Audit

Verify:
- no duplicate credit grants
- no duplicate subscription activation
- duplicate callback safety
- refunds
- no negative balance
- reconciliation

## Performance

Test:
- search
- radius queries
- listing page
- dashboard
- analytics
- image delivery
- concurrent traffic

Optimize with:
- indexes
- eager loading
- caching
- queues
- CDN
- optimized images

## Environments

Use:
- local
- staging
- production

## Deployment

Document:
- environment variables
- migrations
- queues
- scheduler
- storage
- backups
- monitoring
- error tracking
- logs
- rollback

## CI/CD

Pipeline:
1. dependency install
2. lint/static analysis
3. backend tests
4. Flutter tests
5. build
6. staging deploy
7. smoke test
8. production approval
9. deploy
10. rollback strategy

## Production Checklist

Verify:
- HTTPS
- secure cookies/tokens
- CORS
- file permissions
- database backup
- workers
- scheduler
- push notifications
- payment callbacks
- email
- maps/geocoding
- monitoring
- analytics
