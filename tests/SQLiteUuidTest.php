<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Kingmaker\Illuminate\Eloquent\Relations\Tests\Base\SQLiteTestCase;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelUuidStub;

class SQLiteUuidTest extends SQLiteTestCase
{
    protected function setUp(): void
    {
        $this->setModelClass(ModelUuidStub::class);

        parent::setUp();
    }
}
