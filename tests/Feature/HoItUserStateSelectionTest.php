<?php

namespace Tests\Feature;

use App\Http\Requests\Admin\UserMasterRequest;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HoItUserStateSelectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('mst_role', function ($table) {
            $table->increments('role_id');
            $table->string('role_code');
            $table->string('role_name');
            $table->string('role_category')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_system_role')->default(false);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('mst_role');

        parent::tearDown();
    }

    public function test_ho_it_role_requires_multi_state_selection(): void
    {
        $role = Role::query()->create([
            'role_code' => 'HO_IT',
            'role_name' => 'HO IT',
            'role_category' => 'HO',
            'description' => 'HO IT',
            'is_system_role' => 1,
        ]);

        $request = UserMasterRequest::create('/user-master', 'POST', [
            'role_id' => $role->role_id,
        ]);
        $request->setContainer(app());

        $rules = $request->rules();

        $this->assertArrayHasKey('state_ids', $rules);
        $this->assertSame(['required', 'array', 'min:1'], $rules['state_ids']);
    }
}
