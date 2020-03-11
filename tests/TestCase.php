<?php

namespace Tests;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Mocks\Models\CustomModelMock;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        $this->setUpDatabase();
        $this->setUpData();
    }

    public function tearDown(): void
    {
    }

    private function setUpDatabase()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        Capsule::schema()->create('custom_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });
    }

    private function setUpData()
    {
        foreach (range(1, 10) as $item) {
            (new CustomModelMock())->forceFill([
                'title' => 'Title ' . $item,
            ])->save();
        }
    }
}
