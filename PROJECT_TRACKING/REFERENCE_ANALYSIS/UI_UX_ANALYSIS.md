# UI/UX Reference Analysis: ToLetKoi

> **PRIMARY UI SOURCE:** Next.js Project (`C:\Antigravity_New_Apps\toletkoi_v3\web-admin`)
> **SECONDARY UI REFERENCE:** Raw PHP Project (`C:\xampp\htdocs\toletkoi_web\toletkoi_web`)
> **TARGET:** Laravel Project (`C:\Antigravity_New_Apps\toletkoi_laravel`)

---

## 1. Next.js Visual Design System

### 1.1 Color System

The Next.js project uses a **MaterialM** design system (based on Material Design by WrapPixel) with the following custom Tailwind colors:

| Variable | Hex | Usage |
|----------|-----|-------|
| `primary` | `#00a1ff` | Primary actions, links, active states |
| `primary-dark` | `#008be0` | Hover states |
| `primary-light` | `#e6f6ff` | Background tints |
| `secondary` | `#49beff` | Secondary actions |
| `success` | `#13deb9` | Success states |
| `warning` | `#ffae1f` | Warning states |
| `danger` | `#fa896b` | Destructive actions |
| `info` | `#539bff` | Info states |
| `dark` | `#2a3547` | Text primary, headings |
| `muted` | `#5a6a85` | Text secondary, placeholder |
| `light` | `#f6f9fc` | Page backgrounds |

**Laravel gap:** Currently uses `emerald-#500/#600` as primary via Tailwind defaults. Does not have MaterialM color tokens.

### 1.2 Typography

- **Font family:** `Plus Jakarta Sans` (loaded via Google Fonts in `app/layout.js`)
- **Fallback:** `system-ui`, `sans-serif`
- **Headings:** `font-bold` (600+), `text-dark` (`#2a3547`)
- **Body text:** `text-sm` (14px base), `text-xs` (12px) for secondary
- **Sizes:**
  - `text-[10px]`: Micro labels, badges, spec details
  - `text-xs`: Captions, body secondary
  - `text-sm`: Body text
  - `text-base`: Default body
  - `text-lg` to `text-3xl`: Headings
  - `text-2xl` to `text-5xl`: Large headings, page titles

**Laravel gap:** Currently uses `Instrument Sans` via Tailwind v4 `@theme`. Does not use Plus Jakarta Sans.

### 1.3 Spacing & Layout

- **Container max-width:** `max-w-6xl` (1152px) for public pages, `max-w-7xl` for some admin pages
- **Section padding:** `py-6` to `py-10` vertical rhythm
- **Grid gaps:** `gap-3`, `gap-4`, `gap-6` (12-24px)
- **Card padding:** `p-4` to `p-6`

### 1.4 Border Radius

| Token | Value | Usage |
|-------|-------|-------|
| Default | `rounded-lg` | Standard elements |
| `rounded-full` | `9999px` | Pills, circular avatars |
| `rounded-xl` | `0.75rem` (12px) | Standard cards |
| `rounded-[1.5rem]` | `24px` | Property card images |
| `rounded-2xl` | `1rem` (16px) | Dashboard cards, modals |
| `rounded-3xl` | `1.5rem` (24px) | Large cards, auth cards |

Custom Tailwind values:
- `borderRadius.material`: `7px`
- `borderRadius.material-lg`: `12px`
- `borderRadius.material-xl`: `20px`

**Laravel gap:** Uses Tailwind default radii (`rounded-lg`, `rounded-xl`). Does not include MaterialM custom radii or `rounded-[1.5rem]` for property cards.

### 1.5 Shadows

| Token | Value | Usage |
|-------|-------|-------|
| `shadow-sm` | `0 1px 2px 0 rgba(0,0,0,0.05)` | Default cards |
| `shadow` | `0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06)` | |
| `shadow-md` | `0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06)` | |
| `shadow-lg` | `0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05)` | |
| `shadow-xl` | `0 20px 25px -5px rgba(0,0,0,0.1)` | Modals, dropdowns |
| `shadow-material` | `0 2px 4px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.08)` | MaterialM cards |
| `shadow-material-lg` | `0 8px 16px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.08)` | Elevated cards |

