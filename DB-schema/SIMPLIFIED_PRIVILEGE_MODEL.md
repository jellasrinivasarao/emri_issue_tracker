# EMRI Issue Tracker - Simplified Privilege Model

## Overview

The EMRI Issue Tracker uses a **simplified, action-based privilege model** that applies consistently across all modules and features.

---

## Privilege Model Architecture

### Two-Level Permission System

```
USER
  ↓
ROLE
  ├─ Menu Access (can user access this page?)
  │   └─ Stored in: map_role_privilege(role_id, menu_id, is_allowed)
  │
  └─ Action Permission (what can user do on this page?)
      └─ Stored in: map_role_privilege(role_id, privilege_id, is_allowed)
```

### 18 Core Action-Based Privileges

These 18 privileges are **action-centric** and apply uniformly across all modules:

| # | Code | Name | Order | Purpose |
|---|------|------|-------|---------|
| 1 | `VIEW` | View | 1 | View/read data, access pages |
| 2 | `ADD` | Add | 2 | Create new records |
| 3 | `EDIT` | Edit | 3 | Modify existing records |
| 4 | `DELETE` | Delete | 4 | Remove records permanently |
| 5 | `APPROVE` | Approve | 5 | Approve pending items |
| 6 | `REJECT` | Reject | 6 | Reject items |
| 7 | `ASSIGN` | Assign | 7 | Assign to users/groups |
| 8 | `ESCALATE` | Escalate | 8 | Escalate priority/level |
| 9 | `RESOLVE` | Resolve | 9 | Mark as resolved |
| 10 | `CLOSE` | Close | 10 | Close/finalize items |
| 11 | `REOPEN` | Reopen | 11 | Reopen closed items |
| 12 | `EXPORT` | Export | 12 | Export data to files |
| 13 | `IMPORT` | Import | 13 | Import bulk data |
| 14 | `DOWNLOAD` | Download | 14 | Download files |
| 15 | `UPLOAD` | Upload | 15 | Upload files |
| 16 | `SUBMIT` | Submit | 16 | Submit forms/requests |
| 17 | `REVIEW` | Review | 17 | Review & audit logs |
| 18 | `CLARIFICATION` | Clarification | 18 | Request clarification |

---

## Privilege Groups by Function

### Read Operations
- **VIEW** - Access and read data
- **DOWNLOAD** - Download files/reports
- **EXPORT** - Export to external formats

### Create Operations
- **ADD** - Create new records
- **SUBMIT** - Submit forms/requests
- **UPLOAD** - Upload files

### Modify Operations
- **EDIT** - Update records
- **IMPORT** - Bulk import data

### Delete Operations
- **DELETE** - Remove records

### Workflow Operations
- **APPROVE** - Approve transitions
- **REJECT** - Reject transitions
- **ASSIGN** - Assignment transitions
- **ESCALATE** - Escalation transitions
- **RESOLVE** - Resolution transitions
- **CLOSE** - Closure transitions
- **REOPEN** - Reopening transitions

### Admin Operations
- **REVIEW** - Audit review
- **CLARIFICATION** - Request clarification

---

## How Privileges Map to Features

### Example 1: Issue Management
```
Issue Tracker Page (Menu: role.issue.dashboard)
├─ Can Access? (needs menu access)
├─ Can View Issues? (needs VIEW privilege)
├─ Can Create Issue? (needs ADD privilege)
├─ Can Edit Issue? (needs EDIT privilege)
├─ Can Assign to Vendor? (needs ASSIGN privilege)
├─ Can Escalate Issue? (needs ESCALATE privilege)
├─ Can Resolve Issue? (needs RESOLVE privilege)
├─ Can Close Issue? (needs CLOSE privilege)
└─ Can Download Report? (needs DOWNLOAD privilege)
```

### Example 2: State Master
```
State Master Page (Menu: state.master)
├─ Can Access? (needs menu access)
├─ Can View States? (needs VIEW privilege)
├─ Can Add State? (needs ADD privilege)
├─ Can Edit State? (needs EDIT privilege)
├─ Can Delete State? (needs DELETE privilege)
├─ Can Export States? (needs EXPORT privilege)
└─ Can Import States? (needs IMPORT privilege)
```

### Example 3: Approvals
```
Approval Workflow
├─ Can View Pending? (needs VIEW privilege)
├─ Can Approve? (needs APPROVE privilege)
├─ Can Reject? (needs REJECT privilege)
└─ Can Request Clarification? (needs CLARIFICATION privilege)
```

---

## Database Tables

### mst_privilege Table

```sql
CREATE TABLE mst_privilege (
    privilege_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    privilege_name VARCHAR(100) NOT NULL,        -- e.g., "View", "Add", "Edit"
    privilege_code VARCHAR(100) NOT NULL UNIQUE, -- e.g., "VIEW", "ADD", "EDIT"
    display_order INT DEFAULT 0,                 -- Display sequence
    is_active TINYINT(1) NOT NULL DEFAULT 1,    -- Active/Inactive flag
    created_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (privilege_id),
    UNIQUE KEY uk_privilege_code (privilege_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### map_role_privilege Table (Mapping)

```sql
-- Used to grant permissions to roles

-- Record 1: Grant menu access
INSERT INTO map_role_privilege (role_id, menu_id, privilege_id, is_allowed)
VALUES (1, 5, NULL, 1);  -- Role 1 can access Menu 5

