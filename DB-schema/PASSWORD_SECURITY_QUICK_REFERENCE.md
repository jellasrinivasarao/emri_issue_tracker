# User Password & Security Management - Quick Reference

## Database Tables at a Glance

### mst_user (Enhanced with Security Fields)

```
Core User Fields:
├─ user_id (INT, PK, AI)
├─ organisation_id
├─ head_office_id
├─ employee_code
├─ user_name
├─ login_id (UNIQUE)
├─ official_email
├─ mobile_number
├─ password_hash

Login & Security Fields:
├─ user_status (Active/Inactive/Suspended)
├─ last_login_at (TIMESTAMP)
├─ password_changed_at (TIMESTAMP)
├─ password_reset_otp (VARCHAR 10)
├─ password_reset_otp_expires_at (TIMESTAMP)
├─ email_verified_at (TIMESTAMP)

✨ NEW First-Time Login Fields:
├─ is_first_login (TINYINT, 1=first time)
├─ password_force_change (TINYINT, 1=must change at login)

✨ NEW Password Policy Fields:
├─ password_expires_at (TIMESTAMP, when password expires)
├─ password_history_days (how many days to remember)

✨ NEW Account Security Fields:
├─ failed_login_attempts (INT, count)
├─ locked_until (TIMESTAMP, account lock time)

✨ NEW OTP Management Fields:
├─ otp_verified_at (TIMESTAMP)
├─ otp_attempts (INT, failed attempts)

✨ NEW 2FA Fields:
├─ two_factor_enabled (TINYINT, 1=enabled)
├─ two_factor_secret (VARCHAR 255, 2FA secret)

Status Fields:
├─ is_active (TINYINT)
├─ created_at (TIMESTAMP)
└─ updated_at (TIMESTAMP)
```

---

## New Tables Created

### 1. txn_password_reset
**Track password reset requests**

```
Columns (Key):
├─ reset_id (PK)
├─ user_id (FK to mst_user)
├─ reset_token (UNIQUE)
├─ reset_status (PENDING|CONFIRMED|EXPIRED|CANCELLED)
├─ reset_type (MANUAL|ADMIN|SYSTEM|EXPIRED_POLICY)
├─ requested_at (TIMESTAMP)
├─ token_expires_at (TIMESTAMP)
├─ confirmed_at (TIMESTAMP)
└─ confirmed_ip_address

Indexes:
├─ PK: reset_id
├─ UNIQUE: reset_token
├─ idx_user_id
├─ idx_reset_status
├─ idx_token_expires_at
└─ idx_user_status_expires (composite)

Use Case:
User clicks "Forgot Password" → New row in txn_password_reset
User clicks email link → Update reset_status to CONFIRMED
System updates mst_user password → Reset complete
```

---

### 2. txn_otp_management
**Handle OTP generation and verification**

```
Columns (Key):
├─ otp_id (PK)
├─ user_id (FK to mst_user)
├─ otp_code (UNIQUE, 6-10 digits)
├─ otp_type (PASSWORD_RESET|2FA|VERIFICATION|EMAIL_CONFIRM)
├─ delivery_method (EMAIL|SMS|APP)
├─ delivery_address (email/phone)
├─ generated_at (TIMESTAMP)
├─ expires_at (TIMESTAMP, usually 5-10 min)
├─ verified_at (TIMESTAMP)
├─ verification_status (PENDING|VERIFIED|FAILED|EXPIRED)
├─ failed_attempts (INT, count)
└─ max_attempts (INT, default 5)

Indexes:
├─ PK: otp_id
├─ UNIQUE: otp_code
├─ idx_user_id
├─ idx_otp_type
├─ idx_verification_status
└─ idx_user_otp_type (composite)

Use Case:
User requests password reset → Generate OTP
System sends OTP via email → Insert in txn_otp_management
User enters OTP → Verify and mark as VERIFIED
User can now change password
```

---

