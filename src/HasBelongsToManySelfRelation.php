<?php

namespace Kingmaker\Illuminate\Eloquent\Relations;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Support\Arr;

trait HasBelongsToManySelfRelation
{
    use HasRelationships {
        guessBelongsToManyRelation as parentGuessBelongsToManyRelation;
    }

    /**
     * create the BelongsToManySelf relation on the same Model via a pivot table
     *
     * @param string $table Pivot table name
     * @param string $pivotKey1 Pivot table foreign key 1
     * @param string $pivotKey2 Pivot table foreign key 2
     * @param string|null $relatedKey Related key on the parent table
     * @param string|null $relation Relation name
     * @return BelongsToManySelf
     */
    public function belongsToManySelf(string $table, string $pivotKey1, string $pivotKey2, $relatedKey = null, $relation = null)
    {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance(get_class($this));

        // If the related Key was not passed, we will get the primary key as the
        // key for the key column on the table
        $relatedKey = $relatedKey ?: $this->getKeyName();

        return $this->newBelongsToManySelfRelation($instance, $table, $pivotKey1, $pivotKey2, $relatedKey, $relation);
    }

    /**
     * Get the relationship name of the belongsToMany relationship.
     *
     * @return string|null
     */
    protected function guessBelongsToManyRelation(): ?string
    {
        $caller = Arr::first(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), function ($trace) {
            return ! in_array(
                $trace['function'],
                array_merge(static::$manyMethods, ['guessBelongsToManyRelation', 'belongsToManySelf'])
            );
        });

        return ! is_null($caller) ? $caller['function'] : null;
    }

    /**
     * Instantiate a new BelongsToManySelf Relation
     *
     * @param $instance
     * @param string $table
     * @param string $pivotKey1
     * @param string $pivotKey2
     * @param $relatedKey
     * @param $relation
     * @return BelongsToManySelf
     */
    protected function newBelongsToManySelfRelation($instance, string $table, string $pivotKey1, string $pivotKey2, $relatedKey, $relation)
    {
        return new BelongsToManySelf($instance->newQuery(), $this, $table, $pivotKey1, $pivotKey2, $relatedKey, $relation);
    }
}
