<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    // --- index ---

    public function test_index_renders_published_posts(): void
    {
        Post::factory()->count(3)->create(['published_at' => now()->subDay()]);
        Post::factory()->draft()->create();

        $this->get('/posts')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Posts/Index')
                ->has('posts.data', 3));
    }

    // --- create ---

    public function test_create_requires_auth(): void
    {
        $this->get('/posts/create')->assertRedirect();
    }

    public function test_create_renders_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/posts/create')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Web/Posts/Create'));
    }

    // --- store ---

    public function test_store_creates_post_with_body(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/posts', [
                'title' => 'テスト記事',
                'place_name' => '浅草',
                'lat' => 35.7148,
                'lng' => 139.7967,
                'visited_at' => '2026-06-14',
                'published_at' => '2026-06-14',
                'body' => '今日は**浅草**を散歩した。',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'title' => 'テスト記事',
            'user_id' => $user->id,
        ]);
        $this->assertSame('今日は**浅草**を散歩した。', Post::first()->body);
    }

    public function test_store_saves_as_draft_when_published_at_null(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/posts', [
                'title' => '下書き',
                'visited_at' => '2026-06-14',
                'published_at' => null,
            ])
            ->assertRedirect();

        $post = Post::first();
        $this->assertNull($post->published_at);
    }

    public function test_store_validates_required_title(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/posts', [
                'title' => '',
                'visited_at' => '2026-06-14',
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_store_requires_auth(): void
    {
        $this->post('/posts', ['title' => 'x', 'visited_at' => '2026-06-14'])
            ->assertRedirect();
    }

    // --- show ---

    public function test_show_renders_post(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subDay()]);

        $this->get("/posts/{$post->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Posts/Show')
                ->where('post.id', $post->id));
    }

    // --- edit ---

    public function test_edit_requires_auth(): void
    {
        $post = Post::factory()->create();
        $this->get("/posts/{$post->id}/edit")->assertRedirect();
    }

    public function test_owner_can_edit(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get("/posts/{$post->id}/edit")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Web/Posts/Create'));
    }

    public function test_other_user_cannot_edit(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get("/posts/{$post->id}/edit")
            ->assertForbidden();
    }

    // --- update ---

    public function test_owner_can_update(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'title' => '旧タイトル']);

        $this->actingAs($user)
            ->patch("/posts/{$post->id}", [
                'title' => '新タイトル',
                'visited_at' => '2026-06-14',
                'body' => '更新されたMarkdown本文。',
            ])
            ->assertRedirect("/posts/{$post->id}");

        $this->assertSame('新タイトル', $post->fresh()->title);
        $this->assertSame('更新されたMarkdown本文。', $post->fresh()->body);
    }

    public function test_other_user_cannot_update(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->patch("/posts/{$post->id}", ['title' => 'hack', 'visited_at' => '2026-06-14'])
            ->assertForbidden();
    }

    // --- destroy ---

    public function test_owner_can_delete(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete("/posts/{$post->id}")
            ->assertRedirect('/posts');

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_other_user_cannot_delete(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->delete("/posts/{$post->id}")
            ->assertForbidden();
    }
}