### 3. txn_password_history
**Audit trail of password changes**

```
Columns (Key):
├─ history_id (PK)
├─ user_id (FK to mst_user)
├─ old_password_hash (VARCHAR 500)
├─ change_reason (USER_REQUEST|ADMIN|SYSTEM|EXPIRED_POLICY|RESET)
├─ changed_by_user_id (who made the change)
├─ changed_from_ip_address
└─ changed_at (TIMESTAMP)

Indexes:
├─ PK: history_id
├─ idx_user_id
├─ idx_changed_at
├─ idx_change_reason
└─ idx_user_changed_date (composite)

Use Case:
Track all password changes for compliance
Prevent password reuse (check last 5 passwords)
Audit: who changed, when, and why
```

---

### 4. txn_first_login_tracking
**New user onboarding workflow**

```
Columns (Key):
├─ tracking_id (PK)
├─ user_id (FK, UNIQUE)
├─ temporary_password_hash
├─ temporary_password_expires_at
├─ email_verified_at
├─ phone_verified_at
├─ security_questions_completed_at
├─ first_actual_login_at (TIMESTAMP)
├─ first_login_ip_address
├─ onboarding_status (PENDING|IN_PROGRESS|COMPLETED|CANCELLED)
└─ setup_completed_at (TIMESTAMP)

Indexes:
├─ PK: tracking_id
├─ UNIQUE: user_id
├─ idx_onboarding_status
└─ idx_temporary_password_expires

Workflow:
Step 1: Admin creates user → Auto-create in txn_first_login_tracking
Step 2: Send temp password → Record temp_password_sent_at
Step 3: User verifies email → Record email_verified_at
Step 4: User answers security Q&A → Record security_questions_completed_at
Step 5: User logs in → Record first_actual_login_at
Step 6: Mark is_first_login = 0 in mst_user
```

---

### 5. txn_login_attempt_log
**Audit all login attempts**

```
Columns (Key):
├─ attempt_id (PK)
├─ user_id (FK, NULL if unknown user)
├─ login_id (username)
├─ attempt_status (SUCCESS|FAILED)
├─ failure_reason (INVALID_PASSWORD|USER_LOCKED|USER_INACTIVE|etc)
├─ ip_address
├─ user_agent (browser info)
├─ device_info (device details)
├─ location_info (geographic)
├─ attempted_at (TIMESTAMP)
└─ session_id (if successful)

Indexes:
├─ PK: attempt_id
├─ idx_user_id
├─ idx_login_id
├─ idx_attempt_status
├─ idx_attempted_at
└─ idx_user_status_time (composite)

Use Case:
Log every login attempt
Track failed attempts per user/hour
Detect suspicious login patterns
Geographic anomalies
Device fingerprinting
```

---

### 6. txn_user_session
**Active user session management**

```
Columns (Key):
├─ session_id (PK)
├─ user_id (FK to mst_user)
├─ session_token (UNIQUE, session ID)
├─ ip_address
├─ user_agent
├─ device_info
├─ logged_in_at (TIMESTAMP)
├─ last_activity_at (TIMESTAMP)
├─ logged_out_at (TIMESTAMP, NULL if active)
├─ session_status (ACTIVE|EXPIRED|LOGGED_OUT|TERMINATED)
└─ is_first_login_of_day (TINYINT)

Indexes:
├─ PK: session_id
├─ UNIQUE: session_token
├─ idx_user_id
├─ idx_session_status
└─ idx_user_status (composite)

Use Case:
Track active logins per user
Terminate sessions on logout
Detect inactive sessions (for timeout)
Multi-session/device detection
```

---

### 7. mst_security_question
**Security questions library**

```
Columns:
├─ question_id (PK)
├─ question_text (VARCHAR 255)
├─ question_category (PERSONAL|LOCATION|INTEREST|EDUCATION)
├─ display_order (INT)
└─ is_active (TINYINT)

Sample Questions (10 provided):
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
```

---

