<?php

namespace Tests\Mocks\CustomQueryBuilders;

use LGrevelink\CustomQueryBuilder\Database\Eloquent\CustomQueryBuilder;

class CustomQueryBuilderMock extends CustomQueryBuilder
{
    public function filterOnId(int $id)
    {
        return $this->where('id', $id);
    }

    public function filterOnTitles(array $titles)
    {
        $this->query->whereIn('title', $titles);

        return $this;
    }
}
