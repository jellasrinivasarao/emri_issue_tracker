# Database Schema - Quick Reference Guide

## Files Generated

1. **complete_database_schema.sql** - Complete CREATE TABLE statements for all 52 tables
2. **SCHEMA_DOCUMENTATION.md** - Comprehensive documentation with examples
3. **SCHEMA_QUICK_REFERENCE.md** - This quick reference guide

---

## Quick Table Lookup

### Search by Purpose

#### Organization Management
- `mst_organisation_type` - Organization type master
- `mst_organisation` - Main organization entity
- `mst_state` - States/divisions
- `mst_head_office` - Head office locations

#### Vendor & Support
- `mst_vendor` - Vendor information
- `mst_support_group` - Support teams

#### Application Management
- `mst_application` - Applications
- `mst_module` - Application modules
- `map_project_application` - Project ↔ Application mapping
- `map_project_service` - Project ↔ Service mapping
- `map_project_application_module` - 3-way mapping

#### Project Management
- `mst_project` - Projects
- `mst_service` - Services

#### Issue Management - Masters
- `mst_issue_status` - Status codes (Open, Closed, etc.)
- `mst_issue_category` - Issue types/categories
- `mst_priority` - Priority levels

#### Issue Management - Transactions
- `txn_issue` - Main issue tickets
- `txn_issue_assignment` - Issue assignments
- `txn_issue_attachment` - Issue files
- `txn_issue_status_history` - Status change history
- `t_issue_history` - Action history

#### Working Calendar
- `mst_working_calendar` - Calendars
- `mst_working_schedule` - Work shifts/hours
- `mst_calendar_holiday` - Holidays

#### SLA & Configuration
- `mst_sla_configuration` - SLA master
- `cfg_sla_policy` - SLA policies
- `mst_project_support_configuration` - Project support config
- `cfg_issue_routing` - Routing configuration
- `mst_issue_routing_rule` - Routing rules

#### Users & Access Control
- `mst_user` - Users
- `mst_role` - Roles
- `mst_privilege` - Permissions
- `mst_menu` - Menu items
- `map_user_role` - User-Role mapping
- `map_user_project` - User-Project mapping
- `map_role_privilege` - Role-Privilege/Menu mapping

#### Requirements
- `requirements` - Requirement tickets
- `requirement_files` - Requirement attachments
- `requirement_status_histories` - Requirement status history
- `clarifications` - Clarification requests
- `clarification_replies` - Clarification responses

#### Mail
- `mst_mail_setting` - Mail server config
- `mst_mail_configuration` - Mail recipients per state
- `mst_mail_log` - Mail log

---

## Common Queries

### 1. Get Active Issues Count
```sql
SELECT COUNT(*) as total_issues
FROM txn_issue
WHERE is_active = 1 
  AND status_id NOT IN (SELECT status_id FROM mst_issue_status WHERE is_closed_status = 1);
```

### 2. Get Issue with Full Details
```sql
SELECT 
    i.issue_number,
    i.issue_title,
    s.status_name,
    p.priority_name,
    c.category_name,
    u.user_name as reporter,
    u2.user_name as current_owner,
    r.role_name,
    i.raised_at,
    i.sla_due_at
FROM txn_issue i
JOIN mst_issue_status s ON i.status_id = s.status_id
JOIN mst_priority p ON i.priority_id = p.priority_id
JOIN mst_issue_category c ON i.issue_category_id = c.issue_category_id
JOIN mst_user u ON i.raised_by_user_id = u.user_id
LEFT JOIN mst_user u2 ON i.current_owner_user_id = u2.user_id
LEFT JOIN mst_role r ON i.current_owner_role_id = r.role_id
WHERE i.issue_id = ?;
```

### 3. Get User Permissions
```sql
SELECT DISTINCT 
    p.privilege_code,
    p.privilege_name,
    m.display_name
FROM map_user_role ur
JOIN mst_role r ON ur.role_id = r.role_id
JOIN map_role_privilege rp ON r.role_id = rp.role_id
LEFT JOIN mst_privilege p ON rp.privilege_id = p.privilege_id
LEFT JOIN mst_menu m ON rp.menu_id = m.menu_id
WHERE ur.user_id = ? 
  AND ur.is_active = 1
  AND rp.is_allowed = 1;
```

