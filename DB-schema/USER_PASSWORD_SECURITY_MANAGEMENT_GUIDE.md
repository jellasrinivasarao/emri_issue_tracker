# EMRI Issue Tracker - User Password & Security Management System

## Overview

Complete password management, OTP handling, first-time login tracking, and security management system for the EMRI Issue Tracker.

---

## Database Tables Overview

### Summary Table
| Table Name | Type | Purpose | Rows |
|---|---|---|---|
| **mst_user** | Master | User master with security fields (Enhanced) | N/A |
| **txn_password_reset** | Transaction | Password reset workflows | N/A |
| **txn_otp_management** | Transaction | OTP generation and verification | N/A |
| **txn_password_history** | Transaction | Password change audit trail | N/A |
| **txn_first_login_tracking** | Transaction | First-time login workflow | N/A |
| **txn_login_attempt_log** | Transaction | Login attempt auditing | N/A |
| **txn_user_session** | Transaction | Active user sessions | N/A |
| **mst_security_question** | Master | Security questions for account recovery | 10 (sample) |
| **txn_user_security_answer** | Transaction | User security question answers | N/A |

---

## Part 1: Enhanced mst_user Table (Existing Table)

### New Security Fields Added

| Field Name | Type | Purpose | Default |
|---|---|---|---|
| `is_first_login` | TINYINT(1) | Track if user has logged in before | 1 |
| `password_expires_at` | TIMESTAMP | Password expiration date | NULL |
| `failed_login_attempts` | INT | Count of failed login attempts | 0 |
| `locked_until` | TIMESTAMP | Account lock expiration time | NULL |
| `password_force_change` | TINYINT(1) | Force password change at next login | 0 |
| `otp_verified_at` | TIMESTAMP | When OTP was last verified | NULL |
| `otp_attempts` | INT | Failed OTP verification attempts | 0 |
| `two_factor_enabled` | TINYINT(1) | Is 2FA enabled? | 0 |
| `two_factor_secret` | VARCHAR(255) | 2FA secret key | NULL |

### Enhanced mst_user SQL (Sample Row)

```sql
INSERT INTO `mst_user` (
    `employee_code`, `user_name`, `login_id`, `official_email`, 
    `mobile_number`, `password_hash`, `user_status`, `is_active`,
    `is_first_login`, `password_expires_at`, `password_force_change`
) VALUES (
    'NEWUSER001', 
    'New User Name', 
    'newuser', 
    'newuser@example.com', 
    '+91-9876543210',
    '$2y$10$...bcrypt_hash...',  -- Temporary password hash
    'Active', 
    1,
    1,  -- is_first_login = TRUE (first time)
    DATE_ADD(NOW(), INTERVAL 90 DAY),  -- Password expires in 90 days
    1   -- password_force_change = TRUE (must change on first login)
);
```

---

## Part 2: Password Reset Workflow

### txn_password_reset Table

**Purpose:** Track all password reset requests and confirmations

**Fields:**
```
reset_id                 - Unique reset request ID
user_id                  - User requesting password reset
reset_token              - Unique token sent via email
old_password_hash        - Previous password (for audit)
new_password_hash        - New password (after confirmation)
reset_status             - PENDING | CONFIRMED | EXPIRED | CANCELLED
reset_type               - MANUAL | ADMIN | SYSTEM | EXPIRED_POLICY
requested_at             - When reset was requested
token_expires_at         - When reset token expires (usually 24 hours)
confirmed_at             - When reset was confirmed
confirmed_ip_address     - IP address when confirmed
reset_reason             - Why reset was needed
created_by               - Admin who initiated reset (if admin-triggered)
```

**Usage Example:**