### 1.6 Icons

- **Library:** Google Material Icons (Outlined variant)
- **Font:** `material-icons` (loaded via Google Fonts in `app/layout.js`):
  ```
  https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons|Material+Symbols+Outlined
  ```
- **Usage pattern:** `<i className="material-icons text-[16px]">search</i>`
- Common icons used: `search`, `place`, `bed`, `bathtub`, `square_foot`, `favorite`, `favorite_border`, `home`, `apartment`, `business`, `school`, `hotel`, `storefront`, `chat`, `phone`, `verified`, `star`, `menu`, `close`, `add`, `edit`, `delete_outline`, `visibility`, `chevron_right`, `map`, `location_city`, `work`, `article`, `help`, `settings`, `payments`, `account_balance_wallet`, `tune`, `grid_view`, `view_list`, `refresh`, `flag`, `photo_library`, `open_in_new`, `content_copy`, `facebook`, `share`, `apple`, `android`

**Laravel gap:** Currently uses emoji icons (🏠, 🔍, 💰, 📞, etc.). Does not include Material Icons font.

### 1.7 Buttons

**Standard buttons (`.btn` class in globals.css):**
```
@apply px-4 py-2.5 rounded-lg font-medium transition-all duration-200 inline-flex items-center justify-center gap-2
```

Variants:
| Class | Background | Text |
|-------|-----------|------|
| `.btn-primary` | `#5d87ff` | white |
| `.btn-secondary` | `#49beff` | white |
| `.btn-success` | `#13deb9` | white |
| `.btn-warning` | `#ffae1f` | white |
| `.btn-danger` | `#fa896b` | white |
| `.btn-outline` | transparent, `2px border #5d87ff` | `#5d87ff` |
| `.btn-light` | `#f3f4f6` | `#374151` |

**Inline utility buttons (more common in practice):**
- Primary action: `bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-full`
- Card action: `bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl`
- Outline: `bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary`
- Icon-only: `w-8 h-8 flex items-center justify-center rounded-full`

### 1.8 Cards

- **Property cards:** `bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100`
- **Dashboard cards:** `bg-white rounded-3xl shadow-material border border-gray-100`
- **Info cards:** `bg-white rounded-xl p-5 border border-gray-100`
- **Stat cards:** `bg-white rounded-2xl p-6 shadow-material border border-gray-100`
- **Glass cards:** `bg-white/95 backdrop-blur px-2.5 py-1 rounded-lg shadow-sm`

### 1.9 Badges

- **Featured badge:** `bg-yellow-500 text-white text-[9px] font-bold px-2 py-0.5 rounded shadow-sm`
- **Category badge:** `text-[10px] font-bold text-primary uppercase tracking-wide`
- **Status badges:** `px-3 py-1.5 rounded-full text-[10px] font-black uppercase`
- **Circular badge:** `w-6 h-6 rounded-full flex items-center justify-center`

### 1.10 Inputs

```
.input {
  @apply w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5d87ff] focus:border-transparent transition-all duration-200;
}
```

- Labels: `text-xs font-bold text-dark` or `text-sm font-medium text-gray-700`
- Error state: `border-red-500 focus:ring-red-500`
- Error messages: `text-red-500 text-sm mt-1`
- Search inputs: `bg-gray-50 border-gray-200 rounded-full pl-10`

### 1.11 Layout Containers

- **Page wrapper:** `bg-[#f2f4f8] min-h-screen text-dark font-sans text-sm`
- **Content container:** `max-w-6xl mx-auto px-4`
- **Section:** `py-6 bg-white` or `py-6 bg-[#f2f4f8]` alternating
- **Dashboard main:** `md:ml-64 mt-16 p-6`

### 1.12 Animations

