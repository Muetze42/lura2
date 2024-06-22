<?php

namespace Tests;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

abstract class ModelTestCase extends TestCase
{
    protected Model|Customer $instance;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
        $class = class_basename(get_called_class());
        $this->instance = app('App\Models\\' . substr($class, 0, -4));
    }

    protected function resource(): Model|Customer
    {
        return $this->instance->factory()->create();
    }

    /**
     * @test
     */
    public function canCreate(): void
    {
        $resource = $this->resource();

        $this->assertModelExists($resource);
        $this->assertDatabaseHas($this->instance->getTable(), [
            $this->instance->getKeyName() => $resource->getKey(),
        ]);
    }

    /**
     * @test
     */
    public function canDelete(): void
    {
        $resource = $this->resource();
        $resource->delete();

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($resource))) {
            $this->assertSoftDeleted($resource);
            $resource->forceDelete();
        }

        $this->assertModelMissing($resource);
    }

    /**
     * @test
     */
    public function canRestore(): void
    {
        $resource = $this->resource();
        $resource->delete();

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($resource))) {
            $resource->restore();
            $this->assertNotSoftDeleted($resource);
        } else {
            $this->assertFalse(false);
        }
    }
}
