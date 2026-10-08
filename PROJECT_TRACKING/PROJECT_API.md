# ToLetKoi - API Documentation
**Last Updated:** 2026-09-16
## Barikoi Map Proxy Routes (verified live 2026-09-16)

All routes defined in `routes/web.php` under the `api/maps/` prefix and handled by
`App\Http\Controllers\BarikoiMapController`, which proxies `BarikoiService`
(caching: 5 min autocomplete, 10 min reverse geocode). Rate limited: 120 requests per minute.

### Upstream Barikoi endpoints (corrected 2026-09-16)

| Item | Value |
|---|---|
| Host | `https://barikoi.xyz/v2/api` |
| Credential parameter | `api_key` |
| Autocomplete | `GET /search/autocomplete/place?api_key=&q=` |
| Reverse geocode | `GET /search/reverse/geocode?api_key=&longitude=&latitude=` |
| Coordinate order | longitude first, then latitude |

> Three earlier mistakes broke every request: host `barikoi.com` (marketing site), parameter
> `key`, and reverse path `/search/reverse`. See ISSUE-003.
>
> Forward geocoding has no dedicated v2 endpoint, so `geocode()` uses autocomplete and
> returns the best match.

### Response shapes
All endpoints return JSON. When no API key is configured they return
`{"configured": false, "message": "..."}` with a 503, so the client can fall back to
OpenStreetMap rather than showing an error.

Normalised place object:
```json
{
  "id": 1763868,
  "address": "Assistant Commissioner (Land) Office, Gulshan 2, Dhaka",
  "area": "Gulshan", "city": "Dhaka", "district": "Dhaka", "division": "Dhaka",
  "post_code": "1212", "place_type": "office",
  "longitude": 90.4152, "latitude": 23.7937
}
```

### Error codes
| Code | Meaning |
|---|---|
| 200 | Success |
| 404 | No match for the address or coordinates |
| 422 | Validation failed (missing/short/out-of-range input) |
| 502 | Upstream Barikoi call failed |
| 503 | No API key configured |

---

### GET /api/maps/config
**Purpose:** Return Barikoi tile URL and API key for client-side map initialization
**Auth:** None (public)
**Response:**
```json
{
    "tile_url": "https://tile.barikoi.com/styles/osm-liberty/{z}/{x}/{y}.png?key=...",
    "api_key": "bkoi_..."
}
```
**Used by:** All Leaflet map instances (listing detail, map search page)

---

### GET /api/maps/autocomplete
**Purpose:** Location autocomplete search powered by Barikoi
**Auth:** None (public)
**Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| q | string | Yes | Search query (address/location name) |
**Response:**
```json
{
    "data": [
        {
            "address": "Mirpur 10, Dhaka",
            "lat": 23.8041,
            "lon": 90.3493,
            "place": "..."
        }
    ]
}
```
**Cached:** 5 minutes via `Cache::remember()`
**Used by:** Search modal in navigation bar

---

### GET /api/maps/geocode
**Purpose:** Convert address to latitude/longitude coordinates
**Auth:** None (public)
**Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| address | string | Yes | Full address string |
**Response:**
```json
{
    "lat": 23.8041,
    "lon": 90.3493,
    "address": "Mirpur 10, Dhaka"
}
```
**Cached:** 10 minutes

---

### GET /api/maps/reverse-geocode
**Purpose:** Convert latitude/longitude to address
**Auth:** None (public)
**Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| lat | float | Yes | Latitude |
| lon | float | Yes | Longitude |
**Response:**
```json
{
    "address": "Mirpur 10, Dhaka, Bangladesh",
    "lat": 23.8041,
    "lon": 90.3493
}
```
**Cached:** 10 minutes

---

## Internal AJAX Routes (Web)

All routes defined in `routes/web.php`, returning JSON for frontend AJAX calls.

---

### GET /api/search
**Purpose:** Search listings with filters
**Auth:** None (public)
**Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| q | string | No | Search keywords |
| listing_type | string | No | rent, sale |
| property_type_id | int | No | Property type ID |
| division_id | int | No | Division ID |
| district_id | int | No | District ID |
| area_id | int | No | Area ID |
| min_price | int | No | Minimum price |
| max_price | int | No | Maximum price |
| bedrooms | int | No | Number of bedrooms |
| bathrooms | int | No | Number of bathrooms |
| sort | string | No | price_asc, price_desc, newest, oldest |
| page | int | No | Pagination page |
**Response:** JSON with `data` array of listings, `links` for pagination

---

### GET /api/areas
**Purpose:** List areas for dropdown
**Auth:** None
**Response:** JSON array of {id, name, slug}

---

### GET /api/districts/{divisionId}
**Purpose:** Get districts for a division (cascading dropdown)
**Auth:** None
**Response:** JSON array of {id, name, slug}

---

### GET /api/areas/{districtId}
**Purpose:** Get areas for a district (cascading dropdown)
**Auth:** None
**Response:** JSON array of {id, name, slug}

---

