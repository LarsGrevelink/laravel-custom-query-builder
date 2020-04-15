<?php

namespace Tests\Database\Eloquent;

use Illuminate\Database\Query\Builder;
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
        ])->setMethods(['filterOnCustomFilter', 'filterOnCustomOtherFilters', 'where', 'whereIn'])->getMockForAbstractClass();

        $builder->expects($this->once())->method('filterOnCustomFilter')->willReturnSelf();
        $builder->expects($this->once())->method('filterOnCustomOtherFilters')->willReturnSelf();
        $builder->expects($this->once())->method('where')->with('simple_filter', 'abcdefg');
        $builder->expects($this->once())->method('whereIn')->with('simple_other_filter', ['abcdefg']);

        $builder->applyFilters([
            'custom_filter' => 12345,
            'custom_other_filter' => [12345, 54321],
            'simple_filter' => 'abcdefg',
            'simple_other_filter' => ['abcdefg'],
        ]);
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
        $this->builder->joinOnce('table', 'something', '=', 'else');
        $this->builder->joinOnce('table', 'something', '=', 'else');

        $this->assertSame(1, count($this->builder->getQuery()->joins));

        $this->builder->joinOnce('table as table2', 'something2', '=', 'else2');

        $this->assertSame(2, count($this->builder->getQuery()->joins));
    }
}
