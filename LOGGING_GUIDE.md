# Issue Creation Logging Guide

## Overview
Comprehensive logging has been added to track the complete flow of issue creation from the `/issues/create` route through all database table operations and conditional routing logic.

---

## 📊 Table Operations Tracked

### 1. **txn_issue (Main Issue Table)**
- **Operation**: INSERT
- **When**: During `IssueService::create()` → `IssueRepository::create()`
- **Logged Info**:
  - Issue ID, Issue Number
  - Project ID, State ID, Application ID
  - Status ID, Priority ID
  - Created timestamp

### 2. **mst_issue_routing_rule**
- **Operation**: SELECT (Query for routing rules)
- **When**: During `resolveRoutingMetadata()` 
- **Conditions Logged**:
  - `is_active = 1`
  - `project_id = NULL OR project_id = {value}`
  - `state_id = NULL OR state_id = {value}`
  - `application_id = NULL OR application_id = {value}`
  - `ORDER BY routing_level ASC, is_default DESC`
- **Result Logged**: 
  - Rule found / Not found
  - Parsed vendor lists (first level, second level)

### 3. **map_vendor_state**
- **Operation**: SELECT (Multiple queries in different routing paths)
- **When**: 
  - Initial vendor lookup in `findVendorIds()`
  - Each routing decision path (HO not required, holiday, working hours, outside hours)
- **Conditions Logged**:
  - `project_id = {value}`
  - `state_id = {value}`
  - `is_active = 1`
  - `application_id = NULL OR application_id = {value}`
  - `vendor_id IN ({list})`
- **Result Logged**: 
  - Matched vendors
  - Vendor count
  - Query execution path

### 4. **mst_calendar_holiday & mst_working_schedule**
- **Operation**: READ via WorkingCalendarEngine
- **When**: During `resolveRoutingMetadata()` for HO intervention cases
- **Conditions Logged**:
  - Ticket datetime used for check
  - Holiday status (YES/NO)
  - Working hours status (YES/NO)
- **Result Logged**:
  - Is date a holiday?
  - Is time within working hours?
  - Calendar check result

### 5. **issue_history**
- **Operation**: INSERT (Multiple records)
- **When**: After issue creation and assignment
- **Logged Info**:
  - Action type (Issue Created, Assigned, etc.)
  - Remarks/Comments
  - Performed by (User ID)
  - Timestamp

### 6. **txn_issue_attachment**
- **Operation**: INSERT (If attachment provided)
- **When**: During `uploadAttachment()` after issue creation
- **Logged Info**:
  - Attachment filename
  - File size
  - File path
  - Upload timestamp

### 7. **mst_support_configuration**
- **Operation**: SELECT (For HO intervention check)
- **When**: During `resolveHoInterventionRequired()` and calendar lookup
- **Logged Info**:
  - Configuration found/not found
  - SLA configuration status
  - Calendar linked status

---

## 🔄 Routing Logic Flow & Conditions

### **DECISION TREE** (Logged at each branch):

```
START: /issues/create endpoint
│
├─ [CONDITION 1] Check HO Intervention Required
│  ├─ Query: mst_support_configuration + mst_sla_configuration + mst_calendar
│  └─ Result: HO_INTERVENTION_REQUIRED = 1 or 0
│
├─ [CONDITION 2] Determine DateTime for Holiday/Hours Check
│  ├─ Use: occurred_date/occurred_time (or current datetime)
│  └─ Log: DateTime used for all subsequent checks
│
├─ [CONDITION 3] Query mst_issue_routing_rule
│  ├─ Conditions: is_active=1, project/state/app filters
│  └─ Result: Rule found or fallback to map_vendor_state
│
├─ IF HO_INTERVENTION = 0
│  └─ [ROUTING PATH 1] Direct to Vendor L2
│     └─ Query: map_vendor_state with vendor filters
│
└─ IF HO_INTERVENTION = 1
   ├─ [CONDITION 4] Check if Date is Holiday
   │  ├─ Query: mst_calendar_holiday via WorkingCalendarEngine
   │  ├─ IF HOLIDAY = YES
   │  │  └─ [ROUTING PATH 2.1] Route to Vendor L2
   │  │     └─ Query: map_vendor_state
   │  │
   │  └─ IF HOLIDAY = NO
   │     ├─ [CONDITION 5] Check if During Working Hours
   │     │  ├─ Query: mst_working_schedule via WorkingCalendarEngine
   │     │  ├─ IF IS_WORKING = YES
   │     │  │  └─ [ROUTING PATH 2.2] Route to HO (no vendor)
   │     │  │
   │     │  └─ IF IS_WORKING = NO
   │     │     └─ [ROUTING PATH 2.3] Route to Vendor L2
   │     │        └─ Query: map_vendor_state
│
└─ FINAL: Insert into txn_issue with determined routing
   ├─ Write: txn_issue
   ├─ Write: issue_history (Issue Created entry)
   ├─ Write: issue_history (Assigned entry, if auto-assign)
   ├─ Write: txn_issue_attachment (if file uploaded)
   └─ Commit or Rollback all changes
```

---

## 📝 Log Output Examples

