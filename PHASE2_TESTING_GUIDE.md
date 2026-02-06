# Phase 2 Implementation - Testing Guide

## Overview
Phase 2 has successfully implemented all service layer logic for the WhatsApp Auto Message System. All services are ready for integration with Queue/Jobs/Events in Phase 3.

## Services Implemented

### 1. WhatsappService (Updated)
**File:** `app/Services/WhatsappService.php`

**Features:**
- Dynamic settings from database (api_url, api_key, is_mode_safe)
- `sendText()` - Send WhatsApp messages via Waajo API
- `formatPhoneNumber()` - Format Indonesian phone numbers to international format
- `health()` - Check API health status
- `checkNumber()` - Validate WhatsApp numbers

**Key Methods:**
```php
public function sendText(string $recipientNumber, string $text): array
protected function formatPhoneNumber(string $phone): string
public function health()
public function checkNumber(string $phoneNumber)
```

### 2. WhatsappTemplateService (New)
**File:** `app/Services/WhatsappTemplateService.php`

**Features:**
- Parse template variables ({{name}}, {{amount}}, etc.)
- Get available variables by category
- Validate template variables
- Generate dummy data for previews

**Key Methods:**
```php
public function parseVariables(string $content, array $data): string
public function getAvailableVariables(string $category): array
public function validateTemplate(string $content, array $expectedVariables): array
public function getDummyData(string $category): array
```

**Supported Categories:**
- `member` - 8 variables (name, username, email, phone, sponsor_name, upline_name, join_date, login_url)
- `commission` - 6 variables (name, amount, commission_type, from_member, date, balance)
- `withdrawal` - 9 variables (name, amount, bank_name, account_number, account_name, status, reason, date, admin_name)
- `admin` - 6 variables (member_name, member_phone, member_username, action_type, amount, date)
- `general` - 3 variables (announcement_title, announcement_content, date)

### 3. WhatsappMessageService (New)
**File:** `app/Services/WhatsappMessageService.php`

**Features:**
- Queue messages by template code
- Send messages directly (bypass queue)
- Broadcast to multiple recipients
- Full orchestration of message sending

**Key Methods:**
```php
public function sendByTemplate(string $templateCode, string $phone, array $data = [], array $metadata = []): ?WhatsappLog
public function sendDirect(WhatsappLog $log): bool
public function broadcast(string $templateCode, array $recipients, array $commonData = []): array
```

### 4. WhatsappLogService (New)
**File:** `app/Services/WhatsappLogService.php`

**Features:**
- Manual resend of failed messages
- Bulk resend operations
- Auto retry for scheduler
- Dashboard statistics
- Paginated log retrieval with filters

**Key Methods:**
```php
public function resend(int $logId): bool
public function bulkResend(array $logIds): array
public function retryFailed(): int
public function getStats(string $period = 'today'): array
public function getFailedLogs(array $filters = [], int $perPage = 50)
```

### 5. WhatsappServiceProvider (New)
**File:** `app/Providers/WhatsappServiceProvider.php`

Registers all WhatsApp services as singletons for efficient reuse.

## Testing Results

### Automated Tests (All Passed ✓)
Run: `php tests/manual/test_whatsapp_services.php`

**Tests Performed:**
1. ✓ Phone number formatting (6 test cases)
2. ✓ Template variable parsing
3. ✓ Available variables by category (5 categories)
4. ✓ Template validation
5. ✓ Dummy data generation (5 categories)

**All tests passed successfully!**

## Manual Testing Guide (Phase 3)

After database setup, test using Laravel Tinker:

### Test 1: Phone Formatting
```php
$whatsapp = app(\App\Services\WhatsappService::class);
// Not directly accessible due to protected method
// Will be tested indirectly through sendText()
```

### Test 2: Template Service
```php
$template = app(\App\Services\WhatsappTemplateService::class);

// Test parse variables
$result = $template->parseVariables('Halo {{name}}!', ['name' => 'John']);
// Expected: "Halo John!"

// Test get available variables
$vars = $template->getAvailableVariables('member');
// Expected: array with 8 variables

// Test validate template
$validation = $template->validateTemplate('Halo {{name}}', ['name', 'email']);
// Expected: ['valid' => true, 'used_variables' => ['name'], 'invalid_variables' => []]

// Test dummy data
$dummy = $template->getDummyData('member');
// Expected: array with realistic dummy member data
```

