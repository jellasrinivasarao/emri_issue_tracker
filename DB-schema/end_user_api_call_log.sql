-- End User Employee API call log
-- Stores every employee status/details API request and response.

CREATE TABLE IF NOT EXISTS `txn_end_user_api_call_log` (
    `api_log_id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `gid` varchar(100) NOT NULL,
    `url` varchar(1000) NOT NULL,
    `request` longtext DEFAULT NULL,
    `response` longtext DEFAULT NULL,
    `response_time` decimal(12,3) DEFAULT NULL COMMENT 'Response time in milliseconds',
    `http_status_code` smallint unsigned DEFAULT NULL,
    `api_status` varchar(30) DEFAULT NULL,
    `error_message` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`api_log_id`),
    KEY `idx_end_user_api_log_gid` (`gid`),
    KEY `idx_end_user_api_log_created_at` (`created_at`),
    KEY `idx_end_user_api_log_status` (`api_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
