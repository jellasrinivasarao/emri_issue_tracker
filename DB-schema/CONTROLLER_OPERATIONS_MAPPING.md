# Database Schema - Controller Operations Mapping

## Overview
This document maps each controller's CRUD operations to database tables and fields, ensuring perfect alignment between application logic and database structure.

---

## 1. ISSUE MANAGEMENT CONTROLLERS

### IssueController
**File:** `app/Http/Controllers/IssueController.php`

| Operation | Table(s) | Key Fields | Notes |
|-----------|----------|-----------|-------|
| **List Issues** | txn_issue | issue_id, issue_number, status_id, priority_id | Filters on status, priority, date range |
| **Create Issue** | txn_issue | issue_title, issue_description, service_id, project_id | Auto-generates issue_number |
| **View Issue** | txn_issue | All fields + related: status, priority, category, user | Full issue details with relationships |
| **Update Issue** | txn_issue | status_id, current_owner_user_id, resolution_summary | Tracks updated_by, updated_at |
| **Close Issue** | txn_issue | status_id, closed_at, resolved_by, resolved_at | Sets is_closed_status = 1 |
| **Assign Issue** | txn_issue_assignment | issue_id, assigned_user_id, support_team_id, status | Creates assignment record |
| **Attach Files** | txn_issue_attachment | issue_id, user_id, file info | Stores file metadata |
| **Track Changes** | txn_issue_status_history | issue_id, from_status_id, to_status_id, changed_by | Audit trail for status changes |
| **Get Related** | txn_issue_assignment | issue_id | Retrieves all assignments for issue |

**Service Dependencies:**
- `IssueService` - Core issue operations
- `IssueRoutingService` - Auto-routing logic
- `IssueWorkflowService` - Status transitions
- `IssueSlaService` - SLA calculations

---

### RequirementController
**File:** `app/Http/Controllers/RequirementController.php`

| Operation | Table(s) | Key Fields | Status Tracking |
|-----------|----------|-----------|-----------------|
| **List Requirements** | requirements | id, requirement_no, title, status | Filters: In Progress, UAT, Production, Clarification |
| **Create Requirement** | requirements | requirement_no, title, description, state_id, project_id | Status defaults to 'OPEN' |
| **Update Requirement** | requirements | status, vendor_remarks, delivery_status | Tracks created_by, updated_by |
| **Filter Requirements** | requirements | status, state_id, project_id, assigned_vendor_id | Multiple filter support |
| **Attach Documents** | requirement_files | requirement_id, file_path, mime_type | Upload tracking |
| **Track Clarifications** | clarifications | requirement_id, user_id, message, status | Clarification pending status |
| **Request Clarification** | clarifications | requirement_id, user_id, message, status | Status: OPEN, CLOSED |
| **Reply to Clarification** | clarification_replies | clarification_id, user_id, message | Thread support |

**Status Lifecycle:**
- OPEN → In Progress → UAT Requested → UAT In Progress → UAT Completed → Moved to Production
- Special: Clarification Pending (blocks progression)

---

### VendorRequirementController  
**File:** `app/Http/Controllers/VendorRequirementController.php`

| Operation | Table(s) | Access Control | Key Fields |
|-----------|----------|-----------------|-----------|
| **List (Vendor View)** | requirements | assigned_vendor_id = auth()->vendor_id | requirement_no, title, status |
| **Update (Vendor Only)** | requirements | WHERE assigned_vendor_id = auth()->vendor_id | vendor_remarks, status |
| **Track Status** | requirement_status_histories | requirement_id | Status transitions |
| **File Upload** | requirement_files | requirement_id | uploaded_by = auth()->id |

**Authorization:** Vendor Admin role only sees requirements assigned to their vendor_id

---

## 2. USER & ROLE MANAGEMENT (Admin Controllers)

### UserMasterController
**File:** `app/Http/Controllers/Admin/UserMasterController.php`

