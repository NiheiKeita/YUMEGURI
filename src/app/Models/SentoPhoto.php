<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\PhotoCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $sento_id
 * @property int $user_id
 * @property int|null $review_id
 * @property string $path
 * @property PhotoCategory $category
 * @property string|null $caption
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string|null $url
 * @property-read Sento $sento
 * @property-read User $user
 * @property-read SentoReview|null $review
 */
class SentoPhoto extends Model
{
    /** @use HasFactory<\Database\Factories\SentoPhotoFactory> */
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'sento_id',
        'user_id',
        'review_id',
        'path',
        'category',
        'caption',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'category' => PhotoCategory::class,
    ];

    /** @var list<string> */
    protected $appends = ['url'];

    /** @return BelongsTo<Sento, $this> */
    public function sento(): BelongsTo
    {
        return $this->belongsTo(Sento::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<SentoReview, $this> */
    public function review(): BelongsTo
    {
        return $this->belongsTo(SentoReview::class, 'review_id');
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::url($this->path) : null;
    }
}
