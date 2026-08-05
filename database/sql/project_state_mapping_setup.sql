-- Project State Mapping setup script
-- Creates the project-state mapping table, menu entry, and central admin permissions.

CREATE TABLE IF NOT EXISTS `map_project_state` (
  `mapping_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `state_project_unique` (`state_id`,`project_id`),
  KEY `map_project_state_state_id_index` (`state_id`),
  KEY `map_project_state_project_id_index` (`project_id`),
  CONSTRAINT `map_project_state_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE,
  CONSTRAINT `map_project_state_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `mst_menu` (`display_name`, `route_name`, `uri`, `display_order`, `created_at`, `updated_at`)
VALUES ('Project State Mapping', 'project.state.mapping', '/project-state-mapping', 11, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `display_name` = VALUES(`display_name`),
  `uri` = VALUES(`uri`),
  `display_order` = VALUES(`display_order`),
  `updated_at` = NOW();

INSERT INTO `mst_privilege` (`privilege_code`, `privilege_name`, `module_name`, `description`, `created_at`)
VALUES
  ('PRIV019', 'Manage Project State Mapping', 'Organisation Setup', 'Can manage project-state mappings.', NOW())
ON DUPLICATE KEY UPDATE
  `privilege_name` = VALUES(`privilege_name`),
  `module_name` = VALUES(`module_name`),
  `description` = VALUES(`description`),
  `updated_at` = NOW();

INSERT INTO `map_role_menu` (`role_id`, `menu_id`, `is_allowed`, `created_at`, `updated_at`)
SELECT r.role_id, m.menu_id, 1, NOW(), NOW()
FROM `mst_role` r
JOIN `mst_menu` m ON m.route_name = 'project.state.mapping'
WHERE r.role_name = 'Central Admin'
ON DUPLICATE KEY UPDATE
  `is_allowed` = VALUES(`is_allowed`),
  `updated_at` = VALUES(`updated_at`);

INSERT INTO `map_role_privilege` (`role_id`, `privilege_id`, `is_allowed`, `created_at`, `updated_at`)
SELECT r.role_id, p.privilege_id, 1, NOW(), NOW()
FROM `mst_role` r
JOIN `mst_privilege` p ON p.privilege_code = 'PRIV019'
WHERE r.role_name = 'Central Admin'
ON DUPLICATE KEY UPDATE
  `is_allowed` = VALUES(`is_allowed`),
  `updated_at` = VALUES(`updated_at`);
