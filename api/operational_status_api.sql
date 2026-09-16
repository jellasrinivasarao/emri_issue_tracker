-- Dedicated end-user credentials and bearer-token storage for the operational
-- status integration API. Run this script manually.

CREATE TABLE IF NOT EXISTS `mst_end_user_api_user` (
    `end_user_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `user_name` VARCHAR(150) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `last_login_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`end_user_id`),
    UNIQUE KEY `uq_end_user_api_username` (`username`),
    KEY `idx_end_user_api_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `api_access_tokens` (
    `token_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `end_user_id` BIGINT UNSIGNED NOT NULL,
    `token_name` VARCHAR(100) NOT NULL DEFAULT 'operational-dashboard-status',
    `token_hash` CHAR(64) NOT NULL,
    `expires_at` DATETIME NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_used_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`token_id`),
    UNIQUE KEY `uq_api_access_token_hash` (`token_hash`),
    KEY `idx_api_access_tokens_end_user` (`end_user_id`),
    KEY `idx_api_access_tokens_active_expiry` (`is_active`, `expires_at`),
    CONSTRAINT `fk_api_access_tokens_user`
        FOREIGN KEY (`end_user_id`) REFERENCES `mst_end_user_api_user` (`end_user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If api_access_tokens already exists with user_id from the previous design,
-- migrate it before using this script:
--   ALTER TABLE api_access_tokens DROP FOREIGN KEY fk_api_access_tokens_user;
--   ALTER TABLE api_access_tokens DROP INDEX idx_api_access_tokens_user;
--   ALTER TABLE api_access_tokens DROP COLUMN user_id;
--   ALTER TABLE api_access_tokens ADD COLUMN end_user_id BIGINT UNSIGNED NOT NULL AFTER token_id;
--   ALTER TABLE api_access_tokens ADD KEY idx_api_access_tokens_end_user (end_user_id);
--   ALTER TABLE api_access_tokens ADD CONSTRAINT fk_api_access_tokens_end_user
--       FOREIGN KEY (end_user_id) REFERENCES mst_end_user_api_user (end_user_id) ON DELETE CASCADE;

-- If mst_end_user_api_user already exists with password_hash, rename it:
--   ALTER TABLE mst_end_user_api_user CHANGE password_hash password VARCHAR(255) NOT NULL;

-- The API password is stored in the password column and compared directly.
-- Send the end-user credentials dynamically to:
--   POST /api/integration/token
--   {"username":"CURRENT_LOGIN_ID","password":"CURRENT_PASSWORD"}
-- The API looks up mst_end_user_api_user.username and verifies the supplied
-- password against the password column.

-- The application inserts api_access_tokens automatically only after valid
-- credentials are supplied. The token is non-expiring because expires_at is NULL.