<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\PostgresTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidStub;

class PostgresUuidTest extends PostgresTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidStub::class);

        parent::setUp();
    }
}