- `animate-fade-in`: `fadeIn 0.3s ease-out` (opacity + translateY)
- `animate-slide-in`: `slideIn 0.3s ease-out` (translateX)
- Next.js uses: `transition-all duration-200`, `transition-transform duration-500`, `group-hover:scale-105`

---

## 2. Next.js Page Inventory

### 2.1 Public Pages (Consumer-facing)

| Route | File | Description |
|-------|------|-------------|
| `/` | `(public)/page.js` | Homepage: hero search, category tabs, property listings, stats, articles, app download, testimonials, career, purpose, news coverage |
| `/properties` | `(public)/properties/page.js` | Property search: sticky filter bar, grid/list/map view toggle, active filters, pagination |
| `/properties/[id]` | `(public)/properties/[id]/page.js` | Property detail: image gallery, basic info, property details table, contact (credit-gated), map, description, price, facilities, similar properties carousel |
| `/about` | `(public)/about/` | About page |
| `/contact` | `(public)/contact/` | Contact page |
| `/articles` | `(public)/articles/page.js` | Article listing with categories, search, popular articles |
| `/career` | `(public)/career/` | Career page |
| `/earn-money` | `(public)/earn-money/` | Earn money page |
| `/order-home` | `(public)/order-home/` | Order home page |
| `/faq` | `(public)/faq/` | FAQ page |
| `/properties/[slug]` | `(public)/[slug]/page.js` | Dynamic slug page |
| `/division/[slug]` | `(public)/division/` | Division landing |
| `/district/[slug]` | `(public)/district/` | District landing |
| `/area/[slug]` | `(public)/area/` | Area landing |

### 2.2 Auth Pages

| Route | File | Description |
|-------|------|-------------|
| `/login` | `(auth)/login/page.js` | Login: MaterialM card, social login (Google, Facebook), email/password |
| `/register` | `(auth)/register/page.js` | Register: MaterialM card, name/email/phone/password/confirm |

### 2.3 User Dashboard Pages

| Route | File | Description |
|-------|------|-------------|
| `/user/dashboard` | `user/dashboard/page.js` | User dashboard: role switcher, balance card, quick action cards |
| `/user/profile` | `user/profile/page.js` | Profile: avatar, tabs (profile/security), form fields, password change |
| `/user/credits` | `user/credits/page.js` | Credits: balance card with earned/spent, purchase packages, transaction history table |
| `/user/my-listings` | `user/my-listings/page.js` | Listings table: stats pills, filter bar, table with action buttons |
| `/user/my-listings/new` | `user/my-listings/new/page.js` | Property posting form |
| `/user/my-listings/[id]/edit` | `user/my-listings/[id]/edit/page.js` | Property edit form |
| `/user/saved-properties` | `user/saved-properties/page.js` | Saved properties grid: empty state with CTA, remove button |
| `/user/my-inquiries` | `user/my-inquiries/page.js` | Inquiry list |
| `/user/credits/history` | (implied) | Credit history |

### 2.4 Admin Dashboard Pages

| Route | File | Description |
|-------|------|-------------|
| `/dashboard` | `dashboard/page.js` | Admin overview |
| `/dashboard/listings` | `dashboard/listings/page.js` | All listings management |
| `/dashboard/listings/new` | `dashboard/listings/new/page.js` | Add new listing |
| `/dashboard/users` | `dashboard/users/page.js` | User management |
| `/dashboard/locations/*` | `dashboard/locations/` | Division/district/area management |
| `/dashboard/content/*` | `dashboard/content/` | Articles, pages, FAQ, testimonials, news |
| `/dashboard/finance/*` | `dashboard/finance/` | Transactions |
| `/dashboard/inquiries` | `dashboard/inquiries/page.js` | Inquiry management |
| `/dashboard/ecommerce` | `dashboard/ecommerce/` | E-commerce |
| `/dashboard/crm` | `dashboard/crm/` | CRM |
| `/dashboard/help` | `dashboard/help/page.js` | Help center |
| `/dashboard/messages` | `dashboard/messages/page.js` | Messages |
| `/dashboard/notes` | `dashboard/notes/page.js` | Notes |
| `/dashboard/kanban` | `dashboard/kanban/page.js` | Kanban board |
| `/dashboard/calendar` | `dashboard/calendar/page.js` | Calendar |
| `/dashboard/settings` | `dashboard/settings/page.js` | Settings |

