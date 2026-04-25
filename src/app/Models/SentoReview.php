<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SentoReview extends Model
{
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

    /** @return BelongsTo<Sento, SentoReview> */
    public function sento(): BelongsTo
    {
        return $this->belongsTo(Sento::class);
    }

    /** @return BelongsTo<User, SentoReview> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<SentoPhoto> */
    public function photos(): HasMany
    {
        return $this->hasMany(SentoPhoto::class, 'review_id');
    }
}
