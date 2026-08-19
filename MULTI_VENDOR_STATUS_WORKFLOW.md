# Multi-Vendor Status Workflow Implementation

## Overview

This document describes the implementation of multiple vendor status tracking in the EMRI Issue Tracker. Each vendor assigned to an issue can have an independent status, and the overall ticket status is calculated based on all active vendors' resolution states.

---

## Architecture

### Core Principle

```
EXISTING VENDOR ASSIGNMENT != VENDOR STATUS

- Vendor Assignment: The assignment of a vendor to work on an issue
- Vendor Status: Each vendor's individual progress/resolution status
```

### Database Tables (No Changes)

All implementation uses existing database columns:

1. **map_issue_vendor_assignment**
   - `vendor_status_id` - Current vendor status (FK to mst_issue_status)
   - `is_active` - Flag indicating if vendor is active (1) or rejected (0)
   - `status_updated_by` - User ID who last updated vendor status
   - `status_updated_at` - Timestamp of last status update
   - `status_remarks` - Comments on vendor status change

2. **txn_issue_status_history**
   - `vendor_id` - Optional vendor ID for vendor-specific status changes
   - Tracks vendor status changes separately from main issue status

3. **txn_issue**
   - `status_id` - Overall ticket status (NOT updated by individual vendor status changes)

---

## Workflow

### 1. Vendor Status Update Flow

**Endpoint:** `POST /issues/{issue}/vendor-status`

```php
POST /issues/127/vendor-status
{
    "vendor_id": 5,
    "vendor_status_id": 3,  // 3 = Resolved status
    "remarks": "Issue resolved"
}
```

**Process:**
1. Validate vendor is assigned to issue
2. Call `IssueService::updateVendorStatus()`
3. Update vendor's row in `map_issue_vendor_assignment`
4. Record status change in `txn_issue_status_history` with vendor_id
5. Do NOT update main `txn_issue.status_id`

**Code Location:**
- Controller: [app/Http/Controllers/IssueController.php](app/Http/Controllers/IssueController.php#L1155)
- Service: [app/Services/IssueService.php](app/Services/IssueService.php#L1977)
- Route: [routes/web.php](routes/web.php#L104)

---

### 2. Vendor Status States

#### Active Vendor
- `is_active = 1`
- Can have any status: New, In Progress, Resolved, etc.
- Counts toward vendor resolution requirement

#### Rejected Vendor
- `is_active = 0`
- Status = "Reject"
- Does NOT count toward vendor resolution requirement
- Previous rejection does not count as resolution

---

### 3. Vendor Resolution Calculation

**Method:** `IssueService::getVendorProgress()`

**Calculation:**
1. Query all vendors with `is_active = 1`
2. Count total active vendors
3. Count vendors with `vendor_status_id = 3` (Resolved)
4. Return counts

**Example:**
```
Issue IS-127 has:
- Achala: is_active=1, status_id=3 (Resolved)
- Convox: is_active=1, status_id=2 (In Progress)
- TechCorp: is_active=0 (Rejected)

Result:
- Active vendors = 2
- Resolved vendors = 1
- Workflow INCOMPLETE (1/2 resolved)
```

---

### 4. Issue Close Validation

**Method:** `IssueService::validateVendorResolutionBeforeClose()`

**Validation Rules:**
1. If NO vendors assigned → Allow close
2. If any `is_active = 0` vendors → Ignore them
3. For all `is_active = 1` vendors:
   - Check if all have `vendor_status_id = 3` (Resolved)
   - If NOT all resolved → BLOCK close
   - If all resolved → Allow close

**Error Message:**
```
"Ticket cannot be closed. The following vendors have not resolved the issue:
- Convox - In Progress
- TechCorp - Pending Review"
```

**Code Location:**
[app/Services/IssueService.php](app/Services/IssueService.php#L1937)

---

### 5. Issue Queue Display

**Location:** [resources/views/pages/role-issue-dashboard.blade.php](resources/views/pages/role-issue-dashboard.blade.php#L143-L152)

**Display Format for Multi-Vendor Tickets:**

```
Ticket: IS-127
Project: 108
Vendors:
  - Achala: Resolved ✓
  - Convox: In Progress ◐
```

**Data Source:** PageController builds `vendor_progress` array:
```php
$vendorProgress = [
    'active_count' => 2,
    'resolved_count' => 1,
    'total_count' => 2,
    'vendors' => [
        ['vendor_id' => 1, 'vendor_name' => 'Achala', 'status_name' => 'Resolved', 'is_active' => 1, 'is_resolved' => 1],
        ['vendor_id' => 2, 'vendor_name' => 'Convox', 'status_name' => 'In Progress', 'is_active' => 1, 'is_resolved' => 0],
    ],
    'display' => "1 / 2 Resolved",
    'status_text' => "Vendor Work Pending"
]
```

---

## Requirements Verification

### ✅ Requirement 1: Multiple Vendor Status Tracking
- Each vendor has independent status in `map_issue_vendor_assignment.vendor_status_id`
- Vendor status changes do NOT affect main ticket status
- Status changes recorded in `txn_issue_status_history` with vendor_id

### ✅ Requirement 2: Vendor Status Update Endpoint
- Endpoint: `POST /issues/{issue}/vendor-status`
- Updates only the specific vendor's row
- Does NOT update `txn_issue.status_id`
- History recorded with vendor_id

### ✅ Requirement 3: Issue Queue Display
- Shows vendor-wise status for each vendor assigned
- Displays color-coded status indicators
- Shows count of resolved vendors (e.g., "1 / 2 Resolved")

### ✅ Requirement 4: Vendor Resolution Calculation
- Correctly calculates from active vendors only
- Rejected vendors excluded from count
- Only `vendor_status_id = 3` counts as resolved

### ✅ Requirement 5: Close Validation
- Blocks close if any active vendor not resolved
- Provides detailed error message with pending vendors
- Allows close only when all active vendors are resolved

### ✅ Requirement 6: Reject Handling
- Sets `is_active = 0` when vendor rejects
- Rejected vendor removed from active requirement count
- History records rejection

### ✅ Requirement 7: Status History
- Vendor status changes recorded in `txn_issue_status_history`
- Vendor_id included in history for tracking
- Separate from main ticket status history

### ✅ Requirement 8: Existing Functionality Preserved
- Single-vendor workflow unchanged
- Manual vendor assignment unchanged
- Role -> Status mapping unchanged
- Existing close workflow enhanced (not replaced)

---

## Usage Examples

### Example 1: Update Vendor Status (JavaScript/Fetch)

```javascript
async function updateVendorStatus(issueId, vendorId, statusId, remarks) {
    const response = await fetch(`/issues/${issueId}/vendor-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            vendor_id: vendorId,
            vendor_status_id: statusId,
            remarks: remarks
        })
    });
    
    const data = await response.json();
    if (data.success) {
        console.log('Vendor status updated:', data.data);
    } else {
        console.error('Error:', data.message);
    }
}

