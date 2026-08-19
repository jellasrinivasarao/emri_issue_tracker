# MULTI-VENDOR STATUS WORKFLOW - QUICK REFERENCE

## THE PROBLEM SOLVED

**Before:** Grid showed only main ticket status (e.g., "In Progress"), hiding individual vendor progress
**After:** Grid shows each vendor's independent status (e.g., "Achala - In Progress, Convox - New")

---

## THE SOLUTION

### 1. Grid Display (Visual)
Each ticket row now displays vendor-wise statuses:

```
Ticket IS-20260817127
┌─────────────────────────────────────────┐
│ Vendor Column: Achala, Convox           │
│ Status Column:                          │
│   Achala - In Progress  🟡             │
│   Convox - New          🟡             │
└─────────────────────────────────────────┘
```

**Color Scheme:**
- 🟢 Green: Resolved
- 🟡 Yellow: In Progress / other active status
- 🔴 Red: Rejected (inactive)

### 2. Auto-Resolution Logic (Backend)
When ANY vendor updates status:

```
1. Update ONLY that vendor in map_issue_vendor_assignment
2. Check: Are ALL active vendors (is_active=1) resolved (status_id=3)?
3. If YES → Automatically update main ticket to Resolved
4. If NO → Main ticket stays unchanged
```

### 3. Close Validation (Existing - Verified)
When user tries to close ticket:

```
1. Count active vendors (is_active=1)
2. Count resolved active vendors (status_id=3)
3. Allow close ONLY if: active_count = 0 OR active_count = resolved_count
```

---

## WORKFLOW IN ACTION

### Example Ticket: IS-20260817127
**Assigned Vendors:** Achala, Convox

#### Step 1: Initial State
```
map_issue_vendor_assignment:
  Achala  → vendor_status_id=1 (New)     is_active=1
  Convox  → vendor_status_id=1 (New)     is_active=1

txn_issue.status_id = 1 (Open)

Grid Display:
  Achala - New
  Convox - New
```

#### Step 2: Achala Updates to In Progress
```
POST /issues/127/vendor-status
{
  "vendor_id": 1,
  "vendor_status_id": 2,  // In Progress
  "remarks": "Started work"
}

SERVICE LAYER:
1. UPDATE map_issue_vendor_assignment
   SET vendor_status_id = 2 WHERE vendor_id=1

2. INSERT txn_issue_status_history
   (issue_id=127, vendor_id=1, new_status_id=2)

3. CHECK: active_count=2, resolved_count=0
   NOT all resolved → Main ticket unchanged

txn_issue.status_id = 1 (still Open)

Grid Display:
  Achala - In Progress  🟡
  Convox - New          🟡
```

#### Step 3: Achala Updates to Resolved
```
POST /issues/127/vendor-status
{
  "vendor_id": 1,
  "vendor_status_id": 3,  // Resolved
  "remarks": "Issue fixed at our end"
}

SERVICE LAYER:
1. UPDATE map_issue_vendor_assignment
   SET vendor_status_id = 3 WHERE vendor_id=1

2. INSERT txn_issue_status_history
   (issue_id=127, vendor_id=1, new_status_id=3)

3. CHECK: active_count=2, resolved_count=1
   NOT all resolved → Main ticket unchanged

txn_issue.status_id = 1 (still Open)

Grid Display:
  Achala - Resolved     🟢
  Convox - New          🟡
```

