<?php

namespace App\Models;

use App\Domain\Enum\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tel',
        'password_token',
        'role',
        'invited_by',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    public function isAdmin(): bool
    {
        return $this->role instanceof UserRole && $this->role->isAdmin();
    }

    /** @return BelongsTo<User, User> */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(self::class, 'invited_by');
    }

    /** @return HasMany<SentoReview> */
    public function sentoReviews(): HasMany
    {
        return $this->hasMany(SentoReview::class);
    }

    /** @return HasMany<SentoPhoto> */
    public function sentoPhotos(): HasMany
    {
        return $this->hasMany(SentoPhoto::class);
    }

    /** @return HasMany<SentoEditProposal> */
    public function sentoEditProposals(): HasMany
    {
        return $this->hasMany(SentoEditProposal::class, 'proposed_by');
    }
}
