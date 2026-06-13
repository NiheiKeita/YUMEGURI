<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SentoPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SentoPhoto
 */
class SentoPhotoResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sento_id' => $this->sento_id,
            'review_id' => $this->review_id,
            'url' => $this->url,
            'category' => $this->category->value,
            'caption' => $this->caption,
        ];
    }
}
