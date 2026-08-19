# EMRI Issue Tracker - Database Scripts Documentation

## Overview

This document describes all database scripts available in the `DB-schema/` directory. These scripts define the complete database structure for the EMRI Issue Tracker application including:

- Master data tables (Projects, Applications, States, Vendors, etc.)
- Transaction tables (Issues, Status History, etc.)
- User and organization management
- Mapping tables (Role-Menu, User-Project, etc.)
- Configuration tables (SLA, Mail, Routing rules, etc.)

---

## Database Scripts Available

### 1. **master_tables_structure.sql**
**Purpose:** Creates all master/configuration tables for master pages

**Tables Created (18 tables):**

#### Master Data Tables:
- `mst_state` - State/Region master data
- `mst_project` - Project master data
- `mst_application` - Application master data
- `mst_module` - Module/Component master data
- `mst_service` - Service master data
- `mst_vendor` - Vendor master data
- `mst_issue_status` - Issue status master (New, In Progress, Resolved, Closed, etc.)
- `mst_priority` - Priority levels (Critical, High, Medium, Low)
- `mst_issue_category` - Issue category/type master
- `mst_support_group` - Support group master
- `mst_role` - Role master (HO Admin, Vendor IT, State Admin, etc.)
- `mst_menu` - Menu/Navigation master
- `mst_privilege` - Privilege/Permission master

#### Mapping Tables:
- `map_project_application` - Links projects to applications
- `map_project_application_module` - Links projects to applications to modules
- `map_project_service` - Links projects to services
- `map_project_state` - Links projects to states
- `map_vendor_state` - Links vendors to states (with optional project/application)
- `map_role_privilege` - Links roles to menus and privileges
- `map_role_issue_status` - Defines which statuses each role can use
- `map_user_role` - Assigns roles to users
- `map_user_project` - Assigns users to projects
- `map_user_support_group` - Assigns users to support groups
- `map_issue_vendor_assignment` - **CRITICAL** - Tracks vendor assignments to issues (multi-vendor support)

**Key Features:**
- Support for multi-vendor issue assignment
- Role-based status management
- Hierarchical menu structure
- Vendor state mapping with application filtering

**Sample Data Included:**
- Issue statuses (New, Assigned, In Progress, Resolved, Closed, Vendor Assignment, Escalate to Vendor, Rejected, Clarification)
- Priority levels
- System roles (HO Admin, HO IT, Vendor Admin, Vendor IT, State Admin, State IT)

---

### 2. **transaction_tables_structure.sql**
**Purpose:** Creates all transaction/operational tables for issue tracking

**Tables Created (15 tables):**

#### Issue Transaction Tables:
- `txn_issue` - Main issue/ticket table (core data)
- `txn_issue_status_history` - Complete status change audit trail
- `txn_issue_attachment` - File attachments for issues

#### Routing & Assignment:
- `mst_issue_routing` - Routing rules for automatic vendor assignment
- `txn_issue_routing` - Issue routing transaction log

#### SLA & Configuration:
- `mst_sla_policy` - SLA definitions by project/priority
- `mst_sla_configuration` - State/Project/Priority SLA mappings

#### Working Calendar & Schedules:
- `mst_working_calendar` - Working calendar by state and year
- `txn_calendar_holiday` - Holiday definitions
- `mst_working_schedule` - Working hours by day of week per state

#### Mail & Notifications:
- `mst_mail_setting` - SMTP server configurations
- `mst_mail_configuration` - Email routing by state/project
- `txn_mail_log` - Email sending transaction log

**Key Features:**
- Complete audit trail of status changes
- Vendor-specific status tracking (vendor_id in history)
- SLA configuration by priority and project
- Multiple working calendars per state
- Configurable working hours and holidays
- Email logging and tracking

**Critical Fields:**
- `txn_issue_status_history.vendor_id` - Enables tracking vendor-specific status changes
- `map_issue_vendor_assignment` (in master_tables_structure.sql) - Links vendors to issues

---

### 3. **user_organization_tables_structure.sql**
**Purpose:** Creates user management and organization-related tables

**Tables Created (9 tables):**

#### Organization Management:
- `mst_organisation_type` - Organization type master (Corporate, Government, Private, Non-Profit)
- `mst_organisation` - Organization master
- `mst_head_office` - Head office locations per organization
- `mst_user` - User master (with password, email, OTP fields)

