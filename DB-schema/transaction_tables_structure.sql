-- ============================================================================
-- EMRI ISSUE TRACKER - TRANSACTION & OPERATIONAL TABLES STRUCTURE
-- ============================================================================
-- This script creates all transaction and operational tables for the EMRI Issue Tracker
-- DO NOT RUN THIS DIRECTLY - Use Laravel migrations or run sections as needed
-- ============================================================================

-- ============================================================================
-- 1. ISSUE TRANSACTION TABLES
-- ============================================================================

-- ============================================================================
-- Table: txn_issue (Main Issue/Ticket Table)
-- ============================================================================
CREATE TABLE `txn_issue` (
  `issue_id` int NOT NULL AUTO_INCREMENT,
  `issue_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `module_id` int DEFAULT NULL,
  `priority_id` int DEFAULT NULL,
  `issue_category_id` int DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `issue_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_description` longtext COLLATE utf8mb4_unicode_ci,
  `first_level_vendor_ids` text COLLATE utf8mb4_unicode_ci,
  `second_level_vendor_ids` text COLLATE utf8mb4_unicode_ci,
  `created_by_user_id` int DEFAULT NULL,
  `assigned_to_user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_id`),
  UNIQUE KEY `uq_issue_number` (`issue_number`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_priority_id` (`priority_id`),
  KEY `idx_status_id` (`status_id`),
  KEY `idx_created_by` (`created_by_user_id`),
  KEY `idx_assigned_to` (`assigned_to_user_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_status_created` (`status_id`, `created_at`),
  CONSTRAINT `fk_txn_issue_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_issue_status` FOREIGN KEY (`status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: txn_issue_status_history (Issue Status Change History)
-- ============================================================================
CREATE TABLE `txn_issue_status_history` (
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
  KEY `idx_issue_changed_at` (`issue_id`, `changed_at`),
  CONSTRAINT `fk_txn_history_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_txn_history_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_history_status` FOREIGN KEY (`new_status_id`) REFERENCES `mst_issue_status` (`status_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: txn_issue_attachment (Issue Attachments)
-- ============================================================================
CREATE TABLE `txn_issue_attachment` (
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
  KEY `idx_uploaded_at` (`uploaded_at`),
  CONSTRAINT `fk_txn_attachment_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. ISSUE ROUTING & CONFIGURATION TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_issue_routing (Issue Routing Master)
-- ============================================================================
CREATE TABLE `mst_issue_routing` (
  `routing_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `application_id` int DEFAULT NULL,
  `priority_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `routing_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`routing_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_vendor_id` (`vendor_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_routing_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routing_application` FOREIGN KEY (`application_id`) REFERENCES `mst_application` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_category` FOREIGN KEY (`category_id`) REFERENCES `mst_issue_category` (`issue_category_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routing_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: txn_issue_routing (Issue Routing Transaction - Auto-assignment)
-- ============================================================================
CREATE TABLE `txn_issue_routing` (
  `txn_routing_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `routing_id` int DEFAULT NULL,
  `assigned_to_vendor_id` int DEFAULT NULL,
  `vendor_confirmation_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `routed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`txn_routing_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_routing_id` (`routing_id`),
  KEY `idx_vendor_id` (`assigned_to_vendor_id`),
  KEY `idx_routed_at` (`routed_at`),
  CONSTRAINT `fk_txn_routing_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_txn_routing_routing` FOREIGN KEY (`routing_id`) REFERENCES `mst_issue_routing` (`routing_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_txn_routing_vendor` FOREIGN KEY (`assigned_to_vendor_id`) REFERENCES `mst_vendor` (`vendor_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. SLA & CONFIGURATION TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_sla_policy (SLA Policy Master)
-- ============================================================================
CREATE TABLE `mst_sla_policy` (
  `sla_policy_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `priority_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `response_time_hours` int DEFAULT '4',
  `resolution_time_hours` int DEFAULT '24',
  `escalation_threshold_hours` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sla_policy_id`),
  UNIQUE KEY `uq_project_priority_category` (`project_id`, `priority_id`, `category_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_priority_id` (`priority_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_sla_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sla_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sla_category` FOREIGN KEY (`category_id`) REFERENCES `mst_issue_category` (`issue_category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_sla_configuration (SLA Configuration)
-- ============================================================================
CREATE TABLE `mst_sla_configuration` (
  `configuration_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int NOT NULL,
  `project_id` int NOT NULL,
  `priority_id` int NOT NULL,
  `response_time_hours` int DEFAULT '4',
  `resolution_time_hours` int DEFAULT '24',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`configuration_id`),
  UNIQUE KEY `uq_state_project_priority` (`state_id`, `project_id`, `priority_id`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_priority_id` (`priority_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_slacfg_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_slacfg_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_slacfg_priority` FOREIGN KEY (`priority_id`) REFERENCES `mst_priority` (`priority_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. WORKING CALENDAR & SCHEDULE TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_working_calendar (Working Calendar Master)
-- ============================================================================
CREATE TABLE `mst_working_calendar` (
  `calendar_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int DEFAULT NULL,
  `calendar_year` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`calendar_id`),
  UNIQUE KEY `uq_state_year` (`state_id`, `calendar_year`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_calendar_year` (`calendar_year`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_calendar_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: txn_calendar_holiday (Calendar Holiday Transactions)
-- ============================================================================
CREATE TABLE `txn_calendar_holiday` (
  `holiday_id` int NOT NULL AUTO_INCREMENT,
  `calendar_id` int NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `holiday_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'National',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`holiday_id`),
  UNIQUE KEY `uq_calendar_date` (`calendar_id`, `holiday_date`),
  KEY `idx_calendar_id` (`calendar_id`),
  KEY `idx_holiday_date` (`holiday_date`),
  CONSTRAINT `fk_holiday_calendar` FOREIGN KEY (`calendar_id`) REFERENCES `mst_working_calendar` (`calendar_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_working_schedule (Working Schedule Master)
-- ============================================================================
CREATE TABLE `mst_working_schedule` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int DEFAULT NULL,
  `day_of_week` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_working_day` tinyint(1) DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `uq_state_day_of_week` (`state_id`, `day_of_week`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_day_of_week` (`day_of_week`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_schedule_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. MAIL & NOTIFICATION TABLES
-- ============================================================================

-- ============================================================================
-- Table: mst_mail_setting (Mail Server Configuration)
-- ============================================================================
CREATE TABLE `mst_mail_setting` (
  `mail_setting_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int DEFAULT '587',
  `encryption` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'tls',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_setting_id`),
  KEY `idx_is_active` (`is_active`),
  UNIQUE KEY `uq_mail_setting_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: mst_mail_configuration (Mail Configuration by State/Project)
-- ============================================================================
CREATE TABLE `mst_mail_configuration` (
  `mail_configuration_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `mail_setting_id` int DEFAULT NULL,
  `to_address` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bcc_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'NOTIFICATIONS',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_configuration_id`),
  KEY `idx_state_id` (`state_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_mail_setting_id` (`mail_setting_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_mailcfg_state` FOREIGN KEY (`state_id`) REFERENCES `mst_state` (`state_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mailcfg_project` FOREIGN KEY (`project_id`) REFERENCES `mst_project` (`project_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mailcfg_setting` FOREIGN KEY (`mail_setting_id`) REFERENCES `mst_mail_setting` (`mail_setting_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Table: txn_mail_log (Mail Transaction Log)
-- ============================================================================
CREATE TABLE `txn_mail_log` (
  `mail_log_id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int DEFAULT NULL,
  `to_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_log_id`),
  KEY `idx_issue_id` (`issue_id`),
  KEY `idx_sent_at` (`sent_at`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_maillog_issue` FOREIGN KEY (`issue_id`) REFERENCES `txn_issue` (`issue_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
