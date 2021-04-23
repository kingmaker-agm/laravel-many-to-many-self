<?php

namespace Kingmaker\Illuminate\Eloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\JoinClause;

class BelongsToManySelf extends BelongsToMany
{
    /**
     * BelongsToManySelf constructor.
     * @param Builder $query
     * @param Model $parent
     * @param $table
     * @param $pivotKey1
     * @param $pivotKey2
     * @param $parentKey
     * @param null $relationName
     */
    public function __construct(Builder $query, Model $parent, $table, $pivotKey1, $pivotKey2, $parentKey, $relationName = null)
    {
        parent::__construct($query, $parent, $table, $pivotKey1, $pivotKey2, $parentKey, $parentKey, $relationName);
    }

    /**
     * Set the join clause for the relation query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $query
     * @return $this
     */
    protected function performJoin($query = null)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance.
        // Then we can set the "where" for the parent models.
        $query->join($this->table, function($join) {
            /** @var JoinClause $join */
            $join->on(function ($query) {
                /** @var Builder $query */
                return $query
                    ->whereColumn(
                        $this->getQualifiedRelatedKeyName(),
                        '=',
                        $this->getQualifiedRelatedPivotKeyName()
                    )
                    // Fixme check for the whether it is a query on single parent or as multiple parents during eager loading
                    ->where(
                        $this->getQualifiedForeignPivotKeyName(),
                        '=',
                        $this->parent->getKey()
                    );
            })
                ->orOn(function ($query) {
                    /** @var Builder $query */
                    return $query
                        ->whereColumn(
                            $this->getQualifiedRelatedKeyName(),
                            '=',
                            $this->getQualifiedForeignPivotKeyName()
                        )
                        // Fixme check for the whether it is a query on single parent or as multiple parents during eager loading
                        ->where(
                            $this->getQualifiedRelatedPivotKeyName(),
                            '=',
                            $this->parent->getKey()
                        );
                });
        });

        return $this;
    }

    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function addWhereConstraints()
    {
        // We are making the WHERE Constraints on the Join Operation
        return $this;
    }
}
