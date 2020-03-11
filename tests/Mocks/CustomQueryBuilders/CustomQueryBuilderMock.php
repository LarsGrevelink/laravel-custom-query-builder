<?php

namespace Tests\Mocks\CustomQueryBuilders;

use LGrevelink\CustomQueryBuilder\Database\Eloquent\CustomQueryBuilder;

class CustomQueryBuilderMock extends CustomQueryBuilder
{
    public function filterOnSomeId(int $id)
    {
        return $this->where('id', $id);
    }

    public function filterOnOtherIds(array $ids)
    {
        $this->query->whereIn('id', $ids);

        return $this;
    }
}
