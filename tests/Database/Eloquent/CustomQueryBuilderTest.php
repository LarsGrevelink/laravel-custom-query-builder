<?php

namespace Tests\Database\Eloquent;

use Illuminate\Database\Query\Builder;
use LGrevelink\CustomQueryBuilder\Exceptions\QueryBuilder\InvalidFilterException;
use PHPUnit\Framework\MockObject\MockObject;
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
        /** @var CustomQueryBuilderMock|MockObject */
        $builder = $this->getMockBuilder(CustomQueryBuilderMock::class)->setConstructorArgs([
            $this->getMockForAbstractClass(Builder::class, [], '', false),
        ])->setMethods(['filterOnSomeId', 'filterOnOtherIds'])->getMockForAbstractClass();

        $builder->expects($this->once())->method('filterOnSomeId')->willReturnSelf();
        $builder->expects($this->once())->method('filterOnOtherIds')->willReturnSelf();

        $builder->applyFilters([
            'some_id' => 12345,
            'other_id' => [12345, 54321],
        ]);
    }

    public function testApplyFiltersException()
    {
        $this->expectException(InvalidFilterException::class);
        $this->expectExceptionMessage('Invalid filter for query builder');

        $this->builder->applyFilters([
            'unknown_filter' => 12345,
        ]);
    }

    public function testComposeFilterName()
    {
        $this->assertSame('filterOnSomeId', TestUtil::invokeMethod($this->builder, 'composeFilterName', ['some_id', false]));
        $this->assertSame('filterOnOtherIds', TestUtil::invokeMethod($this->builder, 'composeFilterName', ['other_id', true]));
    }

    public function testJoinOnce()
    {
        $this->builder->joinOnce('table', 'something', '=', 'else');
        $this->builder->joinOnce('table', 'something', '=', 'else');

        $this->assertSame(1, count($this->builder->getQuery()->joins));

        $this->builder->joinOnce('table as table2', 'something2', '=', 'else2');

        $this->assertSame(2, count($this->builder->getQuery()->joins));
    }
}
