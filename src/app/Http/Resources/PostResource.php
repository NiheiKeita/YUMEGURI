<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'title' => $this->title,
            'place_name' => $this->place_name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'body' => $this->body,
            'visited_at' => $this->visited_at->toDateString(),
            'published_at' => $this->published_at?->toDateTimeString(),
        ];
    }
}
