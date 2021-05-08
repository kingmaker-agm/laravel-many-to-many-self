<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

class PostgresTest extends TestCase
{
    use ManyToManySelfTestCase;

    protected function getDatabaseDriver(): string
    {
        return 'pgsql';
    }

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'pgsql');
        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => env('POSTGRES_HOST', '127.0.0.1'),
            'port' => env('POSTGRES_PORT',5432),
            'database' => env('POSTGRES_DATABASE'),
            'username' => env('POSTGRES_USERNAME'),
            'password' => env('POSTGRES_PASSWORD'),
            'prefix' => env('POSTGRES_TABLE_PREFIX', '')
        ]);
    }
}
