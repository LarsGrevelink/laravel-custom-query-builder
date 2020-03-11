<?php

namespace Tests\Mocks\Models;

use LGrevelink\CustomQueryBuilder\Model;
use Tests\Mocks\CustomQueryBuilders\CustomQueryBuilderMock;

/**
 * @method CustomQueryBuilderMock query()
 */
class CustomModelMock extends Model
{
    /**
     * @inheritdoc
     */
    protected $queryBuilder = CustomQueryBuilderMock::class;

    /**
     * @inheritdoc
     */
    protected $table = 'custom_models';
}
