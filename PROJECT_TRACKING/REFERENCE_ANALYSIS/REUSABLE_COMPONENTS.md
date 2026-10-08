# ToLetKoi Reusable Components — From Both Reference Projects

## Backend Reusable Patterns

### API Response Format (from Project A)
```php
// Consistent JSON response structure
response()->json([
    'success' => true,
    'data' => $data,
    'message' => 'Success message',
], 200);

response()->json([
    'success' => false,
    'error' => 'Error message',
    'errors' => $validationErrors,
], 422);
```

### Rate Limiting (from Project A)
```php
// Different limits for different endpoint groups
Route::middleware('throttle:10,1')->group(function () {
    // Auth routes: 10 per minute
});

Route::middleware('throttle:100,1')->group(function () {
    // API routes: 100 per minute
});
```

### Soft Deletes Pattern (from Project A)
```php
// Model with soft deletes
class Listing extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}
```

### Credit Ledger Pattern (from Project A)
```php
// Immutable transaction record
class CreditTransaction extends Model
{
    // type: purchase, earn, spend, refund, bonus, admin_adjustment
    // amount: positive (credit) or negative (debit)
    // balance_after: snapshot of balance after transaction
    // reference_type: polymorphic (payment, listing, etc.)
    // reference_id: ID of related record
}
```

### Location Hierarchy Pattern (from both projects)
```php
// Division → District → Area
class Division extends Model
{
    public function districts() { return $this->hasMany(District::class); }
}

class District extends Model
{
    public function division() { return $this->belongsTo(Division::class); }
    public function areas() { return $this->hasMany(Area::class); }
}

class Area extends Model
{
    public function district() { return $this->belongsTo(District::class); }
}
```

### Property Categories (from Project A, 10 categories)
```php
// Category enum or table
enum PropertyCategory: string
{
    case Family = 'family';
    case Bachelor = 'bachelor';
    case Sublet = 'sublet';
    case Office = 'office';
    case Hostel = 'hostel';
    case Shop = 'shop';
    case Factory = 'factory';
    case ReadyShed = 'ready_shed';
    case OpenLand = 'open_land';
    case Apartment = 'apartment';
}
```

### Facility Flags (from Project B, 14 flags)
```php
// Boolean flags on listings or separate table
$table->boolean('has_parking')->default(false);
$table->boolean('has_lift')->default(false);
$table->boolean('has_generator')->default(false);
$table->boolean('has_gas')->default(false);
$table->boolean('electricity_included')->default(false);
$table->boolean('water_included')->default(false);
$table->boolean('has_security')->default(false);
$table->boolean('has_cctv')->default(false);
$table->boolean('has_wifi')->default(false);
$table->boolean('is_furnished')->default(false);
$table->boolean('has_ac')->default(false);
$table->boolean('has_washing_machine')->default(false);
$table->boolean('shared_kitchen')->default(false);
$table->boolean('attached_bathroom')->default(false);
```

### SEO Slug Generation (from Project A)
```php
// Auto-generate SEO-friendly slugs
use Illuminate\Support\Str;

function generateSlug($title, $id)
{
    $slug = Str::slug($title);
    $slug .= '-' . $id;
    return $slug;
}
// Example: "Family Flat in Mirpur 14" → "family-flat-in-mirpur-14-130650"
```

### Bilingual Content Pattern (from Project A)
```php
// EN/BN supported fields on models
$table->string('title');
$table->text('description');
$table->string('address');
```

### Image Upload Pattern (from both projects)
```php
// Upload + resize + WebP conversion
// Project A: Cloudinary
// Project B: GD Library
// Laravel: Intervention + Storage

// Store original + WebP version
$path = $request->file('image')->store('listings', 'public');
// Generate WebP version
\Image::make($file)->resize(1200, 900)->encode('webp', 85)->save($webpPath);
```

## Frontend Reusable Components (Blade)

