-- ============================================================================
-- EMRI ISSUE TRACKER - MENU, PRIVILEGES & CENTRAL ADMIN SETUP
-- ============================================================================
-- Sample data for:
-- 1. mst_menu - Navigation menu structure
-- 2. mst_privilege - System permissions
-- 3. Central Admin role with full privileges
-- 4. Central Admin user (login) for testing/initial setup
-- ============================================================================

-- ============================================================================
-- 1. INSERT MENU ITEMS (mst_menu)
-- ============================================================================

-- Main/Root Menus
INSERT INTO `mst_menu` (`display_name`, `route_name`, `uri`, `parent_menu_id`, `icon`, `display_order`, `is_active`) VALUES

-- TOP LEVEL MENUS
('Dashboard', 'dashboard', '/dashboard', NULL, 'dashboard', 1, 1),
('Role Dashboard', 'role.dashboard', '/role-dashboard', NULL, 'layers', 2, 1),
('Issue Tracker', 'role.issue.dashboard', '/role-issue-dashboard', NULL, 'ticket', 3, 1),
('Issues', 'issues.index', '/issues', NULL, 'list', 4, 1),
('Raise Issue', 'raise.issue', '/issues/create', NULL, 'plus-circle', 5, 1),
('Reports', 'reports', '/reports', NULL, 'chart-bar', 6, 1),

-- ADMINISTRATION SECTION (Parent)
('Administration', 'administration', '/administration', NULL, 'settings', 7, 1),