### Test 3: Message Service (Requires Database)
```php
$message = app(\App\Services\WhatsappMessageService::class);

// Test send by template (creates queued log)
$log = $message->sendByTemplate('welcome_new_member', '081234567890', [
    'name' => 'Test User',
    'username' => 'testuser',
    'sponsor_name' => 'Sponsor Name',
    'upline_name' => 'Upline Name',
    'join_date' => now()->format('d-m-Y'),
    'login_url' => url('/login'),
]);
$log->status; // Expected: 'queued'

// Test send direct (requires valid WhatsApp API credentials)
// $result = $message->sendDirect($log);
// Expected: true if sent, false if failed

// Test broadcast
$results = $message->broadcast('commission_received', [
    [
        'phone' => '081234567890',
        'data' => ['name' => 'User 1', 'amount' => 'Rp 100.000'],
    ],
    [
        'phone' => '081234567891',
        'data' => ['name' => 'User 2', 'amount' => 'Rp 200.000'],
    ],
]);
// Expected: array with status for each recipient
```

### Test 4: Log Service (Requires Database)
```php
$logService = app(\App\Services\WhatsappLogService::class);

// Test get stats
$stats = $logService->getStats('today');
// Expected: ['total' => X, 'sent' => Y, 'failed' => Z, 'pending' => W, 'success_rate' => XX.XX]

// Test get failed logs
$failed = $logService->getFailedLogs(['date_from' => today()], 10);
// Expected: LengthAwarePaginator with failed logs

// Test resend (requires existing failed log)
// $result = $logService->resend($logId);
// Expected: true if resent successfully

// Test bulk resend
// $results = $logService->bulkResend([1, 2, 3]);
// Expected: [1 => true, 2 => false, 3 => true]

// Test retry failed
// $count = $logService->retryFailed();
// Expected: count of successfully retried messages
```

## Database Requirements

### Required Settings in whatsapp_settings table:
```sql
INSERT INTO whatsapp_settings (key, value, type, description) VALUES
('api_url', 'https://api.waajo.id/go-omni-v2/public/whatsapp', 'text', 'WhatsApp API base URL'),
('api_key', 'your_api_key_here', 'text', 'WhatsApp API key'),
('is_mode_safe', 'true', 'boolean', 'Safe mode flag'),
('max_retry_attempts', '3', 'number', 'Maximum retry attempts for failed messages');
```

### Required Templates in whatsapp_templates table:
At least one active template for testing, e.g.:
```sql
INSERT INTO whatsapp_templates (code, name, category, content, is_active) VALUES
('welcome_new_member', 'Welcome New Member', 'member', 
'Selamat datang {{name}}! Username: {{username}}. Sponsor: {{sponsor_name}}. Login: {{login_url}}',
true);
```

## Error Handling

All services implement comprehensive error handling:
- **Template not found:** Returns null (graceful failure)
- **API errors:** Logged and returned as failed status
- **Network exceptions:** Caught, logged, and handled gracefully
- **Database errors:** Thrown for critical failures

## Logging Format

All operations log to Laravel logs with consistent format:
```
[WhatsApp] Operation message with context
```

Log levels:
- `info` - Successful operations
- `warning` - Non-critical issues (template not found, validation failed)
- `error` - Critical issues (API errors, exceptions)

## Next Steps (Phase 3)

1. Create Queue Jobs for async message sending
2. Create Events for system-wide notifications
3. Set up Laravel Scheduler for auto-retry
4. Implement Event Listeners to trigger messages
5. Add rate limiting and throttling

## Notes

- All services are registered as singletons via WhatsappServiceProvider
- Services use constructor dependency injection for testability
- All methods have proper PHPDoc comments and type hints
- No breaking changes to existing code
- Ready for Queue/Jobs/Events integration in Phase 3
