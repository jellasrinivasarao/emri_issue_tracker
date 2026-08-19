<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoleIssueDashboardVendorOptionsTest extends TestCase
{
    public function test_vendor_options_route_is_registered(): void
    {
        $this->assertSame('/role-issue-dashboard/vendor-options', route('role.issue.vendor.options', [], false));
    }
}
