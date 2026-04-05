<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\MysqlTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidStub;

class MySQLUuidTest extends MysqlTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidStub::class);

        parent::setUp();
    }
}
