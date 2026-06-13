<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\ProposalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $sento_id
 * @property int $proposed_by
 * @property array<string, mixed> $changes
 * @property string|null $reason
 * @property ProposalStatus $status
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Sento $sento
 * @property-read User $proposer
 * @property-read User|null $reviewer
 */
class SentoEditProposal extends Model
{
    /** @use HasFactory<\Database\Factories\SentoEditProposalFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'sento_id',
        'proposed_by',
        'changes',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'changes' => 'array',
        'status' => ProposalStatus::class,
        'reviewed_at' => 'datetime',
    ];

    /** @return BelongsTo<Sento, $this> */
    public function sento(): BelongsTo
    {
        return $this->belongsTo(Sento::class);
    }

    /** @return BelongsTo<User, $this> */
    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    /** @return BelongsTo<User, $this> */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ProposalStatus::Pending->value);
    }
}
