# MULTIPLE VENDOR STATUS WORKFLOW - COMPLETE IMPLEMENTATION

## Overview

This document describes the complete multi-vendor status workflow implementation for issue tracking. Each vendor assigned to an issue tracks an independent status, and the main ticket status automatically resolves when all active vendors have resolved their work.

---

## ARCHITECTURE

### Database Tables Used

1. **txn_issue** - Main issue/ticket table
   - `issue_id` - Primary key
   - `status_id` - Overall ticket status (updated only when ALL vendors resolved)
   - `created_at`, `updated_at`

2. **map_issue_vendor_assignment** - Vendor assignment tracking (CRITICAL)
   - `issue_id` - Foreign key to txn_issue
   - `vendor_id` - Foreign key to mst_vendor
   - `vendor_status_id` - Each vendor's individual status
   - `is_active` - Flag (1=active, 0=inactive/rejected)
   - `status_updated_by` - User who updated vendor status
   - `status_updated_at` - When vendor status changed
   - `status_remarks` - Notes from vendor status change

3. **txn_issue_status_history** - Complete status change audit trail
   - `issue_id` - Foreign key to txn_issue
   - `vendor_id` - Foreign key to mst_vendor (NULL for main ticket status changes)
   - `new_status_id` - Status after change
   - `changed_by_user_id` - User who made the change
   - `comment` - Change notes

4. **mst_issue_status** - Status master
   - `status_id` - Primary key
   - `status_name` - Status name (e.g., "New", "In Progress", "Resolved", "Rejected")

5. **mst_vendor** - Vendor master
   - `vendor_id` - Primary key
   - `vendor_name` - Vendor name (e.g., "Achala", "Convox")

---

## WORKFLOW FLOW

### Complete Sequence Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│ VENDOR PERFORMS ACTION (e.g., Mark as "In Progress")            │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ IssueController::updateVendorStatus()                           │
│ Validates:                                                       │
│  - vendor_id exists in mst_vendor                              │
│  - vendor_status_id exists in mst_issue_status                 │
│  - vendor assigned to this issue in map_issue_vendor_assignment│
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ IssueService::updateVendorStatus()                              │
│ 1. Update ONLY this vendor's record in                         │
│    map_issue_vendor_assignment:                                │
│    - vendor_status_id = new status                             │
│    - status_updated_by = current user                          │
│    - status_updated_at = current time                          │
│    - is_active = 0 if Reject, else 1                          │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. Insert vendor status change into                             │
│    txn_issue_status_history:                                   │
│    - vendor_id = this vendor                                   │
│    - new_status_id = vendor's new status                       │
│    - comment = remarks (if provided)                           │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. Calculate Vendor Completion:                                 │
│                                                                 │
│    active_count = COUNT(map_issue_vendor_assignment             │
│                   WHERE is_active = 1)                         │
│                                                                 │
│    resolved_count = COUNT(map_issue_vendor_assignment          │
│                    WHERE is_active = 1                         │
│                    AND vendor_status_id = 3)  // Resolved     │
└────────────────┬────────────────────────────────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
   NOT RESOLVED      ALL RESOLVED
   (Keep existing)   (Condition satisfied)
        │                 │
        │                 ▼
        │       ┌──────────────────────────────┐
        │       │ Update txn_issue:            │
        │       │  status_id = Resolved (3)    │
        │       │  updated_at = now()          │
        │       └──────────┬───────────────────┘
        │                  │
        │                  ▼
        │       ┌──────────────────────────────┐
        │       │ Insert overall resolution    │
        │       │ in txn_issue_status_history: │
        │       │  - vendor_id = NULL          │
        │       │  - new_status_id = 3         │
        │       │  - comment = "All active     │
        │       │             vendors resolved"│
        │       └──────────────────────────────┘
        │
        └──────────────────────┐
                               │
                               ▼
                    ┌─────────────────────┐
                    │ Return Success JSON │
                    │ Issue updated       │
                    └─────────────────────┘
