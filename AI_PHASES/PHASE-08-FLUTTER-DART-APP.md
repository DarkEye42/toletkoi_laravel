# ToLetKoi AI Agent Build Prompt
## Phase 08: Flutter + Dart Mobile Application

IMPORTANT:
This is the separate Flutter phase.
Do not rebuild the Laravel backend.
Consume the existing Laravel API.

Use Flutter + Dart for a production mobile application.

## Before UI

Inspect and confirm:
1. API base URL
2. authentication
3. token/session lifecycle
4. pagination
5. validation errors
6. image upload
7. credit API
8. subscription API
9. payment flow
10. role-switch API
11. notifications
12. messaging

Do not invent endpoints that do not exist.

## Architecture

Use a maintainable structure such as:

lib/
- core/
- config/
- routing/
- network/
- storage/
- auth/
- localization/
- shared/
- listings/
- search/
- map/
- favorites/
- messaging/
- credits/
- subscriptions/
- payments/
- koifind/
- rentals/
- services/
- landlord/
- renter/
- tenant/
- property_manager/
- profile/

Use a consistent state-management architecture such as Riverpod or Bloc.

## Auth

Implement:
- register
- login
- verification
- password recovery
- secure token storage
- refresh/session handling
- logout
- profile completion

## Registration Role

Role must be selected during registration.

Switchable:
- Landlord
- Renter
- Tenant

Fixed:
- every other role

Never expose Admin/Super Admin registration as a normal public role.

## Role Switching

Only show role switcher when current role is:
- Landlord
- Renter
- Tenant

Call Laravel API for role switch.

After success:
- refresh user context
- update permissions
- rebuild navigation
- route to the correct dashboard

Do not rely on Flutter UI for security.
Backend is authoritative.

Fixed-role users:
- no role switcher
- API request denied

## Renter/Tenant Experience

Core navigation:
- Home
- Search
- Map
- Saved
- Messages
- Profile

Include:
- recommendations
- nearby search
- favorites
- comparison
- contact unlock
- credits
- subscriptions
- KoiFind
- applications
- agreements
- rent
- maintenance

## Landlord Experience

- dashboard
- properties
- leads
- promotions
- verification
- payments
- profile

## Property Manager Experience

Fixed-role dashboard:
- overview
- organizations/buildings
- units
- tenants
- rent
- maintenance
- reports
- staff

No role switcher.

## Maps

Support:
- current location
- radius
- markers
- property details
- map filtering
- navigation handoff as supported

## Media

Implement:
- image picker
- compression
- multi-upload
- progress
- retry
- validation

## Reliability

Handle:
- loading
- empty states
- errors
- retry
- offline state
- token expiry
- safe optimistic updates

## Push

Support:
- listing alerts
- price drops
- messages
- contact
- payment
- rent due
- maintenance
- verification

## Deliverables

- complete Flutter app
- role-aware navigation
- tenant/renter UI
- landlord UI
- fixed-role UI
- search
- maps
- listings
- credits
- subscriptions
- payments
- notifications
- messaging
- KoiFind
- rental flows
- maintenance
- tests