### 2.5 Layout Components

| File | Purpose |
|------|---------|
| `components/Navbar.js` | Two-row navbar: branding+actions row, nav links row, category bar, search modal |
| `components/Footer.js` | Footer: popular pages links, payment partners, copyright |
| `components/Sidebar.js` | Dashboard sidebar with navigation groups |
| `components/Topbar.js` | Dashboard topbar with search, notifications, profile dropdown |
| `components/BarikoiMap.jsx` | Map component using Barikoi API |
| `components/ProtectedRoute.js` | Auth guard for protected routes |
| `components/Skeleton.js` | Loading skeletons (PropertyCard, PropertyList, PropertyDetail, Dashboard) |
| `components/Breadcrumb.js` | Breadcrumb component |
| `components/Loader.js` | Full-page loader |

---

## 3. Next.js Reusable Components

### 3.1 PropertyCard (Homepage)
Used on homepage for featured listings:
- `bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100 group flex flex-col h-full`
- Image: `h-32` with `group-hover:scale-105 transition-transform`
- Price badge: `bg-primary/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm`
- Title: `font-bold text-[11px] text-dark line-clamp-2`
- Location: `text-gray-400 text-[10px]` with `place` icon
- Specs: bed/bath/area with `text-[10px]`

### 3.2 PropertyCard (Listings page)
More detailed card with 4-column grid:
- `bg-white rounded-[1.5rem] overflow-hidden shadow-sm hover:shadow-lg border border-gray-100`
- Featured badge: `bg-yellow-500 text-white text-[9px] font-bold`
- Price overlay: `bg-white/95 backdrop-blur px-2.5 py-1 rounded-lg`
- Save button: `absolute top-3 right-3 w-7 h-7 rounded-full`
- Specs: `bg-gray-50 px-1.5 py-0.5 rounded` badges

### 3.3 Search Modal (Navbar)
- Full-screen modal with backdrop blur
- Two-column layout: search keyword, category pills, location search, cascading dropdowns (division/district/area)
- Bottom bar with Clear All and Search button

### 3.4 Dashboard Stats Card
- `bg-white rounded-3xl p-6 shadow-material border border-gray-100`
- Icon circle, value, label
- Grid of 3-4 stat cards

### 3.5 Empty States
- Centered icon `text-6xl`, descriptive text, CTA button
- Example: "No saved homes yet" with heart icon and "Browse Properties" CTA

### 3.6 Loading States
- Skeletons with `animate-pulse`
- Full-page spinners for dashboard sections

---

## 4. Next.js Responsive Behavior

### 4.1 Breakpoints
- `md` (768px): Tablet - shows desktop nav, hides mobile menu button
- `lg` (1024px): Desktop - 3-4 column grids
- `xl` (1280px): 4-5 column grids
- `2xl` (1536px): 5-column grids for property listings

### 4.2 Mobile Patterns
- Mobile menu: hamburger icon expands to full screen overlay
- Category bar: horizontal scrollable with `scrollbar-hide`
- Property grid: `grid-cols-1 sm:grid-cols-2` on mobile/tablet
- Navbar: collapses to single-row with hamburger menu, category bar becomes sticky
- Search modal: full-screen on mobile

### 4.3 Tablet Patterns
- Property grid: `sm:grid-cols-2` to `lg:grid-cols-3`
- Stats: `grid-cols-2 md:grid-cols-4`
- Two-column content on property detail becomes single column

### 4.4 Desktop Patterns
- Property grid: up to `lg:grid-cols-5` (5 columns)
- Sidebar + Topbar for dashboards (sidebar fixed at `w-64`, topbar at `h-16`)
- Main content: `md:ml-64 mt-16 p-6`

---