```

---

## GRID DISPLAY (Issue Queue)

### What Users See

Each ticket row shows:

| TICKET ID | VENDORS | STATUS | PRIORITY | UPDATED ON |
|-----------|---------|--------|----------|------------|
| IS-20260817127 | Achala, Convox | Achala - In Progress<br/>Convox - New | Critical | 2026-08-17 |
| IS-20260817128 | Achala | Achala - Resolved | High | 2026-08-16 |

### Data Source

**Grid Status Column** (resources/views/pages/role-issue-dashboard.blade.php, line 164):
```blade
@if(!empty($ticket['vendor_progress']['vendors']))
    <div class="flex flex-col gap-1">
        @foreach($ticket['vendor_progress']['vendors'] as $vendor)
            <div class="flex items-center gap-1 text-[10px]">
                <span class="font-medium text-slate-600">{{ $vendor['vendor_name'] }}</span>
                <span class="text-slate-400">-</span>
                @if($vendor['is_active'])
                    @if($vendor['is_resolved'])
                        <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 font-semibold text-green-700">Resolved</span>
                    @else
                        <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 font-semibold text-yellow-700">{{ $vendor['status_name'] }}</span>
                    @endif
                @else
                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 font-semibold text-red-700">Rejected</span>
                @endif
            </div>
        @endforeach
    </div>
@else
    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700">{{ $ticket['status'] }}</span>
@endif
```

**Data Structure Passed:**
```php
$ticket['vendor_progress'] = [
    'vendors' => [
        [
            'vendor_id' => 1,
            'vendor_name' => 'Achala',
            'status_name' => 'In Progress',
            'is_active' => true,
            'is_resolved' => false,
        ],
        [
            'vendor_id' => 2,
            'vendor_name' => 'Convox',
            'status_name' => 'New',
            'is_active' => true,
            'is_resolved' => false,
        ],
    ],
    'active_count' => 2,
    'resolved_count' => 0,
]
```

**Source:** [app/Http/Controllers/PageController.php](app/Http/Controllers/PageController.php#L776-L828)

---

## KEY COMPONENTS

### 1. Controller Endpoint

**File:** `app/Http/Controllers/IssueController.php::updateVendorStatus()`
**Route:** `POST /issues/{issue}/vendor-status`
**Route Name:** `issues.vendor.status.update`

**Input Validation:**
- `vendor_id` - Must exist in mst_vendor
- `vendor_status_id` - Must exist in mst_issue_status
- `remarks` - Optional string

**Response:**
```json
{
  "success": true,
  "message": "Vendor status updated successfully.",
  "data": {
    "success": true,
    "issue_id": 127,
    "vendor_id": 5,
    "status_id": 3
  }
}
```

### 2. Service Method: updateVendorStatus()

**File:** `app/Services/IssueService.php` (line 1977)

**Logic:**
1. Begin database transaction
2. Update vendor's record in `map_issue_vendor_assignment`
   - Set `vendor_status_id` to new status
   - Set `is_active = 0` if status is "Reject", else 1
   - Set `status_updated_by` to current user ID
   - Set `status_updated_at` to current timestamp
   - Set `status_remarks` to provided remarks
3. Insert vendor status change into `txn_issue_status_history` with `vendor_id`
4. **NEW:** Calculate active vendor completion:
   - Count active vendors (is_active = 1)
   - Count resolved active vendors (is_active = 1 AND vendor_status_id = 3)
5. **NEW:** If all active vendors resolved, automatically update `txn_issue.status_id = 3` (Resolved)
6. **NEW:** If main ticket resolved, insert overall status change in history (vendor_id = NULL)
7. Commit transaction
8. Return success response

### 3. Service Method: getVendorProgress()

**File:** `app/Services/IssueService.php` (line 2084)

**Returns:**
```php
[
    'active_count' => 2,         // Number of is_active=1 vendors
    'resolved_count' => 1,       // Number with vendor_status_id=3
    'assignments' => [...],      // Raw assignment rows
]
```

### 4. Service Method: validateVendorResolutionBeforeClose()

**File:** `app/Services/IssueService.php` (line 1937)

**Logic:**
- Blocks ticket close if any active vendor (is_active=1) has not resolved (vendor_status_id != 3)
- Throws ValidationException with pending vendors list
- Allows close if:
  - No active vendors, OR
  - All active vendors have vendor_status_id = 3 (Resolved), OR
  - All remaining vendors are inactive/rejected (is_active = 0)

### 5. Service Method: getPendingVendorsList()

**File:** `app/Services/IssueService.php` (line 2122)

**Returns list of vendors blocking close:**
```php
[
    [
        'vendor_id' => 5,
        'vendor_name' => 'Convox',
        'status' => 'In Progress',
    ],
]
```

---

## EXAMPLE SCENARIOS

### Scenario 1: Two Vendors, One Resolves

**Initial State:**
```
Ticket: IS-20260817127
Achala  → New       (is_active=1)
Convox  → New       (is_active=1)

