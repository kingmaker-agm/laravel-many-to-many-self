<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\MysqlTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUsingPivotStub;

class MySQLUsingPivotTest extends MysqlTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUsingPivotStub::class);
        parent::setUp();
    }
}