## 5. Next.js Navigation

### 5.1 Public Navbar
- **Row 1:** Logo + Right actions (Add Property, Order Home, Profile, Login/Register)
- **Row 2:** Nav links (Home, Property List with dropdown, Earn Money, Article, Career, Order Home) + Search button
- **Category bar:** Sticky, horizontal scrollable category pills
- **Search modal:** Triggered by Search button, contains keyword search, category selection, location search with autocomplete, cascading division→district→area dropdowns

### 5.2 User Dashboard Sidebar
- Collapsible sidebar (`md:translate-x-0` always visible on desktop)
- Navigation groups: "My Account" (Dashboard, Profile, Credits, Listings, Saved Homes), "Support" (Help)
- Mobile toggle button at bottom-right
- User profile at bottom with avatar, name, email

### 5.3 User Dashboard Topbar
- Search bar (top-left)
- Notifications dropdown with unread indicators
- Messages link
- Profile dropdown (avatar, name, My Profile, Settings, Help, Logout)

---

## 6. Next.js Forms

### 6.1 Auth Forms (Login/Register)
- MaterialM card styling: `card shadow-material-lg`
- Input groups with `label` + `input` classes
- Social login buttons (Google, Facebook)
- Divider with "or sign in with"
- Error messages inside cards: `bg-danger-light border border-danger text-danger`

### 6.2 Property Search Filters
- Sticky filter bar at top of properties page
- Rounded-full inputs: `px-3 py-2 border-gray-200 rounded-full text-xs`
- Advanced filters panel (conditionally rendered)
- Active filter chips with removal buttons
- Sort dropdown, view mode toggle, clear all

### 6.3 Property Posting Form
- Uses `user/my-listings/new/page.js` (not yet fully inspected)
- Expected: cascading location dropdowns, image upload, form fields

### 6.4 Contact Unlock
- Credit-gated contact view: "Viewing contact details costs 20 credits"
- Confirm dialog before unlocking
- After unlock: shows phone number with `tel:` link

---

## 7. Next.js Property UI

### 7.1 Property Card (Homepage)
See section 3.1 above

### 7.2 Property Card (Listings)
See section 3.2 above

### 7.3 Property Detail Page Structure
1. **Breadcrumb:** Home > Property list > [Division] > [District] > [Area]
2. **Action buttons:** Save Property, Report Property
3. **Main title:** `text-xl font-bold text-gray-900`
4. **Two-column layout:**
   - **Left column (2/3):**
     - Image gallery (main image + thumbnail strip)
     - Basic information (bed, bath, balcony, gender)
     - Property details table (ID, updated, status, availability, category, type, floor, furnishing)
     - Contact section (login required prompt OR contact card with phone/chat buttons)
     - Location information table
     - Summary/Description
     - Price display
     - Facilities list with check icons
     - Map embed (if lat/lon available)
   - **Right column (1/3, sticky):**
     - Price card (sticky)
     - Contact buttons (View Contact Number, Chat)
     - Share buttons (Facebook, WhatsApp, Twitter, Copy)
     - Send Enquiry form (name, phone, message)
     - Login required card (if not authenticated)
5. **Similar properties:** Horizontal scrollable carousel below

---

## 8. Next.js Dashboard UI

### 8.1 User Dashboard
- Welcome message with user name
- Role switcher (Owner/Tenant) with gradient button cards
- Quick action cards (Find Property, Saved Homes, Post New Ad)
- Credits balance overview

### 8.2 Credits Page
- Gradient balance card with earned/spent breakdown
- Purchase packages grid (4 columns on desktop)
  - Popular package badge
  - Price, credits, bonus breakdown
  - Purchase button
- Transaction history table with status badges
- Empty state: animated icon, "No transactions found"

### 8.3 My Listings Page
- Header with "Post New Listing" CTA
- Stats pills (Total, Active, Inactive)
- Filter bar (status filter buttons)
- Table view with:
  - Property image thumbnail
  - Title and location
  - Category badge
  - Price with listing_type
  - Status badge with dot indicator
  - View count
  - Posted date
  - Action buttons (view, edit, toggle status, delete)