```sql
-- Step 1: User requests password reset
INSERT INTO txn_password_reset (user_id, reset_token, reset_status, reset_type, token_expires_at)
VALUES (
    1,
    'abc123def456ghi789jkl',
    'PENDING',
    'MANUAL',
    DATE_ADD(NOW(), INTERVAL 24 HOUR)
);

-- Step 2: User verifies email link and provides new password
UPDATE txn_password_reset
SET reset_status = 'CONFIRMED',
    new_password_hash = '$2y$10$...new_hash...',
    confirmed_at = NOW(),
    confirmed_ip_address = '192.168.1.1'
WHERE reset_token = 'abc123def456ghi789jkl';

-- Step 3: Application confirms and updates mst_user
UPDATE mst_user
SET password_hash = '$2y$10$...new_hash...',
    password_changed_at = NOW()
WHERE user_id = 1;
```

---

## Part 3: OTP Management

### txn_otp_management Table

**Purpose:** Generate, track, and verify one-time passwords

**Fields:**
```
otp_id                   - Unique OTP ID
user_id                  - User receiving OTP
otp_code                 - 6-10 digit OTP code
otp_type                 - PASSWORD_RESET | 2FA | VERIFICATION | EMAIL_CONFIRM
delivery_method          - EMAIL | SMS | APP
delivery_address         - Email/Phone where OTP was sent
generated_at             - When OTP was created
expires_at               - When OTP expires (typically 5-10 minutes)
verified_at              - When OTP was successfully verified
verification_status      - PENDING | VERIFIED | FAILED | EXPIRED
failed_attempts          - Number of wrong OTP entries
max_attempts             - Maximum allowed attempts (default 5)
ip_address               - IP where OTP was used
device_info              - Device details
```

**Usage Example:**

```sql
-- Step 1: Generate OTP for password reset
INSERT INTO txn_otp_management (
    user_id, otp_code, otp_type, delivery_method, 
    delivery_address, expires_at
) VALUES (
    1,
    '123456',
    'PASSWORD_RESET',
    'EMAIL',
    'user@example.com',
    DATE_ADD(NOW(), INTERVAL 10 MINUTE)
);

-- Step 2: User enters OTP - Verify it
SELECT * FROM txn_otp_management
WHERE user_id = 1 
  AND otp_code = '123456'
  AND verification_status = 'PENDING'
  AND expires_at > NOW();

-- Step 3: Mark OTP as verified
UPDATE txn_otp_management
SET verification_status = 'VERIFIED',
    verified_at = NOW()
WHERE otp_id = 1;

-- Step 4: Count failed OTP attempts
UPDATE txn_otp_management
SET failed_attempts = failed_attempts + 1
WHERE otp_id = 1;

-- Step 5: If max attempts reached, mark as expired
UPDATE txn_otp_management
SET verification_status = 'EXPIRED'
WHERE otp_id = 1 AND failed_attempts >= max_attempts;
```

---

## Part 4: First-Time Login Tracking

### txn_first_login_tracking Table

**Purpose:** Track onboarding workflow for new users

**Fields:**
```
tracking_id                    - Unique tracking ID
user_id                        - New user
user_email                     - User email
user_phone                     - User phone
temporary_password_sent_at     - When temp password sent
temporary_password_hash        - Temp password (for security)
temporary_password_expires_at  - Temp password expiration
setup_completed_at             - When all setup completed
email_verified_at              - When email was verified
phone_verified_at              - When phone was verified
security_questions_completed_at - When security Q&A setup
first_actual_login_at          - First actual login timestamp
first_login_ip_address         - IP of first login
first_login_device_info        - Device used for first login
onboarding_status              - PENDING | IN_PROGRESS | COMPLETED | CANCELLED
```

**First-Time Login Workflow:**