### **Successful Creation (No Attachment):**
```
=== ISSUE CREATION STARTED === (route: issue/create)
═══════════════════════════════════════════════════════════════
RESOLVING ROUTING METADATA - Condition Checks Start
═══════════════════════════════════════════════════════════════
[INPUT PARAMETERS] project_id=5, state_id=2, app_id=3, support_config_id=1
[CONDITION 1] Checking HO Intervention Required...
[CONDITION 1 RESULT] ho_intervention_required = 1 (HO Intervention REQUIRED)
[CONDITION 2] Determining DateTime for Holiday/Working Hours Checks...
[TABLE: mst_issue_routing_rule] Starting Query
[TABLE: mst_issue_routing_rule] Query Results ✓ Routing Rule FOUND
[BRANCH A] Routing Rule Found - Extracting Vendor Lists
[DECISION POINT] HO Intervention Decision Logic
[ROUTING PATH 2] HO Intervention REQUIRED → Holiday/Working Hours Check
[TABLE: mst_calendar_holiday] Checking if date is holiday...
[CONDITION RESULT] YES - Date is HOLIDAY
[ROUTING PATH 2.1] HOLIDAY PATH → Route to Vendor L2
[TABLE: map_vendor_state] Querying for mapped vendors (Holiday path)
[TABLE: issues] Row created successfully (issue_id: 123, issue_number: ISSUE-2024-001)
[STEP 4] Running afterCreate hooks...
[TABLE: issue_history] History record inserted successfully
=== ISSUE CREATION COMPLETED === (issue_id: 123)
```

### **With Attachment:**
```
[STEP 3] Uploading attachment...
[TABLE: txn_issue_attachment] Processing attachment
[STEP 3.1] Storing file to disk...
[TABLE: txn_issue_attachment] Inserting attachment record to database
[TABLE: txn_issue_attachment] Attachment record inserted successfully
```

### **Error Case:**
```
[DB] Rolling back transaction due to error...
❌ Issue Create Error (message: constraint violation, file: ..., line: ...)
=== ISSUE CREATION FAILED ===
```

---

## 🔍 How to Use These Logs

### **View Real-time Logs:**
```powershell
tail -f storage/logs/laravel.log
```

### **Filter Specific Table Operations:**
```powershell
grep "\[TABLE:" storage/logs/laravel.log | tail -50
```

### **Filter Specific Routing Path:**
```powershell
grep "ROUTING PATH" storage/logs/laravel.log
```

### **Track Specific Issue:**
```powershell
grep "issue_number=ISSUE-2024-001" storage/logs/laravel.log
```

### **Debug Routing Decisions:**
```powershell
grep "\[CONDITION\|\[BRANCH\|\[ROUTING" storage/logs/laravel.log
```

---

## 📋 Database Queries Being Logged

### **Query 1: mst_issue_routing_rule**
```sql
SELECT * FROM mst_issue_routing_rule
WHERE is_active = 1
  AND (project_id IS NULL OR project_id = ?)
  AND (state_id IS NULL OR state_id = ?)
  AND (application_id IS NULL OR application_id = ?)
ORDER BY routing_level ASC, is_default DESC
LIMIT 1
```

### **Query 2: map_vendor_state (Initial lookup)**
```sql
SELECT DISTINCT vendor_id FROM map_vendor_state
WHERE project_id = ?
  AND state_id = ?
  AND is_active = 1
  AND (application_id IS NULL OR application_id = ?)
ORDER BY vendor_id
```

### **Query 3: map_vendor_state (With vendor filter - multiple times in routing paths)**
```sql
SELECT DISTINCT vendor_id FROM map_vendor_state
WHERE project_id = ?
  AND state_id = ?
  AND is_active = 1
  AND (application_id IS NULL OR application_id = ?)
  AND vendor_id IN (?, ?, ?)  -- vendor IDs determined by routing
ORDER BY vendor_id
```

### **Query 4: mst_support_configuration (For HO check)**
```sql
SELECT * FROM mst_support_configuration
WHERE support_config_id = ?
  AND is_active = 1
```

### **Query 5: txn_issue (Insert)**
```sql
INSERT INTO txn_issue (
  issue_number, state_id, project_id, application_id, module_id,
  issue_category_id, priority_id, issue_title, issue_description,
  status_id, raised_by_user_id, ho_intervention_required,
  ho_working_hours, first_level_vendor_ids, second_level_vendor_ids,
  current_stage, current_owner_type, current_owner_id,
  workflow_status, created_at, updated_at
) VALUES (...)
```

### **Query 6: issue_history (Insert)**
```sql
INSERT INTO issue_history (
  issue_id, action, remarks, performed_by, performed_at
) VALUES (...)
```

### **Query 7: txn_issue_attachment (Insert if file)**
```sql
INSERT INTO txn_issue_attachment (
  issue_id, user_id, original_file_name, stored_file_name,
  file_path, file_size, file_type, uploaded_at, is_active
) VALUES (...)
```

---

## ✨ Summary of All Tables Logged

| Table Name | Operation | Count | Status |
|---|---|---|---|
| txn_issue | INSERT | 1 | ✓ Logged |
| mst_issue_routing_rule | SELECT | 1 | ✓ Logged |
| map_vendor_state | SELECT | 1-3 (per routing path) | ✓ Logged |
| mst_calendar_holiday | READ | Via WorkingCalendarEngine | ✓ Logged |
| mst_working_schedule | READ | Via WorkingCalendarEngine | ✓ Logged |
| issue_history | INSERT | 2-3 (per issue) | ✓ Logged |
| txn_issue_attachment | INSERT | 0-1 | ✓ Logged |
| mst_support_configuration | SELECT | 1-2 | ✓ Logged |

---

**Last Updated**: 2026-08-12  
**Logging Version**: Complete with Routing Conditions
