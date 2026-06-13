<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\BlockType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $post_id
 * @property BlockType $type
 * @property int $sort_order
 * @property string|null $body
 * @property string|null $image_path
 * @property string|null $caption
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 * @property-read Post $post
 */
class PostBlock extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'post_id',
        'type',
        'sort_order',
        'body',
        'image_path',
        'caption',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'type' => BlockType::class,
        'sort_order' => 'integer',
    ];

    /** @var list<string> */
    protected $appends = ['image_url'];

    /** @return BelongsTo<Post, $this> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