```sql
-- Step 1: Admin creates new user
INSERT INTO mst_user (...) VALUES (...);
-- Automatically insert into first_login_tracking:
INSERT INTO txn_first_login_tracking (user_id, user_email, onboarding_status)
SELECT user_id, official_email, 'PENDING' FROM mst_user WHERE user_id = NEW.user_id;

-- Step 2: Admin sends temporary password
UPDATE txn_first_login_tracking
SET temporary_password_hash = '$2y$10$...',
    temporary_password_sent_at = NOW(),
    temporary_password_expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY)
WHERE user_id = 1;

-- Step 3: User verifies email
UPDATE txn_first_login_tracking
SET email_verified_at = NOW()
WHERE user_id = 1;

-- Step 4: User completes security questions
UPDATE txn_first_login_tracking
SET security_questions_completed_at = NOW()
WHERE user_id = 1;

-- Step 5: User logs in for first time
UPDATE txn_first_login_tracking
SET first_actual_login_at = NOW(),
    first_login_ip_address = '192.168.1.1',
    onboarding_status = 'COMPLETED'
WHERE user_id = 1;

-- Step 6: Clear first_login flag
UPDATE mst_user
SET is_first_login = 0
WHERE user_id = 1;
```

---

## Part 5: Login Attempt Tracking

### txn_login_attempt_log Table

**Purpose:** Audit all login attempts (successful and failed)

**Fields:**
```
attempt_id       - Unique attempt ID
user_id          - User attempting login (NULL if unknown user)
login_id         - Username entered
attempt_status   - SUCCESS | FAILED
failure_reason   - INVALID_PASSWORD | USER_LOCKED | USER_INACTIVE | etc
ip_address       - IP address of attempt
user_agent       - Browser/device info
device_info      - Device details
location_info    - Geographic location (if available)
attempted_at     - When login was attempted
session_id       - Session ID if successful
```

**Usage Example:**

```sql
-- Log failed login attempt
INSERT INTO txn_login_attempt_log (
    login_id, attempt_status, failure_reason, ip_address, attempted_at
) VALUES (
    'admin',
    'FAILED',
    'INVALID_PASSWORD',
    '192.168.1.100',
    NOW()
);

-- Log successful login
INSERT INTO txn_login_attempt_log (
    user_id, login_id, attempt_status, ip_address, session_id, attempted_at
) VALUES (
    1,
    'admin',
    'SUCCESS',
    '192.168.1.100',
    'sess_abc123def456',
    NOW()
);

-- Check recent failed attempts (for account locking)
SELECT COUNT(*) as failed_attempts
FROM txn_login_attempt_log
WHERE login_id = 'admin'
  AND attempt_status = 'FAILED'
  AND attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## Part 6: User Session Management

### txn_user_session Table

**Purpose:** Track active user sessions

**Fields:**
```
session_id         - Unique session ID
user_id            - Logged-in user
session_token      - Session token/cookie
ip_address         - Session IP
user_agent         - Device info
device_info        - Detailed device info
logged_in_at       - Session start time
last_activity_at   - Last activity time
logged_out_at      - Session end time (if logged out)
session_status     - ACTIVE | EXPIRED | LOGGED_OUT | TERMINATED
location_info      - Geographic location
is_first_login_of_day - Is this first login today?
```

**Usage Example:**

```sql
-- Create new session on login
INSERT INTO txn_user_session (
    user_id, session_token, ip_address, logged_in_at, session_status
) VALUES (
    1,
    'session_abc123def456',
    '192.168.1.100',
    NOW(),
    'ACTIVE'
);

-- Update last activity
UPDATE txn_user_session
SET last_activity_at = NOW()
WHERE session_token = 'session_abc123def456' AND session_status = 'ACTIVE';

-- Logout
UPDATE txn_user_session
SET session_status = 'LOGGED_OUT',
    logged_out_at = NOW()
WHERE session_token = 'session_abc123def456';