-- Record 2: Grant action privilege
INSERT INTO map_role_privilege (role_id, menu_id, privilege_id, is_allowed)
VALUES (1, NULL, 3, 1);  -- Role 1 can EDIT privilege
```

---

## Role Permission Examples

### Central Admin Role (HO_ADMIN)
**Permissions:**
- All 28 menus (navigation access)
- All 18 privileges (all actions)
- All issue statuses (workflow)

```sql
SELECT 'HO_ADMIN' as role, 28 as menus, 18 as privileges, 'Full Admin' as access_level;
```

### Vendor IT Role
**Permissions:**
- Issue Tracker menu
- Vendor menu
- Privileges: VIEW, EDIT, SUBMIT, ESCALATE, RESOLVE, CLOSE

```sql
SELECT 'VENDOR_IT' as role, 2 as menus, 6 as privileges, 'Vendor Operations' as access_level;
```

### Read-Only Viewer Role
**Permissions:**
- Dashboard menu
- Issue Tracker (read-only)
- Reports menu
- Privileges: VIEW, DOWNLOAD, EXPORT

```sql
SELECT 'VIEWER' as role, 3 as menus, 3 as privileges, 'Read-Only' as access_level;
```

---

## Implementation Patterns

### Grant Menu Access to Role
```sql
INSERT INTO map_role_privilege (role_id, menu_id, is_allowed)
VALUES (
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT'),
    (SELECT menu_id FROM mst_menu WHERE route_name = 'role.issue.dashboard'),
    1
);
```

### Grant Privilege to Role
```sql
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
VALUES (
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT'),
    (SELECT privilege_id FROM mst_privilege WHERE privilege_code = 'VIEW'),
    1
);
```

### Grant Multiple Privileges to Role
```sql
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'EDIT', 'ESCALATE', 'RESOLVE');
```

### Verify Role Permissions
```sql
-- Check menus
SELECT m.display_name
FROM map_role_privilege mrp
JOIN mst_menu m ON mrp.menu_id = m.menu_id
WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT')
  AND mrp.menu_id IS NOT NULL;

-- Check privileges
SELECT p.privilege_name
FROM map_role_privilege mrp
JOIN mst_privilege p ON mrp.privilege_id = p.privilege_id
WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'VENDOR_IT')
  AND mrp.privilege_id IS NOT NULL
ORDER BY p.display_order;
```

---

## Common Role Configurations

### Configuration 1: Admin Role
```sql
-- Grant all menus and all privileges
INSERT INTO map_role_privilege (role_id, menu_id, is_allowed)
SELECT role_id, menu_id, 1 FROM 
  (SELECT 1 as role_id) r 
  CROSS JOIN mst_menu m;

INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT role_id, privilege_id, 1 FROM 
  (SELECT 1 as role_id) r 
  CROSS JOIN mst_privilege p;
```

### Configuration 2: Operator Role (CRUD only)
```sql
-- Grant: VIEW, ADD, EDIT, DELETE privileges
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'OPERATOR'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'ADD', 'EDIT', 'DELETE');
```

### Configuration 3: Viewer Role (Read-only)
```sql
-- Grant: VIEW, DOWNLOAD, EXPORT privileges only
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'VIEWER'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'DOWNLOAD', 'EXPORT');
```

### Configuration 4: Approval Role
```sql
-- Grant: VIEW, APPROVE, REJECT, CLARIFICATION privileges
INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'APPROVER'),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.privilege_code IN ('VIEW', 'APPROVE', 'REJECT', 'CLARIFICATION');
```

---

## Benefits of This Model

✅ **Simplicity** - Only 18 core privileges instead of dozens

✅ **Consistency** - Same privileges work across all modules

✅ **Flexibility** - Combine privileges to create custom roles

✅ **Scalability** - Add new modules without creating new privileges

✅ **Maintainability** - Easy to audit and understand permissions

✅ **Reusability** - Privileges apply uniformly everywhere

---

## Files Updated

| File | Changes |
|------|---------|
| `sample_menu_and_central_admin_setup.sql` | Updated privilege inserts to use 18 core privileges |
| `MENU_AND_CENTRAL_ADMIN_SETUP_GUIDE.md` | Updated documentation for new privilege model |
| `SIMPLIFIED_PRIVILEGE_MODEL.md` | This file (new documentation) |

---

## Migration from Old Model (if applicable)

If migrating from module-specific privileges (ISSUE_VIEW, VENDOR_CREATE, etc.), use:

```sql
-- Map old privileges to new ones
-- Old: ISSUE_VIEW, ISSUE_CREATE → New: VIEW, ADD
-- Old: VENDOR_EDIT, VENDOR_DELETE → New: EDIT, DELETE
-- Old: REPORT_EXPORT → New: EXPORT

UPDATE map_role_privilege 
SET privilege_id = (SELECT privilege_id FROM mst_privilege WHERE privilege_code = 'VIEW' LIMIT 1)
WHERE privilege_id IN (SELECT privilege_id FROM mst_privilege WHERE privilege_code LIKE 'ISSUE_VIEW');
```

---

## Support & Documentation

- **Setup Guide:** [MENU_AND_CENTRAL_ADMIN_SETUP_GUIDE.md](MENU_AND_CENTRAL_ADMIN_SETUP_GUIDE.md)
- **SQL Script:** [sample_menu_and_central_admin_setup.sql](sample_menu_and_central_admin_setup.sql)
- **Database Guide:** [README_DATABASE_SCRIPTS.md](README_DATABASE_SCRIPTS.md)

---

**Last Updated:** 2026-08-18
**Version:** 2.0 (Simplified Privilege Model)
**Compatibility:** MySQL 5.7+, Laravel 10+
