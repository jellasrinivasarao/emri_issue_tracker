# EMRI Issue Tracker - Menu & Central Admin Setup Guide

## Overview

This guide explains how to set up the menu structure, privileges, and central admin user for the EMRI Issue Tracker application.

---

## File: `sample_menu_and_central_admin_setup.sql`

This SQL script contains:

### 1. **Menu Structure (mst_menu)**
Complete hierarchical menu for the application including:
- Dashboard
- Role Dashboard  
- Issue Tracker
- Issues Management (Create, View, etc.)
- Reports
- Administration Section with:
  - Master Pages
  - User Management
  - Mapping Configuration
  - System Configuration

### 2. **Privileges (mst_privilege)**
30+ permission types covering:
- Issue Management (View, Create, Edit, Delete, Close, Reopen)
- Vendor Management (View, Create, Edit, Delete)
- Master Data Operations (View, Create, Edit, Delete)
- User Management (View, Create, Edit, Delete)
- Role & Permission Management (View, Create, Edit, Delete)
- Reports (View, Export)
- Configuration & SLA (View, Edit)
- Audit & Logs (View)
- Dashboard (View)

### 3. **Role-Privilege Mappings (map_role_privilege)**
Grants Central Admin (HO_ADMIN) role:
- Access to ALL menus
- Access to ALL privileges
- Access to ALL issue statuses

### 4. **Central Admin User**
Default admin login for initial system access

### 5. **User-Role Assignment (map_user_role)**
Links central admin user to HO_ADMIN role

---

## Execution Steps

### Step 1: Prerequisites
Ensure these scripts have already been executed in order:
1. ✅ `master_tables_structure.sql` - Master & mapping tables
2. ✅ `transaction_tables_structure.sql` - Transaction tables (optional for menu setup)
3. ✅ `user_organization_tables_structure.sql` - User & organization tables

### Step 2: Run Menu Setup Script

```bash
# Option A: Direct SQL execution
mysql -u root -p emri_issue_tracker < sample_menu_and_central_admin_setup.sql

# Option B: Using Laravel migrations
php artisan db:seed --seeder=MenuAndAdminSeeder
```

### Step 3: Verify Installation

After running the script, verify with these queries:

```sql
-- Check total menus created
SELECT COUNT(*) as total_menus FROM mst_menu WHERE is_active = 1;
-- Expected: 28 menus

-- Check privileges created (18 core action-based privileges)
SELECT COUNT(*) as total_privileges FROM mst_privilege WHERE is_active = 1;
-- Expected: 18 privileges

-- List all privileges
SELECT privilege_id, privilege_code, privilege_name, display_order 
FROM mst_privilege WHERE is_active = 1 ORDER BY display_order;

-- Verify central admin user exists
SELECT user_id, login_id, user_name, is_active FROM mst_user WHERE login_id = 'admin';

-- Verify central admin has admin role
SELECT 
    u.login_id,
    r.role_name,
    mur.is_active
FROM mst_user u
LEFT JOIN map_user_role mur ON u.user_id = mur.user_id
LEFT JOIN mst_role r ON mur.role_id = r.role_id
WHERE u.login_id = 'admin';

-- Verify menu access for admin role (should have all menus)
SELECT COUNT(*) as menu_count
FROM map_role_privilege mrp
WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
AND mrp.menu_id IS NOT NULL;
-- Expected: Should match total active menus (28)

-- Verify privilege access for admin role (should have all privileges)
SELECT COUNT(*) as privilege_count
FROM map_role_privilege mrp
WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
AND mrp.privilege_id IS NOT NULL;
-- Expected: 18 privileges

-- List HO_ADMIN privileges
SELECT 
    p.privilege_code,
    p.privilege_name,
    mrp.is_allowed
FROM map_role_privilege mrp
LEFT JOIN mst_privilege p ON mrp.privilege_id = p.privilege_id
WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
AND mrp.privilege_id IS NOT NULL
ORDER BY p.display_order;
```

---

## Central Admin Default Credentials

### Login Credentials:
| Field | Value |
|-------|-------|
| **Username** | `admin` |
| **Password** | `password` |
| **Email** | `admin@example.com` |
| **Employee Code** | `ADMIN001` |
| **User Status** | Active |

### Important Security Notes:

⚠️ **CRITICAL - Change Password Immediately:**
1. Log in with default credentials
2. Go to Profile/Settings
3. Change password to a strong, unique password
4. Update email if needed