Main ticket status: Open
```

**Achala updates to In Progress:**
```
UPDATE map_issue_vendor_assignment
SET vendor_status_id = 2  -- In Progress status ID
WHERE issue_id = 127 AND vendor_id = 1  -- Achala

INSERT txn_issue_status_history
  (issue_id, vendor_id, new_status_id, changed_by_user_id)
VALUES (127, 1, 2, @user_id)

Calculate:
  active_count = 2
  resolved_count = 0
  → NOT all resolved, main ticket stays unchanged

Main ticket status: Open (unchanged)
```

**Grid Display:**
```
Achala - In Progress
Convox - New
```

### Scenario 2: Both Vendors Resolve

**Current State:**
```
Achala  → In Progress (is_active=1)
Convox  → In Progress (is_active=1)

Main ticket status: Open
```

**Convox updates to Resolved:**
```
UPDATE map_issue_vendor_assignment
SET vendor_status_id = 3  -- Resolved status ID
WHERE issue_id = 127 AND vendor_id = 2  -- Convox

INSERT txn_issue_status_history
  (issue_id, vendor_id, new_status_id, changed_by_user_id)
VALUES (127, 2, 3, @user_id)

Calculate:
  active_count = 2
  resolved_count = 1
  → NOT all resolved yet, main ticket stays unchanged

Main ticket status: Open (unchanged)
```

**Grid Display:**
```
Achala - In Progress
Convox - Resolved
```

**Achala updates to Resolved:**
```
UPDATE map_issue_vendor_assignment
SET vendor_status_id = 3  -- Resolved status ID
WHERE issue_id = 127 AND vendor_id = 1  -- Achala

INSERT txn_issue_status_history
  (issue_id, vendor_id, new_status_id, changed_by_user_id)
VALUES (127, 1, 3, @user_id)

Calculate:
  active_count = 2
  resolved_count = 2
  → ✓ ALL ACTIVE VENDORS RESOLVED!
  
AUTO-UPDATE MAIN TICKET:
UPDATE txn_issue
SET status_id = 3, updated_at = now()
WHERE issue_id = 127

INSERT txn_issue_status_history
  (issue_id, vendor_id, new_status_id, changed_by_user_id, comment)
VALUES (127, NULL, 3, @user_id, 'All active vendors resolved')

Main ticket status: Resolved (AUTO-UPDATED)
```

**Grid Display:**
```
Achala - Resolved
Convox - Resolved
```

### Scenario 3: Reject Vendor

**State:**
```
Achala  → Resolved (is_active=1)
Convox  → In Progress (is_active=1)

Main ticket status: Open
```

**Convox Rejects:**
```
UPDATE map_issue_vendor_assignment
SET vendor_status_id = 4,     -- Reject status ID
    is_active = 0             -- Mark as inactive
WHERE issue_id = 127 AND vendor_id = 2  -- Convox

INSERT txn_issue_status_history
  (issue_id, vendor_id, new_status_id, changed_by_user_id)
VALUES (127, 2, 4, @user_id)

Calculate ACTIVE VENDORS ONLY:
  active_count = 1  -- Only Achala (is_active=1)
  resolved_count = 1  -- Achala is Resolved
  → ✓ ALL ACTIVE VENDORS RESOLVED!

