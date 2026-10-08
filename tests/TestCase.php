<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The application's web middleware performs CSRF verification, which no
     * feature test supplies a token for. Laravel normally handles this when a
     * fresh application is created per test; because this project's tests
     * boot without that, POST/PUT/DELETE requests were returning HTTP 419.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class
        );
    }
}
