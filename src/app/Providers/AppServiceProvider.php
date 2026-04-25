<?php

namespace App\Providers;

use App\Services\Contracts\Geocoder;
use App\Services\Geocoding\GoogleGeocoder;
use App\Services\Sento\Importer\Scrapers\ChibaScraper;
use App\Services\Sento\Importer\Scrapers\KanagawaScraper;
use App\Services\Sento\Importer\Scrapers\SaitamaScraper;
use App\Services\Sento\Importer\Scrapers\TokyoScraper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Geocoder::class, function () {
            return new GoogleGeocoder(
                new Client(),
                (string) config('services.google.geocoding_key', ''),
            );
        });

        // 都県ごとのスクレイパーをタグでまとめ、コマンドから配列で取得できるようにする
        $this->app->tag([
            TokyoScraper::class,
            KanagawaScraper::class,
            SaitamaScraper::class,
            ChibaScraper::class,
        ], 'sento.scrapers');
    }

    public function boot(): void
    {
        Inertia::share([
            'auth.user' => function () {
                // Auth::user() は AdminUser|User の union を返すため、YUMEGURI 用の
                // role/isAdmin は User instance の場合だけ参照する。
                $user = Auth::user();
                if (!$user instanceof \App\Models\User) {
                    return null;
                }
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->value,
                    'is_admin' => $user->isAdmin(),
                ];
            },
        ]);
    }
}
