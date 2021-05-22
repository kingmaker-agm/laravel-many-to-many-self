<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Kingmaker\Illuminate\Eloquent\Relations\HasBelongsToManySelfRelation;

/**
 * Class ModelStub
 * @package Kingmaker\Illuminate\Eloquent\Relations\Tests
 * @property int $id
 * @property string $name
 * @property int $age
 * @property \Illuminate\Support\Carbon $birth_at
 * @property string $email
 * @property-read Collection|ModelStub[] $friends
 * @mixin Builder
 */
class ModelStub extends Model
{
    use HasBelongsToManySelfRelation;

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Timestamp in the Database Table
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Guarded Properties during Mass-Assignment
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'age' => 'integer',
        'birth_at' => 'timestamp'
    ];

    /**
     * ORM Relation
     *
     * @return \Kingmaker\Illuminate\Eloquent\Relations\BelongsToManySelf
     */
    public function friends()
    {
        return $this->belongsToManySelf('friends', 'user1', 'user2');
    }
}
