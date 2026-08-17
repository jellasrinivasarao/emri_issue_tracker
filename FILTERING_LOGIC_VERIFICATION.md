# Filtering Logic Verification

## State Admin (e.g., Saikumar with state_id=3)

### ✅ All Issues
**Expected SQL:** `SELECT * FROM txn_issue t WHERE t.state_id=3;`

**Code Implementation:** [PageController.php:375-378]
```php
elseif ($isStateAdmin && ! empty($user->state_id)) {
    $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
    if (! empty($stateIds)) {
        $issuesQuery->whereIn('i.state_id', $stateIds);  // WHERE state_id IN (3)
    }
}
```
Then when `status_id='total'` or `status_id='all'`:
```php
if ($requestedStatusValue === 'total' || $requestedStatusValue === 'all') {
    // Keep all applicable issues for the selected role.  // NO ADDITIONAL STATUS FILTER
}
```
**Result:** ✅ MATCHES

---

### ✅ In Progress
**Expected SQL:** `SELECT * FROM txn_issue t WHERE t.state_id=3 AND t.status_id NOT IN (4,3);`

**Code Implementation:** [PageController.php:509-516]
```php
elseif ($requestedStatusValue === 'in_process' || $requestedStatusValue === 'in_progress') {
    // Exclude resolved and closed issues from in-progress view
    if (!empty($resolvedStatusIds)) {           // resolvedStatusIds = [3]
        $issuesQuery->whereNotIn('i.status_id', $resolvedStatusIds);  // NOT IN (3)
    }
    if (!empty($closedStatusIds)) {             // closedStatusIds = [4]
        $issuesQuery->whereNotIn('i.status_id', $closedStatusIds);    // NOT IN (4)
    }
}
```
**Result:** ✅ MATCHES - `WHERE state_id IN (3) AND status_id NOT IN (3,4)`

---

### ✅ Resolved
**Expected SQL:** `SELECT * FROM txn_issue t WHERE t.state_id=3 AND t.status_id=3;`

**Code Implementation:** [PageController.php:517-522]
```php
elseif ($requestedStatusValue === 'resolved') {
    if ($isStateIt) {
        // State IT specific logic
    } elseif ($isHoIt) {
        // HO IT specific logic
    } elseif ($isVendorIt) {
        // Vendor IT specific logic
    } else {
        $issuesQuery->whereIn('i.status_id', $resolvedStatusIds ?: [0]);  // whereIn status_id = [3]
    }
}
```
For State Admin (no special IT logic), executes the `else` clause:
- Base query: `WHERE state_id IN (3)`
- Status filter: `WHERE status_id IN (3)`
**Result:** ✅ MATCHES - `WHERE state_id IN (3) AND status_id = 3`

---

### ✅ Closed
**Expected SQL:** `SELECT * FROM txn_issue t WHERE t.state_id=3 AND t.status_id=4;`

**Code Implementation:** [PageController.php:551-576]
```php
elseif ($requestedStatusValue === 'closed') {
    if ($isStateIt) {
        // State IT specific logic - checks changed_by_user_id
    } elseif ($isHoIt) {
        // HO IT specific logic
    } elseif ($isVendorIt) {
        // Vendor IT specific logic
    } else {
        $issuesQuery->whereIn('i.status_id', $closedStatusIds ?: [0]);  // whereIn status_id = [4]
    }
}
```
For State Admin (no special IT logic), executes the `else` clause:
- Base query: `WHERE state_id IN (3)`
- Status filter: `WHERE status_id IN (4)`
**Result:** ✅ MATCHES - `WHERE state_id IN (3) AND status_id = 4`

---

### ✅ Default View (No Filter Parameter)
**Expected SQL:** `SELECT * FROM txn_issue t WHERE t.state_id=3 AND t.status_id NOT IN (4,3);`

**Code Implementation:** [PageController.php:603-609]
```php
elseif (empty($requestedStatusValue) && ! $request->has('status_id')) {
    // Default view: show only in-progress (non-resolved, non-closed) issues
    if (!empty($resolvedStatusIds)) {
        $issuesQuery->whereNotIn('i.status_id', $resolvedStatusIds);  // NOT IN (3)
    }
    if (!empty($closedStatusIds)) {
        $issuesQuery->whereNotIn('i.status_id', $closedStatusIds);    // NOT IN (4)
    }
}
```
**Result:** ✅ MATCHES - Same as "In Progress" view

---

## State IT (e.g., user_id=2 with state_id=3, raised_by_user_id filtering)

### ✅ Base Query
**Expected:** Issues raised by the current user in their state

**Code Implementation:** [PageController.php:380-386]
```php
elseif ($isStateIt && ! empty($user->state_id)) {
    $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
    if (! empty($stateIds)) {
        $issuesQuery->where(function ($query) use ($stateIds, $user) {
            $query->whereIn('i.state_id', $stateIds)
                  ->where('i.raised_by_user_id', $user->user_id);  // WHERE state_id IN (3) AND raised_by_user_id = 2
        });
    }
}
```
**Result:** ✅ MATCHES - `WHERE state_id IN (3) AND raised_by_user_id = 2`

---

### ✅ State IT - In Progress
**Expected:** `SELECT * FROM txn_issue t WHERE t.state_id=3 AND t.raised_by_user_id=2 AND t.status_id NOT IN (4,3);`

**Result:** ✅ MATCHES - Same in-process filter applies on top of base query

---

### ✅ State IT - Resolved
**Expected:** Issues raised by current user with status = Resolved

**Code Implementation:** [PageController.php:519-522]
```php
if ($isStateIt) {
    $issuesQuery->where('i.raised_by_user_id', $user->user_id)  // ADDS raised_by filter
        ->whereIn('i.status_id', $resolvedStatusIds ?: [0]);     // AND status_id = 3
}
```
**Result:** ✅ MATCHES - `WHERE state_id IN (3) AND raised_by_user_id = 2 AND status_id = 3`

---

### ✅ State IT - Closed
**Expected:** Issues closed by current user in their state

**Code Implementation:** [PageController.php:543-551]
```php
if ($isStateIt) {
    $issuesQuery->whereIn('i.issue_id', function ($query) use ($user) {
        $query->select('issue_id')
            ->from('txn_issue_status_history')
            ->where('changed_by_user_id', $user->user_id)        // User who changed status to closed
            ->whereIn('new_status_id', $closedStatusIds ?: [0]); // Status changed TO closed
    })->where('i.raised_by_user_id', $user->user_id);           // Issue raised by current user
}
```
**Result:** ✅ MATCHES - Issues the current State IT user closed, that they raised, in their state

---

## Summary

| View | State Admin | State IT |
|------|-----------|----------|
| **All** | All issues in their state | All issues they raised in their state |
| **In Progress** | ✅ NOT IN (Resolved, Closed) | ✅ NOT IN (Resolved, Closed) they raised |
| **Resolved** | ✅ status = Resolved | ✅ status = Resolved they raised |
| **Closed** | ✅ status = Closed | ✅ Issues they changed to Closed |

**All filtering logic correctly implemented and verified!**