### 8.4 Saved Properties Page
- Header with description
- Loading state with spinner
- Empty state: heart icon, "No saved homes yet", CTA to browse
- Grid of saved property cards with:
  - Image with category and price badges
  - Title, location
  - Specs (bed, bath, sqft)
  - Saved date
  - "View Details" button
  - Remove button

### 8.5 Profile Page
- Avatar with verification badge
- Tab navigation (Profile, Security)
- Profile form: name, email, phone, role, bio
- Security form: current password, new password, confirm
- Two-pane layout using grid

---

## 9. Next.js Search/Filter UI

### 9.1 Homepage Hero Search
- Two-column layout: search form + hero image
- Search form: rounded-full, `bg-white` with shadow
- Input: "Search by location..." with autocomplete dropdown
- Search button: `bg-primary rounded-full`

### 9.2 Properties Page Filters
- Sticky filter bar at top (`z-30`)
- Search input with `search` icon
- Cascading dropdowns: Division, Location (area), Category
- Price range inputs (Min / Max)
- Bedrooms dropdown (1+, 2+, 3+, 4+, 5+)
- Advanced Filters toggle (Bathrooms, Furnishing)
- Sort dropdown (Newest, Oldest, Price Low-High, Price High-Low, Popular)
- View mode toggle (Grid, List, Map)
- Active filter chips with removal
- Clear All button

### 9.3 Map View
- Split layout: map on right (sticky), listings on left
- BarikoiMap component with markers
- Click marker navigates to property detail

---

## 10. Next.js Authentication UI

### 10.1 Login Page
- Centered card on light background (`bg-light`)
- Logo: `w-16 h-16 bg-primary rounded-2xl` with "T" text
- Welcome heading: "Welcome to ToLetKoi", subtitle "Your Admin Dashboard"
- Social login buttons (Google, Facebook)
- Divider with "or sign in with"
- Form fields: Email, Password
- Remember me checkbox + Forgot Password link
- Submit button: `btn btn-primary w-full`
- Footer: "New to ToLetKoi? Create an account" link

### 10.2 Register Page
- Same card style as login
- Header: "Join ToLetKoi", "Create your account to start listing"
- Form fields: Name, Email, Phone (required), Password, Confirm Password
- Submit button: `btn btn-primary w-full`
- Footer: "Already have an account? Sign In"

---

## 11. Next.js Admin UI

### 11.1 Admin Layout
- Sidebar (64px wide) with navigation groups
- Topbar with search, notifications, profile dropdown
- Main content area with `p-6` padding
- ProtectedRoute wrapper for auth

### 11.2 Admin Navigation (Sidebar)
- Overview (Dashboard)
- Users (All Users)
- Listings (All Listings, Add New)
- Locations (Divisions, Districts, Areas)
- Content (Articles, Pages, FAQ, Testimonials, News)
- Finance (Transactions)
- Engagement (Inquiries)
- System (Settings)

---

## 12. Next.js Mobile UX

### 12.1 Mobile Navigation
- Hamburger menu icon toggles mobile menu overlay
- Mobile menu: vertical list of links
- Bottom-right floating action button for menu toggle on desktop hidden

### 12.2 Mobile Property Cards
- Single column on mobile (`grid-cols-1`)
- Image at top, compact info below
- Touch-friendly action buttons (44px minimum touch targets)
- Save button as circular floating action button

### 12.3 Mobile Dashboard
- Sidebar collapses to hamburger menu
- Topbar remains fixed
- Main content shifts with `md:ml-64`
- Stat cards stack vertically

---

## 13. Next.js Accessibility

- Material Icons used with text labels
- Focus states: `focus:outline-none focus:ring-2 focus:ring-primary`
- ARIA labels on icon-only buttons: `aria-label="List view"`, `aria-label="Map view"`
- Semantic HTML5 (header, nav, main, section, footer)
- Form labels associated with inputs
- Keyboard navigation for dropdowns and modals

