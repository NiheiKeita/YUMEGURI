<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\SentoStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sento extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'name_kana',
        'prefecture',
        'city',
        'address',
        'lat',
        'lng',
        'phone',
        'hours',
        'closed_days',
        'price',
        'source_url',
        'nearest_station',
        'walk_minutes',
        'has_shampoo',
        'has_soap',
        'status',
        'info_updated_at',
        'is_manually_updated',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'price' => 'integer',
        'walk_minutes' => 'integer',
        'has_shampoo' => 'boolean',
        'has_soap' => 'boolean',
        'is_manually_updated' => 'boolean',
        'info_updated_at' => 'date',
        'status' => SentoStatus::class,
    ];

    /** @return HasMany<SentoReview> */
    public function reviews(): HasMany
    {
        return $this->hasMany(SentoReview::class);
    }

    /** @return HasMany<SentoPhoto> */
    public function photos(): HasMany
    {
        return $this->hasMany(SentoPhoto::class);
    }

    /** @return HasMany<SentoEditProposal> */
    public function editProposals(): HasMany
    {
        return $this->hasMany(SentoEditProposal::class);
    }

    /** @param Builder<Sento> $query */
    public function scopeOperating(Builder $query): Builder
    {
        return $query->where('status', '!=', SentoStatus::ClosedPerm->value);
    }

    /** @param Builder<Sento> $query */
    public function scopeInPrefecture(Builder $query, string $prefecture): Builder
    {
        return $query->where('prefecture', $prefecture);
    }
}
