# ToLetKoi Business Logic Analysis — From Both Reference Projects

## Core Business Flow

```
Find → Match → Verify → Contact → Rent → Pay → Manage → Live
```

## 1. Property Listings

### Categories (from both projects)
- Family House/Flat
- Bachelor Pad/Room
- Sublet
- Office Space
- Hostel/Room
- Shop/Commercial
- Factory/Warehouse
- Ready Shed
- Open Land
- Apartment

### Listing Types
- Rent
- Sale

### Price Types
- Monthly
- Weekly
- Daily
- Negotiable

### Property Attributes
- Title (EN/BN)
- Description (EN/BN)
- Address (EN/BN)
- Category
- Listing Type
- Price (BDT)
- Price Type
- Bedrooms
- Bathrooms
- Balconies
- Floor/Level
- Total Floors
- Area (sqft)
- Furnishing Status
- Available From
- Lat/Lng (Barikoi)
- Division → District → Area/Thana

### Facility Flags (from Project B, 14 flags)
- Parking
- Lift/Elevator
- Generator
- Gas Connection
- Electricity Included
- Water Included
- Security Guard
- CCTV
- Internet/WiFi
- Furnished
- Air Conditioning
- Washing Machine
- Shared Kitchen
- Attached Bathroom

### Images
- Primary image + gallery
- Max 6-10 images
- Auto-resize (WebP for PHP, 1200x900 for Node)
- Image ordering

### Status Workflow
- Pending → Active → Inactive/Sold/Rented
- Admin can feature/unfeature
- Expiry dates for featured listings

## 2. User System

### Roles (Mandatory from Master Prompt)
- Landlord (switchable)
- Renter (switchable)
- Tenant (switchable)
- Property Manager (fixed)
- Service Provider (fixed)
- Agent/Scout (fixed)
- Corporate/Organization (fixed)
- Staff (fixed)
- Admin (fixed)
- Super Admin (fixed)

### Role Switching Rules
- Only Landlord ↔ Renter ↔ Tenant can switch
- Server-side authorization required
- Every role change must be auditable
- No user can switch into Admin/Staff/etc.

### User Profile
- Name, Email, Phone
- NID (National ID)
- Avatar/Photo
- Address
- Lat/Lng
- Bio/Description
- Verified status
- Active/Suspended status

## 3. Credit System

### Credit Types
- Purchase (buy credits)
- Earn (referral bonuses, finders fees)
- Spend (contact unlock, boosting)
- Refund (failed transactions)
- Bonus (admin giveaways)
- Admin Adjustment (manual credit add/deduct)

### Credit Consumption
- Contact/Phone reveal (X credits)
- Order Home service (100 BDT)
- Listing boost
- Featured listing purchase

### Financial Rules (from Master Prompt)
- Keep Payment Ledger, Revenue Ledger, and Credit Wallet separate
- Never mutate credit balance without auditable transaction
- Payment callbacks must be verified server-side and idempotent

## 4. Payment System

### Payment Methods (Bangladesh)
- bKash
- Nagad
- Rocket (DBBL)
- SSL Commerz (card/bank)
- Manual Bank Transfer
- Cash

### Payment Flow
1. User initiates payment
2. System creates payment order
3. Redirect to payment gateway
4. Gateway callback/webhook
5. Verify callback server-side
6. Update payment status
7. Credit wallet (if credit purchase)
8. Send confirmation

### Idempotency
- Each payment has unique order_id
- Duplicate callbacks are ignored
- Status transitions are validated

## 5. Subscription System

### Plan Types
- Free (basic listing)
- Basic (more listings, basic features)
- Premium (unlimited, priority, analytics)
- Enterprise (bulk, API, dedicated support)

### Subscription Flow
1. User selects plan
2. Payment processed
3. Subscription activated
4. Billing cycle tracked
5. Auto-renewal reminder
6. Expiry handling
7. Downgrade on cancellation

## 6. Location System

### Hierarchy
```
Division (8) → District (64) → Area/Thana (360+)
```

### Barikoi Integration
- Geocoding: Address → Coordinates
- Reverse Geocoding: Coordinates → Address
- Autocomplete: Partial address → Suggestions
- Nearby Search: Coordinates → Nearby places
- Route: Multiple waypoints → Route

### Local Coordinate Database
- Store normalized Barikoi responses
- Reduce external API calls
- Maintain lookup order: Local → Cache → Barikoi

## 7. Search & Discovery

### Search Parameters
- Category
- Listing Type
- Price Range (min/max)
- Bedrooms
- Bathrooms
- Area/Location
- Division/District
- Furnishing Status
- Keywords
- Sort Order

### Search Features
- Text search with LIKE/Full-text
- Radius search (lat/lng + distance)
- Saved searches
- Search history
- Popular searches
- Recommended listings

## 8. Contact/Lead System

### Contact Unlock Flow
1. User views listing
2. Contact info hidden
3. User clicks "Reveal Contact"
4. Credits deducted (or subscription allows)
5. Owner contact shown
6. Lead event logged

### Lead Tracking
- Listing ID
- User ID
- Timestamp
- Credits consumed
- Contact type (phone/email)

## 9. Verification System

### Verification Types
- Email verification (token-based)
- Phone verification (OTP)
- NID verification (document upload)
- Photo verification (selfie)
- Property verification (physical visit)

### Verification Flow
1. User submits verification request
2. Documents uploaded
3. Staff reviews
4. Approve/Reject
5. Verification badge shown
6. Expiry tracking

## 10. KoiFind (Assisted Search)

### Flow
1. Tenant submits search request
2. Request enters queue
3. Agent/Scout assigned
4. Agent finds candidates
5. Shortlist presented
6. Tenant selects
7. Contact arranged
8. Fee charged (100 BDT from Project A)

## 11. Property Management SaaS

### Organization Model
- Organization (company/building owner)
- Buildings
- Units (apartments/rooms)
- Tenants
- Staff

### Features
- Unit management
- Tenant onboarding
- Rent collection
- Maintenance requests
- Expense tracking
- Analytics
- Billing

## 12. Content/CMS

### Content Types
- Static pages (About, Contact, FAQ)
- Blog/Articles (EN/BN)
- Area content (SEO pages)
- Banners/Promotions
- Testimonials
- FAQs

### CMS Features
- WYSIWYG editor
- Image management
- SEO metadata
- Bilingual support
- Publish/draft workflow

## 13. Notification System

### Types
- Email notifications
- SMS notifications
- Push notifications (Firebase)
- In-app notifications

### Triggers
- New inquiry
- Listing approved/rejected
- Payment received
- Subscription expiring
- Verification status change
- Promotional offers

## 14. Analytics & Reporting

### User Analytics
- DAU/WAU/MAU
- Registration/Activation
- Conversion funnel
- Retention/Churn

### Business Analytics
- Revenue by source
- CAC (Customer Acquisition Cost)
- LTV (Lifetime Value)
- Geographic performance
- Listing liquidity

### Property Analytics
- Views per listing
- Contact rate
- Conversion rate
- Time to rent
