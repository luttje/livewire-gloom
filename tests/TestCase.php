<?php

namespace Luttje\LivewireGloom\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Luttje\LivewireGloom\LivewireGloomServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireGloomServiceProvider::class,
        ];
    }
}
