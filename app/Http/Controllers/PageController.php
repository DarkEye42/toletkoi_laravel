<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\Listing;
use App\Models\SubscriptionPlan;
use App\Models\CreditPackage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show a CMS page by slug.
     */
    public function show($slug)
    {
        $page = CmsPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.cms', compact('page'));
    }

    /**
     * Show pricing page.
     */
    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price_monthly', 'asc')->get();
        $creditPackages = CreditPackage::where('is_active', true)->orderBy('price', 'asc')->get();

        return view('pages.pricing', compact('plans', 'creditPackages'));
    }

    /**
     * Show about page.
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Show FAQ page.
     */
    public function faq()
    {
        return view('pages.faq');
    }
}