AUTO-UPDATE MAIN TICKET:
UPDATE txn_issue
SET status_id = 3, updated_at = now()
WHERE issue_id = 127

INSERT txn_issue_status_history
  (issue_id, vendor_id, new_status_id, changed_by_user_id, comment)
VALUES (127, NULL, 3, @user_id, 'All active vendors resolved')

Main ticket status: Resolved (AUTO-UPDATED)
```

**Grid Display:**
```
Achala - Resolved
Convox - Rejected
```

**Close Validation:**
- Only active vendors are checked: Achala
- Achala status: Resolved ✓
- Close is ALLOWED

---

## IMPORTANT RULES

### Rule 1: Vendor Status vs Main Ticket Status

**NEVER read vendor status from `txn_issue.status_id`**

**ALWAYS read vendor status from `map_issue_vendor_assignment.vendor_status_id`**

**Main ticket status (`txn_issue.status_id`) is updated ONLY when:**
- ALL currently ACTIVE vendors (is_active=1) have resolved (vendor_status_id = 3)

### Rule 2: Individual Updates Don't Affect Main Status

When Vendor A updates status to "In Progress", the main ticket status remains unchanged.

The main ticket status is only updated by the automatic check after any vendor update.

### Rule 3: Reject is Not Resolved

Reject (is_active=0) is NOT counted as Resolved.

Rejected vendors are excluded from the active vendor count.

If Achala is Resolved and Convox is Rejected:
- Active vendors: 1 (Achala)
- Resolved active vendors: 1 (Achala)
- Condition satisfied: Main ticket can become Resolved

### Rule 4: Close Validation

When State/Admin attempts to close the ticket:

```
active_vendors = COUNT(map_issue_vendor_assignment
                       WHERE is_active = 1)

resolved_active_vendors = COUNT(map_issue_vendor_assignment
                                WHERE is_active = 1
                                AND vendor_status_id = 3)

Close allowed IF:
  active_vendors = 0
  OR
  active_vendors = resolved_active_vendors
```

### Rule 5: History Tracking

**Vendor Status Change:**
```
txn_issue_status_history.vendor_id = <vendor_id>
txn_issue_status_history.new_status_id = <vendor's new status>
```

**Main Ticket Auto-Resolution:**
```
txn_issue_status_history.vendor_id = NULL
txn_issue_status_history.new_status_id = 3 (Resolved)
```

---

## FILES MODIFIED

### 1. [resources/views/pages/role-issue-dashboard.blade.php](resources/views/pages/role-issue-dashboard.blade.php#L164)

**Change:** Status column now displays individual vendor statuses instead of main ticket status.

**Before:**
```blade
<span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700">
  {{ $ticket['status'] }}
</span>
```

**After:**
```blade
@if(!empty($ticket['vendor_progress']['vendors']))
    <div class="flex flex-col gap-1">
        @foreach($ticket['vendor_progress']['vendors'] as $vendor)
            <div class="flex items-center gap-1 text-[10px]">
                <span class="font-medium text-slate-600">{{ $vendor['vendor_name'] }}</span>
                <span class="text-slate-400">-</span>
                @if($vendor['is_active'])
                    @if($vendor['is_resolved'])
                        <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 font-semibold text-green-700">Resolved</span>
                    @else
                        <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 font-semibold text-yellow-700">{{ $vendor['status_name'] }}</span>
                    @endif
                @else
                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 font-semibold text-red-700">Rejected</span>
                @endif
            </div>
        @endforeach
    </div>
@else
    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700">{{ $ticket['status'] }}</span>
@endif
```

### 2. [app/Services/IssueService.php](app/Services/IssueService.php#L2050-L2097)

**Change:** Added automatic main ticket status resolution when all active vendors are resolved.

**Added Logic:**
```php
// Check if all active vendors are now resolved
Log::info('[VENDOR STATUS UPDATE] Checking if all active vendors are resolved');
$vendorProgress = $this->getVendorProgress($issue);

