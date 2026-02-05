# Phase 3 Testing Guide

## Quick Testing Checklist

This guide provides step-by-step testing procedures for Phase 3 of the Marketing PIN System.

---

## Prerequisites

Before testing, ensure:
- [ ] Database migrations have been run (marketing_pins table exists)
- [ ] At least one Marketing PIN has been generated via Admin panel (Phase 2)
- [ ] You have access to a member account with varying PIN balances
- [ ] You have access to the registration form via Network Tree

---

## Test Case 1: UI Validation

### Objective: Verify all UI elements are present and working

**Steps:**
1. Login as a member
2. Navigate to Network Tree
3. Click "Add Member" or similar button to open registration form
4. Scroll down to find the new "Marketing PIN (Opsional)" section

**Expected Results:**
- [ ] Marketing PIN section appears after "Informasi Upline"
- [ ] Section has heading "Marketing PIN (Opsional)"
- [ ] Input field has placeholder "sedXXXXX"
- [ ] Helper text is visible: "Format: sedXXXXX (8 karakter)..."
- [ ] Green indicator box is hidden initially
- [ ] PIN balance alert shows hint about Marketing PIN

---

## Test Case 2: JavaScript Auto-Uppercase

### Objective: Verify auto-uppercase functionality

**Steps:**
1. Open registration form
2. Click in the Marketing PIN field
3. Type lowercase: "sed12345"

**Expected Results:**
- [ ] Text automatically converts to uppercase: "SED12345"
- [ ] Cursor position maintained
- [ ] Input field updates in real-time

---

## Test Case 3: Visual Indicator Toggle

### Objective: Verify green indicator shows/hides correctly

**Steps:**
1. Open registration form
2. Type any text in Marketing PIN field (e.g., "TEST1234")
3. Observe the green indicator box
4. Clear the field completely
5. Observe the green indicator box again

**Expected Results:**
- [ ] Green indicator appears when text is entered
- [ ] Green indicator shows checkmark icon and message
- [ ] Message says "Mode Marketing PIN Aktif"
- [ ] PIN balance alert becomes dimmed (opacity-50)
- [ ] Clearing field hides the green indicator
- [ ] PIN balance alert opacity restored

---

## Test Case 4: Button State Changes

### Objective: Verify submit button behavior with marketing PIN

**Assumptions:** Member has 5 PINs in balance

**Steps:**
1. Open registration form
2. Note initial button text: "Register Member (1 PIN)"
3. Enter a marketing PIN code: "SED12345"
4. Observe button text change
5. Clear the marketing PIN field
6. Observe button text again

**Expected Results:**
- [ ] Initial button text: "Register Member (1 PIN)"
- [ ] After entering marketing PIN: "Register Member (Marketing PIN)"
- [ ] After clearing field: back to "Register Member (1 PIN)"
- [ ] Button remains enabled throughout (has PIN balance)

---

## Test Case 5: Button Enable with 0 Balance

### Objective: Verify marketing PIN enables button even with 0 PIN balance

**Assumptions:** Member has 0 PINs in balance

**Steps:**
1. Ensure member has 0 PIN balance
2. Open registration form
3. Note button is disabled and warning shows
4. Enter a valid marketing PIN code
5. Observe button state

**Expected Results:**
- [ ] Initial state: Button disabled (insufficient balance)
- [ ] Warning shown: "Saldo tidak cukup untuk registrasi reguler"
- [ ] After entering marketing PIN: Button becomes enabled
- [ ] Button text changes to "Register Member (Marketing PIN)"
- [ ] PIN balance alert becomes dimmed

---

## Test Case 6: Successful Marketing PIN Registration

### Objective: Complete registration using valid marketing PIN

**Prerequisites:**
- [ ] Have a valid, unused marketing PIN code (get from admin panel)
- [ ] Member can have any PIN balance (even 0)

**Steps:**
1. Login as member
2. Navigate to registration form
3. Fill all required fields:
   - Username: "test_member_1"
   - Name: "Test Member One"
   - Email: "test1@example.com"
   - Password: "password123" (and confirmation)
   - DANA details: any valid values
   - Upline: select any
4. Enter valid marketing PIN code: (your code, e.g., "SED12345")
5. Submit form
6. Note the success message

