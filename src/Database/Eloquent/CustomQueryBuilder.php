<?php

namespace LGrevelink\CustomQueryBuilder\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LGrevelink\CustomQueryBuilder\Concerns\QueryBuilder\QualifiesWildcardColumns;
use LGrevelink\CustomQueryBuilder\Exceptions\Builder\StrictFilterException;

/**
 * @see \Illuminate\Database\Eloquent\Builder
 * @see \Illuminate\Database\Query\Builder
 */
class CustomQueryBuilder extends Builder
{
    use QualifiesWildcardColumns;

    /**
     * Query builder filter method format.
     *
     * @var string
     */
    protected $customFilterFormat = 'filterOn%s';

    /**
     * Query builder sorting method format.
     *
     * @var string
     */
    protected $customSortingFormat = 'sortBy%s';

    /**
     * Default sorting when no direction is explicitly given.
     *
     * @var string
     */
    protected $defaultSortingDirection = 'asc';

    /**
     * Apply a set of filter clauses to the query.
     *
     * @param array $filters
     *
     * @return $this
     */
    public function applyFilters(array $filters)
    {
        foreach ($filters as $filter => $value) {
            $this->applyFilter($filter, $value);
        }

        return $this;
    }

    /**
     * Apply a set of filter clauses to the query.
     *
     * @param string $filter
     * @param mixed $value
     *
     * @return $this
     */
    public function applyFilter(string $filter, $value)
    {
        $customFilter = $this->composeFilterName($filter, is_array($value));

        if (method_exists($this, $customFilter)) {
            $this->$customFilter($value);
        } elseif (config('querybuilder.mode') === 'auto') {
            $this->applyDefaultFilter($filter, $value);
        } else {
            throw new StrictFilterException('Unsupported filter: ' . $customFilter);
        }

        return $this;
    }

    /**
     * Applies default filter behaviour on non-custom filters.
     *
     * @param string $column
     * @param mixed $value
     *
     * @return $this
     */
    protected function applyDefaultFilter(string $column, $value)
    {
        if (is_array($value)) {
            $this->query->whereIn($column, $value);
        } else {
            $this->where($column, $value);
        }

        return $this;
    }

    /**
     * Apply a set of sorting clauses to the query.
     *
     * @param array $sorts
     *
     * @return $this
     */
    public function applySortings(array $sorts)
    {
        foreach ($sorts as $sortBy => $direction) {
            if (is_numeric($sortBy)) {
                $this->applySorting($direction, $this->defaultSortingDirection);
            } else {
                $this->applySorting($sortBy, $direction);
            }
        }

        return $this;
    }

    /**
     * Applies a single sorting clause to the query.
     *
     * @param string $sortBy
     * @param string $direction
     *
     * @return $this
     */
    public function applySorting(string $sortBy, string $direction)
    {
        $customSorting = $this->composeSortName($sortBy);

        if (method_exists($this, $customSorting)) {
            $this->$customSorting($direction);
        } else {
            $this->query->orderBy($sortBy, $direction);
        }

        return $this;
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
    public function joinOnce($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $join = Arr::first($this->query->joins ?? [], static function (JoinClause $join) use ($table) {
            return $join->table === $table;
        });

        if (!$join) {
            $this->query->join($table, $first, $operator, $second, $type, $where);
        }

        return $this;
    }

    /**
     * Composes a custom filter function name based on the set format.
     *
     * @param string $filter
     * @param bool $plural
     *
     * @return string
     */
    protected function composeFilterName(string $filter, bool $plural = false)
    {
        return sprintf($this->customFilterFormat, $this->getStudlyName($filter, $plural));
    }

    /**
     * Composes a custom sort function name based on the set format.
     *
     * @param string $sortBy
     *
     * @return string
     */
    protected function composeSortName(string $sortBy)
    {
        return sprintf($this->customSortingFormat, $this->getStudlyName($sortBy));
    }

    /**
     * Composes a studly (pluralised) version of the given name.
     *
     * @param string $name
     * @param bool $plural
     *
     * @return string
     */
    protected function getStudlyName(string $name, bool $plural = false)
    {
        $studlyName = Str::studly($name);

        if ($plural) {
            return Str::pluralStudly($studlyName);
        }

        return $studlyName;
    }
}
