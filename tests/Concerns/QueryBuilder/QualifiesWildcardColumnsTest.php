<?php

namespace Tests\Concerns\QueryBuilder;

use Tests\Mocks\CustomQueryBuilders\CustomQueryBuilderMock;
use Tests\Mocks\Models\CustomModelMock;
use Tests\TestCase;

class QualifiesWildcardColumnsTest extends TestCase
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

    public function testQualifyWildcardColumns()
    {
        // This bubbles through to the QueryBuilder itself
        $qualifiedColumns = $this->builder->qualifyWildcardColumns([
            'foo.bar',
            'bar',
            '*',
        ]);

        $this->assertSame('foo.bar', $qualifiedColumns[0]);
        $this->assertSame('bar', $qualifiedColumns[1]);
        $this->assertSame('custom_models.*', $qualifiedColumns[2]);
    }
}
