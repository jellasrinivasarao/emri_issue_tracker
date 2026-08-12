CREATE TABLE `mst_sla_configuration` (
    `sla_configuration_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,

    `sla_code` VARCHAR(50) NOT NULL,
    `sla_name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,

    `response_sla_hours` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `resolution_sla_hours` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `escalation_sla_hours` DECIMAL(8,2) NULL,

    `support_level` TINYINT UNSIGNED NULL
        COMMENT '1 = HO IT Level-1, 2 = Vendor Level-2',

    `calendar_id` BIGINT(20) UNSIGNED NULL,

    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    `created_by` BIGINT(20) UNSIGNED NULL,
    `updated_by` BIGINT(20) UNSIGNED NULL,

    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (`sla_configuration_id`),

    UNIQUE KEY `uk_sla_configuration_code` (`sla_code`),

    KEY `idx_sla_calendar_id` (`calendar_id`),
    KEY `idx_sla_support_level` (`support_level`),
    KEY `idx_sla_active` (`is_active`),

    -- CONSTRAINT `fk_sla_calendar`
    --     FOREIGN KEY (`calendar_id`)
    --     REFERENCES `mst_working_calendar` (`calendar_id`)
    --     ON UPDATE CASCADE
    --     ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;