**Expected Results:**
- [ ] Form submits successfully
- [ ] Success message: "Member baru berhasil didaftarkan menggunakan Marketing PIN!"
- [ ] Redirected to Network Tree
- [ ] New member appears in network
- [ ] Member's PIN balance UNCHANGED (check via dashboard)

**Database Verification:**
```sql
-- Check marketing PIN was marked as used
SELECT code, status, redeemed_by_member_id, used_at 
FROM marketing_pins 
WHERE code = 'SED12345';

-- Expected: status='used', redeemed_by_member_id='test_member_1', used_at is set

-- Check network record
SELECT member_id, is_marketing, sponsor_id, upline_id 
FROM network 
WHERE member_id = 'test_member_1';

-- Expected: is_marketing = 1 (true)

-- Check NO PIN transaction was created
SELECT * FROM pin_transactions 
WHERE target_id = 'test_member_1';

-- Expected: No records (or record with type != 'reedem')

-- Check NO commission distributed
SELECT * FROM wallet_transactions 
WHERE description LIKE '%test_member_1%' 
AND reference_type = 'commission';

-- Expected: No records
```

---

## Test Case 7: Regular Registration (No Marketing PIN)

### Objective: Verify backward compatibility - regular flow still works

**Prerequisites:**
- [ ] Member has at least 1 PIN balance

**Steps:**
1. Login as member
2. Navigate to registration form
3. Fill all required fields (same as Test Case 6)
4. **Leave marketing PIN field EMPTY**
5. Submit form
6. Note the success message

**Expected Results:**
- [ ] Form submits successfully
- [ ] Success message: "Member baru berhasil didaftarkan!" (no mention of marketing PIN)
- [ ] Redirected to Network Tree
- [ ] New member appears in network
- [ ] Member's PIN balance DECREASED by 1 (check via dashboard)

**Database Verification:**
```sql
-- Check network record
SELECT member_id, is_marketing 
FROM network 
WHERE member_id = 'test_member_2';

-- Expected: is_marketing = 0 (false)

-- Check PIN transaction was created
SELECT * FROM pin_transactions 
WHERE target_id = 'test_member_2' 
AND type = 'reedem';

-- Expected: One record, point = -1

-- Check commission was distributed
SELECT * FROM wallet_transactions 
WHERE description LIKE '%test_member_2%' 
AND reference_type = 'commission';

-- Expected: Multiple records (up to 8 levels of upline)
```

---

## Test Case 8: Invalid Marketing PIN - Wrong Length

### Objective: Verify validation for PIN length

**Steps:**
1. Open registration form
2. Fill all required fields
3. Enter marketing PIN with wrong length: "SED123" (6 chars)
4. Submit form

**Expected Results:**
- [ ] Form does NOT submit
- [ ] Error message appears below marketing PIN field
- [ ] Error message: "Kode Marketing PIN harus 8 karakter."
- [ ] All form data retained (check via `old()` values)
- [ ] User stays on form

---

## Test Case 9: Invalid Marketing PIN - Not Found

### Objective: Verify validation for non-existent PIN

**Steps:**
1. Open registration form
2. Fill all required fields
3. Enter marketing PIN that doesn't exist: "SED99999"
4. Submit form

**Expected Results:**
- [ ] Form does NOT submit
- [ ] Flash error message appears at top
- [ ] Error message: "PIN marketing tidak ditemukan"
- [ ] All form data retained
- [ ] User stays on form

---

## Test Case 10: Invalid Marketing PIN - Already Used

### Objective: Verify validation for used PIN

**Prerequisites:**
- [ ] Have a marketing PIN that was already used in Test Case 6

**Steps:**
1. Open registration form
2. Fill all required fields (with different username/email)
3. Enter the already-used marketing PIN: "SED12345"
4. Submit form

**Expected Results:**
- [ ] Form does NOT submit
- [ ] Flash error message appears at top
- [ ] Error message: "PIN marketing sudah digunakan"
- [ ] All form data retained
- [ ] User stays on form

---

## Test Case 11: Invalid Marketing PIN - Expired

### Objective: Verify validation for expired PIN

**Prerequisites:**
- [ ] Have a marketing PIN with expired_at date in the past

**Steps:**
1. Create expired marketing PIN via database:
   ```sql
   UPDATE marketing_pins 
   SET expired_at = '2020-01-01 00:00:00' 
   WHERE code = 'SEDEXPIR';
   ```
2. Open registration form
3. Fill all required fields
4. Enter the expired marketing PIN: "SEDEXPIR"
5. Submit form