### Property Card
```blade
{{-- Property listing card --}}
<div class="property-card">
    <img src="{{ asset('storage/' . $listing->primary_image) }}" alt="{{ $listing->title }}" loading="lasy">
    <div class="property-info">
        <h3>{{ $listing->title }}</h3>
        <p class="price">৳{{ number_format($listing->price) }}/{{ $listing->price_type }}</p>
        <p class="location">{{ $listing->area->name }}, {{ $listing->district->name }}</p>
        <div class="features">
            <span>{{ $listing->bedrooms }} Bed</span>
            <span>{{ $listing->bathrooms }} Bath</span>
            <span>{{ $listing->area_sqft }} sqft</span>
        </div>
    </div>
</div>
```

### Filter Sidebar
```blade
{{-- Property search filters --}}
<div class="filter-sidebar">
    <form>
        <select name="category">@foreach($categories as $cat) ... @endforeach</select>
        <select name="listing_type">...</select>
        <input type="number" name="price_min" placeholder="Min Price">
        <input type="number" name="price_max" placeholder="Max Price">
        <select name="bedrooms">...</select>
        <select name="area">...</select>
        <button type="submit">Search</button>
    </form>
</div>
```

### Stats Card
```blade
{{-- Dashboard stats card --}}
<div class="stats-card">
    <div class="stats-icon">
        <x-heroicon-o-users />
    </div>
    <div class="stats-content">
        <h3>{{ number_format($totalUsers) }}</h3>
        <p>Total Users</p>
    </div>
</div>
```

### Data Table
```blade
{{-- Sortable, filterable data table --}}
<table class="data-table">
    <thead>
        <tr>
            <th wire:click="sortBy('name')">Name</th>
            <th wire:click="sortBy('email')">Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
                <button wire:click="delete({{ $user->id }})">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

### Search Bar with Autocomplete
```blade
{{-- Location search with Barikoi autocomplete --}}
<div class="search-bar">
    <input type="text" id="location-search" placeholder="Search location...">
    <div id="location-suggestions" class="suggestions-dropdown"></div>
</div>

@push('scripts')
<script>
    // Barikoi autocomplete integration
    const searchInput = document.getElementById('location-search');
    searchInput.addEventListener('input', async (e) => {
        const query = e.target.value;
        const response = await fetch(`/api/v1/maps/autocomplete?q=${query}`);
        const data = await response.json();
        // Show suggestions
    });
</script>
@endpush
```

### Image Gallery
```blade
{{-- Property image gallery --}}
<div class="image-gallery">
    <div class="primary-image">
        <img src="{{ asset('storage/' . $listing->primary_image) }}" alt="{{ $listing->title }}">
    </div>
    <div class="thumbnail-grid">
        @foreach($listing->images as $image)
        <img src="{{ asset('storage/' . $image->path) }}" alt="Property image {{ $loop->iteration }}">
        @endforeach
    </div>
</div>
```

### Toast Notification
```blade
{{-- Flash message toast --}}
@if(session('success'))
<div class="toast toast-success" x-data="{ show: true }" x-show="show" x-transition>
    <p>{{ session('success') }}</p>
    <button @click="show = false">×</button>
</div>
@endif

@if(session('error'))
<div class="toast toast-error" x-data="{ show: true }" x-show="show" x-transition>
    <p>{{ session('error') }}</p>
    <button @click="show = false">×</button>
</div>
@endif
```

### Empty State
```blade
{{-- Empty state with illustration --}}
<div class="empty-state">
    <img src="{{ asset('images/empty-listings.svg') }}" alt="No listings">
    <h3>No Listings Found</h3>
    <p>You haven't created any listings yet.</p>
    <a href="{{ route('listings.create') }}" class="btn-primary">Create Listing</a>
</div>
```

### Loading Skeleton
```blade
{{-- Loading skeleton --}}
<div class="skeleton" wire:loading>
    <div class="skeleton-image"></div>
    <div class="skeleton-text"></div>
    <div class="skeleton-text short"></div>
</div>
```