### 4. Check Working Hours (for SLA calculation)
```sql
SELECT 
    w.calendar_id,
    w.day_of_week,
    ws.start_time,
    ws.end_time,
    ws.is_working_day,
    ws.is_24_hours
FROM mst_working_calendar w
JOIN mst_working_schedule ws ON w.calendar_id = ws.calendar_id
WHERE w.calendar_id = ?
  AND ws.is_active = 1
ORDER BY w.day_of_week, ws.start_time;
```

### 5. Get Issue Status History
```sql
SELECT 
    sh.status_history_id,
    fs.status_name as from_status,
    ts.status_name as to_status,
    u.user_name as changed_by,
    sh.change_reason,
    sh.changed_at
FROM txn_issue_status_history sh
LEFT JOIN mst_issue_status fs ON sh.from_status_id = fs.status_id
LEFT JOIN mst_issue_status ts ON sh.to_status_id = ts.status_id
LEFT JOIN mst_user u ON sh.changed_by = u.user_id
WHERE sh.issue_id = ?
ORDER BY sh.changed_at DESC;
```

### 6. Get SLA Compliance Report
```sql
SELECT 
    i.issue_number,
    i.issue_title,
    sp.response_time_minutes,
    sp.resolution_time_minutes,
    TIMESTAMPDIFF(MINUTE, i.raised_at, COALESCE(i.resolved_at, NOW())) as minutes_taken,
    CASE 
        WHEN i.resolved_at IS NULL THEN 'OPEN'
        WHEN TIMESTAMPDIFF(MINUTE, i.raised_at, i.resolved_at) <= sp.resolution_time_minutes THEN 'COMPLIANT'
        ELSE 'BREACHED'
    END as sla_status
FROM txn_issue i
JOIN cfg_sla_policy sp ON i.service_id = sp.service_id 
    AND i.project_id = sp.project_id 
    AND i.application_id = sp.application_id
    AND i.priority_id = sp.priority_id
ORDER BY i.raised_at DESC;
```

### 7. Get Issue Assignment History
```sql
SELECT 
    ia.assignment_id,
    i.issue_number,
    sg.support_group_name,
    u.user_name as assigned_user,
    ia.assignment_type,
    ia.status,
    ia.assigned_at,
    ia.accepted_at,
    ia.completed_at
FROM txn_issue_assignment ia
JOIN txn_issue i ON ia.issue_id = i.issue_id
LEFT JOIN mst_support_group sg ON ia.support_team_id = sg.support_group_id
LEFT JOIN mst_user u ON ia.assigned_user_id = u.user_id
WHERE ia.issue_id = ?
ORDER BY ia.assigned_at DESC;
```

### 8. Get User Projects
```sql
SELECT 
    p.project_id,
    p.project_code,
    p.project_name,
    s.state_name,
    p.start_date,
    p.end_date,
    COUNT(i.issue_id) as issue_count
FROM map_user_project up
JOIN mst_project p ON up.project_id = p.project_id
JOIN mst_state s ON p.state_id = s.state_id
LEFT JOIN txn_issue i ON p.project_id = i.project_id
WHERE up.user_id = ?
GROUP BY p.project_id
ORDER BY p.project_name;
```

### 9. Get Organization Hierarchy
```sql
SELECT 
    o.organisation_id,
    o.organisation_name,
    o.organisation_code,
    s.state_id,
    s.state_name,
    COUNT(DISTINCT p.project_id) as projects,
    COUNT(DISTINCT pg.support_group_id) as support_groups,
    COUNT(DISTINCT v.vendor_id) as vendors
FROM mst_organisation o
LEFT JOIN mst_state s ON o.organisation_id = s.organisation_id
LEFT JOIN mst_project p ON s.state_id = p.state_id
LEFT JOIN mst_support_group pg ON o.organisation_id = pg.organisation_id
LEFT JOIN mst_vendor v ON o.organisation_id = v.organisation_id
WHERE o.organisation_id = ?
GROUP BY o.organisation_id, s.state_id;
```

### 10. Get Holiday Calendar for Month
```sql
SELECT 
    ch.holiday_date,
    ch.holiday_name,
    ch.holiday_type,
    ch.holiday_code,
    ch.applicable_scope,
    ch.is_recurring
FROM mst_calendar_holiday ch
WHERE ch.calendar_id = ?
  AND MONTH(ch.holiday_date) = MONTH(?)
  AND YEAR(ch.holiday_date) = YEAR(?)
  AND ch.is_active = 1
ORDER BY ch.holiday_date;
```

