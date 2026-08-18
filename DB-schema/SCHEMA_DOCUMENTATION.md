# EMRI Issue Tracker - Complete Database Schema Documentation

## Overview
This document describes the complete database schema for the EMRI Issue Tracker application, generated from 41 Laravel models. The schema includes 52 tables organized into 13 logical groups.

## Schema Summary

| Category | Table Count | Purpose |
|----------|------------|---------|
| Organization & Hierarchy | 4 | Organization structure, states, head offices |
| Vendor & Support Groups | 2 | Vendor management and support teams |
| Application & Modules | 2 | Business applications and modules |
| Projects | 5 | Project management and mappings |
| Issue Master Data | 3 | Issue statuses, categories, priorities |
| Working Calendar | 3 | Calendars, schedules, holidays |
| Roles & Privileges | 3 | User roles, permissions, menus |
| Users | 3 | User management and role assignments |
| SLA & Configuration | 3 | SLA policies and project configurations |
| Issue Routing | 2 | Routing configuration and rules |
| Issue Transactions | 4 | Issues, assignments, attachments, history |
| Requirements | 4 | Requirements and clarifications |
| Mail Configuration | 3 | Mail settings and logs |
| **TOTAL** | **52** | **Complete system schema** |

---

## Database Tables by Category

### 1. ORGANIZATION & HIERARCHY TABLES (4 tables)

#### mst_organisation_type
Master data for organization types.
- **Primary Key:** `organisation_type_id`
- **Key Fields:** `organisation_type_code`, `organisation_type_name`
- **Status Field:** `is_active`
- **Purpose:** Define categories of organizations

#### mst_organisation
Main organization entity.
- **Primary Key:** `organisation_id`
- **Key Fields:** `organisation_code`, `organisation_name`, `short_name`
- **Relations:** 1-to-many with State, HeadOffice, Vendor, SupportGroup, WorkingCalendar
- **Audit Fields:** `created_by`, `updated_by`, `created_at`, `updated_at`

#### mst_state
States within an organization.
- **Primary Key:** `state_id`
- **Key Fields:** `state_code`, `state_name`, `state_short_name`
- **Foreign Key:** `organisation_id` → mst_organisation
- **Relations:** 1-to-many with Project

#### mst_head_office
Head office locations.
- **Primary Key:** `head_office_id`
- **Key Fields:** `head_office_code`, `head_office_name`
- **Foreign Key:** `organisation_id` → mst_organisation
- **Contacts:** `contact_number`, `email`

---

### 2. VENDOR & SUPPORT GROUP TABLES (2 tables)

#### mst_vendor
Vendor master data.
- **Primary Key:** `vendor_id`
- **Key Fields:** `vendor_code`, `vendor_name`, `vendor_category`
- **Foreign Key:** `organisation_id` → mst_organisation
- **Contact Information:** Primary contact, support contact details

#### mst_support_group
Support teams and groups.
- **Primary Key:** `support_group_id`
- **Key Fields:** `support_group_code`, `support_group_name`, `support_group_type`
- **Foreign Key:** `organisation_id` → mst_organisation
- **Relations:** Used in issue routing and assignments

---

### 3. APPLICATION & MODULE TABLES (2 tables)

#### mst_application
Business applications.
- **Primary Key:** `application_id`
- **Key Fields:** `application_code`, `application_name`
- **Relations:** Many-to-many with Project via `map_project_application`
- **Many-to-many with Service via Project**

#### mst_module
Application modules.
- **Primary Key:** `module_id`
- **Key Fields:** `module_code`, `module_name`
- **Relations:** Many-to-many with Project and Application via `map_project_application_module`

---

### 4. PROJECT TABLES (5 tables)

#### mst_project
Projects.
- **Primary Key:** `project_id`
- **Key Fields:** `project_code`, `short_code`, `project_name`
- **Foreign Key:** `state_id` → mst_state
- **Dates:** `start_date`, `end_date`
- **Status:** `project_status`, `is_active`
- **Relations:** Many-to-many with Application, Service, User

