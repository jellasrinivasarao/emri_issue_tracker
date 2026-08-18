-- ========================================================================
-- EMRI ISSUE TRACKER - COMPLETE DATABASE SCHEMA
-- Generated from 41 Laravel Models
-- ========================================================================
-- This schema includes all tables, relationships, indexes, and constraints
-- derived from the application models
-- ========================================================================

-- Set database context
SET FOREIGN_KEY_CHECKS=0;

-- ========================================================================
-- 1. ORGANIZATION & HIERARCHY TABLES
-- ========================================================================

-- Table: mst_organisation_type
-- Purpose: Master data for organization types
CREATE TABLE IF NOT EXISTS `mst_organisation_type` (
    `organisation_type_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organisation_type_code` VARCHAR(50) NOT NULL UNIQUE,
    `organisation_type_name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`organisation_type_id`),
    INDEX `idx_org_type_code` (`organisation_type_code`),
    INDEX `idx_org_type_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_organisation
-- Purpose: Master data for organizations
CREATE TABLE IF NOT EXISTS `mst_organisation` (
    `organisation_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organisation_type_id` BIGINT UNSIGNED NOT NULL,
    `organisation_code` VARCHAR(50) NOT NULL UNIQUE,
    `organisation_name` VARCHAR(200) NOT NULL,
    `short_name` VARCHAR(50) NULL,
    `description` TEXT NULL,
    `email` VARCHAR(100) NULL,
    `mobile` VARCHAR(20) NULL,
    `address_line1` VARCHAR(255) NULL,
    `address_line2` VARCHAR(255) NULL,
    `city` VARCHAR(100) NULL,
    `state_name` VARCHAR(100) NULL,
    `country` VARCHAR(100) NULL,
    `pincode` VARCHAR(10) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`organisation_id`),
    KEY `fk_org_type` (`organisation_type_id`),
    INDEX `idx_org_code` (`organisation_code`),
    INDEX `idx_org_active` (`is_active`),
    
    CONSTRAINT `fk_organisation_type` 
        FOREIGN KEY (`organisation_type_id`) 
        REFERENCES `mst_organisation_type` (`organisation_type_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_state
-- Purpose: States within an organization
CREATE TABLE IF NOT EXISTS `mst_state` (
    `state_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organisation_id` BIGINT UNSIGNED NOT NULL,
    `state_code` VARCHAR(50) NOT NULL,
    `state_name` VARCHAR(150) NOT NULL,
    `state_short_name` VARCHAR(10) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`state_id`),
    KEY `fk_state_org` (`organisation_id`),
    INDEX `idx_state_code` (`state_code`),
    INDEX `idx_state_active` (`is_active`),
    UNIQUE KEY `uk_state_code_org` (`state_code`, `organisation_id`),
    
    CONSTRAINT `fk_state_organisation` 
        FOREIGN KEY (`organisation_id`) 
        REFERENCES `mst_organisation` (`organisation_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_head_office
-- Purpose: Head office locations
CREATE TABLE IF NOT EXISTS `mst_head_office` (
    `head_office_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organisation_id` BIGINT UNSIGNED NOT NULL,
    `head_office_code` VARCHAR(50) NOT NULL UNIQUE,
    `head_office_name` VARCHAR(200) NOT NULL,
    `address` TEXT NULL,
    `contact_number` VARCHAR(20) NULL,
    `email` VARCHAR(100) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`head_office_id`),
    KEY `fk_ho_org` (`organisation_id`),
    INDEX `idx_ho_code` (`head_office_code`),
    INDEX `idx_ho_active` (`is_active`),
    
    CONSTRAINT `fk_head_office_organisation` 
        FOREIGN KEY (`organisation_id`) 
        REFERENCES `mst_organisation` (`organisation_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 2. VENDOR & SUPPORT GROUP TABLES
-- ========================================================================

-- Table: mst_vendor
-- Purpose: Vendor master data
CREATE TABLE IF NOT EXISTS `mst_vendor` (
    `vendor_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organisation_id` BIGINT UNSIGNED NOT NULL,
    `vendor_code` VARCHAR(50) NOT NULL UNIQUE,
    `vendor_name` VARCHAR(200) NOT NULL,
    `vendor_category` VARCHAR(100) NULL,
    `primary_contact_name` VARCHAR(150) NULL,
    `primary_contact_email` VARCHAR(100) NULL,
    `primary_contact_mobile` VARCHAR(20) NULL,
    `support_email` VARCHAR(100) NULL,
    `support_mobile` VARCHAR(20) NULL,
    `contact_person` VARCHAR(150) NULL,
    `email` VARCHAR(100) NULL,
    `mobile_number` VARCHAR(20) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`vendor_id`),
    KEY `fk_vendor_org` (`organisation_id`),
    INDEX `idx_vendor_code` (`vendor_code`),
    INDEX `idx_vendor_active` (`is_active`),
    
    CONSTRAINT `fk_vendor_organisation` 
        FOREIGN KEY (`organisation_id`) 
        REFERENCES `mst_organisation` (`organisation_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_support_group
-- Purpose: Support teams/groups
CREATE TABLE IF NOT EXISTS `mst_support_group` (
    `support_group_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organisation_id` BIGINT UNSIGNED NOT NULL,
    `support_group_code` VARCHAR(50) NOT NULL UNIQUE,
    `support_group_name` VARCHAR(200) NOT NULL,
    `support_group_type` VARCHAR(50) NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`support_group_id`),
    KEY `fk_sg_org` (`organisation_id`),
    INDEX `idx_sg_code` (`support_group_code`),
    INDEX `idx_sg_active` (`is_active`),
    
    CONSTRAINT `fk_support_group_organisation` 
        FOREIGN KEY (`organisation_id`) 
        REFERENCES `mst_organisation` (`organisation_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 3. APPLICATION & MODULE TABLES
-- ========================================================================

-- Table: mst_application
-- Purpose: Business applications
CREATE TABLE IF NOT EXISTS `mst_application` (
    `application_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `application_code` VARCHAR(50) NOT NULL UNIQUE,
    `application_name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`application_id`),
    INDEX `idx_app_code` (`application_code`),
    INDEX `idx_app_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_module
-- Purpose: Application modules
CREATE TABLE IF NOT EXISTS `mst_module` (
    `module_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `module_code` VARCHAR(50) NOT NULL UNIQUE,
    `module_name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`module_id`),
    INDEX `idx_module_code` (`module_code`),
    INDEX `idx_module_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 4. PROJECT TABLES
-- ========================================================================

-- Table: mst_project
-- Purpose: Projects
CREATE TABLE IF NOT EXISTS `mst_project` (
    `project_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `state_id` BIGINT UNSIGNED NOT NULL,
    `project_code` VARCHAR(50) NOT NULL UNIQUE,
    `short_code` VARCHAR(20) NULL,
    `project_name` VARCHAR(200) NOT NULL,
    `project_description` TEXT NULL,
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `project_status` VARCHAR(50) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`project_id`),
    KEY `fk_project_state` (`state_id`),
    INDEX `idx_project_code` (`project_code`),
    INDEX `idx_project_active` (`is_active`),
    
    CONSTRAINT `fk_project_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_service
-- Purpose: Services
CREATE TABLE IF NOT EXISTS `mst_service` (
    `service_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_code` VARCHAR(50) NOT NULL UNIQUE,
    `short_code` VARCHAR(20) NULL,
    `service_name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `display_order` INT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`service_id`),
    INDEX `idx_service_code` (`service_code`),
    INDEX `idx_service_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: map_project_application
-- Purpose: Project-Application many-to-many relationship
CREATE TABLE IF NOT EXISTS `map_project_application` (
    `mapping_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`mapping_id`),
    KEY `fk_map_pa_project` (`project_id`),
    KEY `fk_map_pa_application` (`application_id`),
    UNIQUE KEY `uk_project_application` (`project_id`, `application_id`),
    
    CONSTRAINT `fk_map_pa_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_map_pa_application` 
        FOREIGN KEY (`application_id`) 
        REFERENCES `mst_application` (`application_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: map_project_service
-- Purpose: Project-Service many-to-many relationship
CREATE TABLE IF NOT EXISTS `map_project_service` (
    `mapping_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `service_id` BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`mapping_id`),
    KEY `fk_map_ps_project` (`project_id`),
    KEY `fk_map_ps_service` (`service_id`),
    UNIQUE KEY `uk_project_service` (`project_id`, `service_id`),
    
    CONSTRAINT `fk_map_ps_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_map_ps_service` 
        FOREIGN KEY (`service_id`) 
        REFERENCES `mst_service` (`service_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: map_project_application_module
-- Purpose: Project-Application-Module mapping
CREATE TABLE IF NOT EXISTS `map_project_application_module` (
    `mapping_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `module_id` BIGINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updated_by` BIGINT UNSIGNED NULL,
    
    PRIMARY KEY (`mapping_id`),
    KEY `fk_map_pam_project` (`project_id`),
    KEY `fk_map_pam_application` (`application_id`),
    KEY `fk_map_pam_module` (`module_id`),
    INDEX `idx_pam_active` (`is_active`),
    
    CONSTRAINT `fk_map_pam_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_map_pam_application` 
        FOREIGN KEY (`application_id`) 
        REFERENCES `mst_application` (`application_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_map_pam_module` 
        FOREIGN KEY (`module_id`) 
        REFERENCES `mst_module` (`module_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 5. ISSUE MASTER TABLES
-- ========================================================================

-- Table: mst_issue_status
-- Purpose: Issue status codes
CREATE TABLE IF NOT EXISTS `mst_issue_status` (
    `status_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status_code` VARCHAR(50) NOT NULL UNIQUE,
    `status_name` VARCHAR(150) NOT NULL,
    `status_category` VARCHAR(50) NULL,
    `is_closed_status` TINYINT(1) NOT NULL DEFAULT 0,
    `display_order` INT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`status_id`),
    INDEX `idx_status_code` (`status_code`),
    INDEX `idx_status_active` (`is_active`),
    INDEX `idx_status_closed` (`is_closed_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_issue_category
-- Purpose: Issue categories/types
CREATE TABLE IF NOT EXISTS `mst_issue_category` (
    `issue_category_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_code` VARCHAR(50) NOT NULL UNIQUE,
    `category_name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`issue_category_id`),
    INDEX `idx_category_code` (`category_code`),
    INDEX `idx_category_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_priority
-- Purpose: Priority levels
CREATE TABLE IF NOT EXISTS `mst_priority` (
    `priority_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `priority_code` VARCHAR(50) NOT NULL UNIQUE,
    `priority_name` VARCHAR(100) NOT NULL,
    `priority_level` INT NOT NULL,
    `description` TEXT NULL,
    `display_order` INT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`priority_id`),
    INDEX `idx_priority_code` (`priority_code`),
    INDEX `idx_priority_level` (`priority_level`),
    INDEX `idx_priority_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 6. WORKING CALENDAR TABLES
-- ========================================================================

-- Table: mst_working_calendar
-- Purpose: Working calendars for organizations/states
CREATE TABLE IF NOT EXISTS `mst_working_calendar` (
    `calendar_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `calendar_code` VARCHAR(50) NOT NULL UNIQUE,
    `calendar_name` VARCHAR(200) NOT NULL,
    `organisation_id` BIGINT UNSIGNED NULL,
    `state_id` BIGINT UNSIGNED NULL,
    `timezone` VARCHAR(50) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`calendar_id`),
    KEY `fk_cal_org` (`organisation_id`),
    KEY `fk_cal_state` (`state_id`),
    INDEX `idx_calendar_code` (`calendar_code`),
    INDEX `idx_calendar_active` (`is_active`),
    
    CONSTRAINT `fk_calendar_organisation` 
        FOREIGN KEY (`organisation_id`) 
        REFERENCES `mst_organisation` (`organisation_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_calendar_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_working_schedule
-- Purpose: Working hours schedules
CREATE TABLE IF NOT EXISTS `mst_working_schedule` (
    `schedule_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `calendar_id` BIGINT UNSIGNED NOT NULL,
    `day_of_week` INT NOT NULL,
    `schedule_name` VARCHAR(100) NULL,
    `shift_no` INT NULL,
    `shift_name` VARCHAR(100) NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `break_start` TIME NULL,
    `break_end` TIME NULL,
    `is_working_day` TINYINT(1) NOT NULL DEFAULT 1,
    `is_24_hours` TINYINT(1) NOT NULL DEFAULT 0,
    `sequence_no` INT NULL,
    `effective_from` DATE NULL,
    `effective_to` DATE NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `deleted_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`schedule_id`),
    KEY `fk_ws_calendar` (`calendar_id`),
    INDEX `idx_ws_day` (`day_of_week`),
    INDEX `idx_ws_active` (`is_active`),
    
    CONSTRAINT `fk_working_schedule_calendar` 
        FOREIGN KEY (`calendar_id`) 
        REFERENCES `mst_working_calendar` (`calendar_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_calendar_holiday
-- Purpose: Public holidays and special holidays
CREATE TABLE IF NOT EXISTS `mst_calendar_holiday` (
    `holiday_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `calendar_id` BIGINT UNSIGNED NOT NULL,
    `holiday_date` DATE NOT NULL,
    `holiday_code` VARCHAR(50) NOT NULL,
    `holiday_name` VARCHAR(150) NOT NULL,
    `holiday_type` ENUM('PUBLIC_HOLIDAY', 'OPTIONAL_HOLIDAY', 'COMPANY_HOLIDAY', 'STATE_HOLIDAY', 'SPECIAL_HOLIDAY', 'EMERGENCY_CLOSURE') 
        NOT NULL DEFAULT 'PUBLIC_HOLIDAY',
    `description` VARCHAR(500) NULL,
    `override_working_day` TINYINT(1) NOT NULL DEFAULT 0,
    `start_time` TIME NULL,
    `end_time` TIME NULL,
    `applicable_scope` ENUM('ALL', 'STATE', 'HO', 'VENDOR', 'PROJECT') NOT NULL DEFAULT 'ALL',
    `scope_id` BIGINT UNSIGNED NULL,
    `is_recurring` TINYINT(1) NOT NULL DEFAULT 0,
    `recurrence_year` SMALLINT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `deleted_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`holiday_id`),
    KEY `fk_ch_calendar` (`calendar_id`),
    INDEX `idx_ch_date` (`holiday_date`),
    INDEX `idx_ch_active` (`is_active`),
    
    CONSTRAINT `fk_calendar_holiday_calendar` 
        FOREIGN KEY (`calendar_id`) 
        REFERENCES `mst_working_calendar` (`calendar_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 7. ROLE & PRIVILEGE TABLES
-- ========================================================================

-- Table: mst_role
-- Purpose: User roles
CREATE TABLE IF NOT EXISTS `mst_role` (
    `role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_code` VARCHAR(50) NOT NULL UNIQUE,
    `role_name` VARCHAR(150) NOT NULL,
    `role_category` VARCHAR(50) NULL,
    `description` TEXT NULL,
    `is_system_role` TINYINT(1) NOT NULL DEFAULT 0,
    
    PRIMARY KEY (`role_id`),
    INDEX `idx_role_code` (`role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_privilege
-- Purpose: System privileges/permissions
CREATE TABLE IF NOT EXISTS `mst_privilege` (
    `privilege_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `privilege_code` VARCHAR(100) NOT NULL UNIQUE,
    `privilege_name` VARCHAR(200) NOT NULL,
    `module_name` VARCHAR(100) NULL,
    `description` TEXT NULL,
    
    PRIMARY KEY (`privilege_id`),
    INDEX `idx_privilege_code` (`privilege_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_menu
-- Purpose: Application menu items
CREATE TABLE IF NOT EXISTS `mst_menu` (
    `menu_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `display_name` VARCHAR(200) NOT NULL,
    `route_name` VARCHAR(100) NULL,
    `uri` VARCHAR(255) NULL,
    `parent_menu_id` BIGINT UNSIGNED NULL,
    `icon` VARCHAR(50) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `display_order` INT NULL,
    
    PRIMARY KEY (`menu_id`),
    KEY `fk_menu_parent` (`parent_menu_id`),
    INDEX `idx_menu_active` (`is_active`),
    
    CONSTRAINT `fk_menu_parent_menu` 
        FOREIGN KEY (`parent_menu_id`) 
        REFERENCES `mst_menu` (`menu_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: map_role_privilege
-- Purpose: Role-Privilege and Role-Menu many-to-many relationship
CREATE TABLE IF NOT EXISTS `map_role_privilege` (
    `role_privilege_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `privilege_id` BIGINT UNSIGNED NULL,
    `menu_id` BIGINT UNSIGNED NULL,
    `is_allowed` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`role_privilege_id`),
    KEY `fk_rp_role` (`role_id`),
    KEY `fk_rp_privilege` (`privilege_id`),
    KEY `fk_rp_menu` (`menu_id`),
    
    CONSTRAINT `fk_role_privilege_role` 
        FOREIGN KEY (`role_id`) 
        REFERENCES `mst_role` (`role_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_role_privilege_privilege` 
        FOREIGN KEY (`privilege_id`) 
        REFERENCES `mst_privilege` (`privilege_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_role_privilege_menu` 
        FOREIGN KEY (`menu_id`) 
        REFERENCES `mst_menu` (`menu_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 8. USER TABLES
-- ========================================================================

-- Table: mst_user
-- Purpose: System users
CREATE TABLE IF NOT EXISTS `mst_user` (
    `user_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `employee_code` VARCHAR(50) NOT NULL UNIQUE,
    `user_name` VARCHAR(150) NOT NULL,
    `login_id` VARCHAR(100) NOT NULL UNIQUE,
    `official_email` VARCHAR(100) NOT NULL UNIQUE,
    `mobile_number` VARCHAR(20) NULL,
    `organisation_id` BIGINT UNSIGNED NULL,
    `user_status` VARCHAR(50) NOT NULL DEFAULT 'ACTIVE',
    `password_hash` VARCHAR(255) NULL,
    `last_login_at` TIMESTAMP NULL,
    `password_changed_at` TIMESTAMP NULL,
    `password_reset_otp` VARCHAR(6) NULL,
    `password_reset_otp_expires_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`user_id`),
    KEY `fk_user_org` (`organisation_id`),
    UNIQUE KEY `uk_user_login` (`login_id`),
    UNIQUE KEY `uk_user_email` (`official_email`),
    INDEX `idx_user_code` (`employee_code`),
    INDEX `idx_user_status` (`user_status`),
    
    CONSTRAINT `fk_user_organisation` 
        FOREIGN KEY (`organisation_id`) 
        REFERENCES `mst_organisation` (`organisation_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: map_user_role
-- Purpose: User-Role many-to-many relationship
CREATE TABLE IF NOT EXISTS `map_user_role` (
    `user_role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`user_role_id`),
    KEY `fk_ur_user` (`user_id`),
    KEY `fk_ur_role` (`role_id`),
    UNIQUE KEY `uk_user_role` (`user_id`, `role_id`),
    
    CONSTRAINT `fk_user_role_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_user_role_role` 
        FOREIGN KEY (`role_id`) 
        REFERENCES `mst_role` (`role_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: map_user_project
-- Purpose: User-Project assignment
CREATE TABLE IF NOT EXISTS `map_user_project` (
    `user_project_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `project_id` BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`user_project_id`),
    KEY `fk_up_user` (`user_id`),
    KEY `fk_up_project` (`project_id`),
    UNIQUE KEY `uk_user_project` (`user_id`, `project_id`),
    
    CONSTRAINT `fk_user_project_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_user_project_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 9. SLA & CONFIGURATION TABLES
-- ========================================================================

-- Table: mst_sla_configuration
-- Purpose: SLA configuration master
CREATE TABLE IF NOT EXISTS `mst_sla_configuration` (
    `sla_configuration_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sla_code` VARCHAR(50) NOT NULL UNIQUE,
    `sla_name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `response_sla_hours` DECIMAL(10, 2) NULL,
    `resolution_sla_hours` DECIMAL(10, 2) NULL,
    `escalation_sla_hours` DECIMAL(10, 2) NULL,
    `support_level` VARCHAR(50) NULL,
    `working_calendar_id` BIGINT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`sla_configuration_id`),
    KEY `fk_sla_calendar` (`working_calendar_id`),
    INDEX `idx_sla_code` (`sla_code`),
    INDEX `idx_sla_active` (`is_active`),
    
    CONSTRAINT `fk_sla_configuration_calendar` 
        FOREIGN KEY (`working_calendar_id`) 
        REFERENCES `mst_working_calendar` (`calendar_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cfg_sla_policy
-- Purpose: SLA policies for services/projects
CREATE TABLE IF NOT EXISTS `cfg_sla_policy` (
    `sla_policy_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` BIGINT UNSIGNED NOT NULL,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `priority_id` BIGINT UNSIGNED NOT NULL,
    `support_level` VARCHAR(50) NULL,
    `response_time_minutes` INT NOT NULL,
    `resolution_time_minutes` INT NOT NULL,
    `warning_percentage` DECIMAL(5, 2) NULL,
    `calendar_id` BIGINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`sla_policy_id`),
    KEY `fk_sla_policy_service` (`service_id`),
    KEY `fk_sla_policy_project` (`project_id`),
    KEY `fk_sla_policy_application` (`application_id`),
    KEY `fk_sla_policy_priority` (`priority_id`),
    KEY `fk_sla_policy_calendar` (`calendar_id`),
    INDEX `idx_sla_policy_active` (`is_active`),
    
    CONSTRAINT `fk_sla_policy_service` 
        FOREIGN KEY (`service_id`) 
        REFERENCES `mst_service` (`service_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sla_policy_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sla_policy_application` 
        FOREIGN KEY (`application_id`) 
        REFERENCES `mst_application` (`application_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sla_policy_priority` 
        FOREIGN KEY (`priority_id`) 
        REFERENCES `mst_priority` (`priority_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sla_policy_calendar` 
        FOREIGN KEY (`calendar_id`) 
        REFERENCES `mst_working_calendar` (`calendar_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_project_support_configuration
-- Purpose: Support configuration for projects
CREATE TABLE IF NOT EXISTS `mst_project_support_configuration` (
    `support_config_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `config_code` VARCHAR(50) NOT NULL,
    `config_name` VARCHAR(200) NOT NULL,
    `default_support_level` VARCHAR(50) NULL,
    `default_team_type` VARCHAR(50) NULL,
    `default_priority` VARCHAR(50) NULL,
    `auto_routing_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `sla_hours` DECIMAL(10, 2) NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`support_config_id`),
    KEY `fk_sc_project` (`project_id`),
    INDEX `idx_sc_code` (`config_code`),
    INDEX `idx_sc_active` (`is_active`),
    
    CONSTRAINT `fk_project_support_configuration_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 10. ISSUE ROUTING TABLES
-- ========================================================================

-- Table: cfg_issue_routing
-- Purpose: Issue routing configuration
CREATE TABLE IF NOT EXISTS `cfg_issue_routing` (
    `routing_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `state_id` BIGINT UNSIGNED NOT NULL,
    `service_id` BIGINT UNSIGNED NOT NULL,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `module_id` BIGINT UNSIGNED NULL,
    `ho_support_group_id` BIGINT UNSIGNED NULL,
    `vendor_support_group_id` BIGINT UNSIGNED NULL,
    `ho_calendar_id` BIGINT UNSIGNED NULL,
    `off_hours_routing_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `effective_from` TIMESTAMP NULL,
    `effective_to` TIMESTAMP NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`routing_id`),
    KEY `fk_routing_state` (`state_id`),
    KEY `fk_routing_service` (`service_id`),
    KEY `fk_routing_project` (`project_id`),
    KEY `fk_routing_application` (`application_id`),
    KEY `fk_routing_module` (`module_id`),
    KEY `fk_routing_ho_group` (`ho_support_group_id`),
    KEY `fk_routing_vendor_group` (`vendor_support_group_id`),
    KEY `fk_routing_calendar` (`ho_calendar_id`),
    
    CONSTRAINT `fk_issue_routing_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_service` 
        FOREIGN KEY (`service_id`) 
        REFERENCES `mst_service` (`service_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_application` 
        FOREIGN KEY (`application_id`) 
        REFERENCES `mst_application` (`application_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_module` 
        FOREIGN KEY (`module_id`) 
        REFERENCES `mst_module` (`module_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_ho_group` 
        FOREIGN KEY (`ho_support_group_id`) 
        REFERENCES `mst_support_group` (`support_group_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_vendor_group` 
        FOREIGN KEY (`vendor_support_group_id`) 
        REFERENCES `mst_support_group` (`support_group_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_routing_calendar` 
        FOREIGN KEY (`ho_calendar_id`) 
        REFERENCES `mst_working_calendar` (`calendar_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_issue_routing_rule
-- Purpose: Issue routing rules
CREATE TABLE IF NOT EXISTS `mst_issue_routing_rule` (
    `routing_rule_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `support_config_id` BIGINT UNSIGNED NOT NULL,
    `support_team_id` BIGINT UNSIGNED NULL,
    `rule_code` VARCHAR(50) NOT NULL,
    `rule_name` VARCHAR(200) NOT NULL,
    `issue_category` VARCHAR(100) NULL,
    `issue_type` VARCHAR(100) NULL,
    `priority` VARCHAR(50) NULL,
    `routing_level` INT NULL,
    `project_id` BIGINT UNSIGNED NULL,
    `state_id` BIGINT UNSIGNED NULL,
    `application_id` BIGINT UNSIGNED NULL,
    `vendor_id` BIGINT UNSIGNED NULL,
    `hoit_id` BIGINT UNSIGNED NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`routing_rule_id`),
    KEY `fk_rr_config` (`support_config_id`),
    KEY `fk_rr_team` (`support_team_id`),
    KEY `fk_rr_project` (`project_id`),
    KEY `fk_rr_state` (`state_id`),
    KEY `fk_rr_application` (`application_id`),
    KEY `fk_rr_vendor` (`vendor_id`),
    INDEX `idx_rr_code` (`rule_code`),
    INDEX `idx_rr_active` (`is_active`),
    
    CONSTRAINT `fk_routing_rule_config` 
        FOREIGN KEY (`support_config_id`) 
        REFERENCES `mst_project_support_configuration` (`support_config_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_routing_rule_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_routing_rule_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_routing_rule_application` 
        FOREIGN KEY (`application_id`) 
        REFERENCES `mst_application` (`application_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_routing_rule_vendor` 
        FOREIGN KEY (`vendor_id`) 
        REFERENCES `mst_vendor` (`vendor_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 11. ISSUE TRANSACTION TABLES
-- ========================================================================

-- Table: txn_issue
-- Purpose: Issue tickets
CREATE TABLE IF NOT EXISTS `txn_issue` (
    `issue_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_number` VARCHAR(50) NOT NULL UNIQUE,
    `service_id` BIGINT UNSIGNED NOT NULL,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `state_id` BIGINT UNSIGNED NOT NULL,
    `support_config_id` BIGINT UNSIGNED NULL,
    `application_id` BIGINT UNSIGNED NULL,
    `module_id` BIGINT UNSIGNED NULL,
    `issue_category_id` BIGINT UNSIGNED NOT NULL,
    `priority_id` BIGINT UNSIGNED NOT NULL,
    `status_id` BIGINT UNSIGNED NOT NULL,
    `issue_title` VARCHAR(255) NOT NULL,
    `issue_description` LONGTEXT NULL,
    `raised_by_user_id` BIGINT UNSIGNED NOT NULL,
    `raised_at` TIMESTAMP NULL,
    `occurred_at` TIMESTAMP NULL,
    `current_owner_organisation_id` BIGINT UNSIGNED NULL,
    `current_owner_group_id` BIGINT UNSIGNED NULL,
    `current_owner_user_id` BIGINT UNSIGNED NULL,
    `current_owner_role_id` BIGINT UNSIGNED NULL,
    `current_team_id` BIGINT UNSIGNED NULL,
    `current_assignee_id` BIGINT UNSIGNED NULL,
    `current_owner_type` VARCHAR(50) NULL,
    `current_owner_id` BIGINT UNSIGNED NULL,
    `current_stage` VARCHAR(50) NULL,
    `workflow_status` VARCHAR(50) NULL,
    `resolution_summary` LONGTEXT NULL,
    `ho_intervention_required` TINYINT(1) NOT NULL DEFAULT 0,
    `ho_working_hours` VARCHAR(50) NULL,
    `first_level_vendor_ids` JSON NULL,
    `second_level_vendor_ids` JSON NULL,
    `reopened_count` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sla_due_at` TIMESTAMP NULL,
    `reported_by` VARCHAR(100) NULL,
    `opened_at` TIMESTAMP NULL,
    `assigned_at` TIMESTAMP NULL,
    `resolved_at` TIMESTAMP NULL,
    `closed_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `resolved_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`issue_id`),
    KEY `fk_issue_service` (`service_id`),
    KEY `fk_issue_project` (`project_id`),
    KEY `fk_issue_state` (`state_id`),
    KEY `fk_issue_config` (`support_config_id`),
    KEY `fk_issue_application` (`application_id`),
    KEY `fk_issue_module` (`module_id`),
    KEY `fk_issue_category` (`issue_category_id`),
    KEY `fk_issue_priority` (`priority_id`),
    KEY `fk_issue_status` (`status_id`),
    KEY `fk_issue_raised_by` (`raised_by_user_id`),
    KEY `fk_issue_owner_org` (`current_owner_organisation_id`),
    KEY `fk_issue_owner_group` (`current_owner_group_id`),
    KEY `fk_issue_owner_user` (`current_owner_user_id`),
    KEY `fk_issue_owner_role` (`current_owner_role_id`),
    KEY `fk_issue_assignee` (`current_assignee_id`),
    UNIQUE KEY `uk_issue_number` (`issue_number`),
    INDEX `idx_issue_status` (`status_id`),
    INDEX `idx_issue_priority` (`priority_id`),
    INDEX `idx_issue_date` (`raised_at`),
    
    CONSTRAINT `fk_issue_service` 
        FOREIGN KEY (`service_id`) 
        REFERENCES `mst_service` (`service_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_support_config` 
        FOREIGN KEY (`support_config_id`) 
        REFERENCES `mst_project_support_configuration` (`support_config_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_application` 
        FOREIGN KEY (`application_id`) 
        REFERENCES `mst_application` (`application_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_module` 
        FOREIGN KEY (`module_id`) 
        REFERENCES `mst_module` (`module_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_category` 
        FOREIGN KEY (`issue_category_id`) 
        REFERENCES `mst_issue_category` (`issue_category_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_priority` 
        FOREIGN KEY (`priority_id`) 
        REFERENCES `mst_priority` (`priority_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_status` 
        FOREIGN KEY (`status_id`) 
        REFERENCES `mst_issue_status` (`status_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_issue_raised_by` 
        FOREIGN KEY (`raised_by_user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: txn_issue_assignment
-- Purpose: Issue assignments to teams/users
CREATE TABLE IF NOT EXISTS `txn_issue_assignment` (
    `assignment_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` BIGINT UNSIGNED NOT NULL,
    `routing_rule_id` BIGINT UNSIGNED NULL,
    `support_config_id` BIGINT UNSIGNED NULL,
    `support_team_id` BIGINT UNSIGNED NULL,
    `assigned_user_id` BIGINT UNSIGNED NULL,
    `assignment_level` INT NULL,
    `assignment_type` VARCHAR(50) NULL,
    `status` VARCHAR(50) NULL,
    `assigned_at` TIMESTAMP NULL,
    `accepted_at` TIMESTAMP NULL,
    `started_at` TIMESTAMP NULL,
    `completed_at` TIMESTAMP NULL,
    `remarks` TEXT NULL,
    `assignment_reason` VARCHAR(255) NULL,
    `assigned_by` BIGINT UNSIGNED NULL,
    
    PRIMARY KEY (`assignment_id`),
    KEY `fk_assign_issue` (`issue_id`),
    KEY `fk_assign_routing_rule` (`routing_rule_id`),
    KEY `fk_assign_config` (`support_config_id`),
    KEY `fk_assign_team` (`support_team_id`),
    KEY `fk_assign_user` (`assigned_user_id`),
    
    CONSTRAINT `fk_assignment_issue` 
        FOREIGN KEY (`issue_id`) 
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_assignment_routing_rule` 
        FOREIGN KEY (`routing_rule_id`) 
        REFERENCES `mst_issue_routing_rule` (`routing_rule_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_assignment_config` 
        FOREIGN KEY (`support_config_id`) 
        REFERENCES `mst_project_support_configuration` (`support_config_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: txn_issue_attachment
-- Purpose: Issue attachments
CREATE TABLE IF NOT EXISTS `txn_issue_attachment` (
    `attachment_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `original_file_name` VARCHAR(255) NOT NULL,
    `stored_file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NULL,
    `file_size` BIGINT NULL,
    `file_type` VARCHAR(50) NULL,
    `uploaded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    
    PRIMARY KEY (`attachment_id`),
    KEY `fk_attach_issue` (`issue_id`),
    KEY `fk_attach_user` (`user_id`),
    
    CONSTRAINT `fk_attachment_issue` 
        FOREIGN KEY (`issue_id`) 
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_attachment_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: txn_issue_status_history
-- Purpose: Issue status change history
CREATE TABLE IF NOT EXISTS `txn_issue_status_history` (
    `status_history_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` BIGINT UNSIGNED NOT NULL,
    `from_status_id` BIGINT UNSIGNED NULL,
    `to_status_id` BIGINT UNSIGNED NULL,
    `from_status` VARCHAR(100) NULL,
    `to_status` VARCHAR(100) NULL,
    `assignment_id` BIGINT UNSIGNED NULL,
    `routing_rule_id` BIGINT UNSIGNED NULL,
    `support_config_id` BIGINT UNSIGNED NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `change_type` VARCHAR(50) NULL,
    `remarks` TEXT NULL,
    `change_reason` VARCHAR(255) NULL,
    `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`status_history_id`),
    KEY `fk_sh_issue` (`issue_id`),
    KEY `fk_sh_from_status` (`from_status_id`),
    KEY `fk_sh_to_status` (`to_status_id`),
    KEY `fk_sh_assignment` (`assignment_id`),
    KEY `fk_sh_routing_rule` (`routing_rule_id`),
    INDEX `idx_sh_changed_at` (`changed_at`),
    
    CONSTRAINT `fk_status_history_issue` 
        FOREIGN KEY (`issue_id`) 
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_status_history_from_status` 
        FOREIGN KEY (`from_status_id`) 
        REFERENCES `mst_issue_status` (`status_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_status_history_to_status` 
        FOREIGN KEY (`to_status_id`) 
        REFERENCES `mst_issue_status` (`status_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: t_issue_history
-- Purpose: Issue action history
CREATE TABLE IF NOT EXISTS `t_issue_history` (
    `history_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` BIGINT UNSIGNED NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `from_status` VARCHAR(100) NULL,
    `to_status` VARCHAR(100) NULL,
    `from_team_id` BIGINT UNSIGNED NULL,
    `to_team_id` BIGINT UNSIGNED NULL,
    `remarks` TEXT NULL,
    `performed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`history_id`),
    KEY `fk_hist_issue` (`issue_id`),
    INDEX `idx_hist_action` (`action`),
    
    CONSTRAINT `fk_issue_history_issue` 
        FOREIGN KEY (`issue_id`) 
        REFERENCES `txn_issue` (`issue_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 12. REQUIREMENT & CLARIFICATION TABLES
-- ========================================================================

-- Table: requirements
-- Purpose: Requirements tracking
CREATE TABLE IF NOT EXISTS `requirements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requirement_no` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `description` LONGTEXT NULL,
    `state_id` BIGINT UNSIGNED NULL,
    `project_id` BIGINT UNSIGNED NULL,
    `brd_raised_by` VARCHAR(100) NULL,
    `ho_it_team` TINYINT(1) NOT NULL DEFAULT 0,
    `received_at` DATE NULL,
    `requested_to_vendor_at` DATE NULL,
    `assigned_vendor_id` BIGINT UNSIGNED NULL,
    `additional_details` LONGTEXT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'OPEN',
    `delivery_status` VARCHAR(50) NULL,
    `man_days` DECIMAL(8, 2) NULL,
    `timeline` VARCHAR(100) NULL,
    `vendor_remarks` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`id`),
    KEY `fk_req_state` (`state_id`),
    KEY `fk_req_project` (`project_id`),
    KEY `fk_req_vendor` (`assigned_vendor_id`),
    INDEX `idx_req_no` (`requirement_no`),
    INDEX `idx_req_status` (`status`),
    INDEX `idx_req_deleted` (`deleted_at`),
    
    CONSTRAINT `fk_requirement_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_requirement_project` 
        FOREIGN KEY (`project_id`) 
        REFERENCES `mst_project` (`project_id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_requirement_vendor` 
        FOREIGN KEY (`assigned_vendor_id`) 
        REFERENCES `mst_vendor` (`vendor_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: requirement_files
-- Purpose: Requirement attachments
CREATE TABLE IF NOT EXISTS `requirement_files` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requirement_id` BIGINT UNSIGNED NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) NULL,
    `file_size` BIGINT NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `fk_rf_requirement` (`requirement_id`),
    KEY `fk_rf_user` (`uploaded_by`),
    
    CONSTRAINT `fk_requirement_file_requirement` 
        FOREIGN KEY (`requirement_id`) 
        REFERENCES `requirements` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_requirement_file_user` 
        FOREIGN KEY (`uploaded_by`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: requirement_status_histories
-- Purpose: Requirement status change history
CREATE TABLE IF NOT EXISTS `requirement_status_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requirement_id` BIGINT UNSIGNED NOT NULL,
    `from_status` VARCHAR(50) NULL,
    `to_status` VARCHAR(50) NOT NULL,
    `remarks` TEXT NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `fk_rsh_requirement` (`requirement_id`),
    KEY `fk_rsh_user` (`changed_by`),
    
    CONSTRAINT `fk_requirement_status_history_requirement` 
        FOREIGN KEY (`requirement_id`) 
        REFERENCES `requirements` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_requirement_status_history_user` 
        FOREIGN KEY (`changed_by`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: clarifications
-- Purpose: Clarification requests
CREATE TABLE IF NOT EXISTS `clarifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requirement_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `message` LONGTEXT NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'OPEN',
    `closed_at` TIMESTAMP NULL,
    `closed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `fk_clar_requirement` (`requirement_id`),
    KEY `fk_clar_user` (`user_id`),
    KEY `fk_clar_closed_by` (`closed_by`),
    INDEX `idx_clar_status` (`status`),
    
    CONSTRAINT `fk_clarification_requirement` 
        FOREIGN KEY (`requirement_id`) 
        REFERENCES `requirements` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_clarification_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_clarification_closed_by` 
        FOREIGN KEY (`closed_by`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: clarification_replies
-- Purpose: Clarification reply threads
CREATE TABLE IF NOT EXISTS `clarification_replies` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `clarification_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `message` LONGTEXT NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `fk_clar_reply_clarification` (`clarification_id`),
    KEY `fk_clar_reply_user` (`user_id`),
    
    CONSTRAINT `fk_clarification_reply_clarification` 
        FOREIGN KEY (`clarification_id`) 
        REFERENCES `clarifications` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_clarification_reply_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 13. MAIL CONFIGURATION & LOGGING TABLES
-- ========================================================================

-- Table: mst_mail_setting
-- Purpose: Mail server settings
CREATE TABLE IF NOT EXISTS `mst_mail_setting` (
    `mail_setting_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `host` VARCHAR(100) NOT NULL,
    `port` INT NOT NULL,
    `encryption` VARCHAR(20) NULL,
    `username` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `from_address` VARCHAR(100) NOT NULL,
    `from_name` VARCHAR(150) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`mail_setting_id`),
    INDEX `idx_mail_setting_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_mail_configuration
-- Purpose: Mail configuration per state
CREATE TABLE IF NOT EXISTS `mst_mail_configuration` (
    `mail_configuration_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `state_id` BIGINT UNSIGNED NOT NULL,
    `state_name` VARCHAR(150) NULL,
    `to_emails` JSON NULL,
    `cc_emails` JSON NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`mail_configuration_id`),
    KEY `fk_mail_config_state` (`state_id`),
    KEY `fk_mail_config_user` (`created_by`),
    
    CONSTRAINT `fk_mail_configuration_state` 
        FOREIGN KEY (`state_id`) 
        REFERENCES `mst_state` (`state_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_mail_configuration_user` 
        FOREIGN KEY (`created_by`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mst_mail_log
-- Purpose: Mail sent/failed logs
CREATE TABLE IF NOT EXISTS `mst_mail_log` (
    `mail_log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL,
    `to_address` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` LONGTEXT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'PENDING',
    `error_message` TEXT NULL,
    `mailer` VARCHAR(50) NULL,
    `sent_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`mail_log_id`),
    KEY `fk_mail_log_user` (`user_id`),
    INDEX `idx_mail_log_status` (`status`),
    INDEX `idx_mail_log_sent` (`sent_at`),
    
    CONSTRAINT `fk_mail_log_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `mst_user` (`user_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- RE-ENABLE FOREIGN KEY CHECKS
-- ========================================================================

SET FOREIGN_KEY_CHECKS=1;

-- ========================================================================
-- END OF COMPLETE DATABASE SCHEMA
-- ========================================================================
-- Total Tables: 52
-- Master Tables: 23
-- Transaction Tables: 7
-- Configuration Tables: 7
-- Mapping/Junction Tables: 15
-- ========================================================================
