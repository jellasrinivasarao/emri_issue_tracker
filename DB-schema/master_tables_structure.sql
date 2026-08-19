-- ============================================================================
-- EMRI ISSUE TRACKER - MASTER TABLES STRUCTURE
-- ============================================================================
-- This script creates all master tables for the EMRI Issue Tracker application
-- DO NOT RUN THIS DIRECTLY - Use Laravel migrations or run sections as needed
-- ============================================================================

-- ============================================================================
-- 1. MASTER DATA TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_state (State Master)
-- ============================================================================
CREATE TABLE `mst_state` (
  `state_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `state_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_short_name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`state_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_state_code` (`state_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_state_code_org` (`state_code`, `organisation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_project (Project Master)
-- ============================================================================
CREATE TABLE `mst_project` (
  `project_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int DEFAULT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `project_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_project_code` (`project_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_project_code` (`project_code`),
  CONSTRAINT `fk_project_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_application (Application Master)
-- ============================================================================
CREATE TABLE `mst_application` (
  `application_id` int NOT NULL AUTO_INCREMENT,
  `application_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  KEY `idx_application_code` (`application_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_application_code` (`application_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_module (Module Master)
-- ============================================================================
CREATE TABLE `mst_module` (
  `module_id` int NOT NULL AUTO_INCREMENT,
  `module_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`module_id`),
  KEY `idx_module_code` (`module_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_module_code` (`module_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_service (Service Master)
-- ============================================================================
CREATE TABLE `mst_service` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `service_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_id`),
  KEY `idx_service_code` (`service_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_service_code` (`service_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_vendor (Vendor Master)
-- ============================================================================
CREATE TABLE `mst_vendor` (
  `vendor_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `vendor_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_contact_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_contact_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_contact_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vendor_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_vendor_code` (`vendor_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_vendor_code_org` (`vendor_code`, `organisation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_issue_status (Issue Status Master)
-- ============================================================================
CREATE TABLE `mst_issue_status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_closed_status` tinyint(1) DEFAULT '0',
  `is_resolved` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_id`),
  KEY `idx_status_code` (`status_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_status_code` (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_priority (Priority Master)
-- ============================================================================
CREATE TABLE `mst_priority` (
  `priority_id` int NOT NULL AUTO_INCREMENT,
  `priority_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority_level` int DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`priority_id`),
  KEY `idx_priority_code` (`priority_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_priority_code` (`priority_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_issue_category (Issue Category Master)
-- ============================================================================
CREATE TABLE `mst_issue_category` (
  `issue_category_id` int NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_category_id`),
  KEY `idx_category_code` (`category_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_category_code` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_support_group (Support Group Master)
-- ============================================================================
CREATE TABLE `mst_support_group` (
  `support_group_id` int NOT NULL AUTO_INCREMENT,
  `organisation_id` int DEFAULT NULL,
  `support_group_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_group_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `support_group_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`support_group_id`),
  KEY `idx_organisation_id` (`organisation_id`),
  KEY `idx_support_group_code` (`support_group_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_support_group_code_org` (`support_group_code`, `organisation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_role (Role Master)
-- ============================================================================
CREATE TABLE `mst_role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system_role` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  KEY `idx_role_code` (`role_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_role_code` (`role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_menu (Menu Master)
-- ============================================================================
CREATE TABLE `mst_menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_menu_id` int DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`),
  KEY `idx_parent_menu_id` (`parent_menu_id`),
  KEY `idx_route_name` (`route_name`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_route_name` (`route_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_privilege (Privilege Master)
-- ============================================================================
CREATE TABLE `mst_privilege` (
  `privilege_id` int NOT NULL AUTO_INCREMENT,
  `privilege_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `privilege_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`privilege_id`),
  KEY `idx_privilege_code` (`privilege_code`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_privilege_code` (`privilege_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. MAPPING TABLES
-- ============================================================================

-- ============================================================================
-- Table: map_project_application (Project-Application Mapping)
-- ============================================================================
CREATE TABLE `map_project_application` (
  `project_application_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `application_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_application_id`),
  UNIQUE KEY `uq_project_application` (`project_id`, `application_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_application_id` (`application_id`),
  CONSTRAINT `fk_map_proj_app_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_proj_app_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_project_application_module (Project-Application-Module Mapping)
-- ============================================================================
CREATE TABLE `map_project_application_module` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `application_id` int NOT NULL,
  `module_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mapping_id`),
  UNIQUE KEY `uq_project_app_module` (`project_id`, `application_id`, `module_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_module_id` (`module_id`),
  CONSTRAINT `fk_map_pam_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_pam_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_pam_module` FOREIGN KEY (`module_id`) REFERENCES `mst_module` (`module_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_project_service (Project-Service Mapping)
-- ============================================================================
CREATE TABLE `map_project_service` (
  `project_service_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `service_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_service_id`),
  UNIQUE KEY `uq_project_service` (`project_id`, `service_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_service_id` (`service_id`),
  CONSTRAINT `fk_map_proj_svc_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_proj_svc_service` FOREIGN KEY (`service_id`) REFERENCES `mst_service` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_project_state (Project-State Mapping)
-- ============================================================================
CREATE TABLE `map_project_state` (
  `project_state_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `state_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_state_id`),
  UNIQUE KEY `uq_project_state` (`project_id`, `state_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_state_id` (`state_id`),
  CONSTRAINT `fk_map_proj_state_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_proj_state_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_vendor_state (Vendor-State Mapping)
-- ============================================================================
CREATE TABLE `map_vendor_state` (
  `vendor_state_id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `state_id` int NOT NULL,
  `project_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vendor_state_id`),
  UNIQUE KEY `uq_vendor_state` (`vendor_id`, `state_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_application_id` (`application_id`),
  CONSTRAINT `fk_map_vendor_state_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_vendor_state_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_role_privilege (Role-Menu-Privilege Mapping)
-- ============================================================================
CREATE TABLE `map_role_privilege` (
  `role_privilege_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `privilege_id` int DEFAULT NULL,
  `is_allowed` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_privilege_id`),
  UNIQUE KEY `uq_role_menu_privilege` (`role_id`, `menu_id`, `privilege_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_menu_id` (`menu_id`),
  KEY `idx_privilege_id` (`privilege_id`),
  CONSTRAINT `fk_map_role_priv_role` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_role_priv_menu` FOREIGN KEY (`menu_id`) REFERENCES `mst_menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_role_priv_privilege` FOREIGN KEY (`privilege_id`) REFERENCES `mst_privilege` (`privilege_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_role_issue_status (Role-Issue Status Mapping)
-- ============================================================================
CREATE TABLE `map_role_issue_status` (
  `role_status_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `status_id` int NOT NULL,
  `is_allowed` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_status_id`),
  UNIQUE KEY `uq_role_status` (`role_id`, `status_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_status_id` (`status_id`),
  CONSTRAINT `fk_map_role_status_role` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_role_status_status` FOREIGN KEY (`status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_user_role (User-Role Mapping)
-- ============================================================================
CREATE TABLE `map_user_role` (
  `user_role_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `uq_user_role` (`user_id`, `role_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role_id` (`role_id`),
  CONSTRAINT `fk_map_user_role_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_user_role_role` FOREIGN KEY (`role_id`) REFERENCES `mst_role` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_user_project (User-Project Mapping)
-- ============================================================================
CREATE TABLE `map_user_project` (
  `user_project_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `project_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_project_id`),
  UNIQUE KEY `uq_user_project` (`user_id`, `project_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_project_id` (`project_id`),
  CONSTRAINT `fk_map_user_proj_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_user_proj_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_user_support_group (User-Support Group Mapping)
-- ============================================================================
CREATE TABLE `map_user_support_group` (
  `user_support_group_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `support_group_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_support_group_id`),
  UNIQUE KEY `uq_user_support_group` (`user_id`, `support_group_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_support_group_id` (`support_group_id`),
  CONSTRAINT `fk_map_user_sg_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_user_sg_support_group` FOREIGN KEY (`support_group_id`) REFERENCES `mst_support_group` (`support_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: map_issue_vendor_assignment (Issue-Vendor Assignment for Multi-Vendor)
-- ============================================================================
CREATE TABLE `map_issue_vendor_assignment` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `vendor_status_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `status_updated_by` int DEFAULT NULL,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `status_remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `uq_issue_vendor` (`issue_id`, `vendor_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_vendor_status_id` (`vendor_status_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_map_issue_vendor_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_issue_vendor_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_map_issue_vendor_status` FOREIGN KEY (`vendor_status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE DATA INSERTS (Optional - for testing)
-- ============================================================================

-- Insert sample statuses for vendor workflow
INSERT INTO `mst_issue_status` (`status_code`, `status_name`, `status_category`, `is_closed_status`, `display_order`, `is_active`) VALUES
('NEW', 'New', 'Open', 0, 1, 1),
('ASSIGNED', 'Assigned', 'Open', 0, 2, 1),
('IN_PROGRESS', 'In Progress', 'Open', 0, 3, 1),
('RESOLVED', 'Resolved', 'Closed', 0, 4, 1),
('CLOSED', 'Closed', 'Closed', 1, 5, 1),
('VENDOR_ASSIGNMENT', 'Vendor Assignment', 'Vendor', 0, 6, 1),
('ESCALATE_TO_VENDOR', 'Escalate to Vendor', 'Vendor', 0, 7, 1),
('REJECTED', 'Rejected', 'Closed', 0, 8, 1),
('CLARIFICATION', 'Clarification', 'Pending', 0, 9, 1)
ON DUPLICATE KEY UPDATE `status_name` = VALUES(`status_name`);

-- Insert sample priorities
INSERT INTO `mst_priority` (`priority_code`, `priority_name`, `priority_level`, `display_order`, `is_active`) VALUES
('CRITICAL', 'Critical', 1, 1, 1),
('HIGH', 'High', 2, 2, 1),
('MEDIUM', 'Medium', 3, 3, 1),
('LOW', 'Low', 4, 4, 1)
ON DUPLICATE KEY UPDATE `priority_name` = VALUES(`priority_name`);

-- Insert sample roles
INSERT INTO `mst_role` (`role_code`, `role_name`, `role_category`, `is_system_role`, `is_active`) VALUES
('HO_ADMIN', 'HO Admin', 'System', 1, 1),
('HO_IT', 'HO IT', 'System', 1, 1),
('VENDOR_ADMIN', 'Vendor Admin', 'Vendor', 0, 1),
('VENDOR_IT', 'Vendor IT', 'Vendor', 0, 1),
('STATE_ADMIN', 'State Admin', 'State', 0, 1),
('STATE_IT', 'State IT', 'State', 0, 1)
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`);

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
