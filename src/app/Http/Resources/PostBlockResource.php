<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\PostBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PostBlock
 */
class PostBlockResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'sort_order' => $this->sort_order,
            'body' => $this->body,
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'caption' => $this->caption,
        ];
    }
}
