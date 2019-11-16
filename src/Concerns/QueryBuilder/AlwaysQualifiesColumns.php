<?php

namespace LGrevelink\CustomQueryBuilder\Concerns\QueryBuilder;

use Illuminate\Support\Arr;

trait AlwaysQualifiesColumns
{
    /**
     * @inheritdoc
     */
    public function select($columns = ['*'])
    {
        return parent::select($this->qualifyColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function find($id, $columns = ['*'])
    {
        return parent::find($id, $this->qualifyColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function first($columns = ['*'])
    {
        return parent::first($this->qualifyColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function getModels($columns = ['*'])
    {
        return parent::getModels($this->qualifyColumns($columns));
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return parent::paginate($perPage, $this->qualifyColumns($columns), $pageName, $page);
    }

    /**
     * @inheritdoc
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return parent::simplePaginate($perPage, $this->qualifyColumns($columns), $pageName, $page);
    }

    /**
     * Qualify the given column names by the model's table.
     *
     * @param array|string $columns
     *
     * @return array
     */
    public function qualifyColumns($columns)
    {
        return array_map(function ($column) {
            return $this->qualifyColumn($column);
        }, Arr::wrap($columns));
    }
}
