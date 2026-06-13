<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $place_name
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $body
 * @property Carbon $visited_at
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PostBlock> $blocks
 */
class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'title',
        'place_name',
        'lat',
        'lng',
        'body',
        'visited_at',
        'published_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'visited_at' => 'date',
        'published_at' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<PostBlock, $this> */
    public function blocks(): HasMany
    {
        return $this->hasMany(PostBlock::class)->orderBy('sort_order');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
