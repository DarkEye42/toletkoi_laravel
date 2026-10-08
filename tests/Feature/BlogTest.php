<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The homepage "Blog & Articles" section links to the blog routes, so these
 * pages must resolve for those links to work.
 */
class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected function makePost(array $overrides = []): BlogPost
    {
        $user = User::factory()->create();

        return BlogPost::create(array_merge([
            'user_id' => $user->id,
            'title' => 'How to find a rental home',
            'slug' => 'how-to-find-a-rental-home',
            'content' => '<p>Helpful rental advice.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ], $overrides));
    }

    public function test_blog_index_loads(): void
    {
        $this->get('/blog')->assertStatus(200);
    }

    public function test_blog_index_shows_only_published_posts(): void
    {
        $this->makePost(['title' => 'Live article', 'slug' => 'live-article']);
        $this->makePost([
            'title' => 'Draft article',
            'slug' => 'draft-article',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Live article')
            ->assertDontSee('Draft article');
    }

    public function test_blog_show_page_loads_for_published_post(): void
    {
        $post = $this->makePost();

        $this->get('/blog/' . $post->slug)
            ->assertOk()
            ->assertSee($post->title);
    }

    public function test_blog_show_page_increments_view_count(): void
    {
        $post = $this->makePost();

        $this->assertSame(0, (int) $post->fresh()->view_count);

        $this->get('/blog/' . $post->slug)->assertOk();

        $this->assertSame(1, (int) $post->fresh()->view_count);
    }

    public function test_blog_show_returns_404_for_draft_post(): void
    {
        $post = $this->makePost([
            'slug' => 'hidden-draft',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/blog/' . $post->slug)->assertStatus(404);
    }

    public function test_blog_show_returns_404_for_unknown_slug(): void
    {
        $this->get('/blog/this-slug-does-not-exist')->assertStatus(404);
    }

    public function test_homepage_blog_links_resolve(): void
    {
        $post = $this->makePost(['title' => 'Featured rental tips', 'slug' => 'featured-rental-tips']);

        // The homepage renders a link to the post; following it must not 404.
        $this->get(route('blog.show', $post->slug))->assertOk();
        $this->get(route('blog.index'))->assertOk();
    }
}
