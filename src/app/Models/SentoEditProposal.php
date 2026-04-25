<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enum\ProposalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentoEditProposal extends Model
{
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

    /** @return BelongsTo<Sento, SentoEditProposal> */
    public function sento(): BelongsTo
    {
        return $this->belongsTo(Sento::class);
    }

    /** @return BelongsTo<User, SentoEditProposal> */
    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    /** @return BelongsTo<User, SentoEditProposal> */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** @param Builder<SentoEditProposal> $query */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ProposalStatus::Pending->value);
    }
}