---

## 14. Next.js Loading/Error/Empty States

### 14.1 Loading States
- **Skeletons:** `animate-pulse` with gray placeholder shapes
  - PropertyCardSkeleton: gray blocks for image, title, specs
  - PropertyListSkeleton: horizontal layout skeleton
  - PropertyDetailSkeleton: full page layout skeleton
  - DashboardSkeleton: stat cards + list skeleton
- **Spinners:** SVG circular spinner (`animate-spin`)
- **Full-page:** "Loading properties...", "Loading details..."

### 14.2 Error States
- Error message cards with `bg-danger-light border border-danger text-danger`
- Form-level error display
- Toasts for async errors (via `useToast` context)

### 14.3 Empty States
- **Properties:** "No properties found matching your filters" + "Clear all filters" button
- **Saved:** Heart icon + "No saved homes yet" + "Browse Properties" CTA
- **My Listings:** "No listings yet" + "Post Your First Listing" CTA
- **Transactions:** "No transactions found" with icon
- **Location search:** "No locations found"

---

## 15. Next.js Strengths

1. **Consistent design system:** MaterialM palette applied uniformly across all pages
2. **Comprehensive state management:** AuthContext, ToastContext provide global state
3. **Rich interactivity:** Search modal with cascading dropdowns, autocomplete, debounced search
4. **Multiple property view modes:** Grid, list, and map views with toggle
5. **Thoughtful empty states:** Each empty state has appropriate icon, message, and CTA
6. **Loading skeletons:** Page-specific skeleton components for better perceived performance
7. **Credit-based gating:** Clear UX for premium features (contact unlock)
8. **Responsive grid system:** Up to 5 columns on xl screens for property listings
9. **Role switching:** User can switch between Owner and Tenant roles
10. **Map integration:** BarikoiMap for Bangladesh-specific location services

---

## 16. Next.js Weaknesses

1. **Admin label on user login:** Login page says "Your Admin Dashboard" which is confusing for end users
2. **Missing role selection on register:** No Owner/Tenant/Agent role choice during registration (contradicts role switching feature)
3. **Emoji in Topbar:** Uses emoji characters (🔍, 🔔, 💬) instead of Material Icons for some elements
4. **No dark mode:** Design system only supports light mode
5. **Image optimization:** Uses raw `<img>` tags instead of Next.js `<Image>` component (slower, no lazy loading optimization)
6. **Hardcoded API fallback URLs:** Unsplash image URLs as fallbacks
7. **Alert for chat:** Uses `window.alert()` for "Chat functionality coming soon" instead of toast
8. **Limited SEO per-page:** Relies on client-side data fetching which can hurt SEO

---

## 17. Required Laravel Adaptations

### 17.1 Design System Adaptations
| Next.js | Laravel Adaptation |
|---------|-------------------|
| Tailwind v3 with custom config | Tailwind v4 via `@import 'tailwindcss'` + `@theme` |
| Material Icons font | Add to `app.blade.php` `<head>` |
| `bg-[#f2f4f8]` page background | Add to CSS or use Tailwind class |
| `max-w-6xl` container | Update from `max-w-7xl` |
| `rounded-[1.5rem]`, `rounded-3xl` | Use Tailwind classes |
| `shadow-material`, `shadow-material-lg` | Add as CSS classes or Tailwind shadows |
| `font-sans: 'Plus Jakarta Sans'` | Update `@theme` and add Google Fonts link |
| `primary: #00a1ff` color | Add as CSS variable and Tailwind color |
| `bg-primary` shorthand | Add to Tailwind config or use utility classes |
| `animate-fade-in` | Add custom animation classes |
| `scrollbar-hide` | Add custom utility |