| CRUD | Table(s) | Operations | Key Features |
|------|----------|-----------|---------------|
| **READ** | mst_user, mst_role | List with pagination, export (CSV/PDF) | Hierarchy-aware role filtering |
| **CREATE** | mst_user | Auto-generate employee_code (EMP####) | Uses Schema::hasColumn() checks |
| **UPDATE** | mst_user | Edit user details, password reset | Handles both created_at/updated_at |
| **DELETE** | mst_user | Soft delete via is_active toggle | Not full deletion |

**Key Logic:**
```
- Employee Code: Auto-generate 'EMP' + padded ID if not provided
- Login ID: Derive from username if not provided (handle duplicates)
- State Filtering: Role-based (if role_name contains 'State')
- Vendor Filtering: Vendor Admin gets scoped access (auth()->vendor_id)
- Role Hierarchy: Uses map_role_hierarchy table
  └── Central Admin can see all users
  └── State Admin sees only sub-roles (from map_role_hierarchy.parent_role_id)
  └── Vendor Admin sees only their vendor users
```

**Fields Used:**
- mst_user: employee_code, user_name, login_id, official_email, mobile_number
- mst_user: role_id, vendor_id, user_status, is_active, created_by, updated_by
- map_user_role: For multi-role assignments
- map_user_state: For state admin role scoping
- map_user_support_group: For support team assignments
- map_role_hierarchy: For role level restrictions

---

### RoleMasterController
**File:** `app/Http/Controllers/Admin/RoleMasterController.php`

| Operation | Table(s) | Fields | Notes |
|-----------|----------|--------|-------|
| **CREATE** | mst_role | role_code, role_name, role_category, description | Role code auto-generated or provided |
| **READ** | mst_role | All role details with permission counts | Shows assigned privileges/menus |
| **UPDATE** | mst_role | role_name, role_category, description | Cannot modify role_code |
| **TOGGLE** | mst_role | is_active | Soft deactivation |

**Associated Tables:**
- map_role_privilege: Links role to permissions/menus
- map_role_hierarchy: Parent-child role relationships
- map_user_role: Users assigned to roles

---

### RolePrivilegeMappingController
**File:** `app/Http/Controllers/Admin/RolePrivilegeMappingController.php`

| Operation | Table(s) | Key Logic |
|-----------|----------|-----------|
| **Assign Privileges** | map_role_privilege | role_id → privilege_ids (bulk) |
| **Assign Menus** | map_role_privilege | role_id → menu_ids (bulk) |
| **Set Permissions** | map_role_privilege | is_allowed flag (grant/revoke) |
| **View Assignments** | map_role_privilege | Show role's menu & privilege access |

**Input:** Array of privilege_ids OR menu_ids
**Output:** Creates/updates map_role_privilege records

---

### UserRoleMappingController & UserSupportGroupMappingController

| Table | Operation | Key Field(s) |
|-------|-----------|--------------|
| map_user_role | Assign role to user | user_id, role_id |
| map_user_state | State admin scoping | user_id, state_id |
| map_user_support_group | Team assignment | user_id, support_group_id |
| map_user_project | Project assignment | user_id, project_id |

---

## 3. MASTER DATA MANAGEMENT (Admin Controllers)

### ProjectMasterController
**File:** `app/Http/Controllers/Admin/ProjectMasterController.php`

| Operation | SQL | Fields | Auto-Generation |
|-----------|-----|--------|-----------------|
| **List** | SELECT from mst_project | project_id, project_name, short_code, is_active | - |
| **Create** | INSERT into mst_project | project_name, short_code, project_description, state_id | project_code = 'PRJ' + padded_id |
| **Update** | UPDATE mst_project | Modifies existing fields | Checks update_at OR updated_at |
| **Toggle Active** | UPDATE is_active | Boolean toggle | Timestamps via both field names |

**Code Generation:**
```php
project_code = 'PRJ' . str_pad(project_id, 3, '0', STR_PAD_LEFT)
// Example: PRJ001, PRJ002, PRJ123
```

**Schema Flexibility:**
- Checks for both `updated_at` and `update_at` (handles naming inconsistency)
- Uses Schema::hasColumn() to verify optional fields

---

### ServiceMasterController
**File:** `app/Http/Controllers/Admin/ServiceMasterController.php`

| Operation | Table | Key Fields | Code Format |
|-----------|-------|-----------|-------------|
| **CRUD** | mst_service | service_code, service_name, short_code, display_order | 'SRV' + padded_id |
| **List** | mst_service | All with is_active filter | Ordering by display_order |
| **Export** | mst_service | Formatted for CSV/PDF/Excel | With timestamps |

---

### VendorController
**File:** `app/Http/Controllers/Admin/VendorController.php`

| Operation | Table | Relations | Key Fields |
|-----------|-------|-----------|-----------|
| **LIST** | mst_vendor | Has organisation_id | vendor_code, vendor_name, category |
| **CREATE** | mst_vendor + map_vendor_state | New vendor + state mappings | vendor_code = 'VND' + ID |
| **UPDATE** | mst_vendor | Contact info, category | Support email/mobile |
| **Delete States** | map_vendor_state | Remove vendor from states | Vendor still exists |

**State Mapping:**
- Vendors can operate in multiple states
- Stored in map_vendor_state (vendor_id, state_id)
- Used for Vendor Admin role filtering

---

### ApplicationMasterController & ModuleMasterController

| Controller | Table | Auto-Code | Key Operation |
|-----------|-------|-----------|--------------|
| ApplicationMaster | mst_application | application_code (user-provided) | Basic CRUD |
| ModuleMaster | mst_module | module_code (user-provided) | Basic CRUD |

Both support:
- List with is_active filter
- Create new entity
- Update fields
- Toggle active status

---

### ProjectApplicationModuleMappingController
**File:** `app/Http/Controllers/Admin/ProjectApplicationModuleMappingController.php`

| Operation | Table | Fields | Cascade |
|-----------|-------|--------|---------|
| **Create Mapping** | map_project_application_module | project_id, application_id, module_id | - |
| **List Mappings** | map_project_application_module | Show active mappings | - |
| **Update** | map_project_application_module | is_active flag | - |
| **Delete** | map_project_application_module | CASCADE delete | Removes mapping, keeps entities |

---

## 4. CALENDAR & SCHEDULE MANAGEMENT

### WorkingScheduleController
**File:** `app/Http/Controllers/Admin/WorkingScheduleController.php`

| Operation | Table | Fields | Time Handling |
|-----------|-------|--------|--------------|
| **List** | mst_working_schedule | By calendar_id and day_of_week | Shows all shifts |
| **Create** | mst_working_schedule | start_time, end_time, break times | TIME format |
| **Update** | mst_working_schedule | Schedule details, effective dates | handled via timestamps |
| **Delete** | mst_working_schedule | Soft delete via deleted_at | Preserves audit trail |

**Fields:**
- day_of_week: 0-6 (Sunday-Saturday)
- shift_no, shift_name: For multiple shifts per day
- is_24_hours: Override for round-the-clock
- effective_from, effective_to: Schedule validity period

---

### CalendarHolidayController
**File:** `app/Http/Controllers/Admin/CalendarHolidayController.php`

| CRUD | Table | Key Fields | Special Logic |
|------|-------|-----------|--------------|
| **CREATE** | mst_calendar_holiday | holiday_date, holiday_name, holiday_type | Scope support (ALL, STATE, HO, VENDOR) |
| **LIST** | mst_calendar_holiday | By month/year and calendar_id | Shows all holidays |
| **UPDATE** | mst_calendar_holiday | Dates, type, scope | Recurring support (recurrence_year) |
| **DELETE** | mst_calendar_holiday | Soft delete | Soft deletes via deleted_at |

**Holiday Types:**
- PUBLIC_HOLIDAY
- OPTIONAL_HOLIDAY
- COMPANY_HOLIDAY
- STATE_HOLIDAY
- SPECIAL_HOLIDAY
- EMERGENCY_CLOSURE

**Scope:**
- ALL: Organization-wide
- STATE: Specific state
- HO: Head office
- VENDOR: Specific vendor
- PROJECT: Specific project

---

### SlaConfigurationController

| Operation | Table(s) | SLA Times | Calendar Link |
|-----------|----------|-----------|--------------|
| **Create SLA** | mst_sla_configuration | response_sla_hours, resolution_sla_hours | Links to working_calendar_id |
| **Define Policy** | cfg_sla_policy | Service + Project + App + Priority | Response & resolution in minutes |
| **Apply to Project** | mst_project_support_configuration | sla_hours field | Default SLA for project |

**Relationship:**
```
SLA Configuration (master template)
    ↓
SLA Policy (specific application mapping)
    └─→ Service, Project, Application, Priority specific
    └─→ Response & resolution times in minutes
    └─→ Links to working calendar for business hours
```

---

## 5. ISSUE ROUTING & AUTOMATION

### IssueRoutingRuleController & AutomaticRoutingController

| Controller | Table | Operations | Auto-Assignment |
|-----------|-------|-----------|-----------------|
| **IssueRoutingRule** | mst_issue_routing_rule | Define routing rules | Matches issue characteristics |
| **AutomaticRouting** | mst_issue_routing_rule | Enable/disable auto-routing | routing_level, is_default |

**Matching Criteria:**
- issue_category: Category of issue
- issue_type: Type/nature
- priority: Priority level
- routing_level: 1st level, 2nd level, etc.
- is_default: Fallback rule

**Routing Details:**
- support_config_id: Which project configuration
- support_team_id: Target team
- project_id, application_id, vendor_id: Optional scope

---

## 6. ORGANIZATION HIERARCHY

### StateMasterController
**File:** `app/Http/Controllers/Admin/StateMasterController.php`

| Operation | Table | Fields | Parent |
|-----------|-------|--------|--------|
| **CREATE** | mst_state | state_code, state_name, state_short_name | organisation_id |
| **READ** | mst_state | All state data | Filtered by organisation |
| **UPDATE** | mst_state | Code, name, short name | Cannot change organisation |
| **TOGGLE** | mst_state | is_active | Soft deactivation |

---

### ProjectStateMappingController

| Operation | Table | Logic |
|-----------|-------|-------|
| **Assign Projects to States** | mst_project | project.state_id = state_id |
| **List Projects by State** | mst_project | WHERE state_id = ? |
| **Unassign** | mst_project | Update state_id to new value |

---

### VendorStateMappingController

| Operation | Table | Mapping |
|-----------|-------|---------|
| **Assign Vendor to States** | map_vendor_state | vendor_id, state_id pairs |
| **List State Coverage** | map_vendor_state | By vendor_id |
| **Remove Coverage** | map_vendor_state | Delete mapping |

**Used By:**
- Vendor Admin role: Scoped access to assigned states
- Requirement routing: Vendor availability by state

---

## 7. PAGINATION & FILTERING PATTERNS

All controllers use these patterns:

### Common Filters:
```php
// Status filter
$query->where('is_active', 1)

// Date range
$query->whereBetween('created_at', [$from_date, $to_date])

// Search
$query->where('name', 'LIKE', "%$search%")

// Pagination
->paginate(15)->withQueryString()
```

### Export Formats:
- **CSV**: Comma-separated with escaped quotes
- **XLSX**: Excel format
- **PDF**: HTML table in PDF

---

## 8. AUDIT TRAIL PATTERNS

All transaction tables track:

| Field | Type | Purpose | Auto-Set |
|-------|------|---------|----------|
| created_by | BIGINT FK | Who created | auth()->id() |
| updated_by | BIGINT FK | Who updated | auth()->id() on update |
| created_at | TIMESTAMP | When created | CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | When updated | ON UPDATE CURRENT_TIMESTAMP |
| deleted_by | BIGINT FK | Who deleted | auth()->id() on soft delete |
| deleted_at | TIMESTAMP | Soft delete marker | NULL or CURRENT_TIMESTAMP |

---

## 9. SCHEMA FIELD NAMING CONVENTIONS (Controller-Aligned)

| Pattern | Examples | Used For |
|---------|----------|----------|
| `{entity}_id` | user_id, project_id, state_id | Primary/Foreign Keys |
| `{entity}_code` | project_code, role_code, service_code | Unique business identifiers |
| `{entity}_name` | project_name, role_name, user_name | Display text |
| `is_{property}` | is_active, is_working_day, is_default | Boolean flags |
| `{action}_by` | created_by, updated_by, resolved_by | User tracking |
| `{action}_at` | created_at, raised_at, resolved_at | Timestamp tracking |
| `{property}_ids` | first_level_vendor_ids | JSON arrays |

---

## 10. CODE AUTO-GENERATION PATTERNS

| Entity | Pattern | Example | Generated By |
|--------|---------|---------|--------------|
| Employee | EMP + 4-digit | EMP0001 | UserMasterController |
| Project | PRJ + 3-digit | PRJ001 | ProjectMasterController |
| Service | SRV + 3-digit | SRV001 | ServiceMasterController |
| Vendor | VND + 3-digit | VND001 | VendorController |
| Issue Number | Custom format | PROJ-2024-0001 | IssueService |

---

## 11. ROLE-BASED ACCESS CONTROL

### Controller Authorization Pattern:
```
Central Admin → Full access to all resources
    ↓
State Admin → Limited to assigned states (via map_user_state)
    ↓
Vendor Admin → Limited to assigned vendors (via vendor_id field + map_vendor_state)
    ↓
HO Admin → Limited to HO operations
```

### Database Enforcement:
1. **Role Hierarchy:** `map_role_hierarchy` (parent → child)
2. **Role Permissions:** `map_role_privilege` (role → menu/privilege)
3. **User Roles:** `map_user_role` (user → multiple roles)
4. **Scope Filters:**
   - State Admin: `map_user_state` (user → state)
   - Vendor Admin: `vendor_id` on users + `map_vendor_state` (vendor → states)
   - Support Groups: `map_user_support_group` (user → group)

---

## 12. KEY FIELDS FOR FILTERING

| Controller | Primary Filter | Secondary Filters |
|-----------|-----------------|-------------------|
| Issue | status_id | priority_id, project_id, state_id, assigned_at |
| Requirement | status | state_id, project_id, assigned_vendor_id |
| User | is_active | role_id, organisation_id, user_status |
| Project | is_active | state_id, project_status |
| Role | - | is_system_role, role_category |
| Vendor | is_active | organisation_id |

---

## 13. TRANSACTION ISOLATION

**Transactions Used In:**
1. Issue creation → Creates issue + routing rules + initial status history
2. Requirement workflow → Updates requirement + creates status history
3. User creation → Creates user + assigns roles + sends email
4. SLA application → Applies SLA policy to issue on creation

---

## Summary of Controller-Database Alignment

✅ **Perfect Alignment:**
- Auto-code generation patterns
- Role hierarchy support via map_role_hierarchy
- State/Vendor scoping for multi-tenancy
- Audit trail on all critical tables
- Status lifecycle tracking

⚠️ **Handled with Schema::hasColumn():**
- created_at vs created_date variations
- updated_at vs update_at variations
- Optional vendor_id field on users

✨ **Robust Features:**
- Soft deletes for audit compliance
- JSON fields for complex data (vendor_ids)
- Cascading deletes for data consistency
- Unique constraints for business logic
- Comprehensive indexing for performance

---

*Last Updated: 2024*
*Database Version: MySQL 5.7+*
*Schema Version: 57 tables (controller-aligned)*
