<?php

namespace Tests\Concerns;

use LGrevelink\CustomQueryBuilder\Concerns\HasCustomQueryBuilder;
use LGrevelink\CustomQueryBuilder\Database\Eloquent\CustomQueryBuilder;
use Tests\Mocks\CustomQueryBuilders\CustomQueryBuilderMock;
use Tests\Mocks\Models\CustomModelMock;
use Tests\TestCase;
use Tests\TestUtil;

class HasCustomQueryBuilderTest extends TestCase
{
    /**
     * @var HasCustomQueryBuilder
     */
    protected $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = $this->getMockForTrait(HasCustomQueryBuilder::class);
    }

    public function testNewEloquentBuilder()
    {
        $this->assertInstanceOf(CustomQueryBuilder::class, CustomModelMock::query());
    }

    public function testSetQueryBuilder()
    {
        $customQueryBuilder = CustomQueryBuilderMock::class;

        $this->assertSame(TestUtil::getProperty($this->trait, 'queryBuilder'), CustomQueryBuilder::class);

        $this->trait->useQueryBuilder($customQueryBuilder);

        $this->assertSame(TestUtil::getProperty($this->trait, 'queryBuilder'), $customQueryBuilder);
    }
}
