CREATE TABLE `txn_issue_status_history` (

    `status_history_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `issue_id` BIGINT UNSIGNED NOT NULL,

    `from_status_id` BIGINT UNSIGNED NULL,

    `to_status_id` BIGINT UNSIGNED NULL,

    `from_status` VARCHAR(50) NULL,

    `to_status` VARCHAR(50) NOT NULL,

    `assignment_id` BIGINT UNSIGNED NULL,

    `routing_rule_id` BIGINT UNSIGNED NULL,

    `support_config_id` BIGINT UNSIGNED NULL,

    `changed_by` BIGINT UNSIGNED NULL,

    `change_type` VARCHAR(30) NOT NULL DEFAULT 'MANUAL',

    `remarks` TEXT NULL,

    `change_reason` VARCHAR(255) NULL,

    `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`status_history_id`),

    KEY `idx_status_history_issue`
        (`issue_id`),

    KEY `idx_status_history_from_status`
        (`from_status_id`),

    KEY `idx_status_history_to_status`
        (`to_status_id`),

    KEY `idx_status_history_assignment`
        (`assignment_id`),

    KEY `idx_status_history_rule`
        (`routing_rule_id`),

    KEY `idx_status_history_config`
        (`support_config_id`),

    KEY `idx_status_history_changed_by`
        (`changed_by`),

    KEY `idx_status_history_changed_at`
        (`changed_at`),

    CONSTRAINT `fk_status_history_issue`
        FOREIGN KEY (`issue_id`)
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_status_history_assignment`
        FOREIGN KEY (`assignment_id`)
        REFERENCES `txn_issue_assignment` (`assignment_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_status_history_rule`
        FOREIGN KEY (`routing_rule_id`)
        REFERENCES `mst_issue_routing_rule` (`routing_rule_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_status_history_config`
        FOREIGN KEY (`support_config_id`)
        REFERENCES `mst_project_support_configuration` (`support_config_id`)
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;