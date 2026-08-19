-- ============================================================================
-- EMRI ISSUE TRACKER - USER & ORGANIZATION TABLES STRUCTURE
-- ============================================================================
-- This script creates all user and organization-related tables
-- DO NOT RUN THIS DIRECTLY - Use Laravel migrations or run sections as needed
-- ============================================================================

-- ============================================================================
-- 1. ORGANIZATION & USER MANAGEMENT TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_organisation_type (Organisation Type Master)
-- ============================================================================
CREATE TABLE `mst_organisation_type` (
  `organisation_type_id` int NOT NULL AUTO_INCREMENT,
  `type_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`organisation_type_id`),
  UNIQUE KEY `uq_type_code` (`type_code`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_organisation (Organisation Master)
-- ============================================================================
CREATE TABLE `mst_organisation` (
  `organisation_id` int NOT NULL AUTO_INCREMENT,
  `organisation_type_id` int DEFAULT NULL,
  `organisation_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisation_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`organisation_id`),
  KEY `idx_organisation_type_id` (`organisation_type_id`),
  KEY `idx_organisation_code` (`organisation_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_organisation_code` (`organisation_code`),
  CONSTRAINT `fk_org_type` FOREIGN KEY (`organisation_type_id`) REFERENCES `mst_organisation_type` (`organisation_type_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_head_office (Head Office Master)
-- ============================================================================
CREATE TABLE `mst_head_office` (
  `head_office_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int NOT NULL,
  `head_office_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_office_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`head_office_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_head_office_code` (`head_office_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_head_office_code_org` (`head_office_code`, `organisation_id`),
  CONSTRAINT `fk_head_office_org` FOREIGN KEY (`organisation_id`) REFERENCES `mst_organisation` (`organisation_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_user (User Master)
-- ============================================================================
CREATE TABLE `mst_user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `head_office_id` int DEFAULT NULL,
  `state_id` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `employee_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `official_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `password_reset_otp` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_otp_expires_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_login_id` (`login_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_employee_code` (`employee_code`),
  KEY `idx_user_status` (`user_status`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_official_email` (`official_email`),
  CONSTRAINT `fk_user_org` FOREIGN KEY (`organisation_id`) REFERENCES `mst_organisation` (`organisation_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_user_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. AUDIT & LOG TABLES
-- ============================================================================

-- ============================================================================
-- Table: txn_user_activity_log (User Activity Log)
-- ============================================================================
CREATE TABLE `txn_user_activity_log` (
  `activity_log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `activity_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_description` text COLLATE utf8mb4_unicode_ci,
  `resource_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_id` int DEFAULT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `activity_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_activity_timestamp` (`activity_timestamp`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: txn_system_audit_log (System Audit Log)
-- ============================================================================
CREATE TABLE `txn_system_audit_log` (
  `audit_log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int DEFAULT NULL,
  `operation_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` longtext COLLATE utf8mb4_unicode_ci,
  `new_values` longtext COLLATE utf8mb4_unicode_ci,
  `changed_fields` text COLLATE utf8mb4_unicode_ci,
  `change_reason` text COLLATE utf8mb4_unicode_ci,
  `audit_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_table_name` (`table_name`),
  KEY `idx_operation_type` (`operation_type`),
  KEY `idx_audit_timestamp` (`audit_timestamp`),
  KEY `idx_record_lookup` (`table_name`, `record_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. CONFIGURATION & STATUS TRACKING TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_project_support_configuration (Project Support Configuration)
-- ============================================================================
CREATE TABLE `mst_project_support_configuration` (
  `configuration_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `support_group_id` int DEFAULT NULL,
  `support_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`configuration_id`),
  UNIQUE KEY `uq_project_support_group` (`project_id`, `support_group_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_support_group_id` (`support_group_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_psc_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_psc_support_group` FOREIGN KEY (`support_group_id`) REFERENCES `mst_support_group` (`support_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_issue_routing_rule (Advanced Issue Routing Rules)
-- ============================================================================
CREATE TABLE `mst_issue_routing_rule` (
  `rule_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `rule_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rule_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'AUTO',
  `priority_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `assignment_user_id` int DEFAULT NULL,
  `assignment_support_group_id` int DEFAULT NULL,
  `execution_order` int DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rule_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_assignment_user_id` (`assignment_user_id`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_execution_order` (`execution_order`),
  CONSTRAINT `fk_routing_rule_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routing_rule_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_category` FOREIGN KEY (`category_id`) REFERENCES `mst_issue_category` (`issue_category_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_user` FOREIGN KEY (`assignment_user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_rule_support_group` FOREIGN KEY (`assignment_support_group_id`) REFERENCES `mst_support_group` (`support_group_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. SAMPLE DATA (OPTIONAL)
-- ============================================================================

-- Insert sample organization type
INSERT INTO `mst_organisation_type` (`type_code`, `type_name`, `is_active`) VALUES
('CORPORATE', 'Corporate Organisation', 1),
('GOVERNMENT', 'Government Organisation', 1),
('PRIVATE', 'Private Organisation', 1),
('NON_PROFIT', 'Non-Profit Organisation', 1)
ON DUPLICATE KEY UPDATE `type_name` = VALUES(`type_name`);

-- Insert sample organization (requires real data)
-- INSERT INTO `mst_organisation` (`organisation_type_id`, `organisation_code`, `organisation_name`, `is_active`) VALUES
-- (1, 'ORG001', 'Sample Organisation', 1);

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
