<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase
{
    protected abstract function createDatabaseForManyToManySelf(): void;

    protected abstract function seedDataForManyToManySelf(): void;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createDatabaseForManyToManySelf();
        $this->seedDataForManyToManySelf();
    }

    protected function tearDown(): void
    {
        foreach ($this->app['db']->getConnections() as $connection) {
            $connection->disconnect();
        }

        parent::tearDown();
    }

    protected function defineEnvironment($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        parent::defineEnvironment($app);
    }
}
