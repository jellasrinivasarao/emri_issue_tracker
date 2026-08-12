# ROUTING LOGIC VERIFICATION REPORT
**Date:** 2024  
**Status:** ✅ REFACTORED - Spec-Based Implementation Complete

---

## IMPLEMENTATION SUMMARY

The `IssueService.php` has been completely refactored to implement your 7-scenario routing specification with rule-based priority logic.

### Key Changes:

#### 1. **Rule Priority Logic (STEP 3)**
```php
// Now checks mst_issue_routing_rule.rule_code directly
$routeHoL1Active = DB::table('mst_issue_routing_rule')
    ->where('rule_code', 'ROUTE_HO_IT_L1')
    ->where('is_active', 1)
    ->exists();

$routeVendorL2Active = DB::table('mst_issue_routing_rule')
    ->where('rule_code', 'ROUTE_VENDOR_L2')
    ->where('is_active', 1)
    ->exists();

// Priority: ROUTE_HO_IT_L1 takes precedence
if ($routeHoL1Active) {
    return $this->routeHOITL1Flow(...);
} elseif ($routeVendorL2Active) {
    return $this->routeDirectVendorL2Flow(...);
}
```

---

## SCENARIO MAPPINGS

### ✅ SCENARIO 1: Direct Vendor L2 Routing
**Condition:** `ROUTE_HO_IT_L1=0, ROUTE_VENDOR_L2=1`

**Implementation:** `routeDirectVendorL2Flow()` (Line 317)
- Skips holiday and working hours checks
- Queries `map_vendor_state` directly
- Sets: `ho_intervention_required=0, ho_working_hours=0`
- Uses: `first_level_vendor_ids` only

**Logs:**
```
[SCENARIO 1] Direct Vendor L2 Routing
[S1-STEP 1] Skip holiday and working hours checks
[TABLE: map_vendor_state] Query with conditions...
[S1 FINAL RESULT]
```

---

### ✅ SCENARIO 2: HO L1 Routing with Holiday/Working Hours Checks
**Condition:** `ROUTE_HO_IT_L1=1`

**Implementation:** `routeHOITL1Flow()` (Line 352)
- Checks `mst_calendar_holiday` for current date
- If holiday → SCENARIO 2.1
- If NOT holiday → checks working schedule

---

### ✅ SCENARIO 2.1: Holiday Found → Route to Vendor L2
**Condition:** Holiday detected in `mst_calendar_holiday`

**Implementation:** `routeHOHolidayScenario()` (Line 389)
- Sets: `ho_intervention_required=1, ho_working_hours=0`
- Uses: `second_level_vendor_ids` (Vendor L2)
- Reason: "Holiday - HO Unavailable"

**Logs:**
```
[TABLE: mst_calendar_holiday] Checking if date is holiday...
[SCENARIO 2.1] Holiday Found - Route to Vendor L2
[S2.1 FINAL RESULT]
```

---

### ✅ SCENARIO 2.2: Within Working Hours → HO Handles
**Condition:** 
- NOT a holiday
- Current time BETWEEN `start_time` and `end_time`
- `is_working_day=1`

**Implementation:** `routeHOWorkingHoursScenario()` (Line 520)
- Sets: `ho_intervention_required=1, ho_working_hours=1`
- Uses: `first_level_vendor_ids` from routing rule
- Vendor IDs: Fetched from `mst_issue_routing_rule.first_level_vendor_ids`
- Reason: "Within working hours - HO will handle"

**Database Query:**
```php
DB::table('mst_issue_routing_rule')
    ->where('rule_code', 'ROUTE_HO_IT_L1')
    ->where('is_active', 1)
    ->first();
// Uses: $rule->first_level_vendor_ids
```

**Logs:**
```
[TABLE: mst_working_schedule] Working hours confirmed
[SCENARIO 2.2] Within Working Hours - HO Handles
[S2.2 FINAL RESULT]
```

---

