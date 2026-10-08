<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    /**
     * Number of articles shown per page on the blog index.
     */
    protected const PER_PAGE = 9;

    /**
     * Show the paginated list of published articles.
     *
     * The homepage "Blog & Articles" section links here, so this route must
     * exist for those links to resolve.
     */
    public function index()
    {
        $posts = BlogPost::published()
            ->ordered()
            ->paginate(self::PER_PAGE);

        $featured = BlogPost::published()
            ->ordered()
            ->first();

        return view('blog.index', compact('posts', 'featured'));
    }

    /**
     * Show a single published article by slug.
     *
     * Drafts and future-dated posts are not reachable by their slug, and an
     * unknown slug returns a 404 rather than an empty page.
     */
    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment the view counter without touching updated_at.
        $post->increment('view_count');

        $related = BlogPost::published()
            ->ordered()
            ->whereKeyNot($post->getKey())
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
