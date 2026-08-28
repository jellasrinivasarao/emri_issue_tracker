-- Complete ALTER script only.
-- No INSERT, UPDATE, DELETE, CREATE TABLE, or seeder statements.
-- Existing roles remain GLOBAL.
-- State IT uses state_id.
-- Vendor IT uses vendor_id.

ALTER TABLE map_role_privilege
	ADD COLUMN state_id INT NULL AFTER role_id,
	ADD COLUMN vendor_id INT NULL AFTER state_id,
	ADD COLUMN mapping_scope_key VARCHAR(50)
		GENERATED ALWAYS AS (
			CASE
				WHEN state_id IS NOT NULL
					THEN CONCAT('STATE:', state_id)
				WHEN vendor_id IS NOT NULL
					THEN CONCAT('VENDOR:', vendor_id)
				ELSE 'GLOBAL'
			END
		) STORED,
	ADD KEY idx_role_state (role_id, state_id),
	ADD KEY idx_role_vendor (role_id, vendor_id);

ALTER TABLE map_role_privilege
	DROP INDEX uk_role_menu_action,
	ADD UNIQUE KEY uk_role_scope_menu_action
	(
		role_id,
		mapping_scope_key,
		menu_id,
		privilege_id
	);

ALTER TABLE map_role_privilege
	ADD CONSTRAINT chk_one_mapping_scope
	CHECK (
		NOT (
			state_id IS NOT NULL
			AND vendor_id IS NOT NULL
		)
	);