// Usage
updateVendorStatus(127, 5, 3, 'Issue resolved at customer site');
```

### Example 2: Check Vendor Resolution Before Close

```php
use App\Models\Issue;
use App\Services\IssueService;

$issue = Issue::find(127);
$service = app(IssueService::class);

try {
    $service->validateVendorResolutionBeforeClose($issue);
    // Can close the ticket
} catch (\Illuminate\Validation\ValidationException $e) {
    // Cannot close - vendors pending
    $errors = $e->errors();
    echo $errors['vendor_resolution'][0];  // Error message with pending vendors
}
```

### Example 3: Get Vendor Progress

```php
$issue = Issue::find(127);
$service = app(IssueService::class);

$progress = $service->getVendorProgress($issue);

echo "Active vendors: " . $progress['active_count'];      // 2
echo "Resolved: " . $progress['resolved_count'];          // 1
echo "Status: " . $progress['status_text'];               // "Vendor Work Pending"
```

---

## Testing Scenarios

### Scenario A: All Vendors Resolved
1. Assign Achala and Convox to issue
2. Achala updates status to Resolved
3. Convox updates status to Resolved
4. Close button enabled
5. User can close ticket ✅

### Scenario B: One Vendor Resolved, One Pending
1. Assign Achala and Convox to issue
2. Achala updates status to Resolved
3. Convox status still In Progress
4. Close button disabled
5. Error message shows "Convox - In Progress" ❌

### Scenario C: Vendor Rejects
1. Assign Achala and Convox to issue
2. Convox selects Reject
3. Convox becomes inactive (is_active=0)
4. Only Achala counts as active vendor requirement
5. If Achala resolved, close allowed ✅

### Scenario D: Single Vendor Ticket
1. Assign only Achala
2. Achala updates status to Resolved
3. Close allowed (no multi-vendor complexity) ✅

### Scenario E: No Vendors
1. Create issue without vendor assignment
2. Close allowed (no vendor requirements) ✅

---

## Key Differences from Old Workflow

### OLD (Single Vendor Only)
```
Issue Status (txn_issue.status_id)
    = Vendor Status
    = Ticket Progress
```

### NEW (Multi-Vendor)
```
Individual Vendor Status (map_issue_vendor_assignment.vendor_status_id)
    ↓
Aggregated Vendor Progress (getVendorProgress)
    ↓
Close Validation (validateVendorResolutionBeforeClose)
    ↓
Issue Status (txn_issue.status_id)
    = Ticket Progress (calculated from all vendors)
```

---

## Files Modified

1. **[app/Http/Controllers/IssueController.php](app/Http/Controllers/IssueController.php)**
   - Added `updateVendorStatus()` method

2. **[routes/web.php](routes/web.php)**
   - Added vendor status update route

3. **No database changes required** - All columns already exist

---

## Existing Services (No Changes Needed)

These services already have correct implementation:

- `IssueService::updateVendorStatus()` - Updates vendor status
- `IssueService::validateVendorResolutionBeforeClose()` - Validates close
- `IssueService::getVendorProgress()` - Calculates progress
- `IssueService::getPendingVendorsList()` - Lists pending vendors
- `IssueService::createStatusHistory()` - Records history with vendor_id

---

## Troubleshooting

### Issue: Vendor status not updating
- Verify vendor is assigned to issue first
- Check `map_issue_vendor_assignment` has vendor_id record
- Check status_id is valid

### Issue: Close still allowed with pending vendor
- Check `validateVendorResolutionBeforeClose()` is called
- Verify vendor `is_active = 1`
- Verify vendor `vendor_status_id != 3`

### Issue: History not recording vendor_id
- Check `createStatusHistory()` receives vendor_id parameter
- Verify `txn_issue_status_history` table has vendor_id column
- Check Laravel migration/schema

---

## Support

For issues or questions about this workflow:
1. Check the logs in `storage/logs/`
2. Review database records in `map_issue_vendor_assignment`
3. Check `txn_issue_status_history` for history records