⚠️ **Password Hash Details:**
- Current hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
- Algorithm: Laravel Bcrypt (default)
- Plain text: `password` (for testing only)

**To generate a new password hash:**
```php
// In Laravel Tinker console (php artisan tinker)
bcrypt('your_new_password')
```

Then update the database:
```sql
UPDATE mst_user 
SET password_hash = '$2y$10$...' 
WHERE login_id = 'admin';
```

---

## Menu Structure Overview

### Top-Level Menus
1. **Dashboard** - Main dashboard view
2. **Role Dashboard** - Role-specific dashboard
3. **Issue Tracker** - Issue tracking interface
4. **Issues** - View issue list
5. **Raise Issue** - Create new issue
6. **Reports** - Generate & view reports
7. **Administration** - Admin panel (Parent menu)

### Administration Submenu (Master Pages)
1. **State Master** - Manage states/regions
2. **Project Master** - Manage projects
3. **Application Master** - Manage applications
4. **Module Master** - Manage modules/components
5. **Service Master** - Manage services
6. **Vendor Master** - Manage vendors
7. **Support Group Master** - Manage support groups
8. **User Master** - Manage users
9. **Role Master** - Manage roles
10. **Privilege Master** - Manage permissions
11. **Menu Master** - Manage menu items

### Administration Submenu (Mappings)
1. **User Role Mapping** - Assign roles to users
2. **User Project Mapping** - Assign users to projects
3. **User Support Group Mapping** - Assign users to support groups
4. **Vendor State Mapping** - Link vendors to states
5. **Project Application Module Mapping** - Hierarchical project mapping
6. **Project State Mapping** - Link projects to states
7. **Role Menu Mapping** - Control menu access
8. **Role Privilege Mapping** - Assign permissions
9. **Role Issue Status Mapping** - Define available statuses per role

### Administration Submenu (Configuration)
1. **Issue Routing Rules** - Auto-routing configuration
2. **Working Calendar** - Holiday definitions
3. **Working Schedule** - Working hours per day
4. **SLA Configuration** - SLA policies

### Admin Access Menus
- Central Admin (redirects to role dashboard)
- HO Admin (Head Office Admin)
- State Admin (State Administrator)
- Vendor Admin (Vendor Administrator)

---

## Privileges Overview

### 18 Action-Based Core Privileges

The privilege model uses generic, action-centric privileges that apply across all modules:

| # | Privilege Code | Privilege Name | Display Order |
|---|---|---|---|
| 1 | `VIEW` | View | 1 |
| 2 | `ADD` | Add | 2 |
| 3 | `EDIT` | Edit | 3 |
| 4 | `DELETE` | Delete | 4 |
| 5 | `APPROVE` | Approve | 5 |
| 6 | `REJECT` | Reject | 6 |
| 7 | `ASSIGN` | Assign | 7 |
| 8 | `ESCALATE` | Escalate | 8 |
| 9 | `RESOLVE` | Resolve | 9 |
| 10 | `CLOSE` | Close | 10 |
| 11 | `REOPEN` | Reopen | 11 |
| 12 | `EXPORT` | Export | 12 |
| 13 | `IMPORT` | Import | 13 |
| 14 | `DOWNLOAD` | Download | 14 |
| 15 | `UPLOAD` | Upload | 15 |
| 16 | `SUBMIT` | Submit | 16 |
| 17 | `REVIEW` | Review | 17 |
| 18 | `CLARIFICATION` | Clarification | 18 |

### Privilege Categories

| Category | Privileges | Use Case |
|----------|-----------|----------|
| **Read** | VIEW, DOWNLOAD | View data, access read-only pages |
| **Create** | ADD, SUBMIT, UPLOAD | Create new records, submit forms |
| **Modify** | EDIT, IMPORT | Update existing records, bulk operations |
| **Delete** | DELETE | Remove records |
| **Workflow** | APPROVE, REJECT, ASSIGN, ESCALATE, RESOLVE, CLOSE, REOPEN | Move items through workflow states |
| **Administration** | EXPORT, REVIEW, CLARIFICATION | Admin/audit functions |

### How Privileges Work with Menus

**Two-level permission model:**
1. **Menu Access**: Can user see/access the menu?
   - Stored in: `map_role_privilege` (menu_id, privilege_id = NULL)
   - Determines: Navigation visibility, route access

