<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\PostgresTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidUsingPivotStub;

class PostgresUuidUsingPivotTest extends PostgresTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidUsingPivotStub::class);
        parent::setUp();
    }
}