### 8. txn_user_security_answer
**User answers to security questions**

```
Columns:
├─ answer_id (PK)
├─ user_id (FK to mst_user)
├─ question_id (FK to mst_security_question)
├─ answer_hash (SHA2 hashed)
├─ created_at
└─ updated_at

Indexes:
├─ PK: answer_id
└─ UNIQUE: (user_id, question_id)

Use Case:
User sets 3 security Q&A during first login
Used for account recovery if email/phone not available
Never store plain text answers
Always hash with SHA2 or bcrypt
```

---

## Quick Implementation Examples

### Example 1: New User Setup

```php
// Step 1: Admin creates user (manually set is_first_login = 1)
$user = User::create([
    'login_id' => 'newuser',
    'user_name' => 'New User',
    'password_hash' => Hash::make('TempPassword123!'),
    'is_first_login' => 1,
    'password_force_change' => 1,
]);

// Step 2: Auto-create first-login tracking
FirstLoginTracking::create([
    'user_id' => $user->id,
    'user_email' => $user->official_email,
    'temporary_password_hash' => $user->password_hash,
    'temporary_password_expires_at' => now()->addDays(7),
    'onboarding_status' => 'PENDING',
]);

// Step 3: Send welcome email with temp password
Mail::send(new WelcomeEmail($user, 'TempPassword123!'));
```

### Example 2: Password Reset Flow

```php
// Step 1: User requests password reset
$resetToken = Str::random(64);
PasswordReset::create([
    'user_id' => $user->id,
    'reset_token' => $resetToken,
    'reset_status' => 'PENDING',
    'token_expires_at' => now()->addHours(24),
]);

// Step 2: Generate and send OTP
$otp = rand(100000, 999999);
OtpManagement::create([
    'user_id' => $user->id,
    'otp_code' => $otp,
    'otp_type' => 'PASSWORD_RESET',
    'delivery_method' => 'EMAIL',
    'delivery_address' => $user->official_email,
    'expires_at' => now()->addMinutes(10),
]);

// Step 3: User verifies OTP
$otp = OtpManagement::where('otp_code', $submittedOtp)
    ->where('user_id', $user->id)
    ->where('verification_status', 'PENDING')
    ->where('expires_at', '>', now())
    ->first();

if ($otp && $otp->failed_attempts < 5) {
    $otp->update([
        'verification_status' => 'VERIFIED',
        'verified_at' => now(),
    ]);
} else {
    $otp->increment('failed_attempts');
    if ($otp->failed_attempts >= 5) {
        $otp->update(['verification_status' => 'EXPIRED']);
    }
}

// Step 4: User sets new password
$user->update(['password_hash' => Hash::make($newPassword)]);
PasswordReset::where('reset_token', $token)
    ->update(['reset_status' => 'CONFIRMED', 'confirmed_at' => now()]);
```

### Example 3: Account Lockout

```php
// Log failed login
LoginAttemptLog::create([
    'login_id' => $loginId,
    'attempt_status' => 'FAILED',
    'failure_reason' => 'INVALID_PASSWORD',
    'ip_address' => request()->ip(),
]);

// Increment failed attempts
$user->increment('failed_login_attempts');

// Lock if max attempts reached (5)
if ($user->failed_login_attempts >= 5) {
    $user->update([
        'locked_until' => now()->addMinutes(30),
    ]);
    throw new AccountLockedException('Account locked for 30 minutes');
}

// Reset attempts on successful login
// When login succeeds:
$user->update(['failed_login_attempts' => 0, 'locked_until' => null]);
```

### Example 4: First Login Completion

```php
// Called after user successfully logs in for first time
function completeFirstLogin($user, $request) {
    // Verify password isn't temp password anymore
    // Verify email
    // Verify security Q&A answers
    // Then mark as complete:
    
    $user->update([
        'is_first_login' => 0,
        'password_force_change' => 0,
    ]);
    
    FirstLoginTracking::where('user_id', $user->id)->update([
        'first_actual_login_at' => now(),
        'first_login_ip_address' => $request->ip(),
        'onboarding_status' => 'COMPLETED',
    ]);
}
```

