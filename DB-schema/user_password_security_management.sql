-- ============================================================================
-- EMRI ISSUE TRACKER - USER PASSWORD & SECURITY MANAGEMENT TABLES
-- ============================================================================
-- This script adds comprehensive password reset, OTP, and first-time login
-- management capabilities to the user management system
-- ============================================================================

-- ============================================================================
-- PART 1: ENHANCE mst_user TABLE WITH ADDITIONAL SECURITY FIELDS
-- ============================================================================

-- Run these ALTER commands AFTER mst_user table is created
-- If fields already exist, they will be skipped

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `is_first_login` TINYINT(1) DEFAULT 1 
AFTER `is_active` COMMENT 'Track if this is user first login (1=first time, 0=has logged in before)';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `password_expires_at` TIMESTAMP NULL DEFAULT NULL 
AFTER `password_changed_at` COMMENT 'Password expiration date (for password policy)';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `failed_login_attempts` INT DEFAULT 0 
AFTER `password_expires_at` COMMENT 'Count of failed login attempts (for account locking)';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `locked_until` TIMESTAMP NULL DEFAULT NULL 
AFTER `failed_login_attempts` COMMENT 'Account locked until this time (after max failed attempts)';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `password_force_change` TINYINT(1) DEFAULT 0 
AFTER `locked_until` COMMENT 'Force user to change password at next login';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `otp_verified_at` TIMESTAMP NULL DEFAULT NULL 
AFTER `password_reset_otp_expires_at` COMMENT 'When OTP was last verified/used';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `otp_attempts` INT DEFAULT 0 
AFTER `otp_verified_at` COMMENT 'Failed OTP verification attempts';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `two_factor_enabled` TINYINT(1) DEFAULT 0 
AFTER `otp_attempts` COMMENT 'Is two-factor authentication enabled for this user';

ALTER TABLE `mst_user` ADD COLUMN IF NOT EXISTS `two_factor_secret` VARCHAR(255) NULL DEFAULT NULL 
AFTER `two_factor_enabled` COMMENT 'Two-factor secret key (if 2FA enabled)';

-- Add missing indexes for new fields
ALTER TABLE `mst_user` ADD INDEX IF NOT EXISTS `idx_is_first_login` (`is_first_login`);
ALTER TABLE `mst_user` ADD INDEX IF NOT EXISTS `idx_password_expires_at` (`password_expires_at`);
ALTER TABLE `mst_user` ADD INDEX IF NOT EXISTS `idx_locked_until` (`locked_until`);
ALTER TABLE `mst_user` ADD INDEX IF NOT EXISTS `idx_password_force_change` (`password_force_change`);
ALTER TABLE `mst_user` ADD INDEX IF NOT EXISTS `idx_two_factor_enabled` (`two_factor_enabled`);

-- ============================================================================
-- PART 2: PASSWORD RESET WORKFLOW TABLE
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
  KEY `idx_requested_at` (`requested_at`),
  KEY `idx_token_expires_at` (`token_expires_at`),
  KEY `idx_user_status_expires` (`user_id`, `reset_status`, `token_expires_at`),
  CONSTRAINT `fk_pwd_reset_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PART 3: OTP MANAGEMENT TABLE
-- ============================================================================

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
  KEY `idx_otp_type` (`otp_type`),
  KEY `idx_verification_status` (`verification_status`),
  KEY `idx_generated_at` (`generated_at`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_user_otp_type` (`user_id`, `otp_type`, `verification_status`),
  CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PART 4: PASSWORD HISTORY TABLE
-- ============================================================================

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
  KEY `idx_changed_at` (`changed_at`),
  KEY `idx_change_reason` (`change_reason`),
  KEY `idx_user_changed_date` (`user_id`, `changed_at`),
  CONSTRAINT `fk_pwd_history_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PART 5: FIRST-TIME LOGIN TRACKING TABLE
-- ============================================================================

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
  KEY `idx_onboarding_status` (`onboarding_status`),
  KEY `idx_temporary_password_expires` (`temporary_password_expires_at`),
  KEY `idx_first_actual_login` (`first_actual_login_at`),
  CONSTRAINT `fk_first_login_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PART 6: LOGIN ATTEMPT TRACKING TABLE
