<?php

namespace Tests\Database\Eloquent;

use Tests\Mocks\CustomQueryBuilders\CustomQueryBuilderMock;
use Tests\Mocks\Models\CustomModelMock;
use Tests\TestCase;
use Tests\TestUtil;

class CustomQueryBuilderTest extends TestCase
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

    public function testApplyFilters()
    {
        $this->builder->applyFilters([
            'some_column' => 'foo',
            'id' => 2,
            'some_other_column' => ['foo', 'bar'],
            'title' => ['Title 1', 'Title 2'],
        ]);

        // Verify single simple filter
        $where = $this->builder->getQuery()->wheres[0];

        $this->assertSame('Basic', $where['type']);
        $this->assertSame('custom_models.some_column', $where['column']);
        $this->assertSame('=', $where['operator']);
        $this->assertSame('foo', $where['value']);
        $this->assertSame('and', $where['boolean']);

        // Verify single complex filter
        $where = $this->builder->getQuery()->wheres[1];

        $this->assertSame('Basic', $where['type']);
        $this->assertSame('id', $where['column']);
        $this->assertSame('=', $where['operator']);
        $this->assertSame(2, $where['value']);
        $this->assertSame('and', $where['boolean']);

        // Verify multi simple filter
        $where = $this->builder->getQuery()->wheres[2];

        $this->assertSame('In', $where['type']);
        $this->assertSame('custom_models.some_other_column', $where['column']);
        $this->assertSame(['foo', 'bar'], $where['values']);
        $this->assertSame('and', $where['boolean']);

        // Verify multi complex filter
        $where = $this->builder->getQuery()->wheres[3];

        $this->assertSame('In', $where['type']);
        $this->assertSame('title', $where['column']);
        $this->assertSame(['Title 1', 'Title 2'], $where['values']);
        $this->assertSame('and', $where['boolean']);
    }

    public function testApplyFilter()
    {
        // Verify single simple filter
        $this->builder->applyFilter('some_column', 'foo');

        $where = $this->builder->getQuery()->wheres[0];

        $this->assertSame('Basic', $where['type']);
        $this->assertSame('custom_models.some_column', $where['column']);
        $this->assertSame('=', $where['operator']);
        $this->assertSame('foo', $where['value']);
        $this->assertSame('and', $where['boolean']);

        // Verify single complex filter
        $this->builder->applyFilter('id', 2);

        $where = $this->builder->getQuery()->wheres[1];

        $this->assertSame('Basic', $where['type']);
        $this->assertSame('id', $where['column']);
        $this->assertSame('=', $where['operator']);
        $this->assertSame(2, $where['value']);
        $this->assertSame('and', $where['boolean']);

        // Verify multi simple filter
        $this->builder->applyFilter('some_other_column', ['foo', 'bar']);

        $where = $this->builder->getQuery()->wheres[2];

        $this->assertSame('In', $where['type']);
        $this->assertSame('custom_models.some_other_column', $where['column']);
        $this->assertSame(['foo', 'bar'], $where['values']);
        $this->assertSame('and', $where['boolean']);

        // Verify multi complex filter
        $this->builder->applyFilter('title', ['Title 1', 'Title 2']);

        $where = $this->builder->getQuery()->wheres[3];

        $this->assertSame('In', $where['type']);
        $this->assertSame('title', $where['column']);
        $this->assertSame(['Title 1', 'Title 2'], $where['values']);
        $this->assertSame('and', $where['boolean']);
    }

    public function testComposeFilterName()
    {
        $this->assertSame('filterOnSomeId', TestUtil::invokeMethod($this->builder, 'composeFilterName', ['some_id', false]));
        $this->assertSame('filterOnOtherIds', TestUtil::invokeMethod($this->builder, 'composeFilterName', ['other_id', true]));
    }

    public function testComposeSortingName()
    {
        $this->assertSame('sortBySomeId', TestUtil::invokeMethod($this->builder, 'composeSortName', ['some_id']));
    }

    public function testGetStudlyName()
    {
        $this->assertSame('SomeId', TestUtil::invokeMethod($this->builder, 'getStudlyName', ['some_id', false]));
        $this->assertSame('OtherIds', TestUtil::invokeMethod($this->builder, 'getStudlyName', ['other_id', true]));
    }

    public function testJoinOnce()
    {
        $this->assertNull($this->builder->getQuery()->joins);

        $this->builder->joinOnce('table', 'something', '=', 'else');

        $this->assertSame(1, count($this->builder->getQuery()->joins));

        $this->builder->joinOnce('table', 'something', '=', 'else');

        $this->assertSame(1, count($this->builder->getQuery()->joins));

        $this->builder->joinOnce('table as table2', 'something2', '=', 'else2');

        $this->assertSame(2, count($this->builder->getQuery()->joins));
    }
}
