<?php

namespace App\Models;

use App\Domain\Enum\UserRole;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $tel
 * @property string|null $password_token
 * @property UserRole|null $role
 * @property int|null $invited_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $inviter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoReview> $sentoReviews
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoPhoto> $sentoPhotos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SentoEditProposal> $sentoEditProposals
 */
class User extends Authenticatable
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
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

    /** @return BelongsTo<User, $this> */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(self::class, 'invited_by');
    }

    /** @return HasMany<SentoReview, $this> */
    public function sentoReviews(): HasMany
    {
        return $this->hasMany(SentoReview::class);
    }

    /** @return HasMany<SentoPhoto, $this> */
    public function sentoPhotos(): HasMany
    {
        return $this->hasMany(SentoPhoto::class);
    }

    /** @return HasMany<SentoEditProposal, $this> */
    public function sentoEditProposals(): HasMany
    {
        return $this->hasMany(SentoEditProposal::class, 'proposed_by');
    }

    /** @return HasMany<Post, $this> */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
