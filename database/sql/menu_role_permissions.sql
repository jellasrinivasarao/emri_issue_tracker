-- Required tables for role-based page/menu access

CREATE TABLE IF NOT EXISTS `mst_menu` (
  `menu_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(150) NOT NULL,
  `route_name` varchar(150) NOT NULL UNIQUE,
  `uri` varchar(255) DEFAULT NULL,
  `parent_menu_id` bigint(20) unsigned DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`),
  KEY `mst_menu_parent_menu_id_index` (`parent_menu_id`),
  CONSTRAINT `mst_menu_parent_menu_id_foreign` FOREIGN KEY (`parent_menu_id`) REFERENCES `mst_menu` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_role_menu` (
  `role_menu_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `menu_id` bigint(20) unsigned NOT NULL,
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_menu_id`),
  UNIQUE KEY `map_role_menu_role_id_menu_id_unique` (`role_id`,`menu_id`),
  KEY `map_role_menu_role_id_index` (`role_id`),
  KEY `map_role_menu_menu_id_index` (`menu_id`),
  CONSTRAINT `map_role_menu_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `map_role_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `mst_menu` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_privilege` (
  `privilege_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `privilege_code` varchar(50) NOT NULL UNIQUE,
  `privilege_name` varchar(150) NOT NULL,
  `module_name` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_role_privilege` (
  `role_privilege_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `privilege_id` bigint(20) unsigned NOT NULL,
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_privilege_id`),
  UNIQUE KEY `map_role_privilege_role_id_privilege_id_unique` (`role_id`,`privilege_id`),
  KEY `map_role_privilege_role_id_index` (`role_id`),
  KEY `map_role_privilege_privilege_id_index` (`privilege_id`),
  CONSTRAINT `map_role_privilege_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `map_role_privilege_privilege_id_foreign` FOREIGN KEY (`privilege_id`) REFERENCES `mst_privilege` (`privilege_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Remove existing sample menu and privilege data, then insert authoritative menu definitions
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `map_role_privilege`;
TRUNCATE TABLE `mst_privilege`;
TRUNCATE TABLE `map_role_menu`;
TRUNCATE TABLE `mst_menu`;
SET FOREIGN_KEY_CHECKS = 1;

-- Seed menu items
INSERT INTO `mst_menu` (`display_name`, `route_name`, `uri`, `display_order`, `created_at`, `updated_at`)
VALUES
  ('Dashboard', 'dashboard', '/dashboard', 1, NOW(), NOW()),
  ('Issues', 'issues', '/issues', 2, NOW(), NOW()),
  ('Raise Issue', 'raise.issue', '/raise-issue', 3, NOW(), NOW()),
  ('Reports', 'reports', '/reports', 4, NOW(), NOW()),
  ('Administration', 'administration', '/administration', 5, NOW(), NOW()),
  ('Central Admin', 'central.admin', '/central-admin', 6, NOW(), NOW()),
  ('State Admin', 'state.admin', '/state-admin', 7, NOW(), NOW()),
  ('HO Admin', 'ho.admin', '/ho-admin', 8, NOW(), NOW()),
  ('Vendor Admin', 'vendor.admin', '/vendor-admin', 9, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `display_name` = VALUES(`display_name`),
  `uri` = VALUES(`uri`),
  `display_order` = VALUES(`display_order`),
  `updated_at` = NOW();

-- Seed role-to-menu mappings for existing roles
INSERT INTO `map_role_menu` (`role_id`, `menu_id`, `is_allowed`, `created_at`, `updated_at`)
SELECT r.role_id, m.menu_id, 1, NOW(), NOW()
FROM `mst_role` r
JOIN `mst_menu` m ON (
  (r.role_name = 'Central Admin' AND m.route_name IN ('dashboard','issues','raise.issue','reports','administration','central.admin','state.admin','ho.admin','vendor.admin'))
  OR (r.role_name = 'State Admin' AND m.route_name IN ('dashboard','raise.issue','state.admin'))
  OR (r.role_name = 'Vendor User' AND m.route_name IN ('dashboard','issues','vendor.admin'))
)
ON DUPLICATE KEY UPDATE
  `is_allowed` = VALUES(`is_allowed`),
  `updated_at` = VALUES(`updated_at`);

-- Optional: create role mapping for HO Admin if role is added later
INSERT IGNORE INTO `mst_role` (`role_code`, `role_name`, `role_category`, `description`, `is_system_role`, `created_at`)
VALUES ('RL004', 'HO Admin', 'HO', 'HO Admin role', 0, NOW());

INSERT IGNORE INTO `map_role_menu` (`role_id`, `menu_id`, `is_allowed`, `created_at`, `updated_at`)
SELECT r.role_id, m.menu_id, 1, NOW(), NOW()
FROM `mst_role` r
JOIN `mst_menu` m ON r.role_name = 'HO Admin' AND m.route_name IN ('dashboard','issues','ho.admin')
WHERE EXISTS (SELECT 1 FROM `mst_role` WHERE `role_name` = 'HO Admin');

-- Privilege master data for action-level access
INSERT INTO `mst_privilege` (`privilege_code`, `privilege_name`, `module_name`, `description`, `created_at`)
VALUES
  ('PRIV001', 'View Issues', 'Issue Module', 'Can view issue list and details', NOW()),
  ('PRIV002', 'Create Issue', 'Issue Module', 'Can create a new issue', NOW()),
  ('PRIV003', 'Edit Issue', 'Issue Module', 'Can update issue details', NOW()),
  ('PRIV004', 'Assign Issue', 'Issue Module', 'Can assign issue to support teams', NOW()),
  ('PRIV005', 'Escalate Issue', 'Issue Module', 'Can escalate issue to vendor or higher support', NOW()),
  ('PRIV006', 'View Reports', 'Reports Module', 'Can view reporting dashboards', NOW()),
  ('PRIV007', 'Manage Administration', 'Administration Module', 'Can access system administration pages', NOW()),
  ('PRIV008', 'View State', 'State Module', 'Can view state-level issue details', NOW()),
  ('PRIV009', 'Manage Vendor', 'Vendor Module', 'Can view vendor-related pages and actions', NOW()),
  ('PRIV010', 'Create Organisation Masters', 'Administration Module', 'Can create states, vendors, services, projects, applications, and modules', NOW()),
  ('PRIV011', 'Create Admin Users', 'Administration Module', 'Can create State Admin, HO Admin, and Vendor Admin users', NOW()),
  ('PRIV012', 'Configure Roles & Privileges', 'Administration Module', 'Can configure roles, privileges, and organisation mappings', NOW()),
  ('PRIV013', 'Configure HO Calendar', 'Administration Module', 'Can configure HO working calendar, holidays, and off-hours routing', NOW()),
  ('PRIV014', 'Configure Vendor Settings', 'Administration Module', 'Can configure vendor level 2 mapping, categories, priority, severity, and statuses', NOW()),
  ('PRIV015', 'Configure SLA & Notifications', 'Administration Module', 'Can configure SLA rules and notification settings', NOW()),
  ('PRIV016', 'View Audit Logs', 'Administration Module', 'Can view audit and activity logs', NOW()),
  ('PRIV017', 'Confirm State Issue Closure', 'Issue Module', 'Can confirm state issue closure when required', NOW()),
  ('PRIV018', 'Modify/Delete Issue or Audit History', 'Issue Module', 'Can modify or delete issue or audit history', NOW())
ON DUPLICATE KEY UPDATE
  `privilege_name` = VALUES(`privilege_name`),
  `module_name` = VALUES(`module_name`),
  `description` = VALUES(`description`),
  `updated_at` = NOW();

-- Map roles to privileges
INSERT INTO `map_role_privilege` (`role_id`, `privilege_id`, `is_allowed`, `created_at`, `updated_at`)
SELECT r.role_id, p.privilege_id, 1, NOW(), NOW()
FROM `mst_role` r
JOIN `mst_privilege` p ON (
  (r.role_name = 'Central Admin' AND p.privilege_code IN ('PRIV001','PRIV002','PRIV003','PRIV004','PRIV005','PRIV006','PRIV007','PRIV008','PRIV009','PRIV010','PRIV011','PRIV012','PRIV013','PRIV014','PRIV015','PRIV016'))
  OR (r.role_name = 'State Admin' AND p.privilege_code IN ('PRIV001','PRIV002','PRIV006','PRIV008'))
  OR (r.role_name = 'Vendor User' AND p.privilege_code IN ('PRIV001','PRIV003','PRIV005','PRIV009'))
  OR (r.role_name = 'HO Admin' AND p.privilege_code IN ('PRIV001','PRIV003','PRIV004','PRIV005','PRIV006'))
)
ON DUPLICATE KEY UPDATE
  `is_allowed` = VALUES(`is_allowed`),
  `updated_at` = VALUES(`updated_at`);

-- Do not drop issue workflow tables unless you are removing issue tracking entirely.
