<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase
{
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[ManyToManySelfTestCase::class])) {
            /** @var ManyToManySelfTestCase $this */
            $this->createDatabaseForManyToManySelf();
        }

        return $uses;
    }

    protected function defineEnvironment($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/../');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        parent::defineEnvironment($app);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Spatie\LaravelRay\RayServiceProvider::class
        ];
    }
}
