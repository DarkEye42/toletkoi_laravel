# ToLetKoi AI Agent Build Prompt
## Phase 07: Property Management SaaS

Transform the platform into a property management system for multi-unit landlords and managers.

## Customers

- multi-unit landlords
- building owners
- hostel/mess operators
- property managers
- real-estate companies
- corporate housing operators

## Organization Model

Support:
- organizations
- buildings
- units
- tenants
- occupancies
- agreements
- rent schedules
- payments
- expenses
- maintenance
- staff

All organization data must be isolated.

## Pricing

Property Manager:
**৳1,999/month**

Enterprise:
**৳4,999+/month**

Make limits configurable:
- buildings
- units
- staff
- storage
- reports

## Dashboard

Show:
- total units
- occupied
- vacant
- rent due
- collected
- overdue
- maintenance
- occupancy
- expected monthly income
- actual collected
- expenses

## Rent

Support:
- recurring schedules
- due dates
- reminders
- partial payments
- late status
- receipts

## Maintenance

Support:
- ticket
- priority
- assignment
- provider
- cost
- attachments
- tenant notes

## Staff

Use least privilege:
- Manager
- Accountant
- Support
- Maintenance
- Viewer

These are fixed organization roles. They do not use the consumer role-switch system.

## Reporting

Provide:
- income
- outstanding
- expense
- occupancy
- turnover
- maintenance cost
- unit performance

## Security

Use `organization_id` or equivalent tenant scoping.

A manager must never access another organization.

Use:
- query scopes
- policies
- automated authorization tests

## Deliverables

- organization tenancy
- SaaS billing
- buildings/units
- tenant management
- rent management
- maintenance
- staff permissions
- dashboards
- reports
- tests
