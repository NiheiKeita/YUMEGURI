<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // Inertia ページのレンダリングに Vite マニフェストが必要だが、テストでは
        // `npm run build` を要求しないようにスタブ化する。
        $this->withoutVite();
    }
}