-- Find active sessions for user
SELECT * FROM txn_user_session
WHERE user_id = 1 AND session_status = 'ACTIVE';
```

---

## Part 7: Security Questions

### mst_security_question Table

**Purpose:** Store security questions for account recovery

**Predefined Questions:**
1. What is your mother maiden name?
2. What was the name of your first pet?
3. What is your favorite color?
4. In what city were you born?
5. What is your favorite book?
6. What was the name of your first school?
7. What is your favorite movie?
8. What street did you first live on?
9. What is your favorite music artist?
10. What year was your mother born?

### txn_user_security_answer Table

**Purpose:** Store user answers to security questions

**Fields:**
```
answer_id          - Unique answer ID
user_id            - User
question_id        - Security question
answer_hash        - Hashed answer (never store plain text)
updated_at         - Last update time
created_at         - Creation time
```

**Usage Example:**

```sql
-- User sets security answers during first login
INSERT INTO txn_user_security_answer (user_id, question_id, answer_hash)
VALUES 
    (1, 1, SHA2('smith', 256)),      -- Mother's maiden name: Smith
    (1, 2, SHA2('fluffy', 256)),     -- First pet name: Fluffy
    (1, 4, SHA2('newyork', 256));    -- Birth city: New York

-- Verify security answer for account recovery
SELECT * FROM txn_user_security_answer
WHERE user_id = 1 
  AND question_id = 1
  AND answer_hash = SHA2('smith', 256);  -- Correct answer
```

---

## Part 8: Database Relationships

```
mst_user (1) ──┬─── (M) txn_password_reset
               ├─── (M) txn_otp_management
               ├─── (M) txn_password_history
               ├─── (M) txn_first_login_tracking
               ├─── (M) txn_login_attempt_log
               ├─── (M) txn_user_session
               └─── (M) txn_user_security_answer

mst_security_question (1) ──── (M) txn_user_security_answer
```

---

## Part 9: Common Workflows

### Workflow 1: New User Onboarding

```
1. Admin creates user in mst_user
   ├─ is_first_login = 1
   ├─ password_force_change = 1
   └─ Automatic entry in txn_first_login_tracking

2. System sends temporary password via email
   └─ txn_first_login_tracking.temporary_password_sent_at = NOW()

3. User receives email and logs in for first time
   ├─ txn_login_attempt_log logs attempt
   ├─ Temporary password verified
   └─ Application enforces password change

4. User changes password
   ├─ Update mst_user.password_hash
   ├─ Insert old hash into txn_password_history
   ├─ Set password_force_change = 0
   └─ Set is_first_login = 0

5. User completes profile setup
   ├─ Verify email → txn_first_login_tracking.email_verified_at
   ├─ Answer security questions → txn_user_security_answer
   └─ Set onboarding_status = COMPLETED
```

### Workflow 2: Password Reset

```
1. User requests password reset
   └─ INSERT into txn_password_reset with reset_token

2. System generates OTP
   └─ INSERT into txn_otp_management with 6-digit code

3. Send reset link + OTP to email
   └─ txn_otp_management.delivery_address

4. User clicks link and enters OTP
   ├─ Verify OTP not expired
   ├─ Verify attempt count < max_attempts
   ├─ UPDATE txn_otp_management.verification_status = VERIFIED
   └─ Allow password change

5. User enters new password
   ├─ UPDATE mst_user.password_hash
   ├─ INSERT into txn_password_history
   ├─ UPDATE txn_password_reset.reset_status = CONFIRMED
   └─ Log in txn_login_attempt_log
```

### Workflow 3: Account Lockout After Failed Attempts

```
1. User fails login attempt
   ├─ UPDATE mst_user.failed_login_attempts += 1
   └─ INSERT into txn_login_attempt_log with failure reason

2. Check if max attempts reached
   └─ IF failed_attempts >= 5

3. Lock account
   ├─ UPDATE mst_user.locked_until = NOW() + 30 MINUTES
   └─ INSERT into txn_login_attempt_log with ACCOUNT_LOCKED

4. User cannot login until locked_until expires

5. Admin can manually reset attempts
   ├─ UPDATE mst_user.failed_login_attempts = 0
   └─ UPDATE mst_user.locked_until = NULL
```

### Workflow 4: Two-Factor Authentication (2FA)

```
1. User enables 2FA
   ├─ UPDATE mst_user.two_factor_enabled = 1
   ├─ Generate and store secret key
   └─ UPDATE mst_user.two_factor_secret = '...'

