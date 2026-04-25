<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SentoReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sento_id' => $this->sento_id,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'visited_at' => $this->visited_at?->toDateString(),
            'rating' => $this->rating,
            'body' => $this->body,
            'has_sauna' => $this->has_sauna,
            'sauna_temp' => $this->sauna_temp,
            'has_mizuburo' => $this->has_mizuburo,
            'mizuburo_temp' => $this->mizuburo_temp,
            'bath_types' => $this->bath_types ?? [],
            'want_revisit' => $this->want_revisit,
            'crowding' => $this->crowding,
            'best_time' => $this->best_time,
            'photos' => SentoPhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
