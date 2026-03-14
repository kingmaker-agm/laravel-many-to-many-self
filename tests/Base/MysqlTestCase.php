<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests\Base;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Concerns\ManyToManySelfTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\TestCase;

abstract class MysqlTestCase extends TestCase
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
            'prefix' => env('MYSQL_TABLE_PREFIX', ''),
        ]);
    }

    protected function getDatabaseDriver(): string
    {
        return 'mysql';
    }
}
