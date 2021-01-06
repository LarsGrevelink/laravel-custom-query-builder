<?php

namespace LGrevelink\CustomQueryBuilder\Concerns;

use LGrevelink\CustomQueryBuilder\Database\Eloquent\CustomQueryBuilder;

trait HasCustomQueryBuilder
{
    /**
     * Class reference towards the custom query builder.
     *
     * @var string
     */
    protected $queryBuilder = CustomQueryBuilder::class;

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
     * @inheritdoc
     */
    public function newEloquentBuilder($query)
    {
        return new $this->queryBuilder($query);
    }

    /**
     * @inheritdoc
     */
    public function registerGlobalScopes($builder)
    {
        // Trigger a builder's registerGlobalScopes when present
        if (method_exists($builder, 'registerGlobalScopes')) {
            $builder->registerGlobalScopes();
        }

        return parent::registerGlobalScopes($builder);
    }
}
