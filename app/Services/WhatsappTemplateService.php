<?php

namespace App\Services;

/**
 * WhatsappTemplateService
 *
 * Handles template-related logic: parsing, validation, dummy data
 *
 * @package App\Services
 */
class WhatsappTemplateService
{
    /**
     * Replace {{variables}} in template with actual data
     * 
     * @param string $content Template content with variables
     * @param array $data Data to replace variables with
     * @return string Parsed content with variables replaced
     */
    public function parseVariables(string $content, array $data): string
    {
        // Loop through data and replace variables
        foreach ($data as $key => $value) {
            // Handle different value types
            if (is_array($value)) {
                $value = json_encode($value);
            } elseif (is_null($value)) {
                $value = '';
            }

            // Replace {{key}} with value
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        // Clean up unused variables (remove remaining {{...}} patterns)
        $content = preg_replace('/\{\{[^}]+\}\}/', '', $content);

        return $content;
    }

    /**
     * Get list of available variables by category with descriptions
     * 
     * @param string $category Category: member, commission, withdrawal, admin, general
     * @return array Array of variables with descriptions
     */
    public function getAvailableVariables(string $category): array
    {
        return match ($category) {
            'member' => [
                'name' => 'Nama member',
                'username' => 'Username',
                'email' => 'Email',
                'phone' => 'Nomor HP',
                'sponsor_name' => 'Nama sponsor',
                'upline_name' => 'Nama upline',
                'join_date' => 'Tanggal bergabung',
                'login_url' => 'Link login',
            ],
            'commission' => [
                'name' => 'Nama member',
                'amount' => 'Jumlah komisi',
                'commission_type' => 'Tipe komisi',
                'from_member' => 'Dari member',
                'date' => 'Tanggal',
                'balance' => 'Total balance',
            ],
            'withdrawal' => [
                'name' => 'Nama member',
                'amount' => 'Jumlah withdrawal',
                'bank_name' => 'Nama bank',
                'account_number' => 'Nomor rekening',
                'account_name' => 'Nama rekening',
                'status' => 'Status',
                'reason' => 'Alasan (jika ditolak)',
                'date' => 'Tanggal request',
                'admin_name' => 'Nama admin yang approve',
            ],
            'admin' => [
                'member_name' => 'Nama member',
                'member_phone' => 'Nomor HP member',
                'member_username' => 'Username member',
                'action_type' => 'Tipe aksi',
                'amount' => 'Jumlah (jika ada)',
                'date' => 'Tanggal',
            ],
            'general' => [
                'announcement_title' => 'Judul pengumuman',
                'announcement_content' => 'Isi pengumuman',
                'date' => 'Tanggal',
            ],
            default => [],
        };
    }

    /**
     * Validate that template variables are valid for category
     * 
     * @param string $content Template content
     * @param array $expectedVariables List of expected variable names
     * @return array Validation result with used and invalid variables
     */
    public function validateTemplate(string $content, array $expectedVariables): array
    {
        // Extract all {{variables}} from content
        preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);
        $usedVariables = $matches[1] ?? [];

        // Remove duplicates
        $usedVariables = array_unique($usedVariables);

        // Find invalid variables (used but not in expected list)
        $invalidVariables = array_diff($usedVariables, $expectedVariables);

        return [
            'valid' => empty($invalidVariables),
            'used_variables' => array_values($usedVariables),
            'invalid_variables' => array_values($invalidVariables),
        ];
    }

    /**
     * Get realistic dummy data for preview by category
     * 
     * @param string $category Category: member, commission, withdrawal, admin, general
     * @return array Dummy data for the category
     */
    public function getDummyData(string $category): array
    {
        return match ($category) {
            'member' => [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'phone' => '0812-3456-7890',
                'sponsor_name' => 'Jane Smith',
                'upline_name' => 'Bob Wilson',
                'join_date' => now()->format('d-m-Y'),
                'login_url' => url('/login'),
            ],
            'commission' => [
                'name' => 'John Doe',
                'amount' => 'Rp 150.000',
                'commission_type' => 'Komisi Direct Referral',
                'from_member' => 'Jane Smith',
                'date' => now()->format('d-m-Y'),
                'balance' => 'Rp 1.500.000',
            ],
            'withdrawal' => [
                'name' => 'John Doe',
                'amount' => 'Rp 500.000',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'John Doe',
                'status' => 'Disetujui',
                'reason' => 'Data rekening tidak valid',
                'date' => now()->format('d-m-Y'),
                'admin_name' => 'Admin Budi',
            ],
            'admin' => [
                'member_name' => 'John Doe',
                'member_phone' => '0812-3456-7890',
                'member_username' => 'johndoe',
                'action_type' => 'Registrasi',
                'amount' => 'Rp 100.000',
                'date' => now()->format('d-m-Y H:i'),
            ],
            'general' => [
                'announcement_title' => 'Maintenance Sistem',
                'announcement_content' => 'Sistem akan maintenance pada tanggal 10 Februari 2026 pukul 22:00 WIB',
                'date' => now()->format('d-m-Y'),
            ],
            default => [],
        };
    }
}
