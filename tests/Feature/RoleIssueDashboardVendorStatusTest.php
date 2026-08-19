<?php

namespace Tests\Feature;

use App\Http\Controllers\PageController;
use Tests\TestCase;

class RoleIssueDashboardVendorStatusTest extends TestCase
{
    public function test_vendor_status_options_render_when_status_options_is_php_array(): void
    {
        $controller = new PageController();

        $message = $controller->getVendorResolutionValidationMessage([
            ['vendor_name' => 'Achala', 'status_name' => 'In Progress', 'is_active' => 1, 'vendor_status_id' => 2],
            ['vendor_name' => 'Convox', 'status_name' => 'Resolved', 'is_active' => 1, 'vendor_status_id' => 3],
        ], 3);

        $this->assertStringContainsString('Ticket cannot be resolved', $message);
        $this->assertStringContainsString('Achala', $message);
        $this->assertStringContainsString('In Progress', $message);
    }

    public function test_vendor_resolution_message_blocks_unresolved_active_vendors(): void
    {
        $controller = new PageController();

        $message = $controller->getVendorResolutionValidationMessage([
            ['vendor_name' => 'Achala', 'is_active' => 1, 'vendor_status_id' => 2],
            ['vendor_name' => 'Convox', 'is_active' => 1, 'vendor_status_id' => 3],
            ['vendor_name' => 'Rejected Vendor', 'is_active' => 0, 'vendor_status_id' => 3],
        ], 3);

        $this->assertStringContainsString('Achala', $message);
        $this->assertStringContainsString('Ticket cannot be resolved', $message);
    }
}
