-- Grant the Internal Issue page to End User role ID 9.
-- Uses the existing VIEW privilege; no new privilege is created.

INSERT INTO `mst_menu`
(`display_name`, `route_name`, `uri`, `parent_menu_id`, `icon`, `is_active`, `display_order`, `created_at`, `updated_at`)
VALUES
('Internal Issue', 'internal.issue', '/internal-issue', NULL, 'life-buoy', 1, 6, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `display_name` = VALUES(`display_name`),
    `uri` = VALUES(`uri`),
    `icon` = VALUES(`icon`),
    `is_active` = VALUES(`is_active`),
    `display_order` = VALUES(`display_order`),
    `updated_at` = NOW();

INSERT INTO `map_role_privilege`
(`role_id`, `menu_id`, `privilege_id`, `is_allowed`, `created_at`, `updated_at`)
SELECT
    9,
    menu.`menu_id`,
    privilege.`privilege_id`,
    1,
    NOW(),
    NOW()
FROM `mst_menu` menu
CROSS JOIN `mst_privilege` privilege
WHERE menu.`route_name` = 'internal.issue'
  AND privilege.`privilege_code` = 'VIEW'
  AND NOT EXISTS (
      SELECT 1
      FROM `map_role_privilege` existing
      WHERE existing.`role_id` = 9
        AND existing.`menu_id` = menu.`menu_id`
        AND existing.`privilege_id` = privilege.`privilege_id`
        AND existing.`state_id` IS NULL
        AND existing.`vendor_id` IS NULL
  );
