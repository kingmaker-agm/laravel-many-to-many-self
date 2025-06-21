<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\SqlServerTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidStub;

class SqlServerUuidTest extends SqlServerTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidStub::class);

        parent::setUp();
    }
}