-- MASTER PAGES (Under Administration)
('State Master', 'state.master', '/state-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'map-pin', 1, 1),
('Project Master', 'project.master', '/project-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'briefcase', 2, 1),
('Application Master', 'application.master', '/application-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'layers-outline', 3, 1),
('Module Master', 'module.master', '/module-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'box', 4, 1),
('Service Master', 'service.master', '/service-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'cog', 5, 1),
('Vendor Master', 'vendor.master', '/vendor-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'truck', 6, 1),
('Support Group Master', 'support-group.master', '/support-group-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'users', 7, 1),
('User Master', 'user.master', '/user-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'user-circle', 8, 1),
('Role Master', 'role.master', '/role-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'shield', 9, 1),
('Privilege Master', 'privilege.master', '/privilege-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'lock', 10, 1),
('Menu Master', 'menu.master', '/menu-master', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'menu', 11, 1),

-- MAPPING MENUS (Under Administration)
('User Role Mapping', 'user.role.mapping', '/user-role-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 12, 1),
('User Project Mapping', 'user.project.mapping', '/user-project-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 13, 1),
('User Support Group Mapping', 'user.support.group.mapping', '/user-support-group-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 14, 1),
('Vendor State Mapping', 'vendor.state.mapping', '/vendor-state-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 15, 1),
('Project Application Module Mapping', 'project.application.module.mapping', '/project-application-module-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 16, 1),
('Project State Mapping', 'project.state.mapping', '/project-state-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 17, 1),
('Role Menu Mapping', 'role.menu.mapping', '/role-menu-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 18, 1),
('Role Privilege Mapping', 'role.privilege.mapping', '/role-privilege-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 19, 1),
('Role Issue Status Mapping', 'role.issue.status.mapping', '/role-issue-status-mapping', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'link', 20, 1),

-- CONFIGURATION MENUS (Under Administration)
('Issue Routing Rules', 'issue.routing.rules', '/issue-routing-rules', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'flow-chart', 21, 1),
('Working Calendar', 'working.calendar', '/working-calendar', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'calendar', 22, 1),
('Working Schedule', 'working.schedule', '/working-schedule', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'clock', 23, 1),
('SLA Configuration', 'sla.configuration', '/sla-configuration', (SELECT menu_id FROM mst_menu WHERE route_name = 'administration' LIMIT 1), 'timer', 24, 1),

-- ADMIN ROLE ACCESS (for authentication purposes)
('Central Admin', 'central.admin', '/central-admin', NULL, 'shield-admin', 25, 1),
('HO Admin', 'ho.admin', '/ho-admin', NULL, 'shield-check', 26, 1),
('State Admin', 'state.admin', '/state-admin', NULL, 'map', 27, 1),
('Vendor Admin', 'vendor.admin', '/vendor-admin', NULL, 'truck', 28, 1)

ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`);

-- ============================================================================
-- 2. INSERT PRIVILEGES (mst_privilege)
-- ============================================================================
-- Core action-based privileges used across all modules

INSERT INTO `mst_privilege` (`privilege_name`, `privilege_code`, `display_order`, `is_active`)
VALUES
    ('View',              'VIEW',             1,  1),
    ('Add',               'ADD',              2,  1),
    ('Edit',              'EDIT',             3,  1),
    ('Delete',            'DELETE',           4,  1),
    ('Approve',           'APPROVE',          5,  1),
    ('Reject',            'REJECT',           6,  1),
    ('Assign',            'ASSIGN',           7,  1),
    ('Escalate',          'ESCALATE',         8,  1),
    ('Resolve',           'RESOLVE',          9,  1),
    ('Close',             'CLOSE',            10, 1),
    ('Reopen',            'REOPEN',           11, 1),
    ('Export',            'EXPORT',           12, 1),
    ('Import',            'IMPORT',           13, 1),
    ('Download',          'DOWNLOAD',         14, 1),
    ('Upload',            'UPLOAD',           15, 1),
    ('Submit',            'SUBMIT',           16, 1),
    ('Review',            'REVIEW',           17, 1),
    ('Clarification',     'CLARIFICATION',    18, 1)
ON DUPLICATE KEY UPDATE `privilege_name` = VALUES(`privilege_name`);

-- ============================================================================
-- 3. UPDATE CENTRAL ADMIN ROLE (if exists, or it should already exist)
-- ============================================================================
-- Note: Ensure the HO_ADMIN role exists from master_tables_structure.sql

-- ============================================================================
-- 4. MAP ROLE PRIVILEGES FOR CENTRAL ADMIN (HO_ADMIN)
-- ============================================================================

-- Grant all menus to HO_ADMIN role for navigation access
INSERT INTO `map_role_privilege` (`role_id`, `menu_id`, `privilege_id`, `is_allowed`) 
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1),
    m.menu_id,
    NULL,
    1
FROM mst_menu m
WHERE m.is_active = 1
  AND NOT EXISTS (
    SELECT 1 FROM map_role_privilege mrp 
    WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
    AND mrp.menu_id = m.menu_id
  )
ON DUPLICATE KEY UPDATE `is_allowed` = 1;

-- Grant all action-based privileges to HO_ADMIN role (full permissions for all actions)
INSERT INTO `map_role_privilege` (`role_id`, `privilege_id`, `is_allowed`)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1),
    p.privilege_id,
    1
FROM mst_privilege p
WHERE p.is_active = 1
  AND NOT EXISTS (
    SELECT 1 FROM map_role_privilege mrp 
    WHERE mrp.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
    AND mrp.privilege_id = p.privilege_id
  )
ON DUPLICATE KEY UPDATE `is_allowed` = 1;

-- Map HO_ADMIN to all issue statuses (they can use all statuses)
INSERT INTO `map_role_issue_status` (`role_id`, `status_id`, `is_allowed`, `display_order`)
SELECT 
    (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1),
    s.status_id,
    1,
    s.display_order
FROM mst_issue_status s
WHERE s.is_active = 1
  AND NOT EXISTS (
    SELECT 1 FROM map_role_issue_status mris 
    WHERE mris.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
    AND mris.status_id = s.status_id
  )
ON DUPLICATE KEY UPDATE `is_allowed` = 1;

-- ============================================================================
-- 5. INSERT CENTRAL ADMIN USER (CENTRAL LOGIN)
-- ============================================================================

-- IMPORTANT: Adjust password hash and other details as per your security requirements
-- This example uses Laravel's bcrypt hash. For testing, you can use:
-- Password: admin123 (bcrypted hash below)

INSERT INTO `mst_user` (
    `organisation_id`,
    `head_office_id`,
    `state_id`,
    `vendor_id`,
    `employee_code`,
    `user_name`,
    `login_id`,
    `official_email`,
    `mobile_number`,
    `password_hash`,
    `user_status`,
    `is_active`,
    `created_at`,
    `updated_at`
) VALUES (
    1,                                                          -- organisation_id (adjust as per your org)
    1,                                                          -- head_office_id (adjust as needed)
    NULL,                                                       -- state_id (NULL for central admin)
    NULL,                                                       -- vendor_id (NULL for admin)
    'ADMIN001',                                                 -- employee_code
    'Central Administrator',                                    -- user_name
    'admin',                                                    -- login_id (username for login)
    'admin@example.com',                                       -- official_email
    '+91-9876543210',                                          -- mobile_number
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password_hash (password: password)
    'Active',                                                   -- user_status
    1,                                                          -- is_active
    NOW(),                                                      -- created_at
    NOW()                                                       -- updated_at
) ON DUPLICATE KEY UPDATE 
    `user_name` = VALUES(`user_name`),
    `official_email` = VALUES(`official_email`),
    `is_active` = 1;

-- ============================================================================
-- 6. ASSIGN CENTRAL ADMIN ROLE TO CENTRAL ADMIN USER
-- ============================================================================

INSERT INTO `map_user_role` (`user_id`, `role_id`, `is_active`)
SELECT 
    (SELECT user_id FROM mst_user WHERE login_id = 'admin' LIMIT 1),
    (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM map_user_role mur
    WHERE mur.user_id = (SELECT user_id FROM mst_user WHERE login_id = 'admin' LIMIT 1)
    AND mur.role_id = (SELECT role_id FROM mst_role WHERE role_code = 'HO_ADMIN' LIMIT 1)
)
ON DUPLICATE KEY UPDATE `is_active` = 1;

-- ============================================================================
-- 7. ALTERNATIVE CENTRAL ADMIN USER (WITHOUT ORGANISATION DEPENDENCY)
-- ============================================================================
-- Uncomment and use this if you want a user without organisation links

-- INSERT INTO `mst_user` (
--     `employee_code`,
--     `user_name`,
--     `login_id`,
--     `official_email`,
--     `mobile_number`,
--     `password_hash`,
--     `user_status`,
--     `is_active`,
--     `created_at`,
--     `updated_at`
-- ) VALUES (
--     'ADMIN001',
--     'Central Administrator',
--     'centraladmin',
--     'centraladmin@example.com',
--     '+91-9876543210',
--     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
--     'Active',
--     1,
--     NOW(),
--     NOW()
-- ) ON DUPLICATE KEY UPDATE `is_active` = 1;

-- ============================================================================
-- 8. SAMPLE VENDOR STATE MAPPING FOR TESTING
-- ============================================================================
-- Link a vendor to a state (sample data)
-- Uncomment to use after creating sample vendor and state

-- INSERT INTO `map_vendor_state` (`vendor_id`, `state_id`, `is_active`) VALUES
-- (1, 1, 1)  -- Link Vendor 1 to State 1
-- ON DUPLICATE KEY UPDATE `is_active` = 1;

-- ============================================================================
-- 9. VERIFICATION QUERIES (Run after inserts to verify)
-- ============================================================================

-- Verify menus created
-- SELECT COUNT(*) as total_menus FROM mst_menu WHERE is_active = 1;

-- Verify privileges created
-- SELECT COUNT(*) as total_privileges FROM mst_privilege WHERE is_active = 1;

-- Verify HO_ADMIN role permissions
-- SELECT 
--     r.role_name,
--     m.display_name as menu_name,
--     mrp.is_allowed
-- FROM map_role_privilege mrp
-- LEFT JOIN mst_role r ON mrp.role_id = r.role_id
-- LEFT JOIN mst_menu m ON mrp.menu_id = m.menu_id
-- WHERE r.role_code = 'HO_ADMIN'
-- LIMIT 10;

-- Verify central admin user
-- SELECT 
--     u.user_id,
--     u.login_id,
--     u.user_name,
--     r.role_name,
--     u.user_status,
--     u.is_active
-- FROM mst_user u
-- LEFT JOIN map_user_role mur ON u.user_id = mur.user_id
-- LEFT JOIN mst_role r ON mur.role_id = r.role_id
-- WHERE u.login_id = 'admin';

-- ============================================================================
-- 10. IMPORTANT NOTES
-- ============================================================================

-- 📝 BEFORE RUNNING THIS SCRIPT:
-- 1. Ensure master_tables_structure.sql has been executed first
-- 2. Ensure sample roles and statuses have been inserted
-- 3. Ensure mst_privilege table already exists in your database
-- 4. Ensure mst_privilege has the following columns:
--    - privilege_id (BIGINT UNSIGNED, AUTO_INCREMENT, PRIMARY KEY)
--    - privilege_name (VARCHAR)
--    - privilege_code (VARCHAR, UNIQUE)
--    - display_order (INT)
--    - is_active (TINYINT)
--    - created_by, created_at, updated_by, updated_at (optional audit fields)

-- 📝 PRIVILEGES STRUCTURE (Generic Action-Based):
-- The privileges used are action-centric and can be applied to any module:
-- ✓ VIEW        - View/Read access to resources
-- ✓ ADD         - Create/Insert new records
-- ✓ EDIT        - Modify existing records
-- ✓ DELETE      - Remove records
-- ✓ APPROVE     - Approval/Authorization action
-- ✓ REJECT      - Rejection action
-- ✓ ASSIGN      - Assignment action
-- ✓ ESCALATE    - Escalation action
-- ✓ RESOLVE     - Resolution action
-- ✓ CLOSE       - Closing action
-- ✓ REOPEN      - Re-opening action
-- ✓ EXPORT      - Export to file
-- ✓ IMPORT      - Import from file
-- ✓ DOWNLOAD    - Download files
-- ✓ UPLOAD      - Upload files
-- ✓ SUBMIT      - Submission action
-- ✓ REVIEW      - Review action
-- ✓ CLARIFICATION - Request clarification

-- 📝 DEFAULT CENTRAL ADMIN CREDENTIALS:
-- Username: admin
-- Password: password
-- Email: admin@example.com
-- Role: HO_ADMIN (Central Administrator)
-- NOTE: Change password immediately after first login!

-- 📝 MENU STRUCTURE:
-- - Dashboard
-- - Role Dashboard
-- - Issue Tracker
-- - Issues Management (Create, View, etc.)
-- - Reports
-- - Administration (Parent)
--   ├─ Master Pages (State, Project, Application, Module, Service, Vendor, etc.)
--   ├─ User Management (Users, Roles, Privileges)
--   ├─ Mapping Configuration (User-Role, Vendor-State, etc.)
--   └─ System Configuration (SLA, Calendar, Routing Rules)

-- 📝 ROLE-BASED ACCESS:
-- Central Admin (HO_ADMIN):
-- ✓ Full access to all 28+ menus
-- ✓ Full access to all 18 action-based privileges
-- ✓ Can use all issue statuses
-- ✓ Can perform all operations (VIEW, ADD, EDIT, DELETE, APPROVE, REJECT, etc.)

-- 📝 TO ADD MORE USERS:
-- Use User Master page in the application or insert via:
-- INSERT INTO mst_user (...) VALUES (...)
-- Then map their roles via map_user_role
-- Grant specific privileges to roles via map_role_privilege

-- 📝 TO CREATE NEW ROLES WITH LIMITED PERMISSIONS:
-- 1. Insert role into mst_role
-- 2. Grant specific menus via map_role_privilege (menu_id, privilege_id = NULL)
-- 3. Grant specific privileges via map_role_privilege (menu_id = NULL, privilege_id)
-- Example:
--   INSERT INTO map_role_privilege (role_id, menu_id, is_allowed)
--   SELECT 1, m.menu_id, 1 FROM mst_menu WHERE route_name = 'issues.index';
--   
--   INSERT INTO map_role_privilege (role_id, privilege_id, is_allowed)
--   SELECT 1, p.privilege_id, 1 FROM mst_privilege WHERE privilege_code IN ('VIEW', 'EDIT');

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
