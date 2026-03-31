<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\MysqlTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidUsingPivotStub;

class MySQLUuidUsingPivotTest extends MysqlTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidUsingPivotStub::class);
        parent::setUp();
    }
}
