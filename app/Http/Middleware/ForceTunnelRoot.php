<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fix mixed-content when the app runs on http://localhost:8000 but is
 * served publicly over https via a dev tunnel / reverse proxy
 * (devtunnels.ms, ngrok, Cloudflare, ...).
 *
 * asset()/url()/route() are fixed by URL::forceRootUrl()+forceScheme(),
 * but Storage::disk('public')->url() prefixes
 * config('filesystems.disks.public.url') which is built from APP_URL at
 * config-load time — so it must be overridden per-request AND the already
 * resolved disk forgotten, otherwise images keep emitting
 * http://localhost:8000/storage/... which browsers block as mixed-content.
 *
 * Must run AFTER TrustProxies (see bootstrap/app.php) so the forwarded
 * headers are already trusted. A response-level rewrite backs up the URL
 * generator for anything it does not cover.
 */
class ForceTunnelRoot
{
    public function handle(Request $request, Closure $next): Response
    {
        $forwardedHost = $this->firstForwarded($request->header('X-Forwarded-Host'));
        $forwardedProto = $this->firstForwarded($request->header('X-Forwarded-Proto'));

        // No proxy headers means a direct request (plain local development):
        // leave the URL generator alone so the app keeps using APP_URL
        // (http://localhost:8000) and nothing changes for local browsing.
        if (! $forwardedHost && ! $forwardedProto) {
            return $next($request);
        }

        $host = $forwardedHost ?: $request->getHost();

        // A TLS-terminating proxy (devtunnels.ms, ngrok, Cloudflare, ...)
        // forwards the request over plain http, so isSecure() and getScheme()
        // report "http" even though the browser is on https. Trusting only
        // X-Forwarded-Proto made every asset URL http:// and the browser
        // upgraded/blocked it as mixed content. Behind a proxy the public
        // endpoint is https unless the proxy explicitly says otherwise.
        $scheme = ($forwardedProto && ! str_contains($forwardedProto, 'https')) ? 'http' : 'https';

        $root = $scheme.'://'.$host;

        // asset()/url()/route()
        URL::forceRootUrl($root);
        URL::forceScheme($scheme);

        // Storage::disk('public')->url() does NOT go through the URL generator:
        // it prefixes config('filesystems.disks.public.url'), which is built
        // from APP_URL (http://localhost:8000) at config-load time.
        config(['filesystems.disks.public.url' => $root.'/storage']);
        $this->forgetPublicDisk();

        $response = $next($request);

        // Last-resort safety net: a cached Blade view, a hard-coded URL or a
        // package can still emit http://<public-host>/... after the fact.
        // Only rewrite when the app itself is not running on plain http
        // (local development), so nothing changes for localhost browsing.
        if ($scheme === 'https' && ! $this->isLocalHost($host)) {
            $this->upgradeInsecureUrls($response);
        }

        return $response;
    }

    /**
     * Take the first value of a possibly comma-separated proxy header.
     */
    protected function firstForwarded(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = trim(explode(',', $value)[0]);

        return $value === '' ? null : $value;
    }

    protected function isLocalHost(string $host): bool
    {
        $host = strtolower(explode(':', $host)[0]);

        return in_array($host, ['localhost', '127.0.0.1', '[::1]', '::1'], true)
            || str_ends_with($host, '.test');
    }

    /**
     * Rewrite insecure absolute URLs in HTML responses.
     *
     * Scoped deliberately: only src/href attributes and url(...) tokens on
     * http:// URLs are touched, so prose and data: URIs are left alone.
     */
    protected function upgradeInsecureUrls(Response $response): void
    {
        try {
            $contentType = (string) $response->headers->get('Content-Type');

            if (! str_contains($contentType, 'text/html')) {
                return;
            }

            $content = $response->getContent();

            if (! is_string($content) || $content === '' || ! str_contains($content, 'http://')) {
                return;
            }

            $upgraded = preg_replace_callback(
                '/((?:src|href|poster|action)\s*=\s*["\']|url\(\s*["\']?)http:\/\//i',
                static fn (array $m) => $m[1].'https://',
                $content
            );

            if (is_string($upgraded) && $upgraded !== $content) {
                $response->setContent($upgraded);
            }
        } catch (\Throwable) {
            // Never break a response over an asset URL fix.
        }
    }

    protected function forgetPublicDisk(): void
    {
        try {
            // FilesystemManager caches resolved disks; without forgetting,
            // Storage::disk('public') keeps the stale APP_URL-based base URL.
            app('filesystem')->forgetDisk(['public', 'local']);
        } catch (\Throwable) {
            try {
                Storage::forgetDisk('public');
            } catch (\Throwable) {
                // Never break the request over an asset URL fix.
            }
        }
    }
}
