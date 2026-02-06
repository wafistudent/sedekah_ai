<?php

/**
 * Quick test script for WhatsApp services
 * Tests the core functionality without database
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test 1: WhatsappService - formatPhoneNumber
echo "=== Test 1: WhatsappService - formatPhoneNumber ===\n";

// Create a mock WhatsappService for testing formatPhoneNumber without DB
// We'll test it directly with a temporary instance
class TestWhatsappService extends \App\Services\WhatsappService
{
    public function __construct()
    {
        // Skip parent constructor to avoid DB calls
    }
    
    public function testFormatPhoneNumber(string $phone): string
    {
        return $this->formatPhoneNumber($phone);
    }
}

$whatsappService = new TestWhatsappService();

$testCases = [
    '0812-3456-7890' => '6281234567890',
    '62812 3456 7890' => '6281234567890',
    '+62 812-3456-7890' => '6281234567890',
    '81234567890' => '6281234567890',
    '0812345678' => '62812345678',
    '+628123456789' => '628123456789',
];

$allPassed = true;
foreach ($testCases as $input => $expected) {
    $result = $whatsappService->testFormatPhoneNumber($input);
    $passed = $result === $expected;
    $allPassed = $allPassed && $passed;
    $status = $passed ? '✓' : '✗';
    echo "{$status} Input: '{$input}' => Output: '{$result}' (Expected: '{$expected}')\n";
}

// Test 2: WhatsappTemplateService - parseVariables
echo "\n=== Test 2: WhatsappTemplateService - parseVariables ===\n";
$templateService = new \App\Services\WhatsappTemplateService();

$content = "Halo {{name}}! Komisi Anda: {{amount}}. Terima kasih {{unknown_var}}!";
$data = [
    'name' => 'John Doe',
    'amount' => 'Rp 100.000'
];

$result = $templateService->parseVariables($content, $data);
$expected = "Halo John Doe! Komisi Anda: Rp 100.000. Terima kasih !";
$passed = $result === $expected;
$allPassed = $allPassed && $passed;
$status = $passed ? '✓' : '✗';

echo "Input: {$content}\n";
echo "Data: " . json_encode($data) . "\n";
echo "{$status} Result: {$result}\n";
echo "Expected: {$expected}\n";

// Test 3: WhatsappTemplateService - getAvailableVariables
echo "\n=== Test 3: WhatsappTemplateService - getAvailableVariables ===\n";
$categories = ['member', 'commission', 'withdrawal', 'admin', 'general'];
$expectedCounts = [
    'member' => 8,
    'commission' => 6,
    'withdrawal' => 9,
    'admin' => 6,
    'general' => 3,
];

foreach ($categories as $category) {
    $vars = $templateService->getAvailableVariables($category);
    $count = count($vars);
    $expected = $expectedCounts[$category];
    $passed = $count === $expected;
    $allPassed = $allPassed && $passed;
    $status = $passed ? '✓' : '✗';
    echo "{$status} {$category}: {$count} variables (expected: {$expected})\n";
}

// Test 4: WhatsappTemplateService - validateTemplate
echo "\n=== Test 4: WhatsappTemplateService - validateTemplate ===\n";
$content = "Halo {{name}}! Username: {{username}}. Data: {{invalid_var}}";
$expectedVars = ['name', 'username', 'email'];
$validation = $templateService->validateTemplate($content, $expectedVars);

$passed = !$validation['valid'] && 
          count($validation['used_variables']) === 3 && 
          in_array('invalid_var', $validation['invalid_variables']);
$allPassed = $allPassed && $passed;
$status = $passed ? '✓' : '✗';

echo "Content: {$content}\n";
echo "Expected vars: " . implode(', ', $expectedVars) . "\n";
echo "{$status} Valid: " . ($validation['valid'] ? 'YES' : 'NO') . " (expected: NO)\n";
echo "Used variables: " . implode(', ', $validation['used_variables']) . "\n";
echo "Invalid variables: " . implode(', ', $validation['invalid_variables']) . "\n";

// Test 5: WhatsappTemplateService - getDummyData
echo "\n=== Test 5: WhatsappTemplateService - getDummyData ===\n";
foreach ($categories as $category) {
    $dummy = $templateService->getDummyData($category);
    $count = count($dummy);
    $expected = $expectedCounts[$category];
    $passed = $count === $expected;
    $allPassed = $allPassed && $passed;
    $status = $passed ? '✓' : '✗';
    echo "{$status} {$category}: {$count} fields (expected: {$expected})\n";
}

echo "\n=== Test Summary ===\n";
if ($allPassed) {
    echo "✓ ALL TESTS PASSED!\n";
    echo "✓ WhatsappService: formatPhoneNumber works correctly\n";
    echo "✓ WhatsappTemplateService: parseVariables works correctly\n";
    echo "✓ WhatsappTemplateService: getAvailableVariables works correctly\n";
    echo "✓ WhatsappTemplateService: validateTemplate works correctly\n";
    echo "✓ WhatsappTemplateService: getDummyData works correctly\n";
    exit(0);
} else {
    echo "✗ SOME TESTS FAILED\n";
    exit(1);
}

echo "\nNote: WhatsappMessageService and WhatsappLogService require database to test.\n";
echo "These will be tested via tinker after database setup in Phase 3.\n";
