<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Post;

use App\Models\Post;
use App\Models\PostBlock;
use App\Models\User;
use App\Services\Post\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostService();
    }

    public function test_store_creates_post_with_blocks(): void
    {
        $user = User::factory()->create();

        $post = $this->service->store(
            [
                'title' => 'テスト記事',
                'place_name' => '浅草',
                'lat' => 35.7148,
                'lng' => 139.7967,
                'visited_at' => '2026-06-14',
                'published_at' => now()->toDateTimeString(),
            ],
            [
                ['type' => 'text', 'body' => '良い場所でした', 'image_path' => null, 'caption' => null],
                ['type' => 'image', 'body' => null, 'image_path' => 'images/posts/test.jpg', 'caption' => '外観'],
            ],
            $user,
        );

        $this->assertSame('テスト記事', $post->title);
        $this->assertSame($user->id, $post->user_id);
        $this->assertCount(2, $post->blocks);
        $this->assertSame(1, Post::count());
        $this->assertSame(2, PostBlock::count());
    }

    public function test_store_creates_post_without_blocks(): void
    {
        $user = User::factory()->create();

        $post = $this->service->store(
            ['title' => 'ブロックなし', 'visited_at' => '2026-06-14'],
            [],
            $user,
        );

        $this->assertSame('ブロックなし', $post->title);
        $this->assertCount(0, $post->blocks);
        $this->assertSame(0, PostBlock::count());
    }

    public function test_update_updates_post_and_syncs_blocks(): void
    {
        $user = User::factory()->create();
        $post = $this->service->store(
            ['title' => '旧タイトル', 'visited_at' => '2026-06-14'],
            [['type' => 'text', 'body' => '旧テキスト', 'image_path' => null, 'caption' => null]],
            $user,
        );

        $updated = $this->service->update(
            $post,
            ['title' => '新タイトル', 'visited_at' => '2026-06-14'],
            [
                ['type' => 'text', 'body' => '新テキスト1', 'image_path' => null, 'caption' => null],
                ['type' => 'text', 'body' => '新テキスト2', 'image_path' => null, 'caption' => null],
            ],
        );

        $this->assertSame('新タイトル', $updated->title);
        $this->assertCount(2, $updated->blocks);
        $this->assertSame(2, PostBlock::count());
        $this->assertSame('新テキスト1', $updated->blocks->first()->body);
    }

    public function test_update_clears_all_blocks_when_empty(): void
    {
        $user = User::factory()->create();
        $post = $this->service->store(
            ['title' => 'タイトル', 'visited_at' => '2026-06-14'],
            [['type' => 'text', 'body' => 'テキスト', 'image_path' => null, 'caption' => null]],
            $user,
        );

        $this->service->update($post, ['title' => 'タイトル', 'visited_at' => '2026-06-14'], []);

        $this->assertSame(0, PostBlock::count());
    }

    public function test_blocks_are_ordered_by_sort_order(): void
    {
        $user = User::factory()->create();
        $post = $this->service->store(
            ['title' => 'テスト', 'visited_at' => '2026-06-14'],
            [
                ['type' => 'text', 'body' => 'ブロック0', 'image_path' => null, 'caption' => null],
                ['type' => 'text', 'body' => 'ブロック1', 'image_path' => null, 'caption' => null],
                ['type' => 'text', 'body' => 'ブロック2', 'image_path' => null, 'caption' => null],
            ],
            $user,
        );

        $this->assertSame('ブロック0', $post->blocks[0]->body);
        $this->assertSame('ブロック1', $post->blocks[1]->body);
        $this->assertSame('ブロック2', $post->blocks[2]->body);
    }
}
