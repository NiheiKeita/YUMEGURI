<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\EnsureAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_passes_through(): void
    {
        $admin = User::factory()->admin()->create();
        $request = Request::create('/admin/proposals');
        $request->setUserResolver(fn () => $admin);

        $response = (new EnsureAdmin())->handle($request, fn () => response('ok', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }

    public function test_member_is_aborted_403(): void
    {
        $member = User::factory()->create();
        $request = Request::create('/admin/proposals');
        $request->setUserResolver(fn () => $member);

        $caught = null;
        try {
            (new EnsureAdmin())->handle($request, fn () => response('ok'));
        } catch (HttpException $e) {
            $caught = $e;
        }
        $this->assertNotNull($caught);
        $this->assertSame(403, $caught->getStatusCode());
    }

    public function test_unauthenticated_is_aborted_403(): void
    {
        $request = Request::create('/admin/proposals');
        $request->setUserResolver(fn () => null);

        $caught = null;
        try {
            (new EnsureAdmin())->handle($request, fn () => response('ok'));
        } catch (HttpException $e) {
            $caught = $e;
        }
        $this->assertNotNull($caught);
        $this->assertSame(403, $caught->getStatusCode());
    }
}
