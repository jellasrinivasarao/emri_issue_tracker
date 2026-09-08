-- User table alteration required for optional multi-state HO IT mapping.
-- State IDs are stored as comma-separated values, for example: 2,5,8.
ALTER TABLE `mst_user`
	MODIFY COLUMN `state_id` VARCHAR(255) NULL;

-- Group Master IDs are stored as comma-separated values, for example: 1,4,7.
ALTER TABLE `mst_user`
	ADD COLUMN IF NOT EXISTS `support_group_id` VARCHAR(255) NULL;

-- Menu inserts for Group Master and Group Project/Application Mapping.
-- Central Admin role ID: 1.

INSERT INTO `mst_menu`
	(`display_name`, `route_name`, `uri`, `parent_menu_id`, `icon`, `display_order`, `is_active`)
SELECT
	'Group Master',
	'group.master',
	'/group-master',
	`menu_id`,
	'users',
	1,
	1
FROM `mst_menu`
WHERE `route_name` = 'user.security'
LIMIT 1
ON DUPLICATE KEY UPDATE
	`display_name` = VALUES(`display_name`),
	`uri` = VALUES(`uri`),
	`parent_menu_id` = VALUES(`parent_menu_id`),
	`display_order` = VALUES(`display_order`),
	`is_active` = VALUES(`is_active`);

INSERT INTO `mst_menu`
	(`display_name`, `route_name`, `uri`, `parent_menu_id`, `icon`, `display_order`, `is_active`)
SELECT
	'Group Project & Application Mapping',
	'group.project.application.mapping',
	'/group-project-application-mapping',
	`menu_id`,
	'link',
	2,
	1
FROM `mst_menu`
WHERE `route_name` = 'user.security'
LIMIT 1
ON DUPLICATE KEY UPDATE
	`display_name` = VALUES(`display_name`),
	`uri` = VALUES(`uri`),
	`parent_menu_id` = VALUES(`parent_menu_id`),
	`display_order` = VALUES(`display_order`),
	`is_active` = VALUES(`is_active`);

-- Complete privileges for Central Admin role ID 1 on both pages.
INSERT INTO `map_role_privilege`
	(`role_id`, `menu_id`, `privilege_id`, `is_allowed`)
SELECT
	1,
	m.`menu_id`,
	p.`privilege_id`,
	1
FROM `mst_menu` AS m
CROSS JOIN `mst_privilege` AS p
WHERE m.`route_name` IN ('group.master', 'group.project.application.mapping')
  AND p.`is_active` = 1
  AND NOT EXISTS (
	  SELECT 1
	  FROM `map_role_privilege` AS existing
	  WHERE existing.`role_id` = 1
		AND existing.`menu_id` = m.`menu_id`
		AND existing.`privilege_id` = p.`privilege_id`
  );
