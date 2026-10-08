<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use App\Models\PropertyType;
use App\Services\CreditService;
use App\Services\ImageService;
use App\Services\ListingService;
use App\Services\SearchService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CreditService::class);
        $this->app->singleton(ImageService::class);
        $this->app->singleton(ListingService::class);
        $this->app->singleton(SearchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // When served behind a dev tunnel / reverse proxy (devtunnels.ms,
        // ngrok, Cloudflare, ...), the browser loads the page over https on
        // the public host while the app itself runs on http://localhost:8000.
        // Without this, asset()/url()/@vite emit http://localhost:8000 URLs,
        // which browsers block as mixed-content (and cross-origin scripts
        // fail CORS). TrustProxies (see bootstrap/app.php) lets the request
        // carry the forwarded host/proto; force the URL generator to use it.
        if (! $this->app->runningInConsole()) {
            try {
                $request = request();
                $forwardedHost = $request->header('X-Forwarded-Host');
                $forwardedProto = $request->header('X-Forwarded-Proto');

                if ($forwardedHost) {
                    $forwardedHost = trim(explode(',', $forwardedHost)[0]);
                    $scheme = $forwardedProto ? trim(explode(',', $forwardedProto)[0]) : 'https';
                    $root = $scheme.'://'.$forwardedHost;
                    URL::forceRootUrl($root);
                    // Storage::disk('public')->url() does NOT use the URL
                    // generator — it prefixes config('filesystems.disks.public.url')
                    // which is built from APP_URL (http://localhost:8000).
                    // Override it per-request so images resolve to the tunnel host.
                    // NOTE: at boot time the request often has no forwarded headers
                    // yet (middleware runs later), so the authoritative fix lives in
                    // App\Http\Middleware\ForceTunnelRoot. This is a best-effort
                    // fallback only.
                    config(['filesystems.disks.public.url' => $root.'/storage']);
                    try {
                        app('filesystem')->forgetDisk(['public', 'local']);
                    } catch (\Throwable) {
                    }
                }

                if (($forwardedProto && str_contains($forwardedProto, 'https')) || $request->isSecure()) {
                    URL::forceScheme('https');
                }
            } catch (\Throwable) {
                // Never break booting if no request context is available.
            }
        }

        // Share the Barikoi map configuration with every view so maps can be
        // rendered server-side without an extra round trip, and can fall back
        // to OpenStreetMap tiles when no API key is configured.
        View::composer('*', function ($view) {
            $barikoi = app(\App\Services\BarikoiService::class);

            $view->with('barikoiConfig', [
                'configured' => $barikoi->isConfigured(),
                'api_key' => $barikoi->getApiKey(),
                'tile_url' => $barikoi->getTileUrl(),
                'center' => \App\Services\BarikoiService::defaultCenter(),
            ]);
        });

        // Share divisions and property types with the navigation component
        View::composer('components.layouts.nav', function ($view) {
            $view->with('navDivisions', Division::orderBy('name')->get());
            $view->with('navPropertyTypes', PropertyType::orderBy('name')->get());

            // Cascading Division -> District -> Area maps for the search modal.
            // Cached for an hour: these tables change rarely and the map is
            // shipped to the client on every page render.
            $cascade = \Illuminate\Support\Facades\Cache::remember('nav_geo_cascade', 3600, function () {
                $districts = District::orderBy('name')->get(['id', 'name', 'division_id']);
                $areas = Area::orderBy('name')->get(['id', 'name', 'district_id']);

                return [
                    'divisionDistrictMap' => $districts
                        ->groupBy('division_id')
                        ->map(fn ($group) => $group->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values())
                        ->toArray(),
                    'districtAreaMap' => $areas
                        ->groupBy('district_id')
                        ->map(fn ($group) => $group->map(fn ($a) => ['id' => $a->id, 'name' => $a->name])->values())
                        ->toArray(),
                ];
            });

            $view->with('divisionDistrictMap', $cascade['divisionDistrictMap']);
            $view->with('districtAreaMap', $cascade['districtAreaMap']);
        });
    }
}
