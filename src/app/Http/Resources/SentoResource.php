<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_kana' => $this->name_kana,
            'prefecture' => $this->prefecture,
            'city' => $this->city,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'phone' => $this->phone,
            'hours' => $this->hours,
            'closed_days' => $this->closed_days,
            'price' => $this->price,
            'source_url' => $this->source_url,
            'nearest_station' => $this->nearest_station,
            'walk_minutes' => $this->walk_minutes,
            'has_shampoo' => $this->has_shampoo,
            'has_soap' => $this->has_soap,
            'status' => $this->status?->value,
            'avg_rating' => $this->reviews_avg_rating ?? null,
            'review_count' => $this->reviews_count ?? null,
            'distance_km' => isset($this->distance_km) ? (float) $this->distance_km : null,
            'reviews' => SentoReviewResource::collection($this->whenLoaded('reviews')),
            'photos' => SentoPhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
