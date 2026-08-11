CREATE TABLE `mst_working_schedule` (
    `schedule_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `calendar_id` BIGINT(20) UNSIGNED NOT NULL,
    `day_of_week` VARCHAR(20) NOT NULL,
    `schedule_name` VARCHAR(100) DEFAULT NULL,
    `start_time` TIME DEFAULT NULL,
    `end_time` TIME DEFAULT NULL,
    `is_working_day` TINYINT(1) NOT NULL DEFAULT 1,
    `is_24_hours` TINYINT(1) NOT NULL DEFAULT 0,
    `sequence_no` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 1,
    `effective_from` DATE DEFAULT NULL,
    `effective_to` DATE DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    `created_by` BIGINT(20) UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT(20) UNSIGNED DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    `deleted_by` BIGINT(20) UNSIGNED DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,

    `break_start` TIME DEFAULT NULL,
    `break_end` TIME DEFAULT NULL,

    `shift_no` TINYINT(4) DEFAULT 1,
    `shift_name` VARCHAR(50) DEFAULT NULL,

    PRIMARY KEY (`schedule_id`),

    KEY `idx_working_schedule_calendar`
        (`calendar_id`),

    KEY `idx_working_schedule_day`
        (`day_of_week`),

    KEY `idx_working_schedule_effective_from`
        (`effective_from`),

    KEY `idx_working_schedule_active`
        (`is_active`),

    KEY `idx_working_schedule_calendar_day`
        (`calendar_id`, `day_of_week`),

    KEY `idx_working_schedule_calendar_active`
        (`calendar_id`, `is_active`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;



INSERT INTO `mst_working_schedule`
(
    `calendar_id`,
    `day_of_week`,
    `schedule_name`,
    `start_time`,
    `end_time`,
    `is_working_day`,
    `is_24_hours`,
    `sequence_no`,
    `effective_from`,
    `effective_to`,
    `is_active`,
    `created_by`,
    `created_at`,
    `break_start`,
    `break_end`,
    `shift_no`,
    `shift_name`
)
VALUES

-- Monday
(
    1,
    'MONDAY',
    'General Shift',
    '09:00:00',
    '18:00:00',
    1,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    '13:00:00',
    '14:00:00',
    1,
    'General'
),

-- Tuesday
(
    1,
    'TUESDAY',
    'General Shift',
    '09:00:00',
    '18:00:00',
    1,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    '13:00:00',
    '14:00:00',
    1,
    'General'
),

-- Wednesday
(
    1,
    'WEDNESDAY',
    'General Shift',
    '09:00:00',
    '18:00:00',
    1,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    '13:00:00',
    '14:00:00',
    1,
    'General'
),

-- Thursday
(
    1,
    'THURSDAY',
    'General Shift',
    '09:00:00',
    '18:00:00',
    1,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    '13:00:00',
    '14:00:00',
    1,
    'General'
),

-- Friday
(
    1,
    'FRIDAY',
    'General Shift',
    '09:00:00',
    '18:00:00',
    1,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    '13:00:00',
    '14:00:00',
    1,
    'General'
),

-- Saturday
(
    1,
    'SATURDAY',
    'Weekly Off',
    NULL,
    NULL,
    0,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    NULL,
    NULL,
    1,
    'General'
),

-- Sunday
(
    1,
    'SUNDAY',
    'Weekly Off',
    NULL,
    NULL,
    0,
    0,
    1,
    '2026-01-01',
    NULL,
    1,
    1,
    NOW(),
    NULL,
    NULL,
    1,
    'General'
);
