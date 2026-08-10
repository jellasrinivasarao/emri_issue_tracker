<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class VendorMasterViewTest extends TestCase
{
    public function test_vendor_master_view_renders_when_optional_contact_fields_are_missing(): void
    {
        $user = new \stdClass();
        $user->menus = collect();
        $user->role_names = 'Central Admin';
        $user->name = 'Test User';
        $user->login_id = 'test.user';

        Auth::shouldReceive('user')->andReturn($user);

        $vendors = [
            (object) [
                'vendor_id' => 1,
                'vendor_name' => 'Acme Supply',
                'is_active' => 1,
            ],
        ];

        $html = view('pages.vendor-master', [
            'title' => 'Vendor Master',
            'description' => 'Manage vendor master records and vendor information.',
            'vendors' => $vendors,
        ])->render();

        $this->assertStringContainsString('Acme Supply', $html);
        $this->assertStringContainsString('Add Vendor', $html);
    }
}
