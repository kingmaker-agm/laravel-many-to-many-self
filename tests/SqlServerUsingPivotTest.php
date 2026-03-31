<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\SqlServerTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUsingPivotStub;

class SqlServerUsingPivotTest extends SqlServerTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUsingPivotStub::class);
        parent::setUp();
    }
}