if ($vendorProgress && 
    $vendorProgress['active_count'] > 0 && 
    $vendorProgress['active_count'] === $vendorProgress['resolved_count']) {
    
    // Get Resolved status ID (typically 3)
    $resolvedStatus = DB::table('mst_issue_status')
        ->where('status_name', 'Resolved')
        ->first(['status_id']);
    
    if ($resolvedStatus) {
        // Update main ticket
        DB::table('txn_issue')
            ->where('issue_id', $issue->issue_id)
            ->update([
                'status_id' => $resolvedStatus->status_id,
                'updated_at' => now(),
            ]);
        
        // Record overall resolution in history
        $this->createStatusHistory(
            $issue,
            $resolvedStatus->status_id,
            'All active vendors resolved',
            null  // No vendor_id for overall status
        );
    }
}
```

---

## DATABASE VERIFICATION

### Query to Check Current Vendor Statuses

```sql
SELECT 
  i.issue_id,
  i.issue_number,
  m.vendor_id,
  v.vendor_name,
  m.vendor_status_id,
  s.status_name,
  m.is_active,
  m.status_updated_by,
  m.status_updated_at
FROM txn_issue i
LEFT JOIN map_issue_vendor_assignment m ON i.issue_id = m.issue_id
LEFT JOIN mst_vendor v ON m.vendor_id = v.vendor_id
LEFT JOIN mst_issue_status s ON m.vendor_status_id = s.status_id
WHERE i.issue_id = 127  -- Change to your ticket ID
ORDER BY v.vendor_name;
```

### Query to Check if Main Ticket Auto-Resolved

```sql
SELECT 
  issue_id,
  new_status_id,
  vendor_id,
  comment,
  changed_at
