CREATE TABLE `txn_issue_assignment` (
    `assignment_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `issue_id` BIGINT UNSIGNED NOT NULL,

    `routing_rule_id` BIGINT UNSIGNED NULL,

    `support_config_id` BIGINT UNSIGNED NULL,

    `support_team_id` BIGINT UNSIGNED NULL,

    `assigned_user_id` BIGINT UNSIGNED NULL,

    `assignment_level` TINYINT UNSIGNED NOT NULL COMMENT '1=HO IT, 2=Vendor',

    `assignment_type` VARCHAR(20) NOT NULL DEFAULT 'AUTO',

    `status` VARCHAR(30) NOT NULL DEFAULT 'ASSIGNED',

    `assigned_at` DATETIME NOT NULL,

    `accepted_at` DATETIME NULL,

    `started_at` DATETIME NULL,

    `completed_at` DATETIME NULL,

    `remarks` TEXT NULL,

    `assignment_reason` VARCHAR(255) NULL,

    `assigned_by` BIGINT UNSIGNED NULL,

    PRIMARY KEY (`assignment_id`),

    KEY `idx_assignment_issue`
        (`issue_id`),

    KEY `idx_assignment_rule`
        (`routing_rule_id`),

    KEY `idx_assignment_config`
        (`support_config_id`),

    KEY `idx_assignment_team`
        (`support_team_id`),

    KEY `idx_assignment_user`
        (`assigned_user_id`),

    KEY `idx_assignment_status`
        (`status`),

    CONSTRAINT `fk_assignment_issue`
        FOREIGN KEY (`issue_id`)
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_assignment_rule`
        FOREIGN KEY (`routing_rule_id`)
        REFERENCES `mst_issue_routing_rule` (`routing_rule_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_assignment_config`
        FOREIGN KEY (`support_config_id`)
        REFERENCES `mst_project_support_configuration` (`support_config_id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_assignment_team`
        FOREIGN KEY (`support_team_id`)
        REFERENCES `mst_support_team` (`support_team_id`)
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;