#### mst_service
Services.
- **Primary Key:** `service_id`
- **Key Fields:** `service_code`, `short_code`, `service_name`
- **Display:** `display_order`, `is_active`

#### map_project_application
Project-Application many-to-many relationship.
- **Primary Key:** `mapping_id`
- **Foreign Keys:** `project_id`, `application_id`
- **Unique Constraint:** (project_id, application_id)

#### map_project_service
Project-Service many-to-many relationship.
- **Primary Key:** `mapping_id`
- **Foreign Keys:** `project_id`, `service_id`
- **Unique Constraint:** (project_id, service_id)

#### map_project_application_module
Three-way mapping for Project-Application-Module.
- **Primary Key:** `mapping_id`
- **Foreign Keys:** `project_id`, `application_id`, `module_id`
- **Audit Fields:** `created_by`, `updated_by`, `created_at`, `updated_at`

---

### 5. ISSUE MASTER TABLES (3 tables)

#### mst_issue_status
Issue status codes.
- **Primary Key:** `status_id`
- **Key Fields:** `status_code`, `status_name`, `status_category`
- **Flags:** `is_closed_status`, `is_active`
- **Display:** `display_order`
- **Example Status:** Open, In Progress, Closed, Rejected

#### mst_issue_category
Issue categories/types.
- **Primary Key:** `issue_category_id`
- **Key Fields:** `category_code`, `category_name`
- **Example Categories:** Bug, Enhancement, Support Request, etc.

#### mst_priority
Priority levels.
- **Primary Key:** `priority_id`
- **Key Fields:** `priority_code`, `priority_name`
- **Hierarchy:** `priority_level` (numeric for sorting)
- **Example Levels:** Critical, High, Medium, Low

---

### 6. WORKING CALENDAR TABLES (3 tables)

#### mst_working_calendar
Working calendars for organizations/states.
- **Primary Key:** `calendar_id`
- **Key Fields:** `calendar_code`, `calendar_name`
- **Foreign Keys:** `organisation_id`, `state_id` (optional)
- **Timezone:** `timezone`
- **Relations:** 1-to-many with WorkingSchedule, CalendarHoliday

#### mst_working_schedule
Working hours schedules.
- **Primary Key:** `schedule_id`
- **Key Fields:** `calendar_id`, `day_of_week`, `schedule_name`
- **Times:** `start_time`, `end_time`, `break_start`, `break_end`
- **Shifts:** `shift_no`, `shift_name`
- **Flags:** `is_working_day`, `is_24_hours`
- **Effective Period:** `effective_from`, `effective_to`
- **Soft Deletes:** `deleted_at`

#### mst_calendar_holiday
Holidays and special closures.
- **Primary Key:** `holiday_id`
- **Key Fields:** `calendar_id`, `holiday_date`, `holiday_code`, `holiday_name`
- **Types:** PUBLIC_HOLIDAY, OPTIONAL_HOLIDAY, COMPANY_HOLIDAY, STATE_HOLIDAY, SPECIAL_HOLIDAY, EMERGENCY_CLOSURE
- **Scope:** `applicable_scope` (ALL, STATE, HO, VENDOR, PROJECT)
- **Recurrence:** `is_recurring`, `recurrence_year`
- **Soft Deletes:** `deleted_at`

---

### 7. ROLE & PRIVILEGE TABLES (3 tables)

#### mst_role
User roles.
- **Primary Key:** `role_id`
- **Key Fields:** `role_code`, `role_name`, `role_category`
- **Flag:** `is_system_role`
- **Examples:** Admin, Manager, Agent, Viewer

#### mst_privilege
System privileges/permissions.
- **Primary Key:** `privilege_id`
- **Key Fields:** `privilege_code`, `privilege_name`
- **Module:** `module_name`
- **Examples:** ISSUE_CREATE, ISSUE_EDIT, ISSUE_VIEW, ISSUE_DELETE

