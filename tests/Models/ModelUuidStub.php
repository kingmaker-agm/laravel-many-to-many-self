<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kingmaker\Illuminate\Eloquent\Relations\HasBelongsToManySelfRelation;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Contracts\DatabaseSchemaRefreshable;
use Ramsey\Uuid\Uuid;

/**
 * Class ModelStubUuid
 * @package Kingmaker\Illuminate\Eloquent\Relations\Tests
 * @property string $id
 * @property string $name
 * @property int $age
 * @property \Illuminate\Support\Carbon $birth_at
 * @property string $email
 * @property-read Collection|ModelUuidStub[] $friends
 * @mixin Builder
 */
class ModelUuidStub extends Model implements DatabaseSchemaRefreshable
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
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Guarded Properties during Mass-Assignment
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'age' => 'integer',
        'birth_at' => 'datetime'
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
    }

    /**
     * ORM Relation
     *
     * @return \Kingmaker\Illuminate\Eloquent\Relations\BelongsToManySelf
     */
    public function friends()
    {
        return $this->belongsToManySelf('friends', 'user1', 'user2')
            ->withPivot('percentage');
    }

    /**
     * Refresh the Database Schema
     *
     * Existing Database Tables will be dropped, if they already exists.
     * Create the Database Tables.
     * @return void
     */
    public static function refreshDatabaseSchema(): void {
        Schema::dropIfExists('friends');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamp('birth_at')->nullable();
            $table->string('email')->nullable();
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->uuid('user1');
            $table->foreign('user1')
                ->references('id')->on('users');
            $table->uuid('user2');
            $table->foreign('user2')
                ->references('id')->on('users');
            $table->integer('percentage')->nullable();
        });
    }
}
