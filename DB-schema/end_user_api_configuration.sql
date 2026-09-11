-- End User employee API configuration
-- is_api_required = 0: validate only against mst_user
-- is_api_required = 1: call the external employee API, then validate/store in mst_user

CREATE TABLE IF NOT EXISTS `mst_end_user_api_configuration` (
    `api_config_id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `config_code` varchar(50) NOT NULL,
    `api_name` varchar(150) NOT NULL,
    `base_url` varchar(500) DEFAULT NULL,
    `status_endpoint` varchar(255) DEFAULT NULL,
    `details_endpoint` varchar(255) DEFAULT NULL,
    `request_method` varchar(10) NOT NULL DEFAULT 'POST',
    `is_api_required` tinyint(1) NOT NULL DEFAULT 0,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `timeout_seconds` int unsigned NOT NULL DEFAULT 15,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`api_config_id`),
    UNIQUE KEY `uq_end_user_api_config_code` (`config_code`),
    KEY `idx_end_user_api_required_active` (`is_api_required`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample: API disabled, so only mst_user is checked.
INSERT INTO `mst_end_user_api_configuration`
(`config_code`, `api_name`, `base_url`, `status_endpoint`, `details_endpoint`, `request_method`, `is_api_required`, `is_active`, `timeout_seconds`)
VALUES
('EMPLOYEE_STATUS_DETAILS', 'Employee Status and Details API', 'https://employee-api.example.com', '/api/employee/status', '/api/employee/details', 'POST', 0, 1, 15)
ON DUPLICATE KEY UPDATE
    `api_name` = VALUES(`api_name`),
    `base_url` = VALUES(`base_url`),
    `status_endpoint` = VALUES(`status_endpoint`),
    `details_endpoint` = VALUES(`details_endpoint`),
    `request_method` = VALUES(`request_method`),
    `is_api_required` = VALUES(`is_api_required`),
    `is_active` = VALUES(`is_active`),
    `timeout_seconds` = VALUES(`timeout_seconds`),
    `updated_at` = NOW();

-- Enable external API calls when the real endpoint is available.
-- UPDATE `mst_end_user_api_configuration`
-- SET `base_url` = 'https://real-employee-api.example.com',
--     `is_api_required` = 1,
--     `is_active` = 1
-- WHERE `config_code` = 'EMPLOYEE_STATUS_DETAILS';