FROM txn_issue_status_history
WHERE issue_id = 127
ORDER BY changed_at DESC;
```

Note: `vendor_id IS NULL` indicates overall ticket status change.

---

## TESTING CHECKLIST

### Test Case 1: Single Vendor Update
- [ ] Create ticket with 1 vendor
- [ ] Update vendor status to "In Progress"
- [ ] Verify: Grid shows "Vendor - In Progress"
- [ ] Verify: Main ticket status unchanged
- [ ] Update vendor to "Resolved"
- [ ] Verify: Grid shows "Vendor - Resolved"
- [ ] Verify: Main ticket status AUTO-UPDATED to Resolved

### Test Case 2: Multiple Vendors, Partial Completion
- [ ] Create ticket with 2 vendors
- [ ] Update Vendor A to "In Progress"
- [ ] Verify: Grid shows both vendors with correct statuses
- [ ] Verify: Main ticket status unchanged
- [ ] Update Vendor A to "Resolved"
- [ ] Verify: Main ticket still unchanged (Vendor B not resolved)

### Test Case 3: Multiple Vendors, Full Completion
- [ ] Continue from Test Case 2
- [ ] Update Vendor B to "In Progress"
- [ ] Update Vendor B to "Resolved"
- [ ] Verify: Main ticket AUTO-UPDATED to Resolved
- [ ] Verify: Grid shows both vendors as Resolved

### Test Case 4: Vendor Rejection
- [ ] Create ticket with 2 vendors
- [ ] Update both to "In Progress"
- [ ] Vendor A → "Resolved"
- [ ] Vendor B → "Reject"
- [ ] Verify: is_active = 0 for Vendor B
- [ ] Verify: Main ticket AUTO-UPDATED to Resolved (only active vendor resolved)
- [ ] Verify: Close allowed (active vendor resolved)

### Test Case 5: Close Validation
- [ ] Create ticket with 2 vendors
- [ ] Update both to "In Progress"
- [ ] Attempt to close
- [ ] Verify: Close blocked with message about pending vendors
- [ ] Update Vendor A to "Resolved"
- [ ] Attempt to close
- [ ] Verify: Close still blocked (Vendor B pending)
- [ ] Update Vendor B to "Resolved"
- [ ] Attempt to close
- [ ] Verify: Close allowed

### Test Case 6: History Tracking
- [ ] Create ticket with 2 vendors
- [ ] Update vendors through complete workflow
- [ ] Check txn_issue_status_history
- [ ] Verify: Vendor status changes have vendor_id set
- [ ] Verify: Overall auto-resolution has vendor_id = NULL with comment

---

## FREQUENTLY ASKED QUESTIONS

### Q: Why does the grid show vendor statuses but the drawer shows main ticket status?

**A:** The grid must show vendor-wise progress for multi-vendor tickets. When opening the ticket drawer, the main status field shows the overall ticket status, which is auto-calculated based on vendor completion.

### Q: Can I manually update the main ticket status while vendors are working?

**A:** The workflow manages this automatically. Vendor status updates trigger automatic main ticket status updates. Manual changes to main ticket status should preserve vendor assignments.

### Q: What happens if I reject a vendor after they've been working?

**A:** When a vendor rejects:
1. `is_active` is set to 0
2. Vendor is excluded from active vendor count
3. If all remaining active vendors are resolved, main ticket auto-resolves

### Q: Why is there a vendor_id column in txn_issue_status_history?

**A:** To distinguish:
- **Vendor status changes:** vendor_id has a value
- **Main ticket auto-resolution:** vendor_id is NULL
This allows the history to track both individual vendor progress and overall ticket resolution independently.

### Q: Can I assign vendors without using "Vendor Assignment" status?

**A:** The current workflow uses the "Vendor Assignment" status trigger to create map_issue_vendor_assignment entries. Only explicit user selection creates vendor assignments.

### Q: What if no vendors are assigned to a ticket?

**A:** The grid falls back to showing the main ticket status. The multi-vendor workflow only activates when map_issue_vendor_assignment entries exist.

---

## DEPLOYMENT CHECKLIST

- [ ] All code changes applied
- [ ] No new tables created
- [ ] No ALTER TABLE statements run
- [ ] Single-vendor workflow still works
- [ ] Existing vendor assignments still active
- [ ] Re-open workflow still functional
- [ ] Status/role mappings unchanged
- [ ] Database backup created
- [ ] Test on staging environment
- [ ] Verify history tracking in logs
- [ ] Monitor error logs for validation exceptions

---

## SUPPORT & TROUBLESHOOTING

### Issue: Main ticket status not auto-updating

**Check:**
1. Are all vendors actually at resolved status (vendor_status_id = 3)?
2. Are rejected vendors properly marked is_active = 0?
3. Check logs for `[VENDOR STATUS UPDATE]` entries
4. Verify mst_issue_status has "Resolved" status with status_id = 3

### Issue: Close is blocked but should be allowed

**Check:**
1. Run SQL query to see active vendors
2. Verify each active vendor has vendor_status_id = 3
3. Check validateVendorResolutionBeforeClose() logs
4. Verify is_active values are correct in map_issue_vendor_assignment

### Issue: Grid not showing vendor statuses

**Check:**
1. Verify map_issue_vendor_assignment has entries
2. Check PageController::roleIssueDashboard() line 776-828
3. Verify $ticket['vendor_progress'] is populated
4. Check Blade template rendering

---

## LOGGING

Key log entries to monitor:

```
[VENDOR STATUS UPDATE] Starting vendor status update
[VENDOR STATUS UPDATE] Status details
[VENDOR STATUS UPDATE] Determining is_active flag
[VENDOR STATUS UPDATE] Checking if all active vendors are resolved
[VENDOR STATUS UPDATE] All active vendors resolved - updating main ticket status
[VENDOR STATUS UPDATE] Recording overall ticket resolution in history
[VENDOR STATUS UPDATE] Vendor status update completed successfully
[VENDOR VALIDATION] Checking vendor resolution for close
[VENDOR VALIDATION] Not all vendors resolved - close blocked
```

All logged to: `storage/logs/laravel.log`

---

## VERSION INFO

- **Laravel Version:** 11.x
- **PHP Version:** 8.3+
- **Database:** MySQL/MariaDB
- **Implementation Date:** 2026-08-17
- **Status:** Complete

