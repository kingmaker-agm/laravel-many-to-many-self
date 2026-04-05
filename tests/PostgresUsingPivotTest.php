<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\PostgresTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUsingPivotStub;

class PostgresUsingPivotTest extends PostgresTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUsingPivotStub::class);
        parent::setUp();
    }
}
