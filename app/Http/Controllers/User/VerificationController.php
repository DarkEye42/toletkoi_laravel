<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Models\VerificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $verifications = Verification::where('user_id', $user->id)
            ->with('verificationType')
            ->latest()
            ->get();
        $verificationTypes = VerificationType::where('is_active', true)->get();

        return view('dashboard.verification', compact('verifications', 'verificationTypes'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'verification_type_id' => 'required|exists:verification_types,id',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $path = $request->file('document')->store('verifications', 'public');

        Verification::create([
            'user_id' => Auth::id(),
            'verification_type_id' => $request->verification_type_id,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Verification document submitted! It will be reviewed within 24-48 hours.');
    }
}
