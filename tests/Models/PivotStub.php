<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PivotStub extends Pivot
{
    protected $table = 'friends';
    public $incrementing = true;
    public $timestamps = false;

    protected $guarded = [];
}
