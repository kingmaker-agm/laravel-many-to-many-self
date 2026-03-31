<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\SQLiteTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUsingPivotStub;

class SQLiteUsingPivotTest extends SQLiteTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUsingPivotStub::class);
        parent::setUp();
    }
}