2. **Action Permission**: What can user do on that menu?
   - Stored in: `map_role_privilege` (menu_id = NULL, privilege_id)
   - Determines: View, Edit, Delete, Approve buttons; Form submissions; Data operations

**Example: State Master**
- Menu access privilege: Role can access `/state-master` route
- Action privileges: Role can VIEW, ADD, EDIT, DELETE states

---

## Central Admin Role Permissions

The HO_ADMIN (Central Admin) role has:

✅ **Full Menu Access** - All 28+ menus available for navigation
✅ **All Privileges** - All 18 action-based permissions (VIEW, ADD, EDIT, DELETE, APPROVE, REJECT, ASSIGN, ESCALATE, RESOLVE, CLOSE, REOPEN, EXPORT, IMPORT, DOWNLOAD, UPLOAD, SUBMIT, REVIEW, CLARIFICATION)
✅ **All Issue Statuses** - Can transition to any status
✅ **System Administration** - Complete control

### Capabilities by Privilege:

| Privilege | Capability |
|-----------|-----------|
| **VIEW** | Access and read all pages, view all data |
| **ADD** | Create new records (States, Projects, Users, etc.) |
| **EDIT** | Modify existing records |
| **DELETE** | Remove records from system |
| **APPROVE** | Approve pending items |
| **REJECT** | Reject items |
| **ASSIGN** | Assign users to projects, vendors to issues |
| **ESCALATE** | Escalate issues to higher levels |
| **RESOLVE** | Mark issues as resolved |
| **CLOSE** | Close resolved issues |
| **REOPEN** | Reopen closed issues |
| **EXPORT** | Export data to files (Excel, CSV, etc.) |
| **IMPORT** | Import bulk data |
| **DOWNLOAD** | Download reports and files |
| **UPLOAD** | Upload files and documents |
| **SUBMIT** | Submit forms and requests |
| **REVIEW** | Review and audit logs |
| **CLARIFICATION** | Request clarification from users |

---

## How to Add More Users

### Method 1: Using User Master (UI)
1. Log in as Central Admin
2. Go to Administration > User Master
3. Click "Add User"
4. Fill in details and save
5. Assign role via Administration > User Role Mapping

### Method 2: Direct SQL Insert
```sql
-- Insert new user
INSERT INTO mst_user (
    employee_code,
    user_name,
    login_id,
    official_email,
    mobile_number,
    password_hash,
    user_status,
    is_active
) VALUES (
    'EMP001',
    'Employee Name',
    'emp001',
    'emp001@example.com',
    '+91-9876543210',
    bcrypt('InitialPassword123'),
    'Active',
    1
);

-- Assign role to user
INSERT INTO map_user_role (user_id, role_id, is_active)
SELECT 
    (SELECT user_id FROM mst_user WHERE login_id = 'emp001' LIMIT 1),
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT' LIMIT 1),
    1;
```

---

## Customization Guide

### Add Custom Menu
```sql
INSERT INTO mst_menu (display_name, route_name, uri, parent_menu_id, icon, display_order, is_active)
VALUES (
    'Custom Menu',                      -- Menu display name
    'custom.menu',                      -- Route name (must match Laravel route)
    '/custom-page',                     -- URI path
    (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1),  -- Parent menu
    'icon-name',                        -- Icon class
    25,                                 -- Display order
    1                                   -- Is active
);
```

### Grant Menu Access to Role
```sql
-- Grant a specific menu to a role
INSERT INTO map_role_privilege (role_id, menu_id, is_allowed)
VALUES (
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT' LIMIT 1),
    (SELECT menu_id FROM mst_menu WHERE route_name = 'custom.menu' LIMIT 1),
    1
);
```

### Grant Action Permission to Role
```sql
-- Grant VIEW privilege to VENDOR_IT role
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
VALUES (
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT' LIMIT 1),
    (SELECT privilege_id FROM mst_privilege WHERE privilege_code = 'VIEW' LIMIT 1),
    1
);

-- Grant multiple privileges: VIEW, EDIT, SUBMIT
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'EDIT', 'SUBMIT')
  AND NOT EXISTS (
    SELECT 1 FROM map_role_privilege mrp
    WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT')
    AND mrp.privilege_id = p.privilege_id
  );
```

