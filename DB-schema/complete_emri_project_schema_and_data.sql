-- ============================================================================
-- EMRI ISSUE TRACKER - SINGLE SQL FILE (CREATE + INSERT)
-- ============================================================================
-- Purpose:
--   1. Create all master, mapping, user, and transaction tables
--   2. Add missing columns used by the application
--   3. Insert default seed data for project setup
-- NOTE: Do not run unless you are ready to create/update the database schema.
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. MASTER TABLES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mst_state` (
  `state_id` int NOT NULL AUTO_INCREMENT,
  `state_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'India',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`state_id`),
  UNIQUE KEY `uq_state_code` (`state_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_project` (
  `project_id` int NOT NULL AUTO_INCREMENT,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `uq_project_code` (`project_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_application` (
  `application_id` int NOT NULL AUTO_INCREMENT,
  `application_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `uq_application_code` (`application_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_module` (
  `module_id` int NOT NULL AUTO_INCREMENT,
  `module_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `uq_module_code` (`module_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_service` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `service_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `uq_service_code` (`service_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_vendor` (
  `vendor_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `vendor_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `primary_contact_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_contact_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_contact_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vendor_id`),
  UNIQUE KEY `uq_vendor_code_org` (`vendor_code`, `organisation_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_issue_status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_closed_status` tinyint(1) DEFAULT 0,
  `is_resolved` tinyint(1) DEFAULT 0,
  `display_order` int DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `uq_status_code` (`status_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_priority` (
  `priority_id` int NOT NULL AUTO_INCREMENT,
  `priority_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority_level` int DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`priority_id`),
  UNIQUE KEY `uq_priority_code` (`priority_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_issue_category` (
  `issue_category_id` int NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_category_id`),
  UNIQUE KEY `uq_category_code` (`category_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_support_group` (
  `support_group_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `support_group_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_group_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `support_group_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`support_group_id`),
  UNIQUE KEY `uq_support_group_code_org` (`support_group_code`, `organisation_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system_role` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uq_role_code` (`role_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_menu_id` int DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`),
  UNIQUE KEY `uq_route_name` (`route_name`),
  KEY `idx_parent_menu_id` (`parent_menu_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_privilege` (
  `privilege_id` int NOT NULL AUTO_INCREMENT,
  `privilege_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `privilege_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`privilege_id`),
  UNIQUE KEY `uq_privilege_code` (`privilege_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_project_application` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `application_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_project_application` (`project_id`, `application_id`),
  CONSTRAINT `fk_map_project_app_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_project_app_app` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_project_application_module` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `application_id` int NOT NULL,
  `module_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_project_app_module` (`project_id`, `application_id`, `module_id`),
  CONSTRAINT `fk_map_app_module_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_app_module_app` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_app_module_mod` FOREIGN KEY (`module_id`) REFERENCES `mst_module` (`module_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_project_service` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `service_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_project_service` (`project_id`, `service_id`),
  CONSTRAINT `fk_map_project_service_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_project_service_service` FOREIGN KEY (`service_id`) REFERENCES `mst_service` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_project_state` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `state_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_project_state` (`project_id`, `state_id`),
  CONSTRAINT `fk_map_project_state_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_project_state_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_vendor_state` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `state_id` int NOT NULL,
  `project_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_vendor_state_project` (`vendor_id`, `state_id`, `project_id`, `application_id`),
  CONSTRAINT `fk_map_vendor_state_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_vendor_state_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_vendor_state_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_map_vendor_state_app` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_role_privilege` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `menu_id` int DEFAULT NULL,
  `privilege_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_role_menu_privilege` (`role_id`, `menu_id`, `privilege_id`),
  CONSTRAINT `fk_map_role_privilege_role` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_role_privilege_menu` FOREIGN KEY (`menu_id`) REFERENCES `mst_menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_role_privilege_privilege` FOREIGN KEY (`privilege_id`) REFERENCES `mst_privilege` (`privilege_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_role_issue_status` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `status_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_role_status` (`role_id`, `status_id`),
  CONSTRAINT `fk_map_role_status_role` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_role_status_status` FOREIGN KEY (`status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_user_role` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_user_role` (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_user_project` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `project_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_user_project` (`user_id`, `project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_user_support_group` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `support_group_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_user_support_group` (`user_id`, `support_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_issue_vendor_assignment` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `vendor_status_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `uq_issue_vendor` (`issue_id`, `vendor_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_vendor_status` (`vendor_status_id`),
  CONSTRAINT `fk_map_issue_vendor_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_issue_vendor_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. USER / ORGANIZATION TABLES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mst_organisation_type` (
  `organisation_type_id` int NOT NULL AUTO_INCREMENT,
  `organisation_type_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisation_type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`organisation_type_id`),
  UNIQUE KEY `uq_org_type_code` (`organisation_type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_organisation` (
  `organisation_id` int NOT NULL AUTO_INCREMENT,
  `organisation_type_id` int DEFAULT NULL,
  `organisation_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisation_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`organisation_id`),
  UNIQUE KEY `uq_organisation_code` (`organisation_code`),
  CONSTRAINT `fk_org_type` FOREIGN KEY (`organisation_type_id`) REFERENCES `mst_organisation_type` (`organisation_type_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_head_office` (
  `head_office_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `head_office_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_office_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`head_office_id`),
  UNIQUE KEY `uq_head_office_code` (`head_office_code`),
  CONSTRAINT `fk_head_office_org` FOREIGN KEY (`organisation_id`) REFERENCES `mst_organisation` (`organisation_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `head_office_id` int DEFAULT NULL,
  `state_id` int DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `employee_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `official_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `password_reset_otp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_otp_expires_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_first_login` tinyint(1) DEFAULT 1,
  `password_expires_at` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `password_force_change` tinyint(1) DEFAULT 0,
  `otp_verified_at` timestamp NULL DEFAULT NULL,
  `otp_attempts` int DEFAULT 0,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_to_user_id` int DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_login_id` (`login_id`),
  UNIQUE KEY `uq_employee_code` (`employee_code`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_head_office_id` (`head_office_id`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_login_id_status` (`login_id`, `user_status`),
  CONSTRAINT `fk_mst_user_org` FOREIGN KEY (`organisation_id`) REFERENCES `mst_organisation` (`organisation_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mst_user_head_office` FOREIGN KEY (`head_office_id`) REFERENCES `mst_head_office` (`head_office_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mst_user_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mst_user_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_user_activity_log` (
  `activity_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `activity_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_activity_type` (`activity_type`),
  CONSTRAINT `fk_user_activity_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_system_audit_log` (
  `audit_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `module_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_module_name` (`module_name`),
  CONSTRAINT `fk_system_audit_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_project_support_configuration` (
  `support_config_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `state_id` int DEFAULT NULL,
  `support_group_id` int DEFAULT NULL,
  `support_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`support_config_id`),
  UNIQUE KEY `uq_project_state_config` (`project_id`, `state_id`),
  CONSTRAINT `fk_support_config_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_support_config_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_support_config_group` FOREIGN KEY (`support_group_id`) REFERENCES `mst_support_group` (`support_group_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_issue_routing_rule` (
  `routing_rule_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `state_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `module_id` int DEFAULT NULL,
  `priority_id` int DEFAULT NULL,
  `support_config_id` int DEFAULT NULL,
  `support_group_id` int DEFAULT NULL,
  `support_team_id` int DEFAULT NULL,
  `rule_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rule_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `routing_level` int DEFAULT 1,
  `vendor_id` int DEFAULT NULL,
  `first_level_vendor_ids` text COLLATE utf8mb4_unicode_ci,
  `second_level_vendor_ids` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `priority_score` int DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`routing_rule_id`),
  UNIQUE KEY `uq_rule_code` (`rule_code`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_rule_code` (`rule_code`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_routing_rule_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routing_rule_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_module` FOREIGN KEY (`module_id`) REFERENCES `mst_module` (`module_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_support_config` FOREIGN KEY (`support_config_id`) REFERENCES `mst_project_support_configuration` (`support_config_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_group` FOREIGN KEY (`support_group_id`) REFERENCES `mst_support_group` (`support_group_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_created_by` FOREIGN KEY (`created_by`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. TRANSACTION / ROUTING / SLA / CALENDAR TABLES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `txn_issue` (
  `issue_id` int NOT NULL AUTO_INCREMENT,
  `issue_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `module_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `support_config_id` int DEFAULT NULL,
  `priority_id` int DEFAULT NULL,
  `issue_category_id` int DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `issue_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_description` longtext COLLATE utf8mb4_unicode_ci,
  `raised_by_user_id` int DEFAULT NULL,
  `raised_at` timestamp NULL DEFAULT NULL,
  `occurred_at` timestamp NULL DEFAULT NULL,
  `current_owner_organisation_id` int DEFAULT NULL,
  `current_owner_group_id` int DEFAULT NULL,
  `current_owner_user_id` int DEFAULT NULL,
  `current_owner_role_id` int DEFAULT NULL,
  `current_team_id` int DEFAULT NULL,
  `current_assignee_id` int DEFAULT NULL,
  `resolution_summary` longtext COLLATE utf8mb4_unicode_ci,
  `ho_intervention_required` tinyint(1) DEFAULT 0,
  `ho_working_hours` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stage` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'NEW',
  `current_owner_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_owner_id` int DEFAULT NULL,
  `workflow_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'OPEN',
  `first_level_vendor_ids` text COLLATE utf8mb4_unicode_ci,
  `second_level_vendor_ids` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `resolved_by` int DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `reopened_count` int DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sla_due_at` timestamp NULL DEFAULT NULL,
  `reported_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_by_user_id` int DEFAULT NULL,
  `assigned_to_user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_id`),
  UNIQUE KEY `uq_issue_number` (`issue_number`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_module_id` (`module_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_support_config_id` (`support_config_id`),
  KEY `idx_priority_id` (`priority_id`),
  KEY `idx_status_id` (`status_id`),
  KEY `idx_stage` (`current_stage`),
  KEY `idx_workflow_status` (`workflow_status`),
  KEY `idx_raised_by_user_id` (`raised_by_user_id`),
  KEY `idx_current_owner_group_id` (`current_owner_group_id`),
  KEY `idx_current_owner_user_id` (`current_owner_user_id`),
  KEY `idx_current_assignee_id` (`current_assignee_id`),
  KEY `idx_sla_due_at` (`sla_due_at`),
  CONSTRAINT `fk_txn_issue_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_module` FOREIGN KEY (`module_id`) REFERENCES `mst_module` (`module_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_service` FOREIGN KEY (`service_id`) REFERENCES `mst_service` (`service_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_support_config` FOREIGN KEY (`support_config_id`) REFERENCES `mst_project_support_configuration` (`support_config_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_status` FOREIGN KEY (`status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_raised_by` FOREIGN KEY (`raised_by_user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_group` FOREIGN KEY (`current_owner_group_id`) REFERENCES `mst_support_group` (`support_group_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_owner_user` FOREIGN KEY (`current_owner_user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_assignee` FOREIGN KEY (`current_assignee_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_created_by` FOREIGN KEY (`created_by`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_resolved_by` FOREIGN KEY (`resolved_by`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_created_user` FOREIGN KEY (`created_by_user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_assigned_user` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_issue_status_history` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `vendor_id` int DEFAULT NULL,
  `old_status_id` int DEFAULT NULL,
  `new_status_id` int NOT NULL,
  `changed_by_user_id` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_new_status_id` (`new_status_id`),
  KEY `idx_changed_by` (`changed_by_user_id`),
  KEY `idx_changed_at` (`changed_at`),
  CONSTRAINT `fk_txn_history_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_txn_history_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_history_status` FOREIGN KEY (`new_status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_issue_attachment` (
  `attachment_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `original_file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stored_file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by_user_id` int DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attachment_id`),
  KEY `idx_issue_id` (`issue_id`),
  CONSTRAINT `fk_txn_attachment_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_issue_routing` (
  `routing_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `application_id` int DEFAULT NULL,
  `priority_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `routing_order` int DEFAULT 0,
  `rule_priority` int DEFAULT 0,
  `confidence_score` decimal(5,2) DEFAULT 100.00,
  `is_auto_assign` tinyint(1) DEFAULT 1,
  `requires_approval` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`routing_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_rule_priority` (`rule_priority`),
  KEY `idx_confidence_score` (`confidence_score`),
  CONSTRAINT `fk_routing_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routing_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_category` FOREIGN KEY (`category_id`) REFERENCES `mst_issue_category` (`issue_category_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_issue_routing` (
  `txn_routing_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `routing_id` int DEFAULT NULL,
  `assigned_to_vendor_id` int DEFAULT NULL,
  `vendor_confirmation_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `approved_by_user_id` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `escalation_reason` text COLLATE utf8mb4_unicode_ci,
  `routed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`txn_routing_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_routing_id` (`routing_id`),
  KEY `idx_vendor_id` (`assigned_to_vendor_id`),
  KEY `idx_approved_by_user_id` (`approved_by_user_id`),
  CONSTRAINT `fk_txn_routing_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_txn_routing_routing` FOREIGN KEY (`routing_id`) REFERENCES `mst_issue_routing` (`routing_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_routing_vendor` FOREIGN KEY (`assigned_to_vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_routing_approved_by` FOREIGN KEY (`approved_by_user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_working_calendar` (
  `calendar_id` int NOT NULL AUTO_INCREMENT,
  `calendar_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calendar_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`calendar_id`),
  UNIQUE KEY `uq_calendar_code` (`calendar_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_working_schedule` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `calendar_id` int NOT NULL,
  `day_of_week` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_no` int DEFAULT 1,
  `shift_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sequence_no` int DEFAULT 1,
  `schedule_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `break_start_time` time DEFAULT NULL,
  `break_end_time` time DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`schedule_id`),
  KEY `idx_calendar_id` (`calendar_id`),
  KEY `idx_day_of_week` (`day_of_week`),
  KEY `idx_shift_no` (`shift_no`),
  KEY `idx_sequence_no` (`sequence_no`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_schedule_calendar` FOREIGN KEY (`calendar_id`) REFERENCES `mst_working_calendar` (`calendar_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_calendar_holiday` (
  `holiday_id` int NOT NULL AUTO_INCREMENT,
  `calendar_id` int NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `holiday_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PUBLIC',
  `scope` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'STATE',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`holiday_id`),
  UNIQUE KEY `uq_calendar_holiday` (`calendar_id`, `holiday_date`),
  KEY `idx_calendar_id` (`calendar_id`),
  KEY `idx_holiday_date` (`holiday_date`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_holiday_calendar` FOREIGN KEY (`calendar_id`) REFERENCES `mst_working_calendar` (`calendar_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_sla_policy` (
  `sla_policy_id` int NOT NULL AUTO_INCREMENT,
  `policy_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority_id` int DEFAULT NULL,
  `response_time_minutes` int DEFAULT 0,
  `resolution_time_minutes` int DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sla_policy_id`),
  UNIQUE KEY `uq_policy_code` (`policy_code`),
  KEY `idx_priority_id` (`priority_id`),
  CONSTRAINT `fk_sla_policy_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_sla_configuration` (
  `sla_config_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `state_id` int DEFAULT NULL,
  `support_level` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_minutes` int DEFAULT 0,
  `resolution_time_minutes` int DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sla_config_id`),
  KEY `idx_project_id` (`project_id`),
  CONSTRAINT `fk_sla_config_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sla_config_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_mail_setting` (
  `mail_setting_id` int NOT NULL AUTO_INCREMENT,
  `setting_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `smtp_host` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_port` int DEFAULT 587,
  `smtp_username` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_setting_id`),
  UNIQUE KEY `uq_setting_code` (`setting_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_mail_configuration` (
  `mail_config_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `mail_setting_id` int DEFAULT NULL,
  `mail_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_template` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_config_id`),
  CONSTRAINT `fk_mail_config_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mail_config_setting` FOREIGN KEY (`mail_setting_id`) REFERENCES `mst_mail_setting` (`mail_setting_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_mail_log` (
  `mail_log_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `mail_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'SENT',
  PRIMARY KEY (`mail_log_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_mail_log_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mail_log_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. USER SECURITY TABLES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `txn_password_reset` (
  `reset_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_password_hash` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_password_hash` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `reset_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'MANUAL',
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `confirmed_ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`reset_id`),
  UNIQUE KEY `uq_reset_token` (`reset_token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_reset_status` (`reset_status`),
  CONSTRAINT `fk_pwd_reset_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_otp_management` (
  `otp_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PASSWORD_RESET',
  `otp_purpose` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'EMAIL',
  `delivery_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `failed_attempts` int DEFAULT 0,
  `max_attempts` int DEFAULT 5,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_info` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`otp_id`),
  UNIQUE KEY `uq_otp_code` (`otp_code`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_password_history` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `old_password_hash` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'USER_REQUEST',
  `changed_by_user_id` int DEFAULT NULL,
  `changed_from_ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_pwd_history_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_first_login_tracking` (
  `tracking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temporary_password_sent_at` timestamp NULL DEFAULT NULL,
  `temporary_password_hash` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temporary_password_expires_at` timestamp NULL DEFAULT NULL,
  `setup_completed_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `security_questions_completed_at` timestamp NULL DEFAULT NULL,
  `first_actual_login_at` timestamp NULL DEFAULT NULL,
  `first_login_ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_login_device_info` text COLLATE utf8mb4_unicode_ci,
  `onboarding_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tracking_id`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  CONSTRAINT `fk_first_login_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_login_attempt_log` (
  `attempt_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `login_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'FAILED',
  `failure_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device_info` text COLLATE utf8mb4_unicode_ci,
  `location_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attempt_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_login_id` (`login_id`),
  CONSTRAINT `fk_login_attempt_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_user_session` (
  `session_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `session_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device_info` text COLLATE utf8mb4_unicode_ci,
  `logged_in_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `logged_out_at` timestamp NULL DEFAULT NULL,
  `session_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  `location_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_first_login_of_day` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `uq_session_token` (`session_token`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mst_security_question` (
  `question_id` int NOT NULL AUTO_INCREMENT,
  `question_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PERSONAL',
  `display_order` int DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `txn_user_security_answer` (
  `answer_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer_hash` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`answer_id`),
  UNIQUE KEY `uq_user_question` (`user_id`, `question_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_question_id` (`question_id`),
  CONSTRAINT `fk_user_security_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_security_question` FOREIGN KEY (`question_id`) REFERENCES `mst_security_question` (`question_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. INSERT DEFAULT DATA
-- ============================================================================

INSERT INTO `mst_organisation_type` (`organisation_type_code`, `organisation_type_name`, `description`, `is_active`) VALUES
('HO', 'Head Office', 'Central office', 1),
('STATE', 'State', 'State office', 1),
('DISTRICT', 'District', 'District office', 1),
('VENDOR', 'Vendor', 'External vendor organisation', 1)
ON DUPLICATE KEY UPDATE `organisation_type_name` = VALUES(`organisation_type_name`);

INSERT INTO `mst_organisation` (`organisation_type_id`, `organisation_code`, `organisation_name`, `address`, `email`, `phone`, `is_active`) VALUES
(1, 'EMRI-HO', 'EMRI Head Office', 'Hyderabad, Telangana', 'ho@emri.in', '+91-040-0000000', 1),
(2, 'TS-STATE', 'Telangana State Office', 'Hyderabad, Telangana', 'state@emri.in', '+91-040-1111111', 1),
(2, 'AP-STATE', 'Andhra Pradesh State Office', 'Vijayawada, Andhra Pradesh', 'apstate@emri.in', '+91-0866-2222222', 1)
ON DUPLICATE KEY UPDATE `organisation_name` = VALUES(`organisation_name`);

INSERT INTO `mst_head_office` (`organisation_id`, `head_office_code`, `head_office_name`, `address`, `email`, `phone`, `is_active`) VALUES
(1, 'HO-001', 'EMRI Central HO', 'Hyderabad', 'centralho@emri.in', '+91-040-9999999', 1)
ON DUPLICATE KEY UPDATE `head_office_name` = VALUES(`head_office_name`);

INSERT INTO `mst_state` (`state_code`, `state_name`, `country`, `is_active`) VALUES
('AP', 'Andhra Pradesh', 'India', 1),
('TS', 'Telangana', 'India', 1),
('KA', 'Karnataka', 'India', 1),
('TN', 'Tamil Nadu', 'India', 1),
('MH', 'Maharashtra', 'India', 1),
('DL', 'Delhi', 'India', 1)
ON DUPLICATE KEY UPDATE `state_name` = VALUES(`state_name`);

INSERT INTO `mst_project` (`project_code`, `project_name`, `description`, `is_active`) VALUES
('EMRI', 'Emergency Response Management', 'Main EMRI project', 1),
('DASH', 'EMRI Dashboard', 'Monitoring and dashboard module', 1),
('OPS', 'Operations Support', 'Operations related project', 1)
ON DUPLICATE KEY UPDATE `project_name` = VALUES(`project_name`);

INSERT INTO `mst_application` (`application_code`, `application_name`, `application_type`, `is_active`) VALUES
('WEB', 'Web Application', 'Web', 1),
('MOBILE', 'Mobile Application', 'Mobile', 1),
('API', 'API Services', 'API', 1)
ON DUPLICATE KEY UPDATE `application_name` = VALUES(`application_name`);

INSERT INTO `mst_module` (`module_code`, `module_name`, `description`, `is_active`) VALUES
('ISSUE', 'Issue Management', 'Issue and ticket management', 1),
('USER', 'User Management', 'User account management', 1),
('VENDOR', 'Vendor Management', 'Vendor management', 1),
('REPORT', 'Reports', 'Reports and statistics', 1)
ON DUPLICATE KEY UPDATE `module_name` = VALUES(`module_name`);

INSERT INTO `mst_service` (`service_code`, `service_name`, `description`, `is_active`) VALUES
('AMB', 'Ambulance Service', 'Emergency ambulance service', 1),
('HEL', 'Helpline Support', 'Call center support', 1),
('DIS', 'Dispatch Support', 'Dispatch coordination', 1),
('OPS', 'Operations', 'Overall operations help', 1)
ON DUPLICATE KEY UPDATE `service_name` = VALUES(`service_name`);

INSERT INTO `mst_vendor` (`organisation_id`, `vendor_code`, `vendor_name`, `vendor_category`, `description`, `primary_contact_name`, `primary_contact_email`, `primary_contact_mobile`, `contact_person`, `email`, `mobile_number`, `support_email`, `support_mobile`, `is_active`) VALUES
(1, 'VND001', 'Tech Solutions', 'IT Services', 'Technical support vendor', 'Ravi Kumar', 'ravi@techsolutions.in', '+91-9000000001', 'Ravi Kumar', 'ravi@techsolutions.in', '+91-9000000001', 'support@techsolutions.in', '+91-9000000002', 1),
(1, 'VND002', 'Telecom Partners', 'Telecom', 'Telecom support vendor', 'Sunitha Rao', 'sunitha@telecom.in', '+91-9000000003', 'Sunitha Rao', 'sunitha@telecom.in', '+91-9000000003', 'support@telecom.in', '+91-9000000004', 1),
(1, 'VND003', 'Medical Supply Co', 'Medical', 'Medical equipment vendor', 'Imran Khan', 'imran@medical.in', '+91-9000000005', 'Imran Khan', 'imran@medical.in', '+91-9000000005', 'support@medical.in', '+91-9000000006', 1)
ON DUPLICATE KEY UPDATE `vendor_name` = VALUES(`vendor_name`);

INSERT INTO `mst_issue_status` (`status_code`, `status_name`, `status_category`, `is_closed_status`, `is_resolved`, `display_order`, `is_active`) VALUES
('NEW', 'New', 'INITIAL', 0, 0, 1, 1),
('ASSIGNED', 'Assigned', 'WORKFLOW', 0, 0, 2, 1),
('IN_PROGRESS', 'In Progress', 'WORKFLOW', 0, 0, 3, 1),
('CLARIFICATION', 'Clarification', 'WORKFLOW', 0, 0, 4, 1),
('PENDING', 'Pending', 'WORKFLOW', 0, 0, 5, 1),
('VENDOR_ASSIGNMENT', 'Vendor Assignment', 'WORKFLOW', 0, 0, 6, 1),
('ESCALATE_VENDOR', 'Escalate to Vendor', 'WORKFLOW', 0, 0, 7, 1),
('RESOLVED', 'Resolved', 'TERMINAL', 1, 1, 8, 1),
('CLOSED', 'Closed', 'TERMINAL', 1, 1, 9, 1),
('REJECTED', 'Rejected', 'TERMINAL', 0, 0, 10, 1)
ON DUPLICATE KEY UPDATE `status_name` = VALUES(`status_name`);

INSERT INTO `mst_priority` (`priority_code`, `priority_name`, `priority_level`, `description`, `display_order`, `is_active`) VALUES
('CRITICAL', 'Critical', 1, 'Highest priority', 1, 1),
('HIGH', 'High', 2, 'Very urgent', 2, 1),
('MEDIUM', 'Medium', 3, 'Moderate priority', 3, 1),
('LOW', 'Low', 4, 'Low urgency', 4, 1)
ON DUPLICATE KEY UPDATE `priority_name` = VALUES(`priority_name`);

INSERT INTO `mst_issue_category` (`category_code`, `category_name`, `description`, `is_active`) VALUES
('TECH', 'Technical', 'Technical issue', 1),
('OPS', 'Operational', 'Operations issue', 1),
('DATA', 'Data', 'Data issue', 1),
('VENDOR', 'Vendor', 'Vendor issue', 1)
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

INSERT INTO `mst_support_group` (`organisation_id`, `support_group_code`, `support_group_name`, `support_group_type`, `description`, `is_active`) VALUES
(1, 'HO-TECH', 'Head Office Technical Support', 'TECHNICAL', 'Technical support group', 1),
(1, 'HO-OPS', 'Head Office Operations', 'OPERATIONS', 'Operations support group', 1),
(1, 'HO-IT', 'Head Office IT Support', 'IT', 'IT support group', 1)
ON DUPLICATE KEY UPDATE `support_group_name` = VALUES(`support_group_name`);

INSERT INTO `mst_role` (`role_code`, `role_name`, `role_category`, `description`, `is_system_role`, `is_active`) VALUES
('HO_ADMIN', 'Head Office Admin', 'ADMIN', 'Central admin role', 1, 1),
('STATE_ADMIN', 'State Admin', 'ADMIN', 'State office admin', 0, 1),
('OPERATOR', 'Operator', 'OPERATIONS', 'Operations operator', 0, 1),
('VIEWER', 'Viewer', 'VIEW', 'Read-only user', 0, 1)
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`);

INSERT INTO `mst_menu` (`display_name`, `route_name`, `uri`, `parent_menu_id`, `icon`, `display_order`, `is_active`) VALUES
('Dashboard', 'dashboard', 'dashboard', NULL, 'dashboard', 1, 1),
('Issue Tracker', 'issue-tracker', 'issue-tracker', NULL, 'ticket', 2, 1),
('Raise Issue', 'raise-issue', 'raise-issue', NULL, 'plus', 3, 1),
('Vendor Management', 'vendor-master', 'vendor-master', NULL, 'briefcase', 4, 1),
('User Management', 'user-master', 'user-master', NULL, 'users', 5, 1),
('Reports', 'reports', 'reports', NULL, 'chart', 6, 1),
('Administration', 'administration', 'administration', NULL, 'settings', 7, 1)
ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`);

INSERT INTO `mst_privilege` (`privilege_code`, `privilege_name`, `description`, `is_active`) VALUES
('VIEW', 'View', 'View records', 1),
('ADD', 'Add', 'Add records', 1),
('EDIT', 'Edit', 'Edit records', 1),
('DELETE', 'Delete', 'Delete records', 1),
('ASSIGN', 'Assign', 'Assign issue', 1),
('RESOLVE', 'Resolve', 'Resolve ticket', 1),
('CLOSE', 'Close', 'Close ticket', 1),
('REOPEN', 'Reopen', 'Reopen ticket', 1)
ON DUPLICATE KEY UPDATE `privilege_name` = VALUES(`privilege_name`);

INSERT INTO `map_role_privilege` (`role_id`, `menu_id`, `privilege_id`, `is_active`) VALUES
(1, 1, 1, 1), (1, 1, 2, 1), (1, 1, 3, 1), (1, 1, 4, 1), (1, 1, 5, 1), (1, 1, 6, 1), (1, 1, 7, 1), (1, 1, 8, 1),
(1, 2, 1, 1), (1, 2, 2, 1), (1, 2, 3, 1), (1, 2, 4, 1), (1, 2, 5, 1), (1, 2, 6, 1), (1, 2, 7, 1), (1, 2, 8, 1),
(1, 3, 1, 1), (1, 3, 2, 1), (1, 3, 3, 1), (1, 3, 4, 1), (1, 3, 5, 1), (1, 3, 6, 1), (1, 3, 7, 1), (1, 3, 8, 1),
(1, 4, 1, 1), (1, 4, 2, 1), (1, 4, 3, 1), (1, 4, 4, 1), (1, 4, 5, 1), (1, 4, 6, 1), (1, 4, 7, 1), (1, 4, 8, 1),
(1, 5, 1, 1), (1, 5, 2, 1), (1, 5, 3, 1), (1, 5, 4, 1), (1, 5, 5, 1), (1, 5, 6, 1), (1, 5, 7, 1), (1, 5, 8, 1)
ON DUPLICATE KEY UPDATE `is_active` = VALUES(`is_active`);

INSERT INTO `map_role_issue_status` (`role_id`, `status_id`, `is_active`) VALUES
(1, 1, 1),(1, 2, 1),(1, 3, 1),(1, 4, 1),(1, 5, 1),(1, 6, 1),(1, 7, 1),(1, 8, 1),(1, 9, 1),(1, 10, 1)
ON DUPLICATE KEY UPDATE `is_active` = VALUES(`is_active`);

INSERT INTO `mst_user` (
  `organisation_id`, `head_office_id`, `state_id`, `vendor_id`, `employee_code`, `user_name`, `login_id`, `official_email`, `mobile_number`, `password_hash`, `user_status`, `is_active`, `is_first_login`, `password_force_change`, `department`, `designation`
) VALUES
(1, 1, 2, NULL, 'EMRI-001', 'Central Admin', 'admin', 'admin@example.com', '+91-9999999999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ACTIVE', 1, 1, 1, 'Administration', 'System Administrator')
ON DUPLICATE KEY UPDATE `user_name` = VALUES(`user_name`);

INSERT INTO `mst_project_support_configuration` (`project_id`, `state_id`, `support_group_id`, `support_email`, `support_phone`, `is_active`) VALUES
(1, 2, 1, 'support@emri.in', '+91-040-0001111', 1),
(1, 3, 2, 'support@emri.in', '+91-040-0002222', 1)
ON DUPLICATE KEY UPDATE `support_email` = VALUES(`support_email`);

INSERT INTO `mst_issue_routing_rule` (
  `project_id`, `state_id`, `application_id`, `module_id`, `priority_id`, `support_config_id`, `support_group_id`, `rule_code`, `rule_name`, `issue_category`, `issue_type`, `priority`, `routing_level`, `vendor_id`, `first_level_vendor_ids`, `second_level_vendor_ids`, `is_default`, `is_active`, `created_by`
) VALUES
(1, 2, 1, 1, 1, 1, 1, 'ROUTE_HO_IT_L1', 'Head Office IT Level 1 Routing', 'TECH', 'SYSTEM', 'HIGH', 1, NULL, '1,2', NULL, 0, 1, 1),
(1, 2, 1, 1, 2, 1, 1, 'ROUTE_VENDOR_L2', 'Vendor Level 2 Routing', 'TECH', 'SYSTEM', 'MEDIUM', 2, NULL, NULL, '1,2', 0, 1, 1)
ON DUPLICATE KEY UPDATE `rule_name` = VALUES(`rule_name`);

INSERT INTO `mst_working_calendar` (`calendar_code`, `calendar_name`, `start_date`, `end_date`, `is_active`) VALUES
('CAL-01', 'Head Office Working Calendar', '2026-01-01', '2026-12-31', 1),
('CAL-02', 'Field Working Calendar', '2026-01-01', '2026-12-31', 1)
ON DUPLICATE KEY UPDATE `calendar_name` = VALUES(`calendar_name`);

INSERT INTO `mst_calendar_holiday` (`calendar_id`, `holiday_date`, `holiday_name`, `holiday_type`, `scope`, `is_active`) VALUES
(1, '2026-08-15', 'Independence Day', 'PUBLIC', 'STATE', 1),
(1, '2026-10-02', 'Gandhi Jayanti', 'PUBLIC', 'STATE', 1)
ON DUPLICATE KEY UPDATE `holiday_name` = VALUES(`holiday_name`);

INSERT INTO `mst_working_schedule` (`calendar_id`, `day_of_week`, `shift_no`, `shift_name`, `sequence_no`, `schedule_name`, `start_time`, `end_time`, `break_start_time`, `break_end_time`, `is_active`, `created_by`) VALUES
(1, 'MONDAY', 1, 'Morning', 1, 'HO Morning Shift', '09:00:00', '13:00:00', '11:00:00', '11:30:00', 1, 1),
(1, 'MONDAY', 2, 'Afternoon', 2, 'HO Afternoon Shift', '13:00:00', '17:00:00', '15:00:00', '15:30:00', 1, 1),
(2, 'MONDAY', 1, 'Morning', 1, 'Field Morning Shift', '08:00:00', '16:00:00', '12:00:00', '12:30:00', 1, 1)
ON DUPLICATE KEY UPDATE `schedule_name` = VALUES(`schedule_name`);

INSERT INTO `mst_sla_policy` (`policy_code`, `policy_name`, `priority_id`, `response_time_minutes`, `resolution_time_minutes`, `description`, `is_active`) VALUES
('SLA-CRITICAL', 'Critical SLA', 1, 15, 120, 'Critical response and resolution timeline', 1),
('SLA-HIGH', 'High SLA', 2, 30, 480, 'High response and resolution timeline', 1),
('SLA-MEDIUM', 'Medium SLA', 3, 120, 1440, 'Medium response and resolution timeline', 1)
ON DUPLICATE KEY UPDATE `policy_name` = VALUES(`policy_name`);

INSERT INTO `mst_sla_configuration` (`project_id`, `state_id`, `support_level`, `response_time_minutes`, `resolution_time_minutes`, `is_active`) VALUES
(1, 2, 'PRIMARY', 30, 240, 1),
(1, 3, 'SECONDARY', 60, 480, 1)
ON DUPLICATE KEY UPDATE `support_level` = VALUES(`support_level`);

INSERT INTO `mst_security_question` (`question_text`, `question_category`, `display_order`, `is_active`) VALUES
('What is your mother maiden name?', 'PERSONAL', 1, 1),
('What was the name of your first pet?', 'PERSONAL', 2, 1),
('What is your favorite color?', 'PERSONAL', 3, 1),
('In what city were you born?', 'LOCATION', 4, 1),
('What was the name of your first school?', 'EDUCATION', 5, 1)
ON DUPLICATE KEY UPDATE `question_text` = VALUES(`question_text`);

INSERT INTO `mst_mail_setting` (`setting_code`, `setting_name`, `smtp_host`, `smtp_port`, `smtp_username`, `from_email`, `is_active`) VALUES
('SMTP-01', 'Default Mailer', 'smtp.gmail.com', 587, 'admin@example.com', 'admin@example.com', 1)
ON DUPLICATE KEY UPDATE `setting_name` = VALUES(`setting_name`);

-- ============================================================================
-- 6. SAMPLE ISSUE DATA
-- ============================================================================

INSERT INTO `txn_issue` (
  `issue_number`, `state_id`, `project_id`, `application_id`, `module_id`, `service_id`, `support_config_id`, `priority_id`, `issue_category_id`, `status_id`,
  `issue_title`, `issue_description`, `raised_by_user_id`, `raised_at`, `occurred_at`, `current_owner_group_id`, `current_owner_user_id`, `current_assignee_id`,
  `current_stage`, `workflow_status`, `created_by`, `opened_at`, `sla_due_at`, `is_active`
) VALUES
('ISS-EMRI-0001', 2, 1, 1, 1, 1, 1, 1, 1, 2,
 'Ambulance Dispatch System Down', 'Emergency dispatch system not responding to calls.', 1, NOW(), DATE_SUB(NOW(), INTERVAL 2 HOUR), 1, 1, 1,
 'ASSIGNED', 'OPEN', 1, NOW(), DATE_ADD(NOW(), INTERVAL 4 HOUR), 1),
('ISS-EMRI-0002', 3, 1, 1, 1, 2, 2, 2, 2, 3,
 'Helpline Connectivity Issue', 'Customer calls are disconnecting intermittently.', 1, NOW(), DATE_SUB(NOW(), INTERVAL 5 HOUR), 2, 1, 1,
 'IN_PROGRESS', 'OPEN', 1, NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR), 1)
ON DUPLICATE KEY UPDATE `issue_title` = VALUES(`issue_title`);

INSERT INTO `txn_issue_status_history` (`issue_id`, `old_status_id`, `new_status_id`, `changed_by_user_id`, `comment`, `changed_at`) VALUES
(1, NULL, 1, 1, 'Issue created', NOW()),
(1, 1, 2, 1, 'Assigned to technical support', NOW()),
(2, NULL, 1, 1, 'Issue created', NOW()),
(2, 1, 3, 1, 'Investigation started', NOW())
ON DUPLICATE KEY UPDATE `comment` = VALUES(`comment`);

INSERT INTO `map_issue_vendor_assignment` (`issue_id`, `vendor_id`, `vendor_status_id`, `is_active`, `assigned_at`, `comment`) VALUES
(1, 1, 2, 1, NOW(), 'Primary vendor assigned for investigation'),
(2, 2, 2, 1, NOW(), 'Telecom vendor assigned')
ON DUPLICATE KEY UPDATE `comment` = VALUES(`comment`);

-- ============================================================================
-- 7. FINAL SETTINGS
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- END OF FILE