---

## Field Checklists

### For User Creation (Admin)
```
✓ login_id (unique username)
✓ user_name (full name)
✓ official_email (for reset emails)
✓ mobile_number (for OTP)
✓ password_hash (temporary)
✓ is_first_login = 1
✓ password_force_change = 1
✓ user_status = 'Active'
✓ is_active = 1
```

### For First Login
```
✓ Verify email (-> email_verified_at)
✓ Verify phone (optional, -> phone_verified_at)
✓ Answer security Q&A (-> security_questions_completed_at)
✓ Change temporary password (-> password_hash, password_changed_at)
✓ Set is_first_login = 0
✓ Set password_force_change = 0
```

### For Password Reset
```
✓ Create reset token (-> txn_password_reset)
✓ Generate OTP (-> txn_otp_management)
✓ Send via email
✓ User verifies OTP
✓ User sets new password
✓ Record in password_history
✓ Update mst_user.password_changed_at
```

### For Login Attempt
```
✓ Log in txn_login_attempt_log
✓ Check if account locked (locked_until > NOW())
✓ If password wrong: increment failed_login_attempts
✓ If max attempts: set locked_until
✓ If password correct: reset failed_login_attempts to 0
✓ Create session in txn_user_session
```

---

## Stored Procedures Quick Reference

```sql
-- Reset login attempts after manual unlock
CALL sp_reset_login_attempts(user_id);

-- Lock account after max failed attempts
CALL sp_increment_login_attempts(user_id, max_attempts);

-- Complete first login
CALL sp_complete_first_login(user_id, ip_address);

-- Verify OTP
CALL sp_verify_otp(otp_id, max_attempts, @is_valid);
```

---

## Status Codes Reference

### reset_status
- `PENDING` - Reset token generated, awaiting user action
- `CONFIRMED` - User verified token and set new password
- `EXPIRED` - Token expired without confirmation
- `CANCELLED` - User or admin cancelled the reset

### verification_status (OTP)
- `PENDING` - OTP sent, awaiting verification
- `VERIFIED` - OTP verified successfully
- `FAILED` - Max attempts exceeded
- `EXPIRED` - OTP expired

### onboarding_status
- `PENDING` - User created, awaiting setup
- `IN_PROGRESS` - User starting setup
- `COMPLETED` - All onboarding steps finished
- `CANCELLED` - User cancelled setup

### session_status
- `ACTIVE` - User currently logged in
- `EXPIRED` - Session timeout
- `LOGGED_OUT` - User logged out normally
- `TERMINATED` - Admin terminated session

### attempt_status (Login)
- `SUCCESS` - Login successful
- `FAILED` - Login failed

---

## Summary Statistics Queries

```sql
-- Active users who need to change password
SELECT COUNT(*) FROM mst_user 
WHERE password_force_change = 1;

-- First-time users not yet completed onboarding
SELECT COUNT(*) FROM mst_user u
LEFT JOIN txn_first_login_tracking f ON u.user_id = f.user_id
WHERE u.is_first_login = 1;

-- Locked accounts
SELECT COUNT(*) FROM mst_user 
WHERE locked_until > NOW();

-- Passwords expiring soon (7 days)
SELECT COUNT(*) FROM mst_user 
WHERE password_expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY);

-- Failed login attempts in last hour
SELECT login_id, COUNT(*) FROM txn_login_attempt_log
WHERE attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
  AND attempt_status = 'FAILED'
GROUP BY login_id;

-- Active sessions
SELECT COUNT(*) FROM txn_user_session 
WHERE session_status = 'ACTIVE';
```

---

**Quick Reference Version 1.0**
**For detailed info, see: USER_PASSWORD_SECURITY_MANAGEMENT_GUIDE.md**