-- ============================================================================

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
  KEY `idx_attempt_status` (`attempt_status`),
  KEY `idx_attempted_at` (`attempted_at`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_user_status_time` (`user_id`, `attempt_status`, `attempted_at`),
  CONSTRAINT `fk_login_attempt_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PART 7: USER SESSION TABLE
-- ============================================================================

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
  KEY `idx_session_status` (`session_status`),
  KEY `idx_logged_in_at` (`logged_in_at`),
  KEY `idx_last_activity` (`last_activity_at`),
  KEY `idx_user_status` (`user_id`, `session_status`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `mst_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PART 8: SECURITY QUESTION & ANSWER TABLE
-- ============================================================================

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

-- ============================================================================
-- PART 9: USER SECURITY ANSWERS TABLE
-- ============================================================================

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
-- PART 10: SAMPLE SECURITY QUESTIONS
-- ============================================================================

INSERT INTO `mst_security_question` (`question_text`, `question_category`, `display_order`, `is_active`) VALUES
('What is your mother maiden name?', 'PERSONAL', 1, 1),
('What was the name of your first pet?', 'PERSONAL', 2, 1),
('What is your favorite color?', 'PERSONAL', 3, 1),
('In what city were you born?', 'LOCATION', 4, 1),
('What is your favorite book?', 'INTEREST', 5, 1),
('What was the name of your first school?', 'EDUCATION', 6, 1),
('What is your favorite movie?', 'INTEREST', 7, 1),
('What street did you first live on?', 'LOCATION', 8, 1),
('What is your favorite music artist?', 'INTEREST', 9, 1),
('What year was your mother born?', 'PERSONAL', 10, 1)
ON DUPLICATE KEY UPDATE `question_text` = VALUES(`question_text`);

-- ============================================================================
-- PART 11: VERIFICATION QUERIES
-- ============================================================================

-- Verify all password management tables created
-- SELECT COUNT(*) as total_tables FROM INFORMATION_SCHEMA.TABLES 
-- WHERE TABLE_SCHEMA = 'emri_issue_tracker' 
-- AND TABLE_NAME IN (
--   'mst_user', 'txn_password_reset', 'txn_otp_management', 
--   'txn_password_history', 'txn_first_login_tracking', 
--   'txn_login_attempt_log', 'txn_user_session', 
--   'mst_security_question', 'txn_user_security_answer'
-- );

-- Check mst_user enhanced fields
-- SELECT 
--   COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = 'emri_issue_tracker' 
-- AND TABLE_NAME = 'mst_user'
-- AND COLUMN_NAME IN (
--   'is_first_login', 'password_expires_at', 'failed_login_attempts',
--   'locked_until', 'password_force_change', 'otp_verified_at',
--   'otp_attempts', 'two_factor_enabled', 'two_factor_secret'
-- );

-- ============================================================================
-- PART 12: STORED PROCEDURES FOR PASSWORD MANAGEMENT
-- ============================================================================

-- Procedure to reset failed login attempts
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS `sp_reset_login_attempts`(IN p_user_id INT)
BEGIN
  UPDATE `mst_user` 
  SET `failed_login_attempts` = 0, 
      `locked_until` = NULL 
  WHERE `user_id` = p_user_id;
END$$

-- Procedure to increment failed login attempts
CREATE PROCEDURE IF NOT EXISTS `sp_increment_login_attempts`(
  IN p_user_id INT,
  IN p_max_attempts INT
)
BEGIN
  DECLARE v_attempts INT;
  
  UPDATE `mst_user` 
  SET `failed_login_attempts` = `failed_login_attempts` + 1 
  WHERE `user_id` = p_user_id;
  
  SELECT `failed_login_attempts` INTO v_attempts 
  FROM `mst_user` 
  WHERE `user_id` = p_user_id;
  
  IF v_attempts >= p_max_attempts THEN
    UPDATE `mst_user` 
    SET `locked_until` = DATE_ADD(NOW(), INTERVAL 30 MINUTE) 
    WHERE `user_id` = p_user_id;
  END IF;
END$$

-- Procedure to mark first login as complete
CREATE PROCEDURE IF NOT EXISTS `sp_complete_first_login`(
  IN p_user_id INT,
  IN p_ip_address VARCHAR(50)
)
BEGIN
  UPDATE `mst_user` 
  SET `is_first_login` = 0, 
      `last_login_at` = NOW()
  WHERE `user_id` = p_user_id;
  
  UPDATE `txn_first_login_tracking` 
  SET `first_actual_login_at` = NOW(),
      `first_login_ip_address` = p_ip_address,
      `onboarding_status` = 'COMPLETED'
  WHERE `user_id` = p_user_id;
END$$

-- Procedure to verify OTP
CREATE PROCEDURE IF NOT EXISTS `sp_verify_otp`(
  IN p_otp_id INT,
  IN p_max_attempts INT,
  OUT p_is_valid TINYINT
)
BEGIN
  DECLARE v_failed_attempts INT;
  DECLARE v_expires_at TIMESTAMP;
  
  SELECT `failed_attempts`, `expires_at` INTO v_failed_attempts, v_expires_at 
  FROM `txn_otp_management` 
  WHERE `otp_id` = p_otp_id;
  
  IF v_failed_attempts >= p_max_attempts THEN
    SET p_is_valid = 0;
  ELSEIF v_expires_at < NOW() THEN
    SET p_is_valid = 0;
  ELSE
    SET p_is_valid = 1;
    UPDATE `txn_otp_management` 
    SET `verification_status` = 'VERIFIED',
        `verified_at` = NOW()
    WHERE `otp_id` = p_otp_id;
  END IF;
END$$

DELIMITER ;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
