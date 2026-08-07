CREATE TABLE `txn_issue_escalation` (

    `escalation_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `issue_id` BIGINT UNSIGNED NOT NULL,

    /*
    |--------------------------------------------------------------------------
    | Escalation Level
    |--------------------------------------------------------------------------
    */

    `escalation_level` INT UNSIGNED NOT NULL DEFAULT 1,

    `escalation_type` VARCHAR(50) NOT NULL,

    /*
    |--------------------------------------------------------------------------
    | Source / Destination
    |--------------------------------------------------------------------------
    */

    `escalated_from_group_id` BIGINT UNSIGNED NULL,

    `escalated_to_group_id` BIGINT UNSIGNED NULL,

    /*
    |--------------------------------------------------------------------------
    | Escalation User
    |--------------------------------------------------------------------------
    */

    `escalated_by` BIGINT UNSIGNED NULL,

    /*
    |--------------------------------------------------------------------------
    | Escalation Timing
    |--------------------------------------------------------------------------
    */

    `escalated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `acknowledged_at` DATETIME NULL,

    `acknowledged_by` BIGINT UNSIGNED NULL,

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    `escalation_status` VARCHAR(30) NOT NULL DEFAULT 'PENDING',

    /*
    |--------------------------------------------------------------------------
    | Reason / Description
    |--------------------------------------------------------------------------
    */

    `reason` VARCHAR(500) NULL,

    `remarks` TEXT NULL,

    /*
    |--------------------------------------------------------------------------
    | SLA Context
    |--------------------------------------------------------------------------
    */

    `sla_type` VARCHAR(30) NULL,

    `sla_due_at` DATETIME NULL,

    `sla_breached_at` DATETIME NULL,

    /*
    |--------------------------------------------------------------------------
    | Resolution
    |--------------------------------------------------------------------------
    */

    `resolved_at` DATETIME NULL,

    `resolved_by` BIGINT UNSIGNED NULL,

    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    */

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_at` DATETIME NULL DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`escalation_id`),

    KEY `idx_escalation_issue`
        (`issue_id`),

    KEY `idx_escalation_from_group`
        (`escalated_from_group_id`),

    KEY `idx_escalation_to_group`
        (`escalated_to_group_id`),

    KEY `idx_escalation_status`
        (`escalation_status`),

    KEY `idx_escalation_sla_type`
        (`sla_type`),

    KEY `idx_escalation_due`
        (`sla_due_at`),

    CONSTRAINT `fk_escalation_issue`
        FOREIGN KEY (`issue_id`)
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_escalation_from_group`
        FOREIGN KEY (`escalated_from_group_id`)
        REFERENCES `mst_support_group` (`support_group_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_escalation_to_group`
        FOREIGN KEY (`escalated_to_group_id`)
        REFERENCES `mst_support_group` (`support_group_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_escalation_by`
        FOREIGN KEY (`escalated_by`)
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_escalation_acknowledged_by`
        FOREIGN KEY (`acknowledged_by`)
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;