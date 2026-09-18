<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserMasterVendorSelectionTest extends TestCase
{
    public function test_vendor_roles_keep_vendor_field_visible_for_edit_modal(): void
    {
        $view = file_get_contents(base_path('resources/views/pages/user-master.blade.php'));

        $this->assertStringContainsString("const isVendorRole =", $view);
        $this->assertStringContainsString("roleName.includes('vendor')", $view);
        $this->assertStringContainsString("vendorGroup.classList.remove('hidden');", $view);
    }
}
