<?php

namespace LGrevelink\CustomQueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait HasCustomQueryBuilder
{
    /**
     * Class reference towards the custom query builder.
     *
     * @var string
     */
    protected $queryBuilder = Builder::class;

    /**
     * Sets the new query builder class which will be used in future
     * query creations.
     *
     * @param string $className
     *
     * @return $this
     */
    public function useQueryBuilder(string $className)
    {
        $this->queryBuilder = $className;

        return $this;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param QueryBuilder $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query)
    {
        return new $this->queryBuilder($query);
    }
}