**Expected Results:**
- [ ] Form does NOT submit
- [ ] Flash error message appears at top
- [ ] Error message: "PIN marketing sudah expired"
- [ ] All form data retained
- [ ] User stays on form

---

## Test Case 12: Form Data Persistence After Error

### Objective: Verify `old()` helper works for all fields

**Steps:**
1. Open registration form
2. Fill all fields including marketing PIN
3. Intentionally cause validation error (e.g., short password)
4. Submit form
5. Form returns with error

**Expected Results:**
- [ ] All previously entered data is retained:
  - [ ] Username
  - [ ] Name
  - [ ] Email
  - [ ] Phone
  - [ ] DANA name
  - [ ] DANA number
  - [ ] Upline selection
  - [ ] **Marketing PIN code**
- [ ] User doesn't need to re-enter everything
- [ ] Only fix the error and resubmit

---

## Test Case 13: Responsive Design - Mobile

### Objective: Verify form works on mobile devices

**Steps:**
1. Open form on mobile device or use browser dev tools (responsive mode)
2. Set viewport to mobile size (e.g., 375x667)
3. Test all interactions:
   - Scrolling
   - Input fields
   - Marketing PIN field
   - Green indicator
   - Submit button

**Expected Results:**
- [ ] Form is fully responsive
- [ ] All fields stack vertically
- [ ] Marketing PIN section readable
- [ ] Green indicator displays correctly
- [ ] Button is accessible
- [ ] No horizontal scrolling
- [ ] Text is readable

---

## Test Case 14: Responsive Design - Tablet

### Objective: Verify form works on tablet devices

**Steps:**
1. Open form on tablet or use browser dev tools
2. Set viewport to tablet size (e.g., 768x1024)
3. Test all interactions

**Expected Results:**
- [ ] Form layout adapts properly
- [ ] Spacing is appropriate
- [ ] All elements visible and usable
- [ ] Marketing PIN section well-formatted

---

## Test Case 15: Browser Compatibility

### Objective: Verify JavaScript works across browsers

**Test in:**
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Edge

**For each browser:**
- [ ] Auto-uppercase works
- [ ] Indicator toggle works
- [ ] Button state changes work
- [ ] Form submission works

---

## Test Case 16: Security - CSRF Protection

### Objective: Verify CSRF token is required

**Steps:**
1. Open registration form
2. Open browser dev tools
3. Remove or modify CSRF token in form
4. Submit form

**Expected Results:**
- [ ] Form submission rejected
- [ ] 419 error or similar
- [ ] CSRF protection active

---

## Test Case 17: Concurrent Registration Prevention

### Objective: Verify same marketing PIN can't be used twice simultaneously

**Setup:**
- [ ] Open form in two browser tabs
- [ ] Use same marketing PIN in both

**Steps:**
1. In Tab 1: Fill form with marketing PIN "SED12345"
2. In Tab 2: Fill form with same marketing PIN "SED12345"
3. Submit Tab 1 first → should succeed
4. Submit Tab 2 immediately after → should fail

**Expected Results:**
- [ ] Tab 1 submission succeeds
- [ ] Tab 2 submission fails with "PIN marketing sudah digunakan"
- [ ] No race condition issues
- [ ] Transaction isolation works

---

## Performance Tests

### Test Case 18: Form Load Time

**Objective:** Verify additional JavaScript doesn't slow down page

**Steps:**
1. Open browser dev tools (Network tab)
2. Navigate to registration form
3. Measure page load time
4. Check JavaScript execution time

**Expected Results:**
- [ ] Page loads in reasonable time (< 2 seconds on good connection)
- [ ] JavaScript is minimal (~50 lines)
- [ ] No console errors
- [ ] No performance warnings

---

## Accessibility Tests

### Test Case 19: Keyboard Navigation

**Objective:** Verify form is fully keyboard accessible

**Steps:**
1. Open registration form
2. Use only keyboard (Tab, Shift+Tab, Enter, Arrow keys)
3. Navigate through entire form
4. Submit using Enter key

**Expected Results:**
- [ ] Can reach all form fields via Tab
- [ ] Marketing PIN field is in proper tab order
- [ ] Focus indicators visible
- [ ] Can submit form with Enter
- [ ] No keyboard traps

### Test Case 20: Screen Reader Compatibility

**Objective:** Verify form works with screen readers