#### Audit & Activity Logging:
- `txn_user_activity_log` - User login, logout, and actions
- `txn_system_audit_log` - System-level audit trail for all table changes

#### Advanced Configuration:
- `mst_project_support_configuration` - Support group configuration per project
- `mst_issue_routing_rule` - Advanced routing rules (with execution order)

**Key Features:**
- Multi-organization support
- User vendor/state/head office assignment
- Complete audit trail
- Activity logging for security compliance
- Advanced routing rules with conditions
- Password reset OTP support

**Sample Data Included:**
- Organization types

---

## Recommended Script Execution Order

### **Phase 1: Foundation (Run First)**
```
1. user_organization_tables_structure.sql
   └─ Creates: Organization, HeadOffice, User foundations
   
2. master_tables_structure.sql
   └─ Creates: States, Roles, Menus, Privileges
   └─ Creates: All mapping tables
```

### **Phase 2: Issue Management (Run After Phase 1)**
```
3. transaction_tables_structure.sql
   └─ Creates: Issues, Status History, Routing, SLA, Calendars
   └─ Creates: Mail configuration
```

### **Phase 3: Data Population (Optional - After All Scripts)**
```
- Populate mst_state with real states
- Populate mst_organisation with real organizations
- Populate mst_project with real projects
- Add users to mst_user
- Map users to roles via map_user_role
- Configure SLA policies
```

---

## Important Notes

### ⚠️ CRITICAL - Multi-Vendor Workflow
The `map_issue_vendor_assignment` table (in master_tables_structure.sql) is ESSENTIAL for the multi-vendor workflow:
- Each issue can have multiple vendors assigned
- Each vendor has its own status in `vendor_status_id`
- `is_active` flag marks rejected vendors
- `status_remarks` stores vendor-specific notes

### ⚠️ Constraints & Dependencies
**Do NOT run scripts out of order** - Foreign key constraints will fail:
- `mst_user` depends on `mst_organisation`
- `map_user_role` depends on both `mst_user` and `mst_role`
- `txn_issue` depends on `mst_project`, `mst_application`, `mst_priority`, `mst_issue_status`
- `txn_issue_status_history` depends on `txn_issue` and `mst_issue_status`

### ⚠️ Backup Before Modifications
Always backup your database before running these scripts in production:
```sql
-- Backup single database
mysqldump -u root -p emri_issue_tracker > backup_$(date +%Y%m%d_%H%M%S).sql

-- Or use Laravel backup
php artisan backup:run
```

---

## Using Scripts in Different Environments

### **Development Environment**
```bash
# Using Laravel migrations (Recommended)
php artisan migrate

# Using raw SQL (Direct import)
mysql -u root -p emri_issue_tracker < DB-schema/master_tables_structure.sql
mysql -u root -p emri_issue_tracker < DB-schema/transaction_tables_structure.sql
mysql -u root -p emri_issue_tracker < DB-schema/user_organization_tables_structure.sql
```

### **Production Environment**
```bash
# 1. Backup first
mysqldump -u production_user -p production_db > backup_$(date +%Y%m%d).sql

# 2. Run migrations via Laravel (Safer)
php artisan migrate --env=production

# 3. Verify migration status
php artisan migrate:status
```

### **Docker Environment**
```bash
# Execute scripts inside container
docker exec -i mysql_container mysql -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE < master_tables_structure.sql
```

---

## Table Reference by Master Page

| Master Page | Primary Table | Related Mapping Tables |
|---|---|---|
| State Master | `mst_state` | `map_project_state`, `map_vendor_state` |
| Project Master | `mst_project` | `map_project_application`, `map_project_service`, `map_project_state` |
| Application Master | `mst_application` | `map_project_application`, `map_project_application_module` |
| Module Master | `mst_module` | `map_project_application_module` |
| Service Master | `mst_service` | `map_project_service` |
| Vendor Master | `mst_vendor` | `map_vendor_state`, `map_issue_vendor_assignment` |
| Issue Status Master | `mst_issue_status` | `map_role_issue_status`, `txn_issue_status_history` |
| Priority Master | `mst_priority` | `mst_sla_policy`, `mst_sla_configuration` |
| Issue Category Master | `mst_issue_category` | `mst_sla_policy` |
| Support Group Master | `mst_support_group` | `map_user_support_group`, `mst_project_support_configuration` |
| Role Master | `mst_role` | `map_role_privilege`, `map_role_issue_status`, `map_user_role` |
| Menu Master | `mst_menu` | `map_role_privilege` |
| Privilege Master | `mst_privilege` | `map_role_privilege` |
| User Master | `mst_user` | `map_user_role`, `map_user_project`, `map_user_support_group` |

