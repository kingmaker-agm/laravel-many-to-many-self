<?php


namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;


class SqlServerTest extends TestCase
{
    use ManyToManySelfTestCase;

    protected function getDatabaseDriver(): string
    {
        return 'sqlsrv';
    }

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'sqlsrv');
        $app['config']->set('database.connections.sqlsrv', [
            'driver' => 'sqlsrv',
            'host' => env('SQL_SERVER_HOST', '127.0.0.1'),
            'port' => env('SQL_SERVER_PORT',1433),
            'database' => env('SQL_SERVER_DATABASE'),
            'username' => env('SQL_SERVER_USERNAME'),
            'password' => env('SQL_SERVER_PASSWORD'),
            'prefix' => env('SQL_SERVER_TABLE_PREFIX', '')
        ]);
    }
}