### POST /api/favorites/{listing}
**Purpose:** Toggle favorite on a listing
**Auth:** Required
**Headers:** X-CSRF-TOKEN
**Response:**
```json
{
  "favorited": true,
  "count": 5
}
```

---

### POST /api/listings/{listing}/unlock-contact
**Purpose:** Unlock contact info for a listing (costs credits)
**Auth:** Required
**Headers:** X-CSRF-TOKEN
**Response:**
```json
{
  "success": true,
  "phone": "+880...",
  "email": "owner@example.com",
  "credits_remaining": 45
}
```
**Errors:**
- 401: Unauthenticated
- 422: Insufficient credits

---

## Public Web Routes

| Method | URL | Controller | Purpose |
|--------|-----|-----------|---------|
| GET | / | HomeController@index | Homepage |
| GET | /search | SearchController@index | Search page |
| GET | /search/map | MapSearchController@index | Map search |
| GET | /listings | ListingController@index | All listings |
| GET | /listing/{district:slug}/{area:slug}/{propertyType:slug}/{propertySubtype:slug}/{listing:slug} | ListingController@show | Listing detail |
| GET | /division/{slug} | HomeController@division | Division page |
| GET | /division/{d}/district/{s} | HomeController@district | District page |
| GET | /division/{d}/district/{d}/area/{s} | HomeController@area | Area page |
| GET | /property-type/{slug} | HomeController@propertyType | Property type page |
| GET | /page/{slug} | PageController@show | CMS page |
| GET | /pricing | PageController@pricing | Pricing page |
| GET | /about | PageController@about | About page |
| GET | /faq | PageController@faq | FAQ page |
| GET | /contact | ContactController@show | Contact page |
| POST | /contact | ContactController@store | Submit contact form |
| GET | /sitemap.xml | Closure | Dynamic sitemap |

## Auth Routes

| Method | URL | Controller | Purpose |
|--------|-----|-----------|---------|
| GET | /register | RegisterController@show | Registration form |
| POST | /register | RegisterController@register | Process registration |
| GET | /login | LoginController@show | Login form |
| POST | /login | LoginController@login | Process login |
| POST | /logout | LoginController@logout | Logout |
| GET | /forgot-password | ForgotPasswordController@show | Password reset request |
| POST | /forgot-password | ForgotPasswordController@send | Send reset email |
| GET | /reset-password/{token} | ResetPasswordController@show | Reset form |
| POST | /reset-password | ResetPasswordController@reset | Process reset |
| GET | /login/{provider} | SocialLoginController@redirect | Social OAuth redirect |
| GET | /login/{provider}/callback | SocialLoginController@callback | Social OAuth callback |

## Dashboard Routes (Auth Required)

| Method | URL | Controller | Purpose |
|--------|-----|-----------|---------|
| GET | /dashboard | DashboardController@index | Dashboard home |
| GET | /dashboard/profile | ProfileController@edit | Edit profile |
| PUT | /dashboard/profile | ProfileController@update | Update profile |
| PUT | /dashboard/profile/preferences | ProfileController@updatePreferences | Update preferences |
| GET | /dashboard/listings | ListingManageController@index | My listings |
| GET | /dashboard/listings/create | ListingManageController@create | Create listing form |
| POST | /dashboard/listings | ListingManageController@store | Store new listing |
| GET | /dashboard/listings/{listing}/edit | ListingManageController@edit | Edit listing form |
| PUT | /dashboard/listings/{listing} | ListingManageController@update | Update listing |
| DELETE | /dashboard/listings/{listing} | ListingManageController@destroy | Delete listing |
| POST | /dashboard/listings/{listing}/duplicate | ListingManageController@duplicate | Duplicate listing |
| GET | /dashboard/favorites | FavoriteController@index | My favorites |
| POST | /dashboard/favorites/{listing} | FavoriteController@toggle | Toggle favorite |
| DELETE | /dashboard/favorites/{listing} | FavoriteController@destroy | Remove favorite |
| GET | /dashboard/saved-searches | SavedSearchController@index | Saved searches |
| POST | /dashboard/saved-searches | SavedSearchController@store | Save search |
| DELETE | /dashboard/saved-searches/{id} | SavedSearchController@destroy | Delete saved search |
| GET | /dashboard/credits | CreditController@index | Credit wallet |
| GET | /dashboard/credits/history | CreditController@history | Transaction history |
| POST | /dashboard/credits/purchase | CreditController@purchase | Buy credits |
| GET | /dashboard/subscription | SubscriptionController@index | Subscription status |
| POST | /dashboard/subscription/subscribe | SubscriptionController@subscribe | Subscribe to plan |
| POST | /dashboard/subscription/cancel | SubscriptionController@cancel | Cancel subscription |
| GET | /dashboard/koifind | KoiFindController@index | KoiFind requests |
| POST | /dashboard/koifind | KoiFindController@store | Create KoiFind request |
| GET | /dashboard/koifind/{id} | KoiFindController@show | KoiFind detail |
| GET | /dashboard/verification | VerificationController@index | Verification status |
| POST | /dashboard/verification/submit | VerificationController@submit | Submit verification |
