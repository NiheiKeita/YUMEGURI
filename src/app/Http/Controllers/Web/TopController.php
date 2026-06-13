<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class TopController extends Controller
{
    public function index(): Response
    {
        $users = User::withCount(['posts' => fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now())])
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'posts_count' => (int) $u->posts_count,
            ])
            ->all();

        $latestPosts = Post::with('user')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('visited_at')
            ->limit(12)
            ->get();

        return Inertia::render('Web/Top', [
            'users' => $users,
            'latestPosts' => $latestPosts->map(fn ($p) => (new PostResource($p))->resolve())->values()->all(),
        ]);
    }
}
