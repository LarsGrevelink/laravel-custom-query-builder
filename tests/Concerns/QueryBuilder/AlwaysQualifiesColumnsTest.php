<?php

namespace Tests\Concerns\QueryBuilder;

use Tests\Mocks\CustomQueryBuilders\CustomQueryBuilderMock;
use Tests\Mocks\Models\CustomModelMock;
use Tests\TestCase;

class AlwaysQualifiesColumnsTest extends TestCase
{
    /**
     * @var CustomQueryBuilderMock
     */
    protected $builder;

    public function setUp(): void
    {
        parent::setUp();

        $this->builder = CustomModelMock::query();
    }

    public function testQualifyColumns()
    {
        // This bubbles through to the QueryBuilder itself
        $qualifiedColumns = $this->builder->qualifyColumns([
            'foo.bar',
            'bar',
        ]);

        $this->assertSame('foo.bar', $qualifiedColumns[0]);
        $this->assertSame('custom_models.bar', $qualifiedColumns[1]);
    }
}
