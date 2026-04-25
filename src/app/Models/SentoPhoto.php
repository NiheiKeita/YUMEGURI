<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\PhotoCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SentoPhoto extends Model
{
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

    /** @return BelongsTo<Sento, SentoPhoto> */
    public function sento(): BelongsTo
    {
        return $this->belongsTo(Sento::class);
    }

    /** @return BelongsTo<User, SentoPhoto> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<SentoReview, SentoPhoto> */
    public function review(): BelongsTo
    {
        return $this->belongsTo(SentoReview::class, 'review_id');
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::url($this->path) : null;
    }
}