2. User logs in
   └─ Password verification succeeds

3. System requests 2FA code
   ├─ Generate OTP
   ├─ INSERT into txn_otp_management
   └─ otp_type = '2FA'

4. User enters 2FA code
   ├─ Verify OTP
   ├─ Check not expired
   └─ Allow login if valid

5. Create user session
   └─ INSERT into txn_user_session
```

---

## Part 10: Stored Procedures

### sp_reset_login_attempts(user_id)
Clear failed login attempts and unlock account

```sql
CALL sp_reset_login_attempts(1);
```

### sp_increment_login_attempts(user_id, max_attempts)
Increment failed attempts and lock if max reached

```sql
CALL sp_increment_login_attempts(1, 5);
```

### sp_complete_first_login(user_id, ip_address)
Mark first login complete

```sql
CALL sp_complete_first_login(1, '192.168.1.1');
```

### sp_verify_otp(otp_id, max_attempts, is_valid)
Verify OTP validity

```sql
CALL sp_verify_otp(1, 5, @is_valid);
SELECT @is_valid;  -- Returns 0 or 1
```

---

## Part 11: Verification Queries

### Check all tables created
```sql
SELECT COUNT(*) as total_tables FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'emri_issue_tracker' 
AND TABLE_NAME IN (
  'mst_user', 'txn_password_reset', 'txn_otp_management', 
  'txn_password_history', 'txn_first_login_tracking', 
  'txn_login_attempt_log', 'txn_user_session', 
  'mst_security_question', 'txn_user_security_answer'
);
-- Expected: 9 tables
```

### Check user's first login status
```sql
SELECT user_id, login_id, is_first_login, password_force_change
FROM mst_user 
WHERE is_first_login = 1;
```

### Check locked accounts
```sql
SELECT user_id, login_id, locked_until, failed_login_attempts
FROM mst_user
WHERE locked_until > NOW();
```

### Check password expiry
```sql
SELECT user_id, login_id, password_expires_at
FROM mst_user
WHERE password_expires_at < DATE_ADD(NOW(), INTERVAL 7 DAY)
  AND password_expires_at > NOW();
-- Users whose password expires within 7 days
```

### Check recent login attempts
```sql
SELECT user_id, attempt_status, COUNT(*) as count
FROM txn_login_attempt_log
WHERE attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY user_id, attempt_status;
```

### Check active sessions
```sql
SELECT COUNT(*) as active_sessions
FROM txn_user_session
WHERE session_status = 'ACTIVE';
```

---

## Part 12: Execution Order

```
1. Ensure master_tables_structure.sql has been executed
2. Ensure user_organization_tables_structure.sql has been executed
3. Run user_password_security_management.sql
   - Alters mst_user table
   - Creates all new tables
   - Inserts sample security questions
   - Creates stored procedures
```

---

## Part 13: Security Best Practices

✅ **Always hash passwords** - Never store plain text
✅ **Hash OTP answers** - Security answers should be hashed
✅ **Use strong tokens** - Reset tokens must be cryptographically secure
✅ **Expire tokens quickly** - Reset tokens expire in 24 hours
✅ **Limit OTP attempts** - Max 5 attempts before lockout
✅ **Account lockout** - 30-minute lockout after 5 failed attempts
✅ **Audit everything** - Log all login attempts and password changes
✅ **SSL/TLS** - Use HTTPS for all password/OTP operations
✅ **Session security** - Store session tokens securely
✅ **2FA support** - Enable two-factor authentication

---

## Files Provided

| File | Purpose | Size |
|------|---------|------|
| user_password_security_management.sql | Complete SQL scripts | 450+ lines |
| USER_PASSWORD_SECURITY_MANAGEMENT_GUIDE.md | This guide | 600+ lines |

---

**Last Updated:** 2026-08-18
**Version:** 1.0
**Compatibility:** MySQL 5.7+, Laravel 10+
