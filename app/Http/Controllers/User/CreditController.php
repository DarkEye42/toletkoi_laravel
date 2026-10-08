<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CreditWallet;
use App\Models\CreditTransaction;
use App\Models\CreditPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function index()
    {
        $wallet = CreditWallet::where('user_id', Auth::id())->firstOrCreate(['user_id' => Auth::id()]);
        $packages = CreditPackage::where('is_active', true)->orderBy('price', 'asc')->get();
        $recentTransactions = CreditTransaction::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.credits', compact('wallet', 'packages', 'recentTransactions'));
    }

    public function history()
    {
        $transactions = CreditTransaction::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('dashboard.credits-history', compact('transactions'));
    }

    public function purchase(Request $request, CreditPackage $package)
    {
        if (!$package->is_active) {
            return back()->withErrors(['error' => 'This package is no longer available.']);
        }

        // In production: redirect to payment gateway (bKash, Nagad, SSLCommerz)
        // For now: simulate successful payment
        DB::beginTransaction();
        try {
            $wallet = CreditWallet::firstOrCreate(['user_id' => Auth::id()]);
            $wallet->credit($package->credits, "Purchased {$package->name} package", null, CreditTransaction::class, $package->id);

            DB::commit();

            return back()->with('success', "{$package->credits} credits added to your wallet!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Payment failed. Please try again.']);
        }
    }
}