#### Step 4: Convox Updates to Resolved
```
POST /issues/127/vendor-status
{
  "vendor_id": 2,
  "vendor_status_id": 3,  // Resolved
  "remarks": "Issue fixed from our infrastructure"
}

SERVICE LAYER:
1. UPDATE map_issue_vendor_assignment
   SET vendor_status_id = 3 WHERE vendor_id=2

2. INSERT txn_issue_status_history
   (issue_id=127, vendor_id=2, new_status_id=3)

3. CHECK: active_count=2, resolved_count=2
   ✓ ALL ACTIVE VENDORS RESOLVED!

4. AUTO-UPDATE MAIN TICKET:
   UPDATE txn_issue SET status_id=3 WHERE issue_id=127

5. INSERT txn_issue_status_history
   (issue_id=127, vendor_id=NULL, new_status_id=3,
    comment='All active vendors resolved')

txn_issue.status_id = 3 (Resolved) ← AUTO-UPDATED

Grid Display:
  Achala - Resolved     🟢
  Convox - Resolved     🟢
```

#### Step 5: State Can Now Close
```
POST /issues/127/close

VALIDATION:
1. Query active vendors (is_active=1): 2
2. Query resolved vendors: 2
3. Check: 2 == 2 ✓
4. Close ALLOWED
```

---

## REJECTION SCENARIO

### What if Convox Rejects?

#### State: Achala Resolved, Convox In Progress

**Convox Rejects:**
```
POST /issues/127/vendor-status
{
  "vendor_id": 2,
  "vendor_status_id": 4,  // Reject
  "remarks": "Cannot fix from infrastructure side"
}

SERVICE LAYER:
1. UPDATE map_issue_vendor_assignment
   SET vendor_status_id = 4,
       is_active = 0  ← MARKED INACTIVE
   WHERE vendor_id=2

2. INSERT txn_issue_status_history
   (issue_id=127, vendor_id=2, new_status_id=4)

3. CHECK: active_count=1 (only Achala now)
           resolved_count=1 (Achala is resolved)
   ✓ ALL ACTIVE VENDORS RESOLVED!

4. AUTO-UPDATE MAIN TICKET:
   UPDATE txn_issue SET status_id=3 WHERE issue_id=127

Grid Display:
  Achala - Resolved    🟢
  Convox - Rejected    🔴

Close ALLOWED: Convox is inactive, Achala resolved ✓
```

---

## DATABASE CHANGES

### ZERO schema changes required! ✓

Using existing columns:
- `map_issue_vendor_assignment.vendor_status_id` - Each vendor's status
- `map_issue_vendor_assignment.is_active` - Reject flag (1=active, 0=rejected)
- `txn_issue_status_history.vendor_id` - Track vendor vs overall changes
- `txn_issue.status_id` - Main ticket status (auto-updated)

---

## CODE CHANGES

### File 1: Grid Template
**File:** `resources/views/pages/role-issue-dashboard.blade.php` (line 164)

**Change:** Show vendor statuses instead of main ticket status

**Lines Changed:** 164-187 (Status column logic)

### File 2: Auto-Resolution Logic
**File:** `app/Services/IssueService.php` (line 2050-2097)

**Change:** Added auto-update logic in updateVendorStatus() method

**Lines Added:** 2050-2097 (After vendor history insertion)

---

## API ENDPOINT

### Update Vendor Status

**Endpoint:** `POST /issues/{issue_id}/vendor-status`
**Route Name:** `issues.vendor.status.update`
**Auth:** Required

**Request:**
```json
{
  "vendor_id": 1,
  "vendor_status_id": 3,
  "remarks": "Optional comments about status change"
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Vendor status updated successfully.",
  "data": {
    "success": true,
    "issue_id": 127,
    "vendor_id": 1,
    "status_id": 3
  }
}
```

**Response Error:**
```json
{
  "message": "Vendor not assigned to this issue."
}
```

---

## TESTING

### Test 1: Grid Shows Vendor Statuses ✓
1. Open ticket with 2+ vendors
2. Verify each vendor name appears in grid
3. Verify each vendor has independent status badge
4. Edit vendor statuses → Grid updates immediately

### Test 2: Auto-Resolution ✓
1. Ticket with 2 vendors, both "New"
2. Update both to "Resolved"
3. Check grid: Both show "Resolved" 🟢
4. Check main status: Auto-updated to "Resolved"
5. Check txn_issue_status_history: Has entry with vendor_id=NULL