#### mst_menu
Application menu items.
- **Primary Key:** `menu_id`
- **Key Fields:** `display_name`, `route_name`, `uri`
- **Hierarchy:** `parent_menu_id` (self-referencing for nested menus)
- **Display:** `icon`, `display_order`, `is_active`

---

### 8. USER TABLES (3 tables)

#### mst_user
System users.
- **Primary Key:** `user_id`
- **Key Fields:** `employee_code`, `user_name`, `login_id`, `official_email`
- **Unique Constraints:** `login_id`, `official_email`, `employee_code`
- **Organization:** `organisation_id`
- **Status:** `user_status` (ACTIVE, INACTIVE, etc.)
- **Password:** `password_hash`, `password_reset_otp`, `password_reset_otp_expires_at`
- **Timestamps:** `last_login_at`, `password_changed_at`

#### map_user_role
User-Role many-to-many relationship.
- **Primary Key:** `user_role_id`
- **Foreign Keys:** `user_id`, `role_id`
- **Unique Constraint:** (user_id, role_id)
- **Status:** `is_active`

#### map_user_project
User-Project assignments.
- **Primary Key:** `user_project_id`
- **Foreign Keys:** `user_id`, `project_id`
- **Unique Constraint:** (user_id, project_id)

---

### 9. SLA & CONFIGURATION TABLES (3 tables)

#### mst_sla_configuration
SLA configuration master.
- **Primary Key:** `sla_configuration_id`
- **Key Fields:** `sla_code`, `sla_name`
- **SLA Times:** `response_sla_hours`, `resolution_sla_hours`, `escalation_sla_hours`
- **Support Level:** `support_level`
- **Calendar:** `working_calendar_id`
- **Audit:** `created_by`, `updated_by`, `created_at`, `updated_at`

#### cfg_sla_policy
SLA policies for services/projects.
- **Primary Key:** `sla_policy_id`
- **Scope:** `service_id`, `project_id`, `application_id`, `priority_id`
- **Times:** `response_time_minutes`, `resolution_time_minutes`
- **Warning:** `warning_percentage` (for escalation)
- **Calendar:** `calendar_id`

#### mst_project_support_configuration
Support configuration for projects.
- **Primary Key:** `support_config_id`
- **Key Fields:** `project_id`, `config_code`, `config_name`
- **Defaults:** `default_support_level`, `default_team_type`, `default_priority`
- **Features:** `auto_routing_enabled`, `sla_hours`
- **Audit:** `created_by`, `updated_by`, `created_at`, `updated_at`

---

### 10. ISSUE ROUTING TABLES (2 tables)

#### cfg_issue_routing
Issue routing configuration.
- **Primary Key:** `routing_id`
- **Scope:** `state_id`, `service_id`, `project_id`, `application_id`, `module_id`
- **Routing Groups:** `ho_support_group_id`, `vendor_support_group_id`
- **Calendar:** `ho_calendar_id`
- **Features:** `off_hours_routing_enabled`
- **Effective Period:** `effective_from`, `effective_to`

#### mst_issue_routing_rule
Issue routing rules.
- **Primary Key:** `routing_rule_id`
- **Key Fields:** `support_config_id`, `rule_code`, `rule_name`
- **Matching Criteria:** `issue_category`, `issue_type`, `priority`
- **Level:** `routing_level` (1st, 2nd level, etc.)
- **Defaults:** `is_default`
- **Scope:** `project_id`, `state_id`, `application_id`, `vendor_id`
- **Audit:** `created_by`, `updated_by`, `created_at`, `updated_at`

---

### 11. ISSUE TRANSACTION TABLES (4 tables)

