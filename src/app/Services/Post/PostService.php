<?php

declare(strict_types=1);

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class PostService
{
    /**
     * @param array<string, mixed> $data
     * @param list<array<string, mixed>> $blocks
     */
    public function store(array $data, array $blocks, User $user): Post
    {
        return DB::transaction(function () use ($data, $blocks, $user): Post {
            /** @var Post $post */
            $post = Post::create([
                'user_id' => $user->id,
                ...$data,
            ]);

            $this->syncBlocks($post, $blocks);

            return $post->load('blocks');
        });
    }

    /**
     * @param array<string, mixed> $data
     * @param list<array<string, mixed>> $blocks
     */
    public function update(Post $post, array $data, array $blocks): Post
    {
        return DB::transaction(function () use ($post, $data, $blocks): Post {
            $post->fill($data);
            $post->save();

            $this->syncBlocks($post, $blocks);

            return $post->load('blocks');
        });
    }

    /**
     * @param list<array<string, mixed>> $blocks
     */
    private function syncBlocks(Post $post, array $blocks): void
    {
        $post->blocks()->delete();

        if (empty($blocks)) {
            return;
        }

        $records = [];
        foreach ($blocks as $i => $block) {
            $records[] = [
                'post_id' => $post->id,
                'type' => $block['type'],
                'sort_order' => $i,
                'body' => $block['body'] ?? null,
                'image_path' => $block['image_path'] ?? null,
                'caption' => $block['caption'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $post->blocks()->insert($records);
    }
}
