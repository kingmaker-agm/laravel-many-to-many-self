<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

class SQLiteTest extends TestCase
{
    use ManyToManySelfTestCase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);
    }

    protected function getDatabaseDriver(): string
    {
        return 'sqlite';
    }
}
