@extends('layouts.app')

@section('title', 'Blog & Articles - ToLetKoi')
@section('description', 'Rental tips, market insights and property advice for tenants and landlords in Bangladesh.')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-dark">Blog &amp; Articles</h1>
            <p class="text-sm text-gray-400 mt-2">Tips and insights for property seekers and owners.</p>
        </div>

        {{-- Featured Article --}}
        @if ($featured && $posts->currentPage() === 1)
            <a href="{{ route('blog.show', $featured->slug) }}"
                class="group block bg-white rounded-2xl border border-gray-100 hover:border-primary/20 hover:shadow-lg transition-all overflow-hidden mb-8">
                <div class="grid md:grid-cols-2">
                    <div class="h-56 md:h-full bg-gray-100 overflow-hidden">
                        @if ($featured->featured_image)
                            <img src="{{ Storage::disk('public')->url($featured->featured_image) }}"
                                alt="{{ $featured->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="eager">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-secondary/5">
                                <i class="material-icons text-primary/30 text-5xl">article</i>
                            </div>
                        @endif
                    </div>
                    <div class="p-6 md:p-8 flex flex-col justify-center">
                        <span class="badge badge-primary self-start mb-3">Featured</span>
                        <h2 class="text-xl font-bold text-dark group-hover:text-primary transition-colors">
                            {{ $featured->title }}</h2>
                        <p class="text-sm text-muted mt-3 line-clamp-3">
                            {{ $featured->excerpt ?: Str::limit(strip_tags($featured->content), 160) }}
                        </p>
                        <div class="flex items-center gap-2 text-[11px] text-gray-400 mt-4">
                            <i class="material-icons text-[13px]">schedule</i>
                            {{ $featured->published_at?->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </a>
        @endif

        {{-- Article Grid --}}
        @if ($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}"
                        class="group bg-white rounded-2xl border border-gray-100 hover:border-primary/20 hover:shadow-lg transition-all overflow-hidden flex flex-col">
                        <div class="h-40 bg-gray-100 overflow-hidden">
                            @if ($post->featured_image)
                                <img src="{{ Storage::disk('public')->url($post->featured_image) }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-secondary/5">
                                    <i class="material-icons text-primary/30 text-4xl">article</i>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            @if ($post->category)
                                <span
                                    class="text-[10px] font-bold text-primary/70 uppercase tracking-wider mb-1.5">{{ $post->category }}</span>
                            @endif
                            <h3
                                class="font-bold text-sm text-dark leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                                {{ $post->title }}</h3>
                            <p class="text-xs text-muted line-clamp-2 mt-2">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <div class="mt-auto pt-3 flex items-center gap-3 text-[10px] text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="material-icons text-[12px]">schedule</i>
                                    {{ $post->published_at?->format('M d, Y') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="material-icons text-[12px]">visibility</i>
                                    {{ number_format($post->view_count) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="material-icons text-gray-300 text-3xl">article</i>
                </div>
                <h3 class="text-lg font-bold text-dark">No articles yet</h3>
                <p class="text-sm text-muted mt-1.5">Check back soon for rental tips and market insights.</p>
            </div>
        @endif
    </div>
@endsection