**Steps:**
1. Enable screen reader (NVDA, JAWS, VoiceOver, etc.)
2. Navigate through form
3. Listen to announcements

**Expected Results:**
- [ ] All labels are announced
- [ ] Marketing PIN field has clear label
- [ ] Helper text is announced
- [ ] Error messages are announced
- [ ] Green indicator message is accessible

---

## Edge Cases

### Test Case 21: Special Characters in Marketing PIN

**Objective:** Verify only alphanumeric characters work

**Steps:**
1. Try entering: "SED@1234", "SED-1234", "SED 1234"
2. Observe behavior

**Expected Results:**
- [ ] Only alphanumeric allowed
- [ ] Special characters either rejected or stripped
- [ ] Validation fails for invalid format

### Test Case 22: Very Long Username/Email with Marketing PIN

**Objective:** Verify no field length conflicts

**Steps:**
1. Enter maximum length values for all fields
2. Add marketing PIN
3. Submit

**Expected Results:**
- [ ] Form validates all field lengths separately
- [ ] No conflicts with marketing PIN field
- [ ] Proper error messages if limits exceeded

---

## Integration Tests

### Test Case 23: End-to-End Flow (Admin → Member → Database)

**Complete Flow:**
1. **Admin:** Generate marketing PIN
   - Go to Admin panel
   - Generate 1 marketing PIN
   - Copy the generated code
2. **Member:** Register new user with marketing PIN
   - Login as member with 0 PIN balance
   - Open registration form
   - Fill all fields
   - Enter marketing PIN
   - Submit
3. **Verification:** Check all systems
   - Member sees success message
   - New user appears in network tree
   - Database shows correct states
   - No PIN deducted from member
   - Marketing PIN marked as used

**Expected Results:**
- [ ] Complete flow works end-to-end
- [ ] All 3 phases integrated correctly
- [ ] Data consistency across all tables

---

## Regression Tests

### Test Case 24: Old Registration Still Works

**Objective:** Ensure we didn't break existing functionality

**Steps:**
1. Perform registration WITHOUT marketing PIN
2. Verify all original features work:
   - PIN deduction
   - Commission distribution
   - Network creation
   - Transaction logging

**Expected Results:**
- [ ] 100% backward compatibility
- [ ] No changes to regular flow
- [ ] All original features intact

---

## Bug Report Template

If you find any issues during testing, use this template:

```markdown
**Test Case:** [Number and Name]
**Severity:** [Critical/High/Medium/Low]

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Result:**
[What should happen]

**Actual Result:**
[What actually happened]

**Screenshots:**
[Attach if UI issue]

**Environment:**
- Browser: 
- OS: 
- Screen size: 
- Member PIN balance: 
- Marketing PIN status: 

**Additional Notes:**
[Any other relevant information]
```

---

## Testing Completion Checklist

Mark off when all tests pass:

### UI Tests
- [ ] Test Case 1: UI Validation
- [ ] Test Case 2: Auto-Uppercase
- [ ] Test Case 3: Visual Indicator
- [ ] Test Case 4: Button State
- [ ] Test Case 5: Button Enable (0 Balance)

### Functional Tests
- [ ] Test Case 6: Marketing PIN Registration
- [ ] Test Case 7: Regular Registration
- [ ] Test Case 8-11: Validation Tests
- [ ] Test Case 12: Form Persistence

### Responsive Tests
- [ ] Test Case 13: Mobile
- [ ] Test Case 14: Tablet

### Cross-Browser Tests
- [ ] Test Case 15: All Browsers

### Security Tests
- [ ] Test Case 16: CSRF
- [ ] Test Case 17: Concurrent Prevention

### Performance Tests
- [ ] Test Case 18: Load Time

### Accessibility Tests
- [ ] Test Case 19: Keyboard Navigation
- [ ] Test Case 20: Screen Reader

### Edge Cases
- [ ] Test Case 21: Special Characters
- [ ] Test Case 22: Long Values

### Integration Tests
- [ ] Test Case 23: End-to-End

### Regression Tests
- [ ] Test Case 24: Old Flow Works

---

**Testing Status:** 🔄 Pending Manual Testing

**Notes:**
- This testing guide covers all Phase 3 requirements
- Run tests in the order presented for best results
- Document any issues found
- Retest after fixes applied

**Estimated Testing Time:** 2-3 hours for complete coverage

---

Happy Testing! 🧪✅
