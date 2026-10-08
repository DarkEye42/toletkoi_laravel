@extends('layouts.dashboard')
@section('title', 'Verification')
@section('header', 'Account Verification ')

@section('content')
<div class="max-w-3xl space-y-8">
    {{-- Verification Status --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-dark">Verification Status</h3>
        @if($verifications->count())
            <div class="mt-4 space-y-3">
                @foreach($verifications as $v)
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3">
                        <div>
                            <p class="font-medium text-dark">{{ $v->verificationType?->name ?? 'Verification' }}</p>
                            <p class="text-xs text-muted">Submitted {{ $v->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                            {{ $v->status === 'approved' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $v->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : '' }}
                            {{ $v->status === 'rejected' ? 'bg-red-50 text-red-700' : '' }}">
                            {{ ucfirst($v->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-4 text-sm text-muted">No verification requests submitted yet.</p>
        @endif
    </div>

    {{-- Submit New --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-dark">Submit Verification Document</h3>
        <form action="{{ route('dashboard.verification.submit') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Verification Type *</label>
                <select name="verification_type_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select Type</option>
                    @foreach($verificationTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Upload Document * (JPG, PNG, or PDF, max 10MB)</label>
                <input type="file" name="document" required accept="image/*,.pdf"
                       class="mt-1 block w-full text-sm text-muted file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-emerald-100">
            </div>
            <button type="submit" class="rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white hover:bg-primary-dark transition">Submit for Review</button>
        </form>
    </div>
</div>
@endsection
