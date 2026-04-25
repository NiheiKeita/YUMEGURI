<?php

namespace App\Providers;

use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\SentoPhoto;
use App\Models\SentoReview;
use App\Policies\SentoEditProposalPolicy;
use App\Policies\SentoPhotoPolicy;
use App\Policies\SentoPolicy;
use App\Policies\SentoReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Sento::class => SentoPolicy::class,
        SentoReview::class => SentoReviewPolicy::class,
        SentoPhoto::class => SentoPhotoPolicy::class,
        SentoEditProposal::class => SentoEditProposalPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', fn ($user) => $user?->isAdmin() === true);
    }
}
