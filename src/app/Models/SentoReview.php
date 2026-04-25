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
 * @property int $sento_id
 * @property int $user_id
 * @property Carbon $visited_at
 * @property int $rating
 * @property string|null $body
 * @property bool $has_sauna
 * @property int|null $sauna_temp
 * @property bool $has_mizuburo
 * @property int|null $mizuburo_temp
 * @property array<int, string>|null $bath_types
 * @property bool $want_revisit
 * @property int|null $crowding
 * @property string|null $best_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Sento $sento
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoPhoto> $photos
 */
class SentoReview extends Model
{
    /** @use HasFactory<\Database\Factories\SentoReviewFactory> */
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'sento_id',
        'user_id',
        'visited_at',
        'rating',
        'body',
        'has_sauna',
        'sauna_temp',
        'has_mizuburo',
        'mizuburo_temp',
        'bath_types',
        'want_revisit',
        'crowding',
        'best_time',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'visited_at' => 'date',
        'rating' => 'integer',
        'has_sauna' => 'boolean',
        'sauna_temp' => 'integer',
        'has_mizuburo' => 'boolean',
        'mizuburo_temp' => 'integer',
        'bath_types' => 'array',
        'want_revisit' => 'boolean',
        'crowding' => 'integer',
    ];

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

    /** @return HasMany<SentoPhoto, $this> */
    public function photos(): HasMany
    {
        return $this->hasMany(SentoPhoto::class, 'review_id');
    }
}