### ✅ SCENARIO 2.3: Weekly Off → Route to Vendor L2
**Condition:** 
- NOT a holiday
- `is_working_day=0` in `mst_working_schedule`

**Implementation:** `routeHOWeeklyOffScenario()` (Line 563)
- Sets: `ho_intervention_required=1, ho_working_hours=0`
- Uses: `second_level_vendor_ids` (Vendor L2)
- Reason: "Weekly Off - HO Unavailable"

**Logs:**
```
[SCENARIO 2.3] Weekly Off - Route to Vendor L2
[S2.3 FINAL RESULT]
```

---

### ✅ SCENARIO 2.4: Outside Working Hours → Route to Vendor L2
**Condition:** 
- NOT a holiday
- Current time NOT between `start_time` and `end_time`
- (Time > `end_time` OR Time < `start_time`)

**Implementation:** `routeHOOutsideHoursScenario()` (Line 601)
- Sets: `ho_intervention_required=1, ho_working_hours=0`
- Uses: `second_level_vendor_ids` (Vendor L2)
- Reason: "Outside working hours"

**Logs:**
```
[SCENARIO 2.4] Outside Working Hours - Route to Vendor L2
[S2.4 FINAL RESULT]
```

---

## DECISION TREE FLOWCHART

```
REQUEST RECEIVED
    ↓
[STEP 1] Parse Input (project_id, state_id, application_id)
    ↓
[STEP 2] Determine DateTime (occurred_date/time or current)
    ↓
[STEP 3] Check mst_issue_routing_rule.rule_code
    ↓
    ├─→ ROUTE_HO_IT_L1 = ACTIVE?
    │    YES → routeHOITL1Flow()
    │    ├─→ Check mst_calendar_holiday
    │    │   HOLIDAY? YES → SCENARIO 2.1 [Vendor L2]
    │    │   HOLIDAY? NO → Check mst_working_schedule
    │    │   ├─→ is_working_day = 0? YES → SCENARIO 2.3 [Vendor L2]
    │    │   ├─→ is_working_day = 1?
    │    │   │   ├─→ Time BETWEEN start_time-end_time? YES → SCENARIO 2.2 [HO L1]
    │    │   │   ├─→ Time BETWEEN start_time-end_time? NO → SCENARIO 2.4 [Vendor L2]
    │
    ├─→ ROUTE_VENDOR_L2 = ACTIVE?
    │    YES → routeDirectVendorL2Flow() [SCENARIO 1]
    │    NO → routeDirectVendorL2Flow() [FALLBACK]
```

---

## DATABASE TABLES ACCESSED

| Table | Scenarios | Purpose |
|-------|-----------|---------|
| `mst_issue_routing_rule` | ALL | Rule code priority (ROUTE_HO_IT_L1, ROUTE_VENDOR_L2) |
| `mst_calendar_holiday` | 2, 2.1 | Holiday detection |
| `mst_working_schedule` | 2, 2.2, 2.3, 2.4 | Working hours & days |
| `map_vendor_state` | ALL | Vendor mapping by project/state |

---

## LOGGING DETAILS

### Insert Log Channel
All inserts logged to `storage/logs/insert_log.log`:
```
[INSERT] Starting txn_issue INSERT operation
[TABLE: txn_issue] Preparing INSERT: issue_number=..., ho_intervention_required=..., ho_working_hours=...
✓ [TABLE: txn_issue] INSERT Successful (issue_id=...)
```

### Routing Decision Logs
Sent to default `laravel.log`:
```
═══════════════════════════════════════════════════════════════
RESOLVING ROUTING METADATA - Spec-Based Routing Logic
═══════════════════════════════════════════════════════════════
[STEP 1] INPUT PARAMETERS
[STEP 2] Determining DateTime for Checks...
[STEP 3] Checking mst_issue_routing_rule - Rule Priority Logic
[STEP 3 RESULT] Routing Rules Status
[ROUTING PRIORITY] ROUTE_HO_IT_L1 is ACTIVE - Using HO Routing Flow
[SCENARIO 2.1] Holiday Found - Route to Vendor L2
[S2.1 FINAL RESULT]
```

