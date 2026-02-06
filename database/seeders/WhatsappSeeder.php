<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use App\Models\WhatsappLog;
use App\Models\User;

class WhatsappSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Settings
        $this->seedSettings();
        
        // 2. Seed Templates
        $this->seedTemplates();
        
        // 3. Seed Sample Logs
        $this->seedLogs();
    }
    
    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'api_url', 'value' => 'https://api.waajo.id/go-omni-v2/public/wa', 'type' => 'text'],
            ['key' => 'api_key', 'value' => 'your_api_key_here', 'type' => 'text'],
            ['key' => 'is_mode_safe', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'message_delay_seconds', 'value' => '3', 'type' => 'number'],
            ['key' => 'auto_retry_enabled', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'retry_delay_minutes', 'value' => '5', 'type' => 'number'],
            ['key' => 'max_retry_attempts', 'value' => '3', 'type' => 'number'],
        ];
        
        foreach ($settings as $setting) {
            WhatsappSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        
        $this->command->info('✓ Settings seeded');
    }
    
    private function seedTemplates(): void
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$adminUser) {
            $this->command->warn('⚠ No admin user found, skipping template seeding');
            return;
        }
        
        $templates = [
            // Member Templates
            [
                'code' => 'welcome_new_member',
                'name' => 'Welcome New Member',
                'category' => 'member',
                'subject' => 'Selamat Datang!',
                'content' => "Halo *{{name}}*! 👋\n\nSelamat datang di *Sedekah AI MLM*!\nAkun Anda telah berhasil terdaftar.\n\n📌 Username: {{username}}\n📧 Email: {{email}}\n👤 Sponsor: {{sponsor_name}}\n\nSilakan login di: {{login_url}}\n\nTerima kasih telah bergabung! 🙏",
                'is_active' => true,
            ],
            [
                'code' => 'member_activated',
                'name' => 'Member Account Activated',
                'category' => 'member',
                'subject' => 'Akun Aktif',
                'content' => "Halo {{name}}! ✅\n\nSelamat! Akun Anda telah *diaktifkan*.\n\nAnda sekarang dapat:\n• Mendapatkan komisi\n• Mengajukan withdrawal\n• Mengakses semua fitur\n\nSelamat beraktivitas! 🎉",
                'is_active' => true,
            ],
            
            // Commission Templates
            [
                'code' => 'commission_received',
                'name' => 'Commission Received Notification',
                'category' => 'commission',
                'subject' => 'Komisi Diterima',
                'content' => "Selamat {{name}}! 💰\n\nAnda menerima komisi:\n\n*Jenis:* {{commission_type}}\n*Jumlah:* {{amount}}\n*Dari:* {{from_member}}\n*Tanggal:* {{date}}\n\n💳 Total Balance: {{balance}}\n\nTerima kasih! 🙏",
                'is_active' => true,
            ],
            [
                'code' => 'commission_monthly_report',
                'name' => 'Monthly Commission Report',
                'category' => 'commission',
                'subject' => 'Laporan Bulanan',
                'content' => "Hai {{name}}! 📊\n\nLaporan komisi bulan ini:\n\n*Total Komisi:* {{amount}}\n*Total Balance:* {{balance}}\n*Periode:* {{date}}\n\nTerus tingkatkan performa Anda! 💪",
                'is_active' => true,
            ],
            
            // Withdrawal Templates
            [
                'code' => 'withdrawal_requested',
                'name' => 'Withdrawal Request Submitted',
                'category' => 'withdrawal',
                'subject' => 'Pengajuan Withdrawal',
                'content' => "Hai {{name}}! 📝\n\nPengajuan withdrawal Anda telah diterima:\n\n*Jumlah:* {{amount}}\n*Bank:* {{bank_name}}\n*No. Rekening:* {{account_number}}\n*Nama:* {{account_name}}\n*Tanggal:* {{request_date}}\n\nMohon tunggu proses verifikasi.\n\nTerima kasih! 🙏",
                'is_active' => true,
            ],
            [
                'code' => 'withdrawal_approved',
                'name' => 'Withdrawal Request Approved',
                'category' => 'withdrawal',
                'subject' => 'Withdrawal Disetujui',
                'content' => "Selamat {{name}}! ✅\n\nWithdrawal Anda telah *disetujui*:\n\n*Jumlah:* {{amount}}\n*Bank:* {{bank_name}}\n*Status:* {{status}}\n\nDana akan ditransfer dalam 1x24 jam.\n\nTerima kasih! 💰",
                'is_active' => true,
            ],
            [
                'code' => 'withdrawal_rejected',
                'name' => 'Withdrawal Request Rejected',
                'category' => 'withdrawal',
                'subject' => 'Withdrawal Ditolak',
                'content' => "Hai {{name}}, ❌\n\nMaaf, withdrawal Anda ditolak:\n\n*Jumlah:* {{amount}}\n*Alasan:* {{message}}\n\nSilakan hubungi admin untuk informasi lebih lanjut.\n\nTerima kasih.",
                'is_active' => false,
            ],
            
            // Admin Templates
            [
                'code' => 'admin_alert',
                'name' => 'Admin Alert Notification',
                'category' => 'admin',
                'subject' => 'Alert',
                'content' => "⚠️ *ADMIN ALERT* ⚠️\n\n{{message}}\n\n*Waktu:* {{date}} {{time}}\n\nSilakan segera ditindaklanjuti.",
                'is_active' => true,
            ],
            
            // General Templates
            [
                'code' => 'general_announcement',
                'name' => 'General Announcement',
                'category' => 'general',
                'subject' => 'Pengumuman',
                'content' => "Hai {{name}}! 📢\n\n*PENGUMUMAN PENTING*\n\n{{message}}\n\n*Tanggal:* {{date}}\n\nTerima kasih atas perhatiannya.\n\n- Tim {{app_name}}",
                'is_active' => true,
            ],
            [
                'code' => 'general_reminder',
                'name' => 'General Reminder',
                'category' => 'general',
                'subject' => 'Reminder',
                'content' => "Hai {{name}}! 🔔\n\n*PENGINGAT*\n\n{{message}}\n\nJangan lupa ya! 😊",
                'is_active' => true,
            ],
        ];
        
        foreach ($templates as $template) {
            WhatsappTemplate::updateOrCreate(
                ['code' => $template['code']],
                array_merge($template, [
                    'created_by' => $adminUser->id,
                    'variables' => $this->extractVariables($template['content']),
                ])
            );
        }
        
        $this->command->info('✓ Templates seeded (10 templates)');
    }
    
    private function seedLogs(): void
    {
        $users = User::limit(10)->get();
        $templates = WhatsappTemplate::all();
        
        if ($users->isEmpty() || $templates->isEmpty()) {
            $this->command->warn('⚠ No users or templates found, skipping logs seeding');
            return;
        }
        
        $statuses = [
            'sent' => 30,    // 30 sent logs
            'failed' => 10,  // 10 failed logs
            'pending' => 5,  // 5 pending logs
            'queued' => 5,   // 5 queued logs
        ];
        
        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $template = $templates->random();
                
                WhatsappLog::create([
                    'template_id' => $template->id,
                    'recipient_phone' => $user->phone ?? '628' . rand(1000000000, 9999999999),
                    'recipient_name' => $user->name,
                    'message_content' => $this->parseTemplateContent($template->content, $user),
                    'status' => $status,
                    'sent_at' => $status === 'sent' ? now()->subMinutes(rand(1, 1440)) : null,
                    'error_message' => $status === 'failed' ? 'Connection timeout' : null,
                    'retry_count' => $status === 'failed' ? rand(0, 2) : 0,
                    'metadata' => [
                        'event_type' => $template->category . '_event',
                        'event_timestamp' => now()->toISOString(),
                        'user_id' => $user->id,
                    ],
                    'created_at' => now()->subMinutes(rand(1, 2880)),
                ]);
            }
        }
        
        $this->command->info('✓ Sample logs seeded (50 logs)');
    }
    
    private function extractVariables(string $content): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);
        return array_unique($matches[1] ?? []);
    }
    
    private function parseTemplateContent(string $content, User $user): string
    {
        $replacements = [
            '{{name}}' => $user->name,
            '{{username}}' => $user->id ?? 'user',
            '{{email}}' => $user->email,
            '{{phone}}' => $user->phone ?? '-',
            '{{sponsor_name}}' => 'Sponsor Name',
            '{{upline_name}}' => 'Upline Name',
            '{{join_date}}' => now()->format('d M Y'),
            '{{login_url}}' => url('/login'),
            '{{amount}}' => 'Rp ' . number_format(rand(100000, 1000000), 0, ',', '.'),
            '{{balance}}' => 'Rp ' . number_format(rand(1000000, 5000000), 0, ',', '.'),
            '{{commission_type}}' => 'Sponsor Bonus',
            '{{from_member}}' => 'Member Name',
            '{{date}}' => now()->format('d M Y'),
            '{{time}}' => now()->format('H:i'),
            '{{bank_name}}' => 'BCA',
            '{{account_number}}' => '1234567890',
            '{{account_name}}' => $user->name,
            '{{status}}' => 'Diproses',
            '{{request_date}}' => now()->format('d M Y'),
            '{{message}}' => 'Sample message content',
            '{{app_name}}' => config('app.name', 'Sedekah AI'),
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
