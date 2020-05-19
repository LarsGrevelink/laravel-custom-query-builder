<?php

namespace LGrevelink\CustomQueryBuilder\Concerns\QueryBuilder;

trait QualifiesWildcardColumns
{
    /**
     * @inheritdoc
     */
    public function select($columns = ['*'])
    {
        return parent::select($this->qualifyWildcardColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function find($id, $columns = ['*'])
    {
        return parent::find($id, $this->qualifyWildcardColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function first($columns = ['*'])
    {
        return parent::first($this->qualifyWildcardColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function get($columns = ['*'])
    {
        return parent::get($this->qualifyWildcardColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return parent::paginate($perPage, $this->qualifyWildcardColumns($columns), $pageName, $page);
    }

    /**
     * @inheritdoc
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return parent::simplePaginate($perPage, $this->qualifyWildcardColumns($columns), $pageName, $page);
    }

    /**
     * Qualify the given column names by the model's table.
     *
     * @param array|string $columns
     *
     * @return array
     */
    public function qualifyWildcardColumns($columns)
    {
        return array_map(function ($column) {
            if ($column === '*') {
                return $this->qualifyColumn($column);
            }

            return $column;
        }, is_array($columns) ? $columns : func_get_args());
    }
}