---

## Sample Queries for Verification

### Check all tables created
```sql
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'emri_issue_tracker' 
ORDER BY TABLE_NAME;
```

### Verify issue status options
```sql
SELECT status_id, status_name, status_category 
FROM mst_issue_status 
WHERE is_active = 1 
ORDER BY display_order;
```

### Check vendor assignments
```sql
SELECT 
  iva.issue_id,
  v.vendor_name,
  iva.vendor_status_id,
  s.status_name,
  iva.is_active,
  iva.status_remarks
FROM map_issue_vendor_assignment iva
LEFT JOIN mst_vendor v ON iva.vendor_id = v.vendor_id
LEFT JOIN mst_issue_status s ON iva.vendor_status_id = s.status_id
WHERE iva.is_active = 1
ORDER BY iva.issue_id, v.vendor_name;
```

### Verify role permissions
```sql
SELECT 
  r.role_name,
  m.display_name,
  mrp.is_allowed
FROM map_role_privilege mrp
LEFT JOIN mst_role r ON mrp.role_id = r.role_id
LEFT JOIN mst_menu m ON mrp.menu_id = m.menu_id
WHERE r.is_active = 1
ORDER BY r.role_name, m.display_order;
```

---

## Common Issues & Solutions

### Issue 1: Foreign Key Constraint Errors
**Error:** `Cannot add or modify row: foreign key constraint fails`

**Solution:**
- Run scripts in the recommended order
- Ensure parent tables exist before dependent tables
- Check that referenced IDs exist in parent tables

### Issue 2: Duplicate Key Errors
**Error:** `Duplicate entry for key 'uq_*'`

**Solution:**
- Scripts use `ON DUPLICATE KEY UPDATE` for idempotency
- Remove existing tables first if re-running: `DROP TABLE IF EXISTS table_name;`

### Issue 3: Charset/Collation Issues
**Error:** `Illegal mix of collations`

**Solution:**
- Ensure database collation is `utf8mb4_unicode_ci`:
```sql
ALTER DATABASE emri_issue_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Customization Guide

### Add New Status
```sql
INSERT INTO `mst_issue_status` 
(`status_code`, `status_name`, `status_category`, `display_order`, `is_active`)
VALUES ('CUSTOM_STATUS', 'Custom Status Name', 'Custom', 10, 1);
```

### Add New Priority
```sql
INSERT INTO `mst_priority`
(`priority_code`, `priority_name`, `priority_level`, `display_order`, `is_active`)
VALUES ('CUSTOM_PRI', 'Custom Priority', 5, 5, 1);
```

### Add Role Permissions
```sql
INSERT INTO `map_role_privilege`
(`role_id`, `menu_id`, `privilege_id`, `is_allowed`)
VALUES (1, 5, 1, 1);  -- Grant menu 5 to role 1
```

### Configure SLA by Priority
```sql
INSERT INTO `mst_sla_configuration`
(`state_id`, `project_id`, `priority_id`, `response_time_hours`, `resolution_time_hours`, `is_active`)
VALUES (1, 1, 1, 2, 8, 1);  -- Critical: 2h response, 8h resolution
```

---

## Notes for Database Administrators

1. **Indexes:** All scripts include appropriate indexes for common queries
2. **Audit Trail:** Enable `txn_system_audit_log` for compliance
3. **Partitioning:** For large datasets, consider partitioning `txn_issue_status_history` by month
4. **Archiving:** Archive old issues and logs annually to `txn_issue_archive`
5. **Backups:** Maintain daily incremental backups

---

## Support & Maintenance

For questions or issues with these scripts:
1. Check the table definitions and foreign key constraints
2. Review the sample data insertions
3. Consult the specific master page documentation
4. Check Laravel migration status: `php artisan migrate:status`

---

**Last Updated:** 2026-08-18
**Version:** 1.0
**Compatibility:** MySQL 5.7+, Laravel 10+
