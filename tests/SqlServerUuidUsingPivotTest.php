<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\SqlServerTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidUsingPivotStub;

class SqlServerUuidUsingPivotTest extends SqlServerTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidUsingPivotStub::class);
        parent::setUp();
    }
}
