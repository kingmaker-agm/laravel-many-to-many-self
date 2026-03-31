<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\SQLiteTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidUsingPivotStub;

class SQLiteUuidUsingPivotTest extends SQLiteTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidUsingPivotStub::class);
        parent::setUp();
    }
}
