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
     * role は意図的に fillable から外している（mass assignment による権限昇格を防ぐ）。
     * 変更したい場合は promoteToAdmin() / demoteToMember() を経由する。
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tel',
        'password_token',
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

    public function promoteToAdmin(): void
    {
        $this->role = UserRole::Admin;
        $this->save();
    }

    public function demoteToMember(): void
    {
        $this->role = UserRole::Member;
        $this->save();
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
