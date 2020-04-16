<?php

namespace Tests;

use Tests\Mocks\CustomQueryBuilders\CustomQueryBuilderMock;
use Tests\Mocks\Models\CustomModelMock;

class ModelTest extends TestCase
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

    public function testSingleFilter()
    {
        $result = $this->builder->filterOnId(1)->get();

        $this->assertSame(1, $result->count());
        $this->assertSame($result->first()->id, 1);
    }

    public function testMultiFilter()
    {
        $result = $this->builder->filterOnTitles(['Title 1', 'Title 2', 'Title 3', 'Title 999'])->get();

        $this->assertSame(3, $result->count());

        $result->each(function ($model, $key) {
            $this->assertSame($model->id, $key + 1);
        });
    }

    public function testCombiFilter()
    {
        $result = $this->builder->filterOnId(2)->filterOnTitles(['Title 1', 'Title 2', 'Title 3', 'Title 999'])->get();

        $this->assertSame(1, $result->count());
        $this->assertSame($result->first()->id, 2);
    }
}
