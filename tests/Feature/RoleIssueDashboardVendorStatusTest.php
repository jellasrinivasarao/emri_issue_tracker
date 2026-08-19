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

    public function test_reopen_status_ids_are_resolved_by_status_name_keywords(): void
    {
        $controller = new PageController();

        $rows = [
            ['status_id' => 1, 'status_name' => 'New'],
            ['status_id' => 11, 'status_name' => 'Reopened'],
            ['status_id' => 12, 'status_name' => 'Approved'],
            ['status_id' => 13, 'status_name' => 'Rejected'],
            ['status_id' => 3, 'status_name' => 'Resolved'],
        ];

        $this->assertSame([11], $controller->extractMatchingStatusIds($rows, ['reopened']));
        $this->assertSame([12], $controller->extractMatchingStatusIds($rows, ['approved']));
        $this->assertSame([13], $controller->extractMatchingStatusIds($rows, ['rejected']));
    }

    public function test_ho_and_vendor_actions_require_state_approval_for_reopened_tickets(): void
    {
        $controller = new PageController();

        $this->assertTrue($controller->requiresStateApprovalBeforeRoleAction('HO Admin', 'Reopened'));
        $this->assertTrue($controller->requiresStateApprovalBeforeRoleAction('HO IT', 'Reopened'));
        $this->assertTrue($controller->requiresStateApprovalBeforeRoleAction('Vendor Admin', 'Reopened'));
        $this->assertTrue($controller->requiresStateApprovalBeforeRoleAction('Vendor IT', 'Reopened'));
        $this->assertTrue($controller->requiresStateApprovalBeforeRoleAction('State IT', 'Reopened'));
        $this->assertFalse($controller->requiresStateApprovalBeforeRoleAction('HO Admin', 'Approved'));
        $this->assertFalse($controller->requiresStateApprovalBeforeRoleAction('State Admin', 'Reopened'));
    }
}
