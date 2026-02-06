# WhatsApp Auto Message System - Testing Guide

This comprehensive testing guide covers all aspects of the WhatsApp Auto Message System to ensure production readiness.

---

## 📋 Table of Contents

1. [Pre-Testing Setup](#pre-testing-setup)
2. [Database & Seeder Testing](#database--seeder-testing)
3. [Template Management Testing](#template-management-testing)
4. [Message Sending Testing](#message-sending-testing)
5. [Logging & Monitoring Testing](#logging--monitoring-testing)
6. [Settings & Configuration Testing](#settings--configuration-testing)
7. [Queue Processing Testing](#queue-processing-testing)
8. [Error Handling Testing](#error-handling-testing)
9. [UI/UX Testing](#uiux-testing)
10. [Security Testing](#security-testing)
11. [Performance Testing](#performance-testing)
12. [Integration Testing](#integration-testing)
13. [Mobile Responsiveness Testing](#mobile-responsiveness-testing)
14. [Browser Compatibility Testing](#browser-compatibility-testing)
15. [Load Testing](#load-testing)
16. [Edge Cases Testing](#edge-cases-testing)
17. [Accessibility Testing](#accessibility-testing)
18. [API Testing](#api-testing)
19. [Regression Testing](#regression-testing)
20. [User Acceptance Testing (UAT)](#user-acceptance-testing-uat)

---

## 1. Pre-Testing Setup

### 1.1 Environment Setup

```bash
# Clone repository
git clone <repository-url>
cd sedekah_ai

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database
# Edit .env file with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sedekah_ai
DB_USERNAME=root
DB_PASSWORD=your_password

# Configure WhatsApp API
WHATSAPP_API_URL=https://api.waajo.id/go-omni-v2/public/wa
WHATSAPP_API_KEY=your_actual_api_key

# Configure Queue
QUEUE_CONNECTION=database
```

### 1.2 Database Migration

```bash
# Run migrations
php artisan migrate:fresh

# Seed database with test data
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=MemberSeeder
php artisan db:seed --class=WhatsappSeeder
```

### 1.3 Build Assets

```bash
# Build frontend assets
npm run build

# Or for development with hot reload
npm run dev
```

### 1.4 Start Services

```bash
# Terminal 1: Start application server
php artisan serve

# Terminal 2: Start queue worker
php artisan queue:work --verbose

# Terminal 3: (Optional) Watch logs
tail -f storage/logs/laravel.log
```

---

## 2. Database & Seeder Testing

### 2.1 Verify Seeder Execution

**Test Case:** Run WhatsappSeeder and verify data creation

```bash
php artisan db:seed --class=WhatsappSeeder
```

**Expected Results:**
- ✅ 7 settings created (api_url, api_key, is_mode_safe, etc.)
- ✅ 10 templates created (welcome_new_member, commission_received, etc.)
- ✅ 50 sample logs created (30 sent, 10 failed, 5 pending, 5 queued)
- ✅ Console shows success messages: "✓ Settings seeded", "✓ Templates seeded (10 templates)", "✓ Sample logs seeded (50 logs)"

**Verification:**

```sql
-- Check settings
SELECT COUNT(*) FROM whatsapp_settings; -- Should be 7

-- Check templates
SELECT COUNT(*) FROM whatsapp_templates; -- Should be 10

-- Check logs
SELECT status, COUNT(*) FROM whatsapp_logs GROUP BY status;
-- Expected: sent (30), failed (10), pending (5), queued (5)
```

### 2.2 Verify Template Variables

**Test Case:** Check that templates have correct variables extracted

```sql
SELECT code, name, variables FROM whatsapp_templates;
```

**Expected Results:**
- ✅ Each template should have `variables` column populated with JSON array
- ✅ Variables should match placeholders in content (e.g., `{{name}}`, `{{amount}}`)

---

## 3. Template Management Testing

### 3.1 Template List View

**URL:** `/admin/whatsapp/templates`

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 3.1.1 | Display all templates | Navigate to templates index | All 10 templates displayed in table |
| 3.1.2 | Search by name | Enter "commission" in search | Only commission templates shown |
| 3.1.3 | Filter by category | Select "member" category | Only member templates shown |
| 3.1.4 | Filter by status | Select "active" status | Only active templates shown |
| 3.1.5 | Pagination | If more than 20 templates | Pagination controls visible |

### 3.2 Create Template

**URL:** `/admin/whatsapp/templates/create`

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 3.2.1 | Load create form | Navigate to create page | Form loads with all fields |
| 3.2.2 | Required fields validation | Submit empty form | Validation errors shown |
| 3.2.3 | Create valid template | Fill all fields, submit | Template created, redirect to index |
| 3.2.4 | Duplicate code | Create with existing code | Error: "Code already exists" |
| 3.2.5 | Variable extraction | Include `{{name}}` in content | Variable automatically extracted |
| 3.2.6 | Category selection | Change category dropdown | Available variables update |
| 3.2.7 | Live preview | Type in editor | Preview updates in real-time |
| 3.2.8 | Format buttons | Click **B** (bold) button | Selected text wrapped with `*` |
| 3.2.9 | Insert variable | Click "Insert Variable" > "Name" | `{{name}}` inserted at cursor |
| 3.2.10 | Insert emoji | Click 👋 emoji | Emoji inserted at cursor |
| 3.2.11 | Character count | Type 100 characters | Shows "100 characters" |
| 3.2.12 | Test send | Click "Send Test Message" | Modal opens |

### 3.3 Edit Template

**URL:** `/admin/whatsapp/templates/{id}/edit`

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 3.3.1 | Load edit form | Click edit on template | Form pre-filled with template data |
| 3.3.2 | Update content | Change content, save | Template updated successfully |
| 3.3.3 | Cannot change code | Try to change code field | Code field read-only or validated |
| 3.3.4 | Deactivate template | Uncheck "Active", save | Template status changed to inactive |
| 3.3.5 | Category change | Change category | Available variables update |

### 3.4 Delete Template

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 3.4.1 | Delete unused template | Click delete on template with no pending logs | Template soft-deleted |
| 3.4.2 | Delete template with pending logs | Try to delete template with pending logs | Error: "Cannot delete, logs in progress" |
| 3.4.3 | Confirm deletion | Click delete, confirm dialog | Template deleted, redirect to index |
| 3.4.4 | Cancel deletion | Click delete, cancel dialog | Template not deleted |

### 3.5 Duplicate Template

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 3.5.1 | Duplicate template | Click duplicate button | New template created with "_copy" suffix |
| 3.5.2 | Duplicate is inactive | Check duplicated template | Status is "inactive" by default |
| 3.5.3 | Edit duplicated template | Click edit on duplicate | Redirected to edit form |

---

## 4. Message Sending Testing

### 4.1 Test Send Feature

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 4.1.1 | Open test modal | Click "Send Test Message" | Modal opens |
| 4.1.2 | Send without phone | Leave phone empty, click send | Error: "Nomor HP wajib diisi" |
| 4.1.3 | Send valid test | Enter valid phone, click send | Success toast, message sent |
| 4.1.4 | Check message received | Check WhatsApp on test phone | Message received correctly |
| 4.1.5 | Variables replaced | Check received message | Variables replaced with dummy data |
| 4.1.6 | Formatting applied | Check received message | Bold, italic, etc. applied |

### 4.2 Queue Processing

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 4.2.1 | Queue worker running | Start `php artisan queue:work` | Worker starts without errors |
| 4.2.2 | Process pending log | Create log with status "pending" | Log processed and sent |
| 4.2.3 | Failed job retry | Simulate failed send | Job retries based on settings |
| 4.2.4 | Max retry reached | Let job fail 3 times | Status changed to "failed" |
| 4.2.5 | Message delay | Check send timestamps | Delay respected between messages |

---

## 5. Logging & Monitoring Testing

### 5.1 Logs List View

**URL:** `/admin/whatsapp/logs`

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 5.1.1 | Display all logs | Navigate to logs index | All 50 seeded logs displayed |
| 5.1.2 | Filter by status | Select "sent" status | Only sent logs shown |
| 5.1.3 | Filter by template | Select specific template | Only logs for that template shown |
| 5.1.4 | Filter by date range | Select date from/to | Logs within range shown |
| 5.1.5 | Search by phone | Enter phone number | Logs for that phone shown |
| 5.1.6 | Search by name | Enter recipient name | Logs for that name shown |
| 5.1.7 | View stats | Check stats panel | Shows total, sent, failed, pending, success rate |

### 5.2 Log Detail View

**URL:** `/admin/whatsapp/logs/{id}`

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 5.2.1 | View log detail | Click on log | Detail page shows all information |
| 5.2.2 | View message content | Check content section | Full message displayed |
| 5.2.3 | View metadata | Check metadata section | JSON metadata displayed |
| 5.2.4 | View timeline | Check timeline | Creation, queue, sent/failed events shown |
| 5.2.5 | View error (failed log) | Open failed log | Error message displayed |

### 5.3 Resend Functionality

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 5.3.1 | Resend single failed log | Click "Resend" on failed log | Log re-queued, status "queued" |
| 5.3.2 | Cannot resend sent log | Try to resend sent log | Error: "Only failed messages can be resent" |
| 5.3.3 | Bulk resend | Select multiple failed logs, click "Bulk Resend" | All selected logs re-queued |
| 5.3.4 | Resend without selection | Click "Bulk Resend" without selecting | Error: "Please select logs to resend" |

---

## 6. Settings & Configuration Testing

### 6.1 Settings Page

**URL:** `/admin/whatsapp/settings`

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 6.1.1 | Display settings | Navigate to settings | All 7 settings displayed |
| 6.1.2 | Update API URL | Change API URL, save | Setting updated |
| 6.1.3 | Update API Key | Change API key, save | Setting updated (masked in display) |
| 6.1.4 | Toggle safe mode | Toggle is_mode_safe, save | Setting updated |
| 6.1.5 | Update delay | Change message_delay_seconds, save | Setting updated |
| 6.1.6 | Update retry settings | Change retry settings, save | Settings updated |
| 6.1.7 | Invalid value | Enter negative number for delay | Validation error shown |

### 6.2 Settings Validation

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 6.2.1 | Empty API URL | Clear API URL, save | Error: "API URL is required" |
| 6.2.2 | Invalid URL format | Enter "not-a-url", save | Error: "Invalid URL format" |
| 6.2.3 | Delay too low | Enter 0 for delay, save | Error: "Delay must be at least 1 second" |
| 6.2.4 | Delay too high | Enter 999, save | Error: "Delay cannot exceed X seconds" |

---

## 7. Queue Processing Testing

### 7.1 Queue Worker

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 7.1.1 | Start worker | Run `php artisan queue:work` | Worker starts, processes jobs |
| 7.1.2 | Process job | Add log to queue | Job processed successfully |
| 7.1.3 | Failed job | Simulate API error | Job fails, retries scheduled |
| 7.1.4 | Max retries | Let job fail 3 times | Job marked as failed |
| 7.1.5 | Worker restart | Stop and restart worker | Continues processing |

### 7.2 Job Monitoring

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 7.2.1 | View queue stats | Check `jobs` table | Shows pending jobs |
| 7.2.2 | View failed jobs | Check `failed_jobs` table | Shows failed jobs with details |
| 7.2.3 | Retry failed job | Run `php artisan queue:retry {id}` | Job re-queued |
| 7.2.4 | Flush failed jobs | Run `php artisan queue:flush` | All failed jobs cleared |

---

## 8. Error Handling Testing

### 8.1 API Errors

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 8.1.1 | Invalid API key | Set wrong API key, send message | Error logged, status "failed" |
| 8.1.2 | API timeout | Simulate timeout | Error logged, job retries |
| 8.1.3 | Invalid phone format | Send to "123", not "628..." | Error: "Invalid phone format" |
| 8.1.4 | API rate limit | Send many messages quickly | Delay applied, no rate limit hit |

### 8.2 Database Errors

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 8.2.1 | Connection lost | Stop database during operation | Error caught, logged |
| 8.2.2 | Constraint violation | Try to create duplicate code | Error: "Code already exists" |

### 8.3 File System Errors

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 8.3.1 | Log directory not writable | Change permissions | Error logged to stderr |
| 8.3.2 | Disk full | Fill disk space | Graceful error handling |

---

## 9. UI/UX Testing

### 9.1 Editor Component

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 9.1.1 | Toolbar visible | Open create/edit form | All toolbar buttons visible |
| 9.1.2 | Bold formatting | Select text, click **B** | Text wrapped with `*` |
| 9.1.3 | Italic formatting | Select text, click **I** | Text wrapped with `_` |
| 9.1.4 | Strikethrough | Select text, click **S** | Text wrapped with `~` |
| 9.1.5 | Monospace | Select text, click **M** | Text wrapped with ``` |
| 9.1.6 | Variable dropdown | Click "Insert Variable" | Dropdown shows available variables |
| 9.1.7 | Quick emojis | All emoji buttons | Emojis inserted correctly |

### 9.2 Preview Component

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 9.2.1 | Live preview | Type in editor | Preview updates immediately |
| 9.2.2 | Variable replacement | Type `{{name}}` | Preview shows "John Doe" |
| 9.2.3 | Bold preview | Type `*bold*` | Preview shows **bold** |
| 9.2.4 | Italic preview | Type `_italic_` | Preview shows _italic_ |
| 9.2.5 | Line breaks | Type multiline text | Preview shows line breaks |
| 9.2.6 | WhatsApp bubble style | Check preview container | Styled like WhatsApp message |

### 9.3 Modals

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 9.3.1 | Test send modal open | Click "Send Test Message" | Modal opens |
| 9.3.2 | Modal close (X) | Click X button | Modal closes |
| 9.3.3 | Modal close (overlay) | Click outside modal | Modal closes |
| 9.3.4 | Modal form submission | Fill form, submit | AJAX request sent |
| 9.3.5 | Loading state | Submit form | Loading spinner shown |

### 9.4 Toast Notifications

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 9.4.1 | Success toast | Perform successful action | Green toast appears |
| 9.4.2 | Error toast | Perform failed action | Red toast appears |
| 9.4.3 | Auto-dismiss | Wait 5 seconds | Toast disappears |
| 9.4.4 | Manual dismiss | Click X on toast | Toast disappears |
| 9.4.5 | Multiple toasts | Trigger multiple toasts | All shown in sequence |

---

## 10. Security Testing

### 10.1 Authentication & Authorization

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 10.1.1 | Unauthenticated access | Logout, visit `/admin/whatsapp` | Redirect to login |
| 10.1.2 | Non-admin access | Login as member, visit admin panel | Access denied (403) |
| 10.1.3 | Admin access | Login as admin | Full access granted |

### 10.1.4 CSRF Protection

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 10.1.1 | Form without CSRF | Submit form without token | 419 error |
| 10.1.2 | AJAX without CSRF | Send AJAX without `X-CSRF-TOKEN` | 419 error |
| 10.1.3 | Valid CSRF | Submit with valid token | Request processed |

### 10.3 XSS Protection

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 10.3.1 | Script in template name | Enter `<script>alert(1)</script>` | Escaped, no execution |
| 10.3.2 | Script in content | Enter script in message content | Escaped in preview |
| 10.3.3 | HTML in content | Enter `<b>test</b>` | Escaped, not rendered as HTML |

### 10.4 SQL Injection

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 10.4.1 | Search SQL injection | Enter `' OR '1'='1` in search | No SQL error, no data leak |
| 10.4.2 | Filter SQL injection | Enter malicious filter value | Sanitized, no injection |

### 10.5 Mass Assignment

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 10.5.1 | Modify created_by | Try to set `created_by` via form | Field ignored (not fillable) |
| 10.5.2 | Modify timestamps | Try to set `created_at` via form | Field ignored |

---

## 11. Performance Testing

### 11.1 Page Load Time

**Test Cases:**

| # | Test | Metric | Expected |
|---|------|--------|----------|
| 11.1.1 | Templates index | Page load time | < 500ms |
| 11.1.2 | Template create | Page load time | < 300ms |
| 11.1.3 | Logs index (50 records) | Page load time | < 1s |
| 11.1.4 | Logs index (1000 records) | Page load time | < 2s |

### 11.2 Database Queries

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 11.2.1 | N+1 query check | Enable query log, load templates index | No N+1 queries (uses `with()`) |
| 11.2.2 | Stats query optimization | Load logs index | Stats in single query (uses `selectRaw`) |
| 11.2.3 | Index usage | Check query execution plan | Proper indexes used |

### 11.3 Memory Usage

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 11.3.1 | Queue worker memory | Run worker for 1 hour | Memory stable, no leaks |
| 11.3.2 | Batch processing | Process 1000 logs | Memory doesn't exceed limit |

---

## 12. Integration Testing

### 12.1 Event Triggers

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 12.1.1 | New member registered | Register new member | Welcome message queued |
| 12.1.2 | Commission earned | Add commission to member | Commission message queued |
| 12.1.3 | Withdrawal requested | Create withdrawal request | Withdrawal message queued |
| 12.1.4 | Withdrawal approved | Approve withdrawal | Approval message queued |

### 12.2 MLM System Integration

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 12.2.1 | Member data sync | Check member phone in log | Correct phone used |
| 12.2.2 | Sponsor info | Check welcome message | Sponsor name included |
| 12.2.3 | Commission data | Check commission message | Correct amount shown |

---

## 13. Mobile Responsiveness Testing

### 13.1 Screen Sizes

**Test Cases:**

| Device | Width | Test Result |
|--------|-------|-------------|
| Mobile (Portrait) | 375px | Layout adapts, all features accessible |
| Mobile (Landscape) | 667px | Layout adapts, all features accessible |
| Tablet (Portrait) | 768px | Layout adapts, sidebar toggleable |
| Tablet (Landscape) | 1024px | Layout adapts, sidebar visible |
| Desktop | 1280px+ | Full layout, all features optimal |

### 13.2 Touch Interactions

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 13.2.1 | Button tap | Tap buttons on mobile | Responsive, no delay |
| 13.2.2 | Dropdown open | Tap dropdown on mobile | Opens correctly |
| 13.2.3 | Modal scroll | Open modal on mobile | Scrollable if needed |
| 13.2.4 | Editor input | Type in editor on mobile | Smooth input, no lag |

---

## 14. Browser Compatibility Testing

### 14.1 Desktop Browsers

| Browser | Version | Test Result |
|---------|---------|-------------|
| Chrome | Latest | ✅ Full compatibility |
| Firefox | Latest | ✅ Full compatibility |
| Safari | Latest | ✅ Full compatibility |
| Edge | Latest | ✅ Full compatibility |

### 14.2 Mobile Browsers

| Browser | Platform | Test Result |
|---------|----------|-------------|
| Chrome Mobile | Android | ✅ Full compatibility |
| Safari Mobile | iOS | ✅ Full compatibility |
| Firefox Mobile | Android | ✅ Full compatibility |

---

## 15. Load Testing

### 15.1 Concurrent Users

**Test Cases:**

| # | Users | Duration | Expected Result |
|---|-------|----------|-----------------|
| 15.1.1 | 10 | 5 min | No errors, < 1s response time |
| 15.1.2 | 50 | 10 min | No errors, < 2s response time |
| 15.1.3 | 100 | 15 min | May have some delays, no crashes |

### 15.2 Message Volume

**Test Cases:**

| # | Messages | Duration | Expected Result |
|---|----------|----------|-----------------|
| 15.2.1 | 100 | 1 hour | All processed successfully |
| 15.2.2 | 1000 | 6 hours | All processed, delays respected |
| 15.2.3 | 10000 | 24 hours | Queue stable, all processed |

---

## 16. Edge Cases Testing

### 16.1 Empty Data

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 16.1.1 | No templates | Delete all templates | Index shows empty state |
| 16.1.2 | No logs | Delete all logs | Index shows empty state |
| 16.1.3 | No settings | Delete all settings | Defaults used |

### 16.2 Extreme Values

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 16.2.1 | Very long message | 4000+ characters | Character counter shows warning |
| 16.2.2 | Many variables | 50+ variables in template | All extracted correctly |
| 16.2.3 | Long phone number | 20 digits | Validation error |

### 16.3 Special Characters

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 16.3.1 | Emoji in content | Use various emojis | All displayed correctly |
| 16.3.2 | Unicode characters | Use Arabic, Chinese, etc. | All displayed correctly |
| 16.3.3 | Special symbols | Use ©, ®, ™, etc. | All displayed correctly |

---

## 17. Accessibility Testing

### 17.1 Keyboard Navigation

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 17.1.1 | Tab navigation | Press Tab repeatedly | Focus moves logically |
| 17.1.2 | Form submission | Fill form, press Enter | Form submits |
| 17.1.3 | Modal close | Open modal, press Esc | Modal closes |
| 17.1.4 | Dropdown navigation | Focus dropdown, use arrows | Options navigable |

### 17.2 Screen Reader

**Test Cases:**

| # | Test | Steps | Expected Result |
|---|------|-------|-----------------|
| 17.2.1 | Labels | Use screen reader | All form fields have labels |
| 17.2.2 | Buttons | Use screen reader | All buttons have descriptive text |
| 17.2.3 | Status messages | Trigger toast | Screen reader announces |
| 17.2.4 | Errors | Submit invalid form | Screen reader announces errors |

---

## 18. API Testing

### 18.1 Test Send Endpoint

**Endpoint:** `POST /admin/whatsapp/templates/test-send`

**Test Cases:**

| # | Test | Payload | Expected Status | Expected Response |
|---|------|---------|-----------------|-------------------|
| 18.1.1 | Valid request | `{phone: "628...", content: "Test"}` | 200 | `{success: true}` |
| 18.1.2 | Missing phone | `{content: "Test"}` | 422 | Validation error |
| 18.1.3 | Missing content | `{phone: "628..."}` | 422 | Validation error |
| 18.1.4 | Invalid phone | `{phone: "123", content: "Test"}` | 400 | Error message |

### 18.2 Resend Endpoint

**Endpoint:** `POST /admin/whatsapp/logs/{id}/resend`

**Test Cases:**

| # | Test | Steps | Expected Status | Expected Response |
|---|------|-------|-----------------|-------------------|
| 18.2.1 | Valid resend | Resend failed log | 200 | Redirect with success |
| 18.2.2 | Invalid status | Resend sent log | 400 | Error message |
| 18.2.3 | Non-existent log | Resend ID 99999 | 404 | Not found |

---

## 19. Regression Testing

After each update or bug fix, re-run all critical tests to ensure no features broke:

### Critical Test Checklist

- [ ] Login as admin
- [ ] View templates list
- [ ] Create new template
- [ ] Edit template
- [ ] Delete template
- [ ] Send test message
- [ ] View logs list
- [ ] Resend failed log
- [ ] Update settings
- [ ] Queue worker processes jobs

---

## 20. User Acceptance Testing (UAT)

### Scenario-Based Testing

#### Scenario 1: New Member Welcome

1. Admin creates "welcome_new_member" template
2. Member registers via MLM system
3. Welcome message queued automatically
4. Queue worker processes job
5. Member receives WhatsApp message
6. Admin checks log, status "sent"

**Expected Result:** ✅ New member receives personalized welcome message

#### Scenario 2: Commission Notification

1. Admin creates "commission_received" template
2. Member earns commission
3. Commission message queued
4. Queue worker processes job
5. Member receives notification with amount
6. Admin checks log

**Expected Result:** ✅ Member notified of commission with correct details

#### Scenario 3: Bulk Resend Failed Messages

1. Admin views logs, filters by "failed"
2. Selects multiple failed logs
3. Clicks "Bulk Resend"
4. Confirms action
5. Queue worker processes all
6. Admin checks logs, all marked "sent"

**Expected Result:** ✅ All failed messages resent successfully

---

## 📊 Test Results Template

Use this template to track your test results:

```markdown
## Test Execution Report

**Date:** YYYY-MM-DD  
**Tester:** [Your Name]  
**Environment:** Production / Staging / Local  
**Browser:** Chrome 120 / Firefox 121 / etc.

### Summary
- Total Tests: 200+
- Passed: ✅ XXX
- Failed: ❌ XX
- Skipped: ⚠️ X

### Failed Tests

| Test ID | Test Name | Expected | Actual | Severity | Notes |
|---------|-----------|----------|--------|----------|-------|
| 3.2.4 | Duplicate code validation | Error shown | No error | High | Bug filed #123 |

### Recommendations

1. Fix critical bug in X
2. Improve performance in Y
3. Add validation for Z

### Sign-off

- [ ] All critical tests passed
- [ ] All bugs documented
- [ ] System ready for production

**Approved by:** _________________  
**Date:** _________________
```

---

## 🎯 Production Readiness Checklist

Before deploying to production:

- [ ] All tests passed (200+ test cases)
- [ ] No critical bugs
- [ ] Performance acceptable (<2s page load)
- [ ] Security tests passed
- [ ] API keys configured correctly
- [ ] Queue worker setup (Supervisor)
- [ ] Cron jobs configured
- [ ] Backups configured
- [ ] Monitoring tools in place
- [ ] Documentation complete
- [ ] Team trained
- [ ] UAT sign-off received

---

## 📞 Support

If you encounter any issues during testing:

- **Documentation:** `/docs/DEPLOYMENT.md`
- **Bug Reports:** Create GitHub issue with test case details
- **Questions:** Contact development team

---

**Happy Testing! 🚀**
