<?php

namespace Kingmaker\Illuminate\Eloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToManySelf extends BelongsToMany
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $directJoinWhere;

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $inverseJoinWhere;

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
            /** @var \Illuminate\Database\Query\JoinClause $join */
            $join->on(function ($query) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $this->directJoinWhere = $query;

                return $query
                    ->whereColumn(
                        $this->getQualifiedRelatedKeyName(),
                        '=',
                        $this->getQualifiedRelatedPivotKeyName()
                    );
            })
                ->orOn(function ($query) {
                    /** @var \Illuminate\Database\Query\Builder $query */
                    $this->inverseJoinWhere = $query;

                    return $query
                        ->whereColumn(
                            $this->getQualifiedRelatedKeyName(),
                            '=',
                            $this->getQualifiedForeignPivotKeyName()
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
        $this->directJoinWhere->where(
            $this->getQualifiedForeignPivotKeyName(),
            '=',
            $this->parent->getKey()
        );
        $this->addBinding($this->parent->getKey(), 'join');

        $this->inverseJoinWhere->where(
            $this->getQualifiedRelatedPivotKeyName(),
            '=',
            $this->parent->getKey()
        );
        $this->addBinding($this->parent->getKey(), 'join');


        return $this;
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $whereIn = $this->whereInMethod($this->parent, $this->parentKey);

        $this->directJoinWhere->{$whereIn}(
            $this->getQualifiedForeignPivotKeyName(),
            $keys = $this->getKeys($models, $this->parentKey)
        );
        $this->inverseJoinWhere->{$whereIn}(
            $this->getQualifiedRelatedPivotKeyName(),
            $keys
        );

        // Bind the Keys to the "join" part of the parent Eloquent Builder
        $originalJoinKeys = $this->getQuery()->getQuery()->bindings['join'];
        $newJoinKeys = array_merge($keys, $keys, $originalJoinKeys);
        $this->setBindings($newJoinKeys, 'join');
    }
}
