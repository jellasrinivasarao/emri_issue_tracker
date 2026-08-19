# Vendor Resolution Guard Implementation

## Summary

Implemented a business-rule guard that **prevents the main ticket from being marked Resolved/Completed unless all active assigned vendors have explicitly resolved their work**.

---

## Changes Made

### 1. PageController - Vendor Resolution Validation

**File:** `app/Http/Controllers/PageController.php`

#### New Public Method: `getVendorResolutionValidationMessage()`

Generates a user-friendly validation message for active vendors that are not yet resolved.

```php
public function getVendorResolutionValidationMessage(array $vendorAssignments, int $resolvedStatusId = 3): string
```

**Parameters:**
- `$vendorAssignments` - Array of vendor assignment records
- `$resolvedStatusId` - The status ID for "Resolved" (default: 3)

**Returns:**
- User-friendly message listing pending vendors or confirmation message

**Example Output:**
```
Ticket cannot be resolved while active vendors are pending.
- Achala - In Progress
- Convox - New
```

---

#### Enhanced Method: `updateIssueStatus()`

Added vendor resolution check **before allowing ticket to transition to Resolved/Completed status**.

**Logic:**
1. When status change is to "Resolved" or "Completed":
   - Query all active vendor assignments (`is_active = 1`)
   - Check if ALL active vendors have `vendor_status_id = 3` (Resolved)
   - If any vendor is NOT resolved → Throw validation exception
   - If all vendors resolved → Allow status change

2. Rejected vendors (`is_active = 0`) are **excluded from check**

3. Inactive/non-vendor tickets → No check applied

**Code:**
```php
if ($isResolvedStatus) {
    // Fetch active vendor assignments
    $activeVendorAssignments = DB::table('map_issue_vendor_assignment as m')
        ->leftJoin('mst_issue_status as s', 'm.vendor_status_id', '=', 's.status_id')
        ->leftJoin('mst_vendor as v', 'm.vendor_id', '=', 'v.vendor_id')
        ->where('m.issue_id', $issueId)
        ->where('m.is_active', 1)
        ->get([...]);

    // Check if all are resolved
    if (! empty($activeVendorAssignments) && 
        ! collect($activeVendorAssignments)->every(
            fn ($row) => (int) ($row['vendor_status_id'] ?? 0) === $resolvedStatusId
        )
    ) {
        throw new ValidationException(...);
    }
}
```

---

### 2. Test Coverage

**File:** `tests/Feature/RoleIssueDashboardVendorStatusTest.php`

#### New Test: `test_vendor_status_options_render_when_status_options_is_php_array()`

Unit test for the validation message generator.

**Validates:**
- Message correctly identifies unresolved vendors
- Message includes vendor names and current status
- Message clearly states ticket cannot be resolved

**Test Case:**
```php
$message = $controller->getVendorResolutionValidationMessage([
    ['vendor_name' => 'Achala', 'status_name' => 'In Progress', 'is_active' => 1, 'vendor_status_id' => 2],
    ['vendor_name' => 'Convox', 'status_name' => 'Resolved', 'is_active' => 1, 'vendor_status_id' => 3],
], 3);

$this->assertStringContainsString('Ticket cannot be resolved', $message);
$this->assertStringContainsString('Achala', $message);
$this->assertStringContainsString('In Progress', $message);
```

**File:** `tests/Feature/RoleIssueDashboardVendorOptionsTest.php`

#### Updated Test: `test_vendor_options_route_is_registered()`

Fixed to use route URL path only (without domain/protocol).

---

## Workflow Integration

### Before Vendor Assignment

✅ **No change** - Tickets follow existing workflow (Open → Assigned → In Progress → Resolved → Closed)

### After Vendor Assignment

1. **Vendor Assignment Phase**
   - HO/State user selects vendors
   - Each vendor gets `vendor_status_id = 1` (New)
   - `is_active = 1`

2. **Vendor Resolution Phase**
   - Vendors update their own status via `/issues/{issue}/vendor-status`
   - IssueService auto-resolves main ticket when all vendors are Resolved
   - History recorded in `txn_issue_status_history` with `vendor_id`

3. **Final Resolution Phase** (NEW Guard)
   - If user tries to manually mark ticket as "Resolved"
   - PageController checks: Are ALL active vendors resolved?
   - **NO** → ValidationException with pending vendor list
   - **YES** → Status change allowed

---

## Error Handling

### Validation Exception Format

When ticket cannot be resolved:

```json
{
    "success": false,
    "message": "Unable to update the ticket. Please try again.",
    "errors": {
        "vendor_resolution": "Ticket cannot be resolved while active vendors are pending.\n- Achala - In Progress\n- Convox - New"
    }
}
```

### User Feedback

- AJAX error response shows pending vendor list
- User sees in drawer message box which vendors are blocking resolution
- Clear action: "Contact vendors to complete their work"

---

## Preserved Behaviors

✅ **Legacy Single-Vendor Flow** - Unchanged
✅ **Vendor Assignment Process** - Unchanged  
✅ **Individual Vendor Status Updates** - Unchanged (vendors can update independently)
✅ **Auto-Resolution Logic** - Unchanged (happens in IssueService when all resolved)
✅ **Status History** - Unchanged (records all transitions)

---

## Validation Rules

| Scenario | Result |
|----------|--------|
| No vendors assigned | ✅ Can resolve |
| 1 vendor, Resolved | ✅ Can resolve |
| 1 vendor, In Progress | ❌ Cannot resolve |
| 2 vendors, both Resolved | ✅ Can resolve |
| 2 vendors, 1 Resolved + 1 In Progress | ❌ Cannot resolve |
| 2 vendors, 1 Resolved + 1 Rejected (`is_active=0`) | ✅ Can resolve |
| 3 vendors, all Rejected | ✅ Can resolve |

---

## Testing

### Run Vendor-Specific Tests

```bash
php vendor/bin/phpunit tests/Feature/RoleIssueDashboardVendorStatusTest.php \
                        tests/Feature/RoleIssueDashboardVendorOptionsTest.php
```

**Result:** ✅ All 3 tests pass

---

## Future Enhancements

1. **Vendor Clarification Isolation** - Prevent vendors from seeing each other's issues during clarification phase
2. **Escalation Rules** - Allow escalation to bypass vendor check (if business requires)
3. **Partial Resolution** - Support "Partially Resolved" state for partial vendor completion
4. **SLA Auto-Escalation** - Auto-escalate unresolved vendors after SLA breach

