<?php

namespace Kingmaker\Illuminate\Eloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BelongsToManySelf extends BelongsToMany
{
    /**
     * The Aliased Table Name for the intermediate table.
     * --------------------------------------------------
     * Used when applying the Relationship Exists conditions
     *
     * @var string|null
     */
    protected $tableAlias;

    /**
     * @var QueryBuilder
     */
    protected $directJoinWhere;

    /**
     * @var QueryBuilder
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
     * @param \Illuminate\Database\Eloquent\Builder|null  $query
     * @param bool $performPivotAliasing should have to alias the intermediate table during JOIN
     * @return $this
     */
    protected function performJoin($query = null, bool $performPivotAliasing = false)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance.
        // Then we can set the "where" for the parent models.
        $pivotTable = $this->getTable();
        if ($performPivotAliasing)
            $pivotTable = $this->getTable() . ' AS ' . $this->generatePivotTableAlias();

        $query->join($pivotTable, function($join) {
            /** @var \Illuminate\Database\Query\JoinClause $join */
            $join->on(function ($query) {
                /** @var QueryBuilder $query */
                $this->directJoinWhere = $query;

                return $query
                    ->whereColumn(
                        $this->getQualifiedRelatedKeyName(),
                        '=',
                        $this->getQualifiedRelatedPivotKeyName()
                    );
            })
                ->orOn(function ($query) {
                    /** @var QueryBuilder $query */
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
     * Generate an Table Alias for the intermediate Pivot Table
     *
     * @return string
     */
    protected function generatePivotTableAlias(): string
    {
        return $this->tableAlias = $this->getRelationCountHash();
    }

    /**
     * Qualify the given column name by the pivot table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyPivotColumn($column)
    {
        return Str::contains($column, '.')
            ? $column
            : ($this->tableAlias ?? $this->table).'.'.$column;
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
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        // First we will build a dictionary of child models keyed by the foreign key
        // of the relation so that we will easily and quickly match them to their
        // parents without having a possibly slow inner loops for every models.
        $dictionary = [];

        foreach ($results as $result) {
            $foreign_key = $this->getDictionaryKey($result->{$this->accessor}->{$this->foreignPivotKey});
            $related_key = $this->getDictionaryKey($result->{$this->accessor}->{$this->relatedPivotKey});

            if ($result->{$this->parentKey} == $foreign_key)
                $dictionary[$related_key][] = $result;
            else
                $dictionary[$foreign_key][] = $result;
        }

        return $dictionary;
    }

    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        $query->select($columns);

        $query->from($this->related->getTable().' as '.$hash = $this->getRelationCountHash());
        $this->related->setTable($hash);

        $this->performJoin($query, true);

        $this->directJoinWhere->whereColumn(
            $this->getQualifiedForeignPivotKeyName(),
            '=',
            $parentQuery->getModel()->getQualifiedKeyName()
        );
        $this->inverseJoinWhere->whereColumn(
            $this->getQualifiedRelatedPivotKeyName(),
            '=',
            $parentQuery->getModel()->getQualifiedKeyName()
        );

        return $query;
    }

    /**
     * Toggles a model (or models) from the parent.
     *
     * Each existing model is detached, and non existing ones are attached.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return array
     */
    public function toggle($ids, $touch = true)
    {
        $changes = [
            'attached' => [], 'detached' => [],
        ];

        $records = $this->formatRecordsList($this->parseIds($ids));

        // Next, we will determine which IDs should get removed from the join table by
        // checking which of the given ID/records is in the list of current records
        // and removing all of those rows from this "intermediate" joining table.
        $detach = array_values(array_intersect(
            $this->allRelatedIds()->all(),
            array_keys($records)
        ));

        if (count($detach) > 0) {
            $this->detach($detach, false);

            $changes['detached'] = $this->castKeys($detach);
        }

        // Finally, for all of the records which were not "detached", we'll attach the
        // records into the intermediate table. Then, we will add those attaches to
        // this change list and get ready to return these results to the callers.
        $attach = array_diff_key($records, array_flip($detach));

        if (count($attach) > 0) {
            $this->attach($attach, [], false);

            $changes['attached'] = array_keys($attach);
        }

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if ($touch && (count($changes['attached']) ||
                count($changes['detached']))) {
            $this->touchIfTouching();
        }

        return $changes;
    }

    /**
     * Detach models from the relationship.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return int
     */
    public function detach($ids = null, $touch = true)
    {
        if (is_null($ids)) {
            $query = $this->newPivotQuery();
        }
        else {
            $ids = $this->parseIds($ids);
            if (empty($ids)) {
                return 0;
            }

            $query = $this->newPivotStatementForId($ids);
        }

        // Once we have all of the conditions set on the statement, we are ready
        // to run the delete on the pivot table. Then, if the touch parameter
        // is true, we will go ahead and touch all related models to sync.
        $results = $query->delete();

        if ($touch) {
            $this->touchIfTouching();
        }

        return $results;
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array $ids
     * @param bool $detaching
     * @return array
     */
    public function sync($ids, $detaching = true)
    {
        $changes = [
            'attached' => [], 'detached' => [], 'updated' => [],
        ];

        // First we need to attach any of the associated models that are not currently
        // in this joining table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
        $current = $this->allRelatedIds()->all();

        $detach = array_diff($current, array_keys(
            $records = $this->formatRecordsList($this->parseIds($ids))
        ));

        // Next, we will take the differences of the currents and given IDs and detach
        // all of the entities that exist in the "current" array but are not in the
        // array of the new IDs given to the method which will complete the sync.
        if ($detaching && count($detach) > 0) {
            $this->detach($detach);

            $changes['detached'] = $this->castKeys($detach);
        }

        // Now we are finally ready to attach the new records. Note that we'll disable
        // touching until after the entire operation is complete so we don't fire a
        // ton of touch operations until we are totally done syncing the records.
        $changes = array_merge(
            $changes, $this->attachNew($records, $current, false)
        );

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if (count($changes['attached']) ||
            count($changes['updated'])) {
            $this->touchIfTouching();
        }

        return $changes;
    }

    public function newPivotQuery()
    {
        return $this->newPivotQueryWithConstraints()
            ->where(function (QueryBuilder $query) {
                return $query
                    ->where(
                        $this->getQualifiedForeignPivotKeyName(),
                        $this->parent->{$this->parentKey}
                    )
                    ->orWhere(
                        $this->getQualifiedRelatedPivotKeyName(),
                        $this->parent->{$this->parentKey}
                    );
            });
    }

    /**
     * Get a new pivot statement for a given "other" ID.
     *
     * @param mixed $id
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatementForId($id)
    {
        $relatedIds = $this->parseIds($id);

        return $this->newPivotQueryWithConstraints()
            ->where(function (QueryBuilder $query) use ($relatedIds) {
                return $query
                    ->where(function (QueryBuilder $query) use ($relatedIds) {
                        $query->where($this->getQualifiedForeignPivotKeyName(), $this->parent->{$this->parentKey})
                            ->whereIn($this->getQualifiedRelatedPivotKeyName(), $relatedIds);
                    })
                    ->orWhere(function (QueryBuilder $query) use ($relatedIds) {
                        $query->where($this->getQualifiedRelatedPivotKeyName(), $this->parent->{$this->parentKey})
                            ->whereIn($this->getQualifiedForeignPivotKeyName(), $relatedIds);
                    });
            });
    }


    /**
     * @return QueryBuilder
     */
    protected function newPivotQueryWithConstraints()
    {
        $query = $this->newPivotStatement();

        foreach ($this->pivotWheres as $arguments) {
            $query->where(...$arguments);
        }

        foreach ($this->pivotWhereIns as $arguments) {
            $query->whereIn(...$arguments);
        }

        foreach ($this->pivotWhereNulls ?? [] as $arguments) {
            $query->whereNull(...$arguments);
        }

        return $query;
    }

    /**
     * Get all of the IDs for the related models.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allRelatedIds()
    {
        $parentId = $this->parent->{$this->parentKey};

        return $this->newPivotQuery()
            ->get([$this->foreignPivotKey, $this->relatedPivotKey])
            ->map(function ($pivotEntity) use ($parentId) {
                if ($parentId == $pivotEntity->{$this->foreignPivotKey}) {
                    return $pivotEntity->{$this->relatedPivotKey};
                }
                else {
                    return $pivotEntity->{$this->foreignPivotKey};
                }
            });
    }



    //region Laravel Missing functions in this version
    /**
     * Get the fully qualified related key name for the relation.
     *
     * @return string
     */
    public function getQualifiedRelatedKeyName()
    {
        return $this->related->qualifyColumn($this->relatedKey);
    }

    /**
     * Get the fully qualified foreign key for the relation.
     *
     * @return string
     */
    public function getQualifiedForeignPivotKeyName()
    {
        return $this->qualifyPivotColumn($this->foreignPivotKey);
    }

    /**
     * Get the fully qualified "related key" for the relation.
     *
     * @return string
     */
    public function getQualifiedRelatedPivotKeyName()
    {
        return $this->qualifyPivotColumn($this->relatedPivotKey);
    }

    /**
     * Get a dictionary key attribute - casting it to a string if necessary.
     *
     * @param  mixed  $attribute
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function getDictionaryKey($attribute)
    {
        if (is_object($attribute)) {
            if (method_exists($attribute, '__toString')) {
                return $attribute->__toString();
            }

            throw new InvalidArgumentException('Model attribute value is an object but does not have a __toString method.');
        }

        return $attribute;
    }
    //endregion
}
