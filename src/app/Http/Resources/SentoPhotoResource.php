<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SentoPhotoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sento_id' => $this->sento_id,
            'review_id' => $this->review_id,
            'url' => $this->url,
            'category' => $this->category?->value,
            'caption' => $this->caption,
        ];
    }
}
