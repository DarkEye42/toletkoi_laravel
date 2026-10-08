<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MapSearchController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ListingManageController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\User\SavedSearchController;
use App\Http\Controllers\User\CreditController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\KoiFindController;
use App\Http\Controllers\User\VerificationController;
use App\Http\Controllers\BarikoiMapController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest & Authenticated)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Property Search & Listings
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/map', [MapSearchController::class, 'index'])->name('search.map');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listing/{district:slug}/{area:slug}/{propertyType:slug}/{propertySubtype:slug}/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');

// Area-based pages (SEO)
Route::get('/division/{division:slug}', [HomeController::class, 'division'])->name('division.show');
Route::get('/division/{division:slug}/district/{district:slug}', [HomeController::class, 'district'])->name('district.show');
Route::get('/division/{division:slug}/district/{district:slug}/area/{area:slug}', [HomeController::class, 'area'])->name('area.show');

// Property type pages (SEO)
Route::get('/property-type/{propertyType:slug}', [HomeController::class, 'propertyType'])->name('property-type.show');

// Location search API (AJAX)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/api/locations/search', [HomeController::class, 'searchLocations'])->name('api.locations.search');
});

// Barikoi Maps API proxy (server-side so the key is never called directly from JS)
Route::middleware('throttle:120,1')->prefix('api/maps')->name('api.maps.')->group(function () {
    Route::get('/config', [BarikoiMapController::class, 'config'])->name('config');
    Route::get('/autocomplete', [BarikoiMapController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/geocode', [BarikoiMapController::class, 'geocode'])->name('geocode');
    Route::get('/reverse-geocode', [BarikoiMapController::class, 'reverseGeocode'])->name('reverse-geocode');
});

// Legal & Content Pages
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Blog / Articles (homepage links here)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// SEO: Sitemap
Route::get('/sitemap.xml', function () {
    $listings = \App\Models\Listing::where('status', 'active')->latest()->get();
    $propertyTypes = \App\Models\PropertyType::all();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $xml .= '<url><loc>' . url('/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';
    $xml .= '<url><loc>' . url('/pricing') . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>';
    $xml .= '<url><loc>' . url('/about') . '</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>';
    $xml .= '<url><loc>' . url('/contact') . '</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>';
    $xml .= '<url><loc>' . url('/faq') . '</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>';

    foreach ($listings as $listing) {
        $xml .= '<url><loc>' . $listing->getUrl() . '</loc><lastmod>' . $listing->updated_at->format('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
    }

    foreach ($propertyTypes as $type) {
        $xml .= '<url><loc>' . url('/property-type/' . $type->slug) . '</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Social Login
    Route::get('/login/{provider}', [SocialLoginController::class, 'redirect'])->name('social.redirect');
    Route::get('/login/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Dashboard Home
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');

    // Listing Management (owner-protected)
    Route::get('/listings', [ListingManageController::class, 'index'])->name('listings.index');
    Route::get('/listings/create', [ListingManageController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingManageController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingManageController::class, 'edit'])->name('listings.edit')->middleware('listing.owner');
    Route::put('/listings/{listing}', [ListingManageController::class, 'update'])->name('listings.update')->middleware('listing.owner');
    Route::delete('/listings/{listing}', [ListingManageController::class, 'destroy'])->name('listings.destroy')->middleware('listing.owner');
    Route::post('/listings/{listing}/duplicate', [ListingManageController::class, 'duplicate'])->name('listings.duplicate')->middleware('listing.owner');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{listing}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/favorites/{listing}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // Saved Searches
    Route::get('/saved-searches', [SavedSearchController::class, 'index'])->name('saved-searches.index');
    Route::post('/saved-searches', [SavedSearchController::class, 'store'])->name('saved-searches.store');
    Route::delete('/saved-searches/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('saved-searches.destroy');

    // Credit Wallet
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/history', [CreditController::class, 'history'])->name('credits.history');
    Route::post('/credits/purchase', [CreditController::class, 'purchase'])->name('credits.purchase');

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // KoiFind
    Route::get('/koifind', [KoiFindController::class, 'index'])->name('koifind.index');
    Route::post('/koifind', [KoiFindController::class, 'store'])->name('koifind.store');
    Route::get('/koifind/{koifindRequest}', [KoiFindController::class, 'show'])->name('koifind.show');

    // Verification
    Route::get('/verification', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verification/submit', [VerificationController::class, 'submit'])->name('verification.submit');
});

/*
|--------------------------------------------------------------------------
| API-like Routes (require auth, return JSON for AJAX)
|--------------------------------------------------------------------------
*/

// Contact Unlock (AJAX)
Route::post('/api/listings/{listing}/unlock-contact', [ListingController::class, 'unlockContact'])
    ->middleware(['auth', 'verified'])
    ->name('listings.unlock-contact');

// Favorite Toggle (AJAX)
Route::post('/api/favorites/{listing}', [FavoriteController::class, 'toggle'])
    ->middleware(['auth'])
    ->name('api.favorites.toggle');
// Search API (for AJAX/live search)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/api/search', [SearchController::class, 'apiSearch'])->name('api.search');
    Route::get('/api/search/ajax', [SearchController::class, 'apiSearch'])->name('ajax.search');
    Route::get('/api/areas', [SearchController::class, 'apiAreas'])->name('api.areas');
});

// Geography cascading dropdowns (AJAX)
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/api/divisions', function () {
        return response()->json(
            \App\Models\Division::orderBy('name')
                ->get(['id', 'name', 'name_bn', 'slug', 'listings_count', 'property_count'])
        );
    });

    Route::get('/api/districts/{divisionId}', function ($divisionId) {
        $districts = \App\Models\District::where('division_id', $divisionId)->orderBy('name')->get(['id', 'name', 'slug']);
        return response()->json($districts);
    });

    Route::get('/api/areas/{districtId}', function ($districtId) {
        $areas = \App\Models\Area::where('district_id', $districtId)->orderBy('name')->get(['id', 'name', 'slug']);
        return response()->json($areas);
    });
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes (Phase 03)
|--------------------------------------------------------------------------
| Protected by auth + role middleware. Only Admin and Super Admin may enter;
| the role check is enforced server-side so a normal user cannot reach these
| URLs even by guessing them.
*/

Route::middleware(['auth', 'role:Admin|Super Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Overview
        Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Listing moderation
        Route::get('/listings', [\App\Http\Controllers\Admin\AdminListingController::class, 'index'])
            ->name('listings.index');
        Route::get('/listings/{listing}', [\App\Http\Controllers\Admin\AdminListingController::class, 'show'])
            ->name('listings.show');
        Route::post('/listings/{listing}/approve', [\App\Http\Controllers\Admin\AdminListingController::class, 'approve'])
            ->name('listings.approve');
        Route::post('/listings/{listing}/reject', [\App\Http\Controllers\Admin\AdminListingController::class, 'reject'])
            ->name('listings.reject');
        Route::post('/listings/{listing}/feature', [\App\Http\Controllers\Admin\AdminListingController::class, 'toggleFeatured'])
            ->name('listings.feature');
        Route::delete('/listings/{listing}', [\App\Http\Controllers\Admin\AdminListingController::class, 'destroy'])
            ->name('listings.destroy');
    });