### Create New Role with Limited Permissions
```sql
-- Step 1: Create the role
INSERT INTO mst_role (role_code, role_name, role_category, is_system_role, is_active)
VALUES ('VIEWER_ROLE', 'Read-Only Viewer', 'Limited', 0, 1);

-- Step 2: Grant specific menus (navigation access)
INSERT INTO map_role_privilege (role_id, menu_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VIEWER_ROLE'),
    m.menu_id,
    1
FROM mst_menu m
WHERE m.route_name IN ('dashboard', 'role.issue.dashboard', 'issues.index', 'reports');

-- Step 3: Grant only VIEW and EXPORT privileges (read-only + export)
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VIEWER_ROLE'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'EXPORT', 'DOWNLOAD');
```

### Create Vendor Role with Specific Actions
```sql
-- Step 1: Create vendor-specific role
INSERT INTO mst_role (role_code, role_name, role_category, is_system_role, is_active)
VALUES ('VENDOR_ESCALATION', 'Vendor with Escalation', 'Vendor', 0, 1);

-- Step 2: Grant vendor-relevant menus
INSERT INTO map_role_privilege (role_id, menu_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_ESCALATION'),
    m.menu_id,
    1
FROM mst_menu m
WHERE m.route_name IN ('role.issue.dashboard', 'issues.index');

-- Step 3: Grant vendor actions (VIEW, EDIT, ESCALATE, RESOLVE)
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_ESCALATION'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'EDIT', 'ESCALATE', 'RESOLVE', 'SUBMIT');
```

---

## Troubleshooting

### Issue 1: Login fails with correct credentials
**Solution:**
1. Verify user exists: `SELECT * FROM mst_user WHERE login_id = 'admin';`
2. Check is_active = 1
3. Verify password hash: Update with bcrypt
4. Check user has role: `SELECT * FROM map_user_role WHERE user_id = X;`

### Issue 2: Menu not appearing after login
**Solution:**
1. Verify menu exists: `SELECT * FROM mst_menu WHERE route_name = 'xxx';`
2. Check menu is_active = 1
3. Check role-privilege mapping: `SELECT * FROM map_role_privilege WHERE menu_id = X;`
4. Clear cache: `php artisan cache:clear`

### Issue 3: Cannot access specific page
**Solution:**
1. Verify menu.access middleware in routes
2. Check menu exists and is linked to role
3. Verify user has role assigned
4. Check role has menu privilege with is_allowed = 1

### Issue 4: Foreign key constraint error
**Solution:**
1. Ensure master_tables_structure.sql ran first
2. Ensure mst_role table has HO_ADMIN role
3. Check parent table records exist before foreign key reference

---

## Database Relationships

```
mst_user (1) ──── (M) map_user_role ──── (1) mst_role
    │
    ├─── (1) map_role_privilege ──── (1) mst_menu
    │
    └─── (1) map_role_privilege ──── (1) mst_privilege

mst_role (1) ──── (M) map_role_issue_status ──── (1) mst_issue_status
```

---

## Additional Resources

### Related Database Scripts:
- [master_tables_structure.sql](master_tables_structure.sql) - Master data tables
- [transaction_tables_structure.sql](transaction_tables_structure.sql) - Transaction tables
- [user_organization_tables_structure.sql](user_organization_tables_structure.sql) - User management
- [README_DATABASE_SCRIPTS.md](README_DATABASE_SCRIPTS.md) - Complete database guide

### Related Configuration Files:
- `config/auth.php` - Laravel authentication config
- `app/Http/Middleware/MenuAccess.php` - Menu access middleware
- `routes/web.php` - Application routes

---

## Migration to Production

### Backup First:
```bash
mysqldump -u root -p emri_issue_tracker > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Run Script:
```bash
# Using Laravel (Recommended)
php artisan migrate --seed

# Or direct SQL
mysql -u root -p emri_issue_tracker < sample_menu_and_central_admin_setup.sql
```

### Change Admin Password:
```bash
# Use Tinker
php artisan tinker
User::where('login_id', 'admin')->update(['password_hash' => bcrypt('NewStrongPassword123')])
```

### Update Admin Email:
```sql
UPDATE mst_user 
SET official_email = 'actual.admin@yourcompany.com'
WHERE login_id = 'admin';
```

---

## Support

For issues or questions:
1. Check troubleshooting section above
2. Verify all prerequisites are met
3. Review Laravel logs: `storage/logs/laravel.log`
4. Check MySQL error logs for constraint violations
5. Verify database collation: `utf8mb4_unicode_ci`

---

**Last Updated:** 2026-08-18
**Version:** 1.0
**Compatibility:** MySQL 5.7+, Laravel 10+
