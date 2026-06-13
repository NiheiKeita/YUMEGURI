<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\SentoStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $name_kana
 * @property string $prefecture
 * @property string|null $city
 * @property string $address
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $phone
 * @property string|null $hours
 * @property string|null $closed_days
 * @property int|null $price
 * @property string|null $source_url
 * @property string|null $nearest_station
 * @property int|null $walk_minutes
 * @property bool $has_shampoo
 * @property bool $has_soap
 * @property SentoStatus $status
 * @property Carbon|string|null $info_updated_at
 * @property bool $is_manually_updated
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoReview> $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoPhoto> $photos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoEditProposal> $editProposals
 * @property-read float|null $reviews_avg_rating
 * @property-read int|null $reviews_count
 * @property-read int|null $want_revisit_count
 * @property-read float|null $distance_km
 */
class Sento extends Model
{
    /** @use HasFactory<\Database\Factories\SentoFactory> */
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

    /** @return HasMany<SentoReview, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(SentoReview::class);
    }

    /** @return HasMany<SentoPhoto, $this> */
    public function photos(): HasMany
    {
        return $this->hasMany(SentoPhoto::class);
    }

    /** @return HasMany<SentoEditProposal, $this> */
    public function editProposals(): HasMany
    {
        return $this->hasMany(SentoEditProposal::class);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeOperating(Builder $query): Builder
    {
        return $query->where('status', '!=', SentoStatus::ClosedPerm->value);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeInPrefecture(Builder $query, string $prefecture): Builder
    {
        return $query->where('prefecture', $prefecture);
    }
}