---

## Key Field Naming Conventions

### Primary Keys
- `{table}_id` (e.g., `user_id`, `project_id`)
- Auto-incrementing BIGINT UNSIGNED

### Foreign Keys
- `{referenced_table}_id` (e.g., `organisation_id`, `project_id`)

### Status/Flag Fields
- `is_{adjective}` (e.g., `is_active`, `is_working_day`, `is_closed_status`)
- `status` (e.g., `user_status`, `workflow_status`)

### Code Fields
- `{entity}_code` (e.g., `project_code`, `role_code`)

### Name Fields
- `{entity}_name` (e.g., `project_name`, `role_name`)

### Timestamp Fields
- `created_at`, `updated_at`, `deleted_at`
- `{action}_at` (e.g., `raised_at`, `resolved_at`, `closed_at`)

### Audit Fields
- `created_by`, `updated_by`, `deleted_by` (references user_id)

---

## Database Statistics

### Table Counts by Type
```
Master Tables:          23
Transaction Tables:      7
Configuration Tables:    7
Mapping/Junction Tables: 15
Total:                  52
```

### Total Fields: ~350+

### Relationships
- Foreign Keys: 85+
- Many-to-Many: 7
- Unique Constraints: 20+

---

## Connection String Format

### Laravel (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=issue_tracker_db
DB_USERNAME=root
DB_PASSWORD=password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### Direct MySQL
```bash
mysql -h127.0.0.1 -uroot -ppassword issue_tracker_db < complete_database_schema.sql
```

---

## Schema Validation Queries

### Check All Tables
```sql
SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'issue_tracker_db'
ORDER BY TABLE_NAME;
```

### Check Foreign Keys
```sql
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'issue_tracker_db'
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
```

### Check Indexes
```sql
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'issue_tracker_db'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;
```

### Check Table Definitions
```sql
SHOW CREATE TABLE issue_tracker_db.{table_name}\G
```

---

## Common Operations

### Add New Index
```sql
ALTER TABLE txn_issue 
ADD INDEX idx_issue_raised_project (raised_at, project_id);
```

### Add New Column
```sql
ALTER TABLE txn_issue 
ADD COLUMN custom_field VARCHAR(255) NULL 
AFTER issue_description;
```

### Add Foreign Key
```sql
ALTER TABLE txn_issue 
ADD CONSTRAINT fk_issue_custom 
FOREIGN KEY (custom_id) 
REFERENCES mst_custom (custom_id)
ON DELETE SET NULL ON UPDATE CASCADE;
```

### Create Backup
```bash
mysqldump -h127.0.0.1 -uroot -ppassword issue_tracker_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

## Performance Tips

1. **Always include** `is_active` filter for most queries
2. **Use indexes** on frequently filtered columns (status, priority, date)
3. **Paginate** results: `LIMIT 50 OFFSET 0`
4. **Join optimization**: Ensure foreign key columns are indexed
5. **Archive old data** from txn_issue and status history tables
6. **Use EXPLAIN** to analyze query performance
7. **Consider partitioning** txn_issue by date for large datasets

---

## Troubleshooting

### Foreign Key Constraint Error
```sql
-- Check constraint name
SHOW CREATE TABLE txn_issue;
-- Drop and recreate if needed
ALTER TABLE txn_issue DROP FOREIGN KEY constraint_name;
ALTER TABLE txn_issue ADD CONSTRAINT new_name FOREIGN KEY ...;
```

### Deadlock Issues
- Keep transactions short
- Ensure consistent lock ordering
- Monitor with: `SHOW ENGINE INNODB STATUS;`

### Query Performance
```sql
-- Enable query logging
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

-- Analyze table statistics
ANALYZE TABLE table_name;
OPTIMIZE TABLE table_name;
```

---

## Support & Updates

For schema changes or new requirements:
1. Review existing tables and relationships
2. Ensure backward compatibility
3. Create migration (Laravel migration file)
4. Test thoroughly before production deployment
5. Update this documentation

---

*Last Updated: 2024*  
*For detailed information, see: SCHEMA_DOCUMENTATION.md*
