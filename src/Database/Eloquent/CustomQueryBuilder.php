<?php

namespace LGrevelink\CustomQueryBuilder\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LGrevelink\CustomQueryBuilder\Concerns\QueryBuilder\AlwaysQualifiesColumns;
use LGrevelink\CustomQueryBuilder\Exceptions\QueryBuilder\InvalidFilterException;

abstract class CustomQueryBuilder extends Builder
{
    use AlwaysQualifiesColumns;

    /**
     * Query builder filter method format.
     *
     * @var string
     */
    protected const FORMAT_FILTER_METHOD = 'filterOn%s';

    /**
     * Apply a set of filters to the query builder.
     *
     * @param array $filters
     *
     * @throws InvalidFilterException
     *
     * @return $this
     */
    public function applyFilters(array $filters)
    {
        foreach ($filters as $filter => $value) {
            $filterName = $this->composeFilterName($filter, is_array($value));

            if (!method_exists($this, $filterName)) {
                throw new InvalidFilterException('Invalid filter for query builder');
            }

            $this->$filterName($value);
        }

        return $this;
    }

    /**
     * Composes a filter name based on the set format.
     *
     * @param string $filter
     * @param bool $plural
     *
     * @return string
     */
    protected function composeFilterName(string $filter, bool $plural = false)
    {
        $studlyFilter = Str::studly($filter);

        if ($plural) {
            $studlyFilter = Str::pluralStudly($studlyFilter);
        }

        return sprintf(static::FORMAT_FILTER_METHOD, $studlyFilter);
    }

    /**
     * Add a join clause only once to the base query. Simple checking is done on the table name.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     * @param string $type
     * @param bool $where
     *
     * @return $this
     */
    protected function joinOnce($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $baseQueryBuilder = $this->getQuery();

        $join = Arr::first($baseQueryBuilder->joins ?? [], function (JoinClause $join) use ($table) {
            return $join->table === $table;
        });

        if (!$join) {
            $baseQueryBuilder->join($table, $first, $operator, $second, $type, $where);
        }

        return $this;
    }
}