---

## CODE QUALITY CHECK

✅ **PHP Syntax:** `No syntax errors detected`  
✅ **Method Count:** 7 new scenario methods + priority logic  
✅ **Logging:** Comprehensive at each decision point  
✅ **Error Handling:** Try-catch for date parsing, null checks for optional fields  
✅ **Database Queries:** Parameterized with proper conditions  

---

## TESTING RECOMMENDATIONS

### Test Case 1: Direct Vendor L2 (Scenario 1)
```sql
-- Enable ROUTE_VENDOR_L2 only
UPDATE mst_issue_routing_rule SET is_active=0 WHERE rule_code='ROUTE_HO_IT_L1';
UPDATE mst_issue_routing_rule SET is_active=1 WHERE rule_code='ROUTE_VENDOR_L2';

-- Submit issue → Should use first_level_vendor_ids
-- Check logs for: [SCENARIO 1] Direct Vendor L2 Routing
```

### Test Case 2: Holiday Scenario (Scenario 2.1)
```sql
-- Enable ROUTE_HO_IT_L1
UPDATE mst_issue_routing_rule SET is_active=1 WHERE rule_code='ROUTE_HO_IT_L1';

-- Add holiday for today
INSERT INTO mst_calendar_holiday (holiday_date, holiday_name, is_active)
VALUES (CURDATE(), 'Test Holiday', 1);

-- Submit issue with today's date → Should use second_level_vendor_ids
-- Check logs for: [SCENARIO 2.1] Holiday Found
```

### Test Case 3: Working Hours (Scenario 2.2)
```sql
-- Enable ROUTE_HO_IT_L1
UPDATE mst_issue_routing_rule SET is_active=1 WHERE rule_code='ROUTE_HO_IT_L1';

-- Remove holiday if exists
DELETE FROM mst_calendar_holiday WHERE holiday_date=CURDATE();

-- Ensure working_schedule has is_working_day=1 with times 09:00-18:00

-- Submit issue at 10:00 → Should use first_level_vendor_ids, ho_working_hours=1
-- Check logs for: [SCENARIO 2.2] Within Working Hours
```

### Test Case 4: Weekly Off (Scenario 2.3)
```sql
-- Set Sunday (day_of_week='sunday') to is_working_day=0
UPDATE mst_working_schedule SET is_working_day=0 WHERE day_of_week='sunday';

-- Submit issue on Sunday → Should use second_level_vendor_ids
-- Check logs for: [SCENARIO 2.3] Weekly Off
```

### Test Case 5: Outside Hours (Scenario 2.4)
```sql
-- Submit issue at 19:00 (after 18:00 end_time)
-- Should use second_level_vendor_ids, ho_working_hours=0
-- Check logs for: [SCENARIO 2.4] Outside Working Hours
```

---

## KNOWN ASSUMPTIONS

1. **mst_issue_routing_rule** must have `rule_code='ROUTE_HO_IT_L1'` or `'ROUTE_VENDOR_L2'`
2. **mst_working_schedule.day_of_week** must match Carbon format: MONDAY, TUESDAY, etc.
3. **Times** in mst_working_schedule must be HH:i:s format (09:00:00, 18:00:00)
4. **Priority Logic:** If both ROUTE_HO_IT_L1 and ROUTE_VENDOR_L2 are active, HO takes precedence
5. **Vendor IDs:** Expected to be comma-separated strings in first_level_vendor_ids field

---

## NEXT STEPS

1. ✅ Code is ready for testing
2. ⚠️ Ensure database has proper test data:
   - Active routing rules with correct rule_code values
   - Test holidays in mst_calendar_holiday
   - Working schedule entries for each day
   - Vendor mappings in map_vendor_state
3. ⚠️ Run test cases above to verify each scenario
4. 📋 Monitor logs at `storage/logs/laravel.log` and `storage/logs/insert_log.log`

