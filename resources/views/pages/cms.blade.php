@extends('layouts.app')

@section('title', $page->title . ' - ToLetKoi')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-4xl font-bold text-gray-900">{{ $page->title }}</h1>

    @if($page->meta_description)
        <p class="mt-4 text-lg text-gray-600">{{ $page->meta_description }}</p>
    @endif

    <div class="prose prose-lg prose-emerald mx-auto mt-8">
        {!! $page->body !!}
    </div>

    @if($page->updated_at)
        <p class="mt-8 text-sm text-gray-400 text-center">Last updated: {{ $page->updated_at->format('F j, Y') }}</p>
    @endif
</div>
@endsection
