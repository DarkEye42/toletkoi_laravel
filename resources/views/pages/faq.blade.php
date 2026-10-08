@extends('layouts.app')

@section('title', 'FAQ - ToLetKoi')
@section('description', 'Frequently asked questions about ToLetKoi.com')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900">Frequently Asked Questions</h1>
        <p class="mt-4 text-lg text-gray-600">Find answers to common questions about ToLetKoi.</p>
    </div>

    <div class="mt-12 space-y-6">
        @php
        $faqs = [
            ['q' => 'How do I list my property on ToLetKoi?', 'a' => 'Simply register for a free account, click "List Property" from your dashboard, fill in the details, upload photos, and publish. Your listing will be live within minutes.'],
            ['q' => 'Is it free to list properties?', 'a' => 'Yes! Basic listings are completely free. You can upgrade to premium plans for better visibility, more photos, and featured placement.'],
            ['q' => 'How does KoiFind work?', 'a' => 'Post your property requirements (location, budget, type) and our network of verified agents will contact you with matching properties.'],
            ['q' => 'What is the contact unlock system?', 'a' => 'Property owners choose to show or hide contact details. To unlock a contact, you spend credits. You get free credits on signup and can purchase more.'],
            ['q' => 'How do I become a verified agent?', 'a' => 'Register as an agent, complete your profile, submit your professional documents for verification, and our team will review within 48 hours.'],
            ['q' => 'What payment methods do you accept?', 'a' => 'We support bKash, Nagad, Rocket, and traditional bank transfers for credit purchases and subscription plans.'],
            ['q' => 'Can I search for properties on a map?', 'a' => 'Yes! Use our interactive map search to explore properties in specific areas, draw custom search boundaries, and see results geographically.'],
            ['q' => 'How do I report a suspicious listing?', 'a' => 'Click the "Report" button on any listing page and select the reason. Our team investigates all reports within 24 hours.'],
        ];
        @endphp

        @foreach($faqs as $i => $faq)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">{{ $faq['q'] }}</h3>
                <p class="mt-3 text-gray-600 leading-relaxed">{{ $faq['a'] }}</p>
            </div>
        @endforeach

        @if(isset($pages) && $pages->count())
            @foreach($pages as $page)
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $page->title }}</h3>
                    <div class="mt-3 text-gray-600 leading-relaxed">{!! $page->body !!}</div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