#### txn_issue
Main issue tickets table.
- **Primary Key:** `issue_id`
- **Key Fields:** `issue_number` (unique), `issue_title`
- **Scope:** `service_id`, `project_id`, `state_id`, `application_id`, `module_id`
- **Category:** `issue_category_id`, `priority_id`, `status_id`
- **Reporter:** `raised_by_user_id`, `raised_at`, `reported_by`
- **Assignment:** `current_owner_user_id`, `current_owner_group_id`, `current_owner_role_id`, `current_assignee_id`
- **Owner Types:** `current_owner_type`, `current_owner_id`, `current_owner_organisation_id`
- **State:** `current_stage`, `workflow_status`
- **Dates:** `raised_at`, `occurred_at`, `opened_at`, `assigned_at`, `resolved_at`, `closed_at`
- **Resolution:** `resolution_summary`, `resolved_by`, `resolved_at`
- **Escalation:** `ho_intervention_required`, `ho_working_hours`
- **Vendors:** `first_level_vendor_ids` (JSON), `second_level_vendor_ids` (JSON)
- **Metrics:** `reopened_count`, `sla_due_at`
- **Audit:** `created_by`, `updated_by`, `created_at`, `updated_at`

#### txn_issue_assignment
Issue assignments to teams/users.
- **Primary Key:** `assignment_id`
- **References:** `issue_id`, `routing_rule_id`, `support_config_id`
- **Assignee:** `support_team_id`, `assigned_user_id`
- **Assignment Details:** `assignment_level`, `assignment_type`, `assignment_reason`
- **Status:** `status`
- **Dates:** `assigned_at`, `accepted_at`, `started_at`, `completed_at`
- **Metadata:** `remarks`, `assigned_by`

#### txn_issue_attachment
Issue attachments.
- **Primary Key:** `attachment_id`
- **References:** `issue_id`, `user_id`
- **File Info:** `original_file_name`, `stored_file_name`, `file_path`, `file_size`, `file_type`
- **Upload:** `uploaded_at`
- **Status:** `is_active`

#### txn_issue_status_history
Issue status change audit trail.
- **Primary Key:** `status_history_id`
- **References:** `issue_id`, `from_status_id`, `to_status_id`
- **Status Text:** `from_status`, `to_status`
- **Context:** `assignment_id`, `routing_rule_id`, `support_config_id`
- **Change Info:** `change_type`, `change_reason`, `remarks`
- **Audit:** `changed_by`, `changed_at`

#### t_issue_history
Issue action history.
- **Primary Key:** `history_id`
- **References:** `issue_id`
- **Action:** `action`, `from_status`, `to_status`
- **Teams:** `from_team_id`, `to_team_id`
- **Audit:** `performed_by`, `created_at`

---

### 12. REQUIREMENT & CLARIFICATION TABLES (4 tables)

#### requirements
Requirements tracking.
- **Primary Key:** `id`
- **Key Fields:** `requirement_no` (unique), `title`
- **Scope:** `state_id`, `project_id`
- **Initiated By:** `brd_raised_by`
- **Team:** `ho_it_team` (boolean)
- **Vendor:** `assigned_vendor_id`
- **Dates:** `received_at`, `requested_to_vendor_at`
- **Effort:** `man_days`, `timeline`
- **Status:** `status` (OPEN, IN_PROGRESS, COMPLETED, etc.)
- **Delivery:** `delivery_status`, `vendor_remarks`
- **Audit:** `created_by`, `updated_by`, `created_at`, `updated_at`
- **Soft Deletes:** `deleted_at`

#### requirement_files
Requirement attachments.
- **Primary Key:** `id`
- **References:** `requirement_id`, `uploaded_by`
- **File Info:** `original_name`, `file_path`, `mime_type`, `file_size`
- **Timestamps:** `created_at`, `updated_at`

#### requirement_status_histories
Status change history for requirements.
- **Primary Key:** `id`
- **References:** `requirement_id`, `changed_by`
- **Status:** `from_status`, `to_status`
- **Metadata:** `remarks`
- **Timestamps:** `created_at`, `updated_at`

