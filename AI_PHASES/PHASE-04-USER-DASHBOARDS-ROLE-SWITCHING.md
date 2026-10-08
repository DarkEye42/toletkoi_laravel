# ToLetKoi AI Agent Build Prompt
## Phase 04: Separate User Dashboards & Role Switching

This phase is exclusively for authenticated User Dashboards.

Admin Dashboard belongs to a separate phase and must not be merged into this UI.

## Role System

### Switchable
Only:
- Landlord
- Renter
- Tenant

These three may switch between one another.

### Fixed
All other supported roles are fixed after registration.

Examples:
- Property Manager
- Service Provider
- Agent/Scout
- Corporate/Organization
- Staff
- Admin
- Super Admin

Fixed-role users:
- do not see role switch UI
- cannot call a successful role-switch API
- receive server-side authorization failure

## Registration

Require:
- identity information
- phone/email
- password
- selected role

Do not allow public self-registration as Admin/Super Admin/Staff.

## Common Dashboard Features

- profile
- security
- notifications
- saved searches
- favorites
- messages
- credits
- subscription
- payment history
- support
- activity history

## Landlord Dashboard

- properties
- listing creation/editing
- listing analytics
- leads
- contact requests
- promotions
- verification
- tenant inquiries
- rent tools when enabled

## Renter Dashboard

- search history
- saved searches
- favorites
- compare
- property contacts
- applications
- KoiFind
- recommendations
- credits
- subscription

## Tenant Dashboard

- active rental
- agreement
- rent
- payment history
- maintenance
- messages
- move-in information

## Role Switch API

Use a dedicated endpoint such as:
`POST /api/v1/me/switch-role`

Request:
`target_role`

Server must verify:
- authenticated user
- current role is switchable
- target role is switchable
- target is not privileged/fixed
- operation is auditable

On success:
- update primary role
- create role change log
- preserve historical data
- return new role context

On denied request:
- return HTTP 403/422 as appropriate
- do not mutate the role

## UX Rule

Role switcher visibility:
`current_role IN [landlord, renter, tenant]`

Do not depend on CSS/UI hiding as security.

## Tests

Must cover:
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
