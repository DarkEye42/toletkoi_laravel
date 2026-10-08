@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' - ToLetKoi')
@section('description', $post->meta_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 155)))

@push('styles')
    <style>
        .article-body {
            line-height: 1.8;
            color: var(--color-text-muted);
        }

        .article-body h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 1.75rem 0 0.75rem;
            color: var(--color-text-dark);
        }

        .article-body h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 1.5rem 0 0.5rem;
            color: var(--color-text-dark);
        }

        .article-body p {
            margin-bottom: 1rem;
        }

        .article-body ul,
        .article-body ol {
            margin: 1rem 0 1rem 1.25rem;
        }

        .article-body li {
            margin-bottom: 0.4rem;
        }

        .article-body a {
            color: var(--color-primary);
            text-decoration: underline;
        }

        .article-body img {
            border-radius: 0.75rem;
            margin: 1.25rem 0;
            max-width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-5">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <i class="material-icons text-[14px]">chevron_right</i>
            <a href="{{ route('blog.index') }}" class="hover:text-primary transition-colors">Blog</a>
            <i class="material-icons text-[14px]">chevron_right</i>
            <span class="text-dark font-medium truncate">{{ Str::limit($post->title, 40) }}</span>
        </nav>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Article --}}
            <article class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    @if ($post->featured_image)
                        <div class="h-64 md:h-80 bg-gray-100 overflow-hidden">
                            <img src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="{{ $post->title }}"
                                class="w-full h-full object-cover" loading="eager">
                        </div>
                    @endif
                    <div class="p-6 md:p-8">
                        @if ($post->category)
                            <span class="badge badge-primary mb-3">{{ $post->category }}</span>
                        @endif

                        <h1 class="text-2xl md:text-3xl font-extrabold text-dark leading-tight">{{ $post->title }}</h1>

                        <div
                            class="flex flex-wrap items-center gap-4 text-[11px] text-gray-400 mt-4 pb-5 border-b border-gray-100">
                            <span class="flex items-center gap-1.5">
                                <i class="material-icons text-[13px]">person</i>
                                {{ $post->user?->name ?? 'ToLetKoi' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="material-icons text-[13px]">schedule</i>
                                {{ $post->published_at?->format('M d, Y') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="material-icons text-[13px]">visibility</i>
                                {{ number_format($post->view_count) }} views
                            </span>
                        </div>

                        @if ($post->excerpt)
                            <p class="text-sm text-dark font-medium mt-5 mb-0 leading-relaxed">{{ $post->excerpt }}</p>
                        @endif

                        <div class="article-body mt-5 text-sm">
                            {!! $post->content !!}
                        </div>

                        @if (!empty($post->tags))
                            <div class="flex flex-wrap items-center gap-2 mt-8 pt-5 border-t border-gray-100">
                                <i class="material-icons text-gray-300 text-[16px]">local_offer</i>
                                @foreach ((array) $post->tags as $tag)
                                    <span class="badge badge-dark">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('blog.index') }}"
                        class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-primary-dark transition-colors">
                        <i class="material-icons text-[16px]">arrow_back</i> Back to all articles
                    </a>
                </div>
            </article>

            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    @if ($related->count())
                        <div class="bg-white rounded-2xl border border-gray-100 p-5">
                            <h3 class="text-sm font-bold text-dark mb-4">Related Articles</h3>
                            <div class="space-y-4">
                                @foreach ($related as $item)
                                    <a href="{{ route('blog.show', $item->slug) }}" class="group flex gap-3">
                                        <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                            @if ($item->featured_image)
                                                <img src="{{ Storage::disk('public')->url($item->featured_image) }}"
                                                    alt="{{ $item->title }}" class="w-full h-full object-cover"
                                                    loading="lazy">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-primary/5">
                                                    <i class="material-icons text-primary/40 text-[18px]">article</i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h4
                                                class="text-xs font-semibold text-dark leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                                {{ $item->title }}</h4>
                                            <span
                                                class="text-[10px] text-gray-400">{{ $item->published_at?->format('M d, Y') }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- CTA --}}
                    <div class="rounded-2xl bg-gradient-to-br from-primary to-secondary p-6 text-white">
                        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                            <i class="material-icons">home_work</i>
                        </div>
                        <h3 class="text-sm font-bold">Looking for a place?</h3>
                        <p class="text-xs text-white/80 mt-1.5 mb-4 leading-relaxed">Browse thousands of verified rentals
                            across Bangladesh.</p>
                        <a href="{{ route('search') }}"
                            class="block w-full rounded-xl bg-white text-primary px-4 py-2.5 text-center text-xs font-bold hover:bg-gray-50 transition-colors">
                            Search Properties
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