#### clarifications
Clarification request threads.
- **Primary Key:** `id`
- **References:** `requirement_id`, `user_id`
- **Message:** `message` (LONGTEXT)
- **Status:** `status` (OPEN, CLOSED, etc.)
- **Closure:** `closed_at`, `closed_by`
- **Timestamps:** `created_at`, `updated_at`
- **Relations:** 1-to-many with ClarificationReply

#### clarification_replies
Clarification replies.
- **Primary Key:** `id`
- **References:** `clarification_id`, `user_id`
- **Message:** `message` (LONGTEXT)
- **Timestamps:** `created_at`, `updated_at`

---

### 13. MAIL CONFIGURATION & LOGGING TABLES (3 tables)

#### mst_mail_setting
Mail server settings.
- **Primary Key:** `mail_setting_id`
- **Server:** `host`, `port`, `encryption`
- **Credentials:** `username`, `password`
- **From:** `from_address`, `from_name`
- **Status:** `is_active`

#### mst_mail_configuration
Mail configuration per state.
- **Primary Key:** `mail_configuration_id`
- **References:** `state_id`
- **Recipients:** `to_emails` (JSON array), `cc_emails` (JSON array)
- **Status:** `is_active`
- **Audit:** `created_by`, `updated_by`, `created_at`, `updated_at`

#### mst_mail_log
Mail sent/failed logs.
- **Primary Key:** `mail_log_id`
- **References:** `user_id`
- **Email:** `to_address`, `subject`, `body`
- **Status:** `status` (PENDING, SENT, FAILED)
- **Error:** `error_message`
- **Mailer:** `mailer`
- **Sent:** `sent_at`
- **Timestamps:** `created_at`, `updated_at`

---

## Key Features

### 1. Hierarchical Organization Structure
```
OrganisationType → Organisation → State → Project
                             ↓
                       HeadOffice, Vendor, SupportGroup
```

### 2. Issue Management Flow
```
Issue Creation → Assignment → Status Tracking → Resolution → Closure
     ↓              ↓              ↓               ↓            ↓
 Routing Rules  Assignments  Status History  Resolution  Closed Status
```

### 3. SLA Management
- Multiple SLA configurations per organization
- SLA policies tied to Service-Project-Application-Priority combinations
- Working calendar integration for business hour calculations

### 4. Role-Based Access Control
```
User → User_Role → Role → Role_Privilege/Menu → Privileges & Menus
```

### 5. Requirement & Clarification Tracking
```
Requirement → Files, Status History, Clarifications → Replies
```

### 6. Mail Integration
```
Mail Configuration → Mail Logs
    (per state)
Mail Settings → Mailer Integration
```

---

## Foreign Key Relationships Summary

### Cascade Deletes
- Issue → IssueAssignment, IssueAttachment, IssueStatusHistory
- Requirement → RequirementFiles, RequirementStatusHistory, Clarifications
- Clarification → ClarificationReplies
- Project → ProjectApplicationModuleMapping
- WorkingCalendar → WorkingSchedule, CalendarHoliday
- Role → RolePrivilege
- User → UserRole, UserProject
- SlaConfiguration → SlaPolicy
- IssueRoutingRule → (no cascade)

### Restrict Deletes
- OrganisationType → Organisation
- Organisation → State, HeadOffice, Vendor, SupportGroup
- Service → IssuePolicy, IssueTxn
- Priority → IssuePolicy, IssueTxn
- IssueStatus → IssueTxn
- IssueCategory → IssueTxn

### Set Null on Delete
- User foreign keys (created_by, updated_by)
- Optional relationship references
- Menu parent_menu_id

---

## Indexing Strategy

### Primary Indexes
- All primary keys are indexed
- All foreign keys are indexed for join performance

### Performance Indexes
- `mst_issue_status.is_closed_status` - Filter closed issues
- `txn_issue.status_id`, `priority_id` - Common issue filters
- `txn_issue.raised_at` - Date-based queries
- `mst_working_schedule.day_of_week` - Weekly schedule lookups
- `requirement_status_histories.changed_at` - Timeline queries
- `mst_mail_log.status`, `sent_at` - Mail tracking