### 17.2 Component Architecture Adaptations
| Next.js | Laravel Equivalent |
|---------|-------------------|
| `components/Navbar.js` (React) | `components/layouts/nav.blade.php` (Blade) |
| `components/Footer.js` (React) | `components/layouts/footer.blade.php` (Blade) |
| `components/Sidebar.js` (React) | Create `components/layouts/sidebar.blade.php` |
| `components/Topbar.js` (React) | Create `components/layouts/topbar.blade.php` |
| `components/Skeleton.js` (React) | Blade component or inline HTML with `@class` |
| `components/BarikoiMap.jsx` | Use Leaflet.js (already loaded in app.blade.php) |
| React context (AuthContext) | Laravel session + `Auth::user()` |
| React context (ToastContext) | Laravel flash messages + Alpine.js |
| Client-side routing | Laravel routing + `<a>` tags |
| API calls (axios) | Server-side data via controllers + AJAX for interactions |

### 17.3 Interaction Adaptations
| Next.js Behavior | Laravel Approach |
|-----------------|-----------------|
| `useState`, `useEffect` for search | Alpine.js `x-data`, `x-init`, `x-on:input` |
| Search modal toggle | Alpine.js `x-data="{ open: false }"` |
| Cascading dropdowns | AJAX + Alpine.js or form submission |
| Favorites toggle | AJAX POST to route, Alpine.js for optimistic UI |
| Contact unlock | AJAX POST with credit check |
| Role switching | Form POST with confirmation |
| View mode toggle | URL parameter or session, render different Blade partials |
| Map view | Leaflet.js with AJAX data |

---

## 18. New ToLetKoi Improvements (Planned)

1. **Role selection on register:** Add Owner/Tenant/Agent selection (matching Laravel's existing pattern)
2. **Laravel-native form validation:** Use Form Requests for all input validation
3. **Server-side rendering:** Blade templates for SEO-friendly pages (better than Next.js client components)
4. **Laravel File Upload:** Native image upload handling with Intervention/Image
5. **Laravel Notifications:** Email notifications for inquiries, listing approvals
6. **Laravel Queues:** Background job processing for emails and image processing
7. **Laravel Policies:** Authorization policies for listing ownership
8. **Material Icons via CDN:** More reliable than emoji for accessible UI

---

## 19. Elements That Must Be Preserved

1. **Primary color:** `#00a1ff` (blue) — must replace Laravel's emerald green
2. **Material Icons:** Replace all emoji with Material Icons
3. **MaterialM card styling:** `rounded-3xl`, `shadow-material`, border patterns
4. **Property card design:** Image aspect ratio, price badge position, spec layout
5. **Homepage section order:** Hero → Category tabs → Property listings → Stats → Articles → App download → Testimonials → Purpose → News
6. **Search experience:** Hero search, search modal with cascading dropdowns
7. **Filter bar:** Sticky, rounded-full inputs, active filter chips
8. **Dashboard layout:** Sidebar + Topbar + main content with `md:ml-64 mt-16`
9. **Empty states:** Icon + message + CTA pattern
10. **Typography:** `text-dark` for headings, `text-gray-500` for secondary
11. **Border radius:** `rounded-xl` for cards, `rounded-3xl` for dashboard cards
12. **Max width:** `max-w-6xl` for public content
13. **Category badges:** Uppercase, `text-[10px] font-bold`, `text-primary`

---

## 20. Elements That Should Be Redesigned

1. **Navbar:** Currently uses emoji-based single-row nav; needs full rebuild with two-row + category bar + search modal
2. **Footer:** Currently dark 4-column; needs lighter MaterialM-style footer with popular pages
3. **Auth pages:** Currently uses emerald theme; needs MaterialM card styling with primary blue
4. **Homepage hero:** Currently uses emerald gradient; needs primary/5 to primary/10 gradient with Bengali text
5. **Property cards:** Currently uses emerald accents; needs primary blue badges and MaterialM card styling
6. **Dashboard cards:** Currently uses emoji-based stat cards; needs MaterialM icon circles with `rounded-3xl`
7. **Forms:** Currently uses emerald focus rings; needs primary (`#00a1ff`) focus rings
8. **Buttons:** Currently uses `bg-emerald-600`; needs `bg-primary` (#00a1ff) for primary actions