### Test 3: Close Validation ✓
1. Ticket with 2 vendors
2. Update 1 to "Resolved", 1 stays "In Progress"
3. Attempt close → ERROR: "Cannot close, Vendor B pending"
4. Update Vendor B to "Resolved"
5. Attempt close → SUCCESS

### Test 4: Rejection ✓
1. Ticket with 2 vendors
2. Update one to "Resolved", one to "Reject"
3. Verify: is_active=0 for rejected vendor
4. Verify: Main ticket auto-updated to "Resolved"
5. Verify: Close allowed (only active vendor resolved)

---

## IMPORTANT RULES

✋ **DO NOT** update `txn_issue.status_id` directly when updating vendor status
✋ **DO NOT** read vendor status from `txn_issue.status_id`
✋ **DO NOT** count rejected (is_active=0) vendors as active
✋ **DO NOT** auto-update main ticket unless ALL active vendors resolved

✅ **DO** update `map_issue_vendor_assignment.vendor_status_id` for vendor changes
✅ **DO** read vendor status from `map_issue_vendor_assignment`
✅ **DO** use `is_active=1` filter when counting active vendors
✅ **DO** auto-update main ticket only when: active_count > 0 AND active_count == resolved_count

---

## LOGGING

### Key Log Entries

```
[VENDOR STATUS UPDATE] Starting vendor status update
  - issue_id, vendor_id, new_status_id

[VENDOR STATUS UPDATE] All active vendors resolved - updating main ticket status
  - issue_id, active_count, resolved_count

[VENDOR STATUS UPDATE] Vendor status update completed successfully
  - issue_id, vendor_id, status_id

[VENDOR VALIDATION] Checking vendor resolution for close
[VENDOR VALIDATION] Not all vendors resolved - close blocked
[VENDOR VALIDATION] All vendors resolved - close allowed
```

**Log File:** `storage/logs/laravel.log`

---

## SINGLE-VENDOR BEHAVIOR

**Unchanged!** Single-vendor tickets still work exactly as before:

1. Vendor updates status
2. Auto-update main ticket (since 1 active vendor = all active vendors resolved)
3. Close allowed when vendor resolved

Example:
```
Ticket with 1 vendor:
Vendor updates to Resolved
→ active_count=1, resolved_count=1
→ Main ticket auto-updates to Resolved
→ Close allowed
```

---

## TROUBLESHOOTING

### Problem: Main ticket not auto-updating
**Solution:** Check logs for `[VENDOR STATUS UPDATE]` entries. Verify:
- All vendors at resolved (status_id=3)
- All non-rejected vendors have is_active=1
- mst_issue_status has "Resolved" with status_id=3

### Problem: Close blocked but should be allowed
**Solution:** Check SQL:
```sql
SELECT vendor_id, is_active, vendor_status_id, status_name
FROM map_issue_vendor_assignment m
LEFT JOIN mst_issue_status s ON m.vendor_status_id=s.status_id
WHERE issue_id=127 AND is_active=1;
```
All rows should have vendor_status_id=3 (Resolved).

### Problem: Grid not showing vendor statuses
**Solution:** Verify data:
```sql
SELECT * FROM map_issue_vendor_assignment WHERE issue_id=127;
```
If empty, vendor assignment wasn't created. Ensure "Vendor Assignment" status was selected.

---

## DOCUMENTATION

Full documentation: `MULTI_VENDOR_STATUS_COMPLETE_WORKFLOW.md`
- 800+ lines
- Architecture diagrams
- SQL queries
- Scenario walkthrough
- Testing checklist
- FAQ

---

## NEXT STEPS

✅ Implementation complete
✅ Grid display updated
✅ Auto-resolution logic added
✅ Documentation created

👉 **Ready to:**
- Test on staging
- Deploy to production
- Monitor logs
- Gather user feedback