### Unique Constraints
- User credentials: login_id, official_email
- Codes: All entity codes must be unique
- Multi-field unique constraints for mappings

---

## Character Set & Collation
- **Charset:** utf8mb4 (supports emoji and extended Unicode)
- **Collation:** utf8mb4_unicode_ci (case-insensitive, Unicode-aware)

---

## Data Integrity Features

### Soft Deletes
- `mst_working_schedule.deleted_at`
- `mst_calendar_holiday.deleted_at`
- `requirements.deleted_at`

### Audit Trail
- All master tables include `created_by`, `updated_by`, `created_at`, `updated_at`
- Transaction tables track all changes
- Status history tables maintain complete audit trail

### Timestamp Management
- `CURRENT_TIMESTAMP` for creation
- `ON UPDATE CURRENT_TIMESTAMP` for modifications
- Explicit timestamp management for business events

---

## Usage Examples

### Find Current Issue Owner
```sql
SELECT u.user_name, rp.role_name 
FROM txn_issue i
JOIN mst_user u ON i.current_owner_user_id = u.user_id
JOIN mst_role rp ON i.current_owner_role_id = rp.role_id
WHERE i.issue_id = ?
```

### Get Active Issues by Priority
```sql
SELECT i.issue_number, i.issue_title, p.priority_name
FROM txn_issue i
JOIN mst_priority p ON i.priority_id = p.priority_id
WHERE i.is_active = 1 
  AND i.status_id NOT IN (SELECT status_id FROM mst_issue_status WHERE is_closed_status = 1)
ORDER BY p.priority_level DESC
```

### Track SLA Compliance
```sql
SELECT i.issue_number, sp.response_time_minutes, sp.resolution_time_minutes,
       i.raised_at, i.resolved_at,
       TIMESTAMPDIFF(MINUTE, i.raised_at, i.resolved_at) as actual_minutes
FROM txn_issue i
JOIN cfg_sla_policy sp ON i.service_id = sp.service_id 
  AND i.project_id = sp.project_id
WHERE i.resolved_at IS NOT NULL
```

### Get User Permissions
```sql
SELECT p.privilege_code, p.privilege_name, m.display_name
FROM mst_user u
JOIN map_user_role ur ON u.user_id = ur.user_id
JOIN mst_role r ON ur.role_id = r.role_id
JOIN map_role_privilege rp ON r.role_id = rp.role_id
LEFT JOIN mst_privilege p ON rp.privilege_id = p.privilege_id
LEFT JOIN mst_menu m ON rp.menu_id = m.menu_id
WHERE u.user_id = ? AND ur.is_active = 1
```

---

## Migration & Deployment

1. **Backup existing database** before applying schema changes
2. **Run schema creation** with foreign key checks disabled (as provided)
3. **Verify table creation** with information_schema queries
4. **Create indexes** separately if needed for large tables
5. **Test relationships** with sample data inserts
6. **Verify foreign key constraints** are working

---

## Performance Considerations

1. **Large Tables:** txn_issue, txn_issue_assignment, requirement_status_histories
   - Consider partitioning by date for historical queries
   - Archive old closed issues periodically

2. **JSON Columns:** first_level_vendor_ids, second_level_vendor_ids
   - Use JSON path expressions for filtering
   - Consider normalization for frequent JSON queries

3. **Search Performance:**
   - Create full-text indexes on title/description fields if needed
   - Implement proper pagination for large result sets

4. **Audit Trail:** Grows continuously with every issue update
   - Implement archival strategy for historical data
   - Consider separate audit database for long-term retention

---

## Maintenance Notes

- Regularly vacuum unused space after bulk deletes
- Monitor table growth, especially txn_issue and status history tables
- Run ANALYZE TABLE periodically to update index statistics
- Backup procedures should account for table relationships
- Test restore procedures regularly

---

*Generated: 2024*  
*Database Version: MySQL 5.7+*  
*Laravel Version: 10.x*
