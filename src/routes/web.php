<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\MapController;
use App\Http\Controllers\Web\NearbyController;
use App\Http\Controllers\Web\PasswordController;
use App\Http\Controllers\Web\SentoController;
use App\Http\Controllers\Web\SentoEditProposalController;
use App\Http\Controllers\Web\SentoReviewController;
use App\Http\Controllers\Web\TopController;
use App\Http\Controllers\Web\UserProfileController;
use App\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'basicauth'], function () {
    Route::fallback(function () {
        return redirect(route('web.top'));
    });

    Route::middleware('guest.web')->group(function () {
        Route::get('password/edit/{token}', [PasswordController::class, 'edit'])->name('web.password.edit');
        Route::post('password/edit/{token}', [PasswordController::class, 'update'])->name('web.password.update');
    });
    Route::get('login', [LoginController::class, 'create'])->name('user.login');
    Route::post('login', [LoginController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | YUMEGURI Web 画面
    |--------------------------------------------------------------------------
    |
    | 招待制フェーズではほとんどの画面で auth が必要。一般公開フェーズ移行時に
    | 閲覧系（top/map/sentos.*/users.show 等）から auth を外す想定。
    */

    Route::get('/', [TopController::class, 'index'])->name('web.top');
    Route::get('/map', [MapController::class, 'index'])->name('web.map');
    Route::get('/sentos', [SentoController::class, 'index'])->name('web.sentos.index');
    Route::get('/sentos/{sento}', [SentoController::class, 'show'])->name('web.sentos.show');

    Route::middleware('auth')->group(function () {
        // 訪問記録
        Route::get('/sentos/{sento}/review', [SentoReviewController::class, 'create'])
            ->name('web.sentos.review.create');
        Route::post('/sentos/{sento}/review', [SentoReviewController::class, 'store'])
            ->name('web.sentos.review.store');

        // 編集提案
        Route::get('/sentos/{sento}/propose', [SentoEditProposalController::class, 'create'])
            ->name('web.sentos.propose.create');
        Route::post('/sentos/{sento}/propose', [SentoEditProposalController::class, 'store'])
            ->name('web.sentos.propose.store');

        // 直接編集（admin のみ）
        Route::middleware('admin')->group(function () {
            Route::get('/sentos/{sento}/edit', [SentoController::class, 'edit'])
                ->name('web.sentos.edit');
            Route::patch('/sentos/{sento}', [SentoController::class, 'update'])
                ->name('web.sentos.update');
        });

        Route::get('/nearby', [NearbyController::class, 'index'])->name('web.nearby');

        // ユーザープロフィール
        Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('web.users.show');
        Route::get('/users/{user}/map', [UserProfileController::class, 'map'])->name('web.users.map');
        Route::get('/users/{user}/nearby', [UserProfileController::class, 'nearby'])
            ->name('web.users.nearby');
        Route::get('/users/{user}/photos', [UserProfileController::class, 'photos'])
            ->name('web.users.photos');
    });

    // YUMEGURI 管理（admin ロールユーザのみ）
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('proposals', [ProposalController::class, 'index'])->name('proposals.index');
        Route::post('proposals/{proposal}/approve', [ProposalController::class, 'approve'])
            ->name('proposals.approve');
        Route::post('proposals/{proposal}/reject', [ProposalController::class, 'reject'])
            ->name('proposals.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | 既存テンプレート: /admin の AdminUser ベース管理（YUMEGURI とは別系統）
    |--------------------------------------------------------------------------
    */
    Route::get('admin/login', [AdminLoginController::class, 'index'])->name('admin.login');
    Route::post('admin/login', [AdminLoginController::class, 'store'])->name('admin.login');
    Route::middleware('guest.admin')->group(function () {
        Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard.index');

        Route::get('admin/admin_users', [AdminUserController::class, 'index'])->name('admin_user.list');
        Route::get('admin/admin_users/add', [AdminUserController::class, 'create'])->name('admin_user.create');
        Route::post('admin/admin_users/add', [AdminUserController::class, 'store'])->name('admin_user.store');

        Route::get('admin/users', [UserController::class, 'index'])->name('user.list');
        Route::get('admin/users/add', [UserController::class, 'create'])->name('user.create');
        Route::post('admin/users/add', [UserController::class, 'store'])->name('user.store');
        Route::get('admin/users/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('admin/users/{id}', [UserController::class, 'update'])->name('user.update');
    });

    // API
    Route::post('/api/upload', [ImageController::class, 'upload'])->withoutMiddleware(VerifyCsrfToken::class)->name('upload');
    Route::post('/api/upload/ma', [ImageController::class, 'maUpload'])->withoutMiddleware(VerifyCsrfToken::class)->name('upload.ma');
});
