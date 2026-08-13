CREATE TABLE mst_calendar_holiday (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    calendar_id BIGINT UNSIGNED NOT NULL,

    holiday_date DATE NOT NULL,

    holiday_code VARCHAR(50) NOT NULL,
    holiday_name VARCHAR(150) NOT NULL,

    holiday_type ENUM(
        'PUBLIC_HOLIDAY',
        'OPTIONAL_HOLIDAY',
        'COMPANY_HOLIDAY',
        'STATE_HOLIDAY',
        'SPECIAL_HOLIDAY',
        'EMERGENCY_CLOSURE'
    ) NOT NULL DEFAULT 'PUBLIC_HOLIDAY',

    description VARCHAR(500) NULL,

    override_working_day TINYINT(1) NOT NULL DEFAULT 0,

    start_time TIME NULL,
    end_time TIME NULL,

    applicable_scope ENUM(
        'ALL',
        'STATE',
        'HO',
        'VENDOR',
        'PROJECT'
    ) NOT NULL DEFAULT 'ALL',

    scope_id BIGINT UNSIGNED NULL,

    is_recurring TINYINT(1) NOT NULL DEFAULT 0,

    recurrence_year SMALLINT UNSIGNED NULL,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_by BIGINT UNSIGNED NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uk_calendar_holiday (
        calendar_id,
        holiday_date,
        scope_id
    ),

    KEY idx_holiday_date (
        holiday_date
    ),

    KEY idx_holiday_calendar_date (
        calendar_id,
        holiday_date
    ),

    KEY idx_holiday_scope (
        applicable_scope,
        scope_id
    ),

    KEY idx_holiday_active (
        is_active
    ),

    CONSTRAINT fk_holiday_calendar
        FOREIGN KEY (calendar_id)
        REFERENCES mst_working_calendar(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `mst_working_calendar` (
  `calendar_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `calendar_code` varchar(50) NOT NULL,
  `calendar_name` varchar(150) NOT NULL,
  `organisation_id` bigint(20) unsigned DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`calendar_id`),
  UNIQUE KEY `calendar_code` (`calendar_code`),
  KEY `fk_mwc_org` (`organisation_id`),
  CONSTRAINT `fk_mwc_org`
    FOREIGN KEY (`organisation_id`)
    REFERENCES `mst_organisation` (`organisation_id`)
    ON DELETE SET NULL
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

ALTER TABLE mst_working_calendar
    ADD COLUMN description VARCHAR(500) NULL AFTER calendar_name,
    ADD COLUMN effective_from DATE NULL AFTER timezone,
    ADD COLUMN effective_to DATE NULL AFTER effective_from,
    ADD COLUMN version_no INT UNSIGNED NOT NULL DEFAULT 1 AFTER effective_to,
    ADD COLUMN updated_at DATETIME NULL DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP AFTER created_at;
		
		
CREATE TABLE mst_working_schedule (
    schedule_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    calendar_id BIGINT UNSIGNED NOT NULL,

    day_of_week TINYINT UNSIGNED NOT NULL
        COMMENT '1=Monday, 2=Tuesday ... 7=Sunday',

    schedule_name VARCHAR(100) NULL,

    start_time TIME NULL,
    end_time TIME NULL,

    is_working_day TINYINT(1) NOT NULL DEFAULT 1,

    is_24_hours TINYINT(1) NOT NULL DEFAULT 0,

    sequence_no SMALLINT UNSIGNED NOT NULL DEFAULT 1,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NULL
        DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (schedule_id),

    UNIQUE KEY uk_mws_calendar_day_sequence (
        calendar_id,
        day_of_week,
        sequence_no
    ),

    KEY idx_mws_calendar (
        calendar_id
    ),

    CONSTRAINT fk_mws_calendar
        FOREIGN KEY (calendar_id)
        REFERENCES mst_working_calendar(calendar_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE mst_calendar_holiday (
    holiday_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    calendar_id BIGINT UNSIGNED NOT NULL,

    holiday_date DATE NOT NULL,

    holiday_code VARCHAR(50) NOT NULL,
    holiday_name VARCHAR(150) NOT NULL,

    holiday_type ENUM(
        'PUBLIC',
        'OPTIONAL',
        'ORGANISATION',
        'STATE',
        'SPECIAL'
    ) NOT NULL DEFAULT 'PUBLIC',

    description VARCHAR(500) NULL,

    is_working_day_override TINYINT(1) NOT NULL DEFAULT 0,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NULL
        DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (holiday_id),

    UNIQUE KEY uk_mch_calendar_date (
        calendar_id,
        holiday_date
    ),

    KEY idx_mch_calendar (
        calendar_id
    ),

    KEY idx_mch_holiday_date (
        holiday_date
    ),

    CONSTRAINT fk_mch_calendar
        FOREIGN KEY (calendar_id)
        REFERENCES mst_working_calendar(calendar_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;		







CREATE TABLE `mst_working_calendar` (
    `calendar_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `calendar_code` VARCHAR(50) NOT NULL,
    `calendar_name` VARCHAR(150) NOT NULL,
    `description` VARCHAR(500) DEFAULT NULL,

    `organisation_id` BIGINT UNSIGNED DEFAULT NULL,

    `timezone` VARCHAR(100) NOT NULL DEFAULT 'Asia/Kolkata',

    `effective_from` DATE DEFAULT NULL,
    `effective_to` DATE DEFAULT NULL,

    `version_no` INT UNSIGNED NOT NULL DEFAULT 1,

    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_by` BIGINT UNSIGNED DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_by` BIGINT UNSIGNED DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,

    PRIMARY KEY (`calendar_id`),

    UNIQUE KEY `uk_mwc_calendar_code` (`calendar_code`),

    KEY `idx_mwc_organisation` (`organisation_id`),
    KEY `idx_mwc_active` (`is_active`),
    KEY `idx_mwc_effective` (`effective_from`, `effective_to`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `mst_working_schedule` (
    `schedule_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `calendar_id` BIGINT UNSIGNED NOT NULL,

    `day_of_week` TINYINT UNSIGNED NOT NULL
        COMMENT '1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday, 7=Sunday',

    `schedule_name` VARCHAR(100) DEFAULT NULL,

    `start_time` TIME DEFAULT NULL,
    `end_time` TIME DEFAULT NULL,

    `is_working_day` TINYINT(1) NOT NULL DEFAULT 1,

    `is_24_hours` TINYINT(1) NOT NULL DEFAULT 0,

    `sequence_no` SMALLINT UNSIGNED NOT NULL DEFAULT 1,

    `effective_from` DATE DEFAULT NULL,
    `effective_to` DATE DEFAULT NULL,

    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_by` BIGINT UNSIGNED DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_by` BIGINT UNSIGNED DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,

    PRIMARY KEY (`schedule_id`),

    UNIQUE KEY `uk_mws_calendar_day_sequence`
        (`calendar_id`, `day_of_week`, `sequence_no`),

    KEY `idx_mws_calendar`
        (`calendar_id`),

    KEY `idx_mws_day`
        (`day_of_week`),

    KEY `idx_mws_active`
        (`is_active`),

    KEY `idx_mws_effective`
        (`effective_from`, `effective_to`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `mst_calendar_holiday` (
    `holiday_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `calendar_id` BIGINT UNSIGNED NOT NULL,

    `holiday_date` DATE NOT NULL,

    `holiday_code` VARCHAR(50) NOT NULL,
    `holiday_name` VARCHAR(150) NOT NULL,

    `holiday_type` ENUM(
        'PUBLIC',
        'OPTIONAL',
        'ORGANISATION',
        'STATE',
        'SPECIAL'
    ) NOT NULL DEFAULT 'PUBLIC',

    `description` VARCHAR(500) DEFAULT NULL,

    `is_working_day_override` TINYINT(1) NOT NULL DEFAULT 0,

    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_by` BIGINT UNSIGNED DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_by` BIGINT UNSIGNED DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,

    PRIMARY KEY (`holiday_id`),

    UNIQUE KEY `uk_mch_calendar_date`
        (`calendar_id`, `holiday_date`),

    KEY `idx_mch_calendar`
        (`calendar_id`),

    KEY `idx_mch_holiday_date`
        (`holiday_date`),

    KEY `idx_mch_holiday_type`
        (`holiday_type`),

    KEY `idx_mch_active`
        (`is_active`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;