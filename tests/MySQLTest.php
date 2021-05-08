<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

class MySQLTest extends TestCase
{
    use ManyToManySelfTestCase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('MYSQL_HOST', '127.0.0.1'),
            'port' => env('MYSQL_PORT',3306),
            'database' => env('MYSQL_DATABASE'),
            'username' => env('MYSQL_USERNAME'),
            'password' => env('MYSQL_PASSWORD'),
            'prefix' => env('MYSQL_TABLE_PREFIX', '')
        ]);
    }

    protected function getDatabaseDriver(): string
    {
        return 'mysql';
    }
}
