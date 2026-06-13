<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::with('user')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('visited_at')
            ->paginate(12);

        return Inertia::render('Web/Posts/Index', [
            'posts' => PostResource::collection($posts),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Web/Posts/Create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $post = $user->posts()->create($request->validated());

        return redirect()->route('web.posts.show', $post)
            ->with('status', '記事を投稿しました');
    }

    public function show(Post $post): Response
    {
        $post->load('user');

        return Inertia::render('Web/Posts/Show', [
            'post' => (new PostResource($post))->resolve(),
            'canEdit' => request()->user()?->id === $post->user_id,
        ]);
    }

    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('Web/Posts/Create', [
            'post' => (new PostResource($post))->resolve(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()->route('web.posts.show', $post)
            ->with('status', '記事を更新しました');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->route('web.posts.index')
            ->with('status', '記事を削除しました');
    }
}
