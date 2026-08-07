<?php

namespace Tests\Unit;

use App\Models\Module;
use PHPUnit\Framework\TestCase;

class ModuleModelTest extends TestCase
{
    public function test_module_model_allows_created_and_updated_by_fields(): void
    {
        $model = new Module();

        $this->assertContains('created_by', $model->getFillable());
        $this->assertContains('updated_by', $model->getFillable());
    }
}
