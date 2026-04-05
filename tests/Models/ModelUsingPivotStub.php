<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kingmaker\Illuminate\Eloquent\Relations\HasBelongsToManySelfRelation;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Contracts\DatabaseSchemaRefreshable;

class ModelUsingPivotStub extends Model implements DatabaseSchemaRefreshable
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
        'birth_at' => 'datetime'
    ];

    /**
     * ORM Relation
     *
     * @return \Kingmaker\Illuminate\Eloquent\Relations\BelongsToManySelf
     */
    public function friends()
    {
        return $this->belongsToManySelf('friends', 'user1', 'user2')
            ->withPivot('percentage')
            ->using(PivotStub::class);
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
            $table->unsignedBigInteger('id', true);
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamp('birth_at')->nullable();
            $table->string('email')->nullable();
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('user1');
            $table->foreign('user1')
                ->references('id')->on('users');
            $table->unsignedBigInteger('user2');
            $table->foreign('user2')
                ->references('id')->on('users');
            $table->integer('percentage')->nullable();
        });
    }
}
