<?php

namespace Luttje\LivewireGloom\Tests;

use Luttje\LivewireGloom\LivewireGloomServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

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
