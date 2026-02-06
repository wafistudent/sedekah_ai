<?php

namespace Database\Seeders;

use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

/**
 * WhatsappTemplateSeeder
 * 
 * Seeds WhatsApp message templates for MLM system
 * 
 * @package Database\Seeders
 */
class WhatsappTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $templates = [
            // Category: Member (4 templates)
            [
                'code' => 'welcome_new_member',
                'name' => 'Selamat Datang Member Baru',
                'category' => 'member',
                'subject' => 'Selamat Datang di Sedekah AI MLM',
                'content' => "Halo *{{name}}*! 👋\n\nSelamat datang di *Sedekah AI MLM*!\nAkun Anda telah berhasil terdaftar.\n\n📌 Username: {{username}}\n👤 Sponsor: {{sponsor_name}}\n🌳 Upline: {{upline_name}}\n📅 Bergabung: {{join_date}}\n\nSilakan login di: {{login_url}}\n\nTerima kasih telah bergabung! 🙏",
                'variables' => json_encode(['name', 'username', 'sponsor_name', 'upline_name', 'join_date', 'login_url']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'member_activated',
                'name' => 'Akun Diaktifkan',
                'category' => 'member',
                'subject' => 'Akun Anda Telah Aktif',
                'content' => "Halo *{{name}}*! ✅\n\nKabar gembira! Akun Anda telah *diaktifkan* oleh admin.\n\nAnda sekarang dapat:\n✓ Merekrut member baru\n✓ Mendapatkan komisi\n✓ Melakukan withdrawal\n\nSelamat beraktivitas! 🎉",
                'variables' => json_encode(['name']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'birthday_greeting',
                'name' => 'Ucapan Ulang Tahun',
                'category' => 'member',
                'subject' => 'Selamat Ulang Tahun',
                'content' => "Selamat Ulang Tahun *{{name}}*! 🎂🎉\n\nDi hari spesial ini, kami mengucapkan:\n_Semoga panjang umur, sehat selalu, dan semakin sukses!_\n\nTerima kasih telah menjadi bagian dari keluarga Sedekah AI MLM.\n\nSalam hangat, 🙏\nTim Sedekah AI",
                'variables' => json_encode(['name']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'monthly_report',
                'name' => 'Laporan Bulanan',
                'category' => 'member',
                'subject' => 'Laporan Aktivitas Bulanan',
                'content' => "Halo *{{name}}*! 📊\n\n*Laporan Bulan {{month}}*\n\n👥 Downline Baru: {{new_downlines}}\n💰 Total Komisi: Rp {{total_commission}}\n💸 Total Withdrawal: Rp {{total_withdrawal}}\n💵 Saldo Akhir: Rp {{balance}}\n\nTerus semangat! 🚀",
                'variables' => json_encode(['name', 'month', 'new_downlines', 'total_commission', 'total_withdrawal', 'balance']),
                'is_active' => true,
                'created_by' => null,
            ],
            
            // Category: Commission (3 templates)
            [
                'code' => 'commission_received',
                'name' => 'Komisi Diterima',
                'category' => 'commission',
                'subject' => 'Komisi Masuk',
                'content' => "Halo *{{name}}*! 💰\n\n*Komisi Masuk!*\n\nAnda menerima komisi:\n💵 Jumlah: *Rp {{amount}}*\n📝 Jenis: {{commission_type}}\n👤 Dari: {{from_member}}\n📅 Tanggal: {{date}}\n\n💼 Saldo Anda sekarang: Rp {{balance}}\n\nSelamat! 🎉",
                'variables' => json_encode(['name', 'amount', 'commission_type', 'from_member', 'date', 'balance']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'commission_level_up',
                'name' => 'Naik Level',
                'category' => 'commission',
                'subject' => 'Selamat Naik Level',
                'content' => "Selamat *{{name}}*! 🎊\n\nAnda telah *naik level* ke:\n⭐ *{{new_level}}*\n\nBenefit baru:\n✓ Komisi {{commission_percentage}}%\n✓ Bonus tambahan\n✓ Privilege eksklusif\n\nTerus tingkatkan! 🚀",
                'variables' => json_encode(['name', 'new_level', 'commission_percentage']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'bonus_achieved',
                'name' => 'Bonus Target Tercapai',
                'category' => 'commission',
                'subject' => 'Bonus Target',
                'content' => "Luar biasa *{{name}}*! 🏆\n\nAnda mencapai target dan mendapat:\n💎 *BONUS: Rp {{bonus_amount}}*\n\n📊 Target: {{target_description}}\n📅 Period: {{period}}\n\nBonus akan ditambahkan ke saldo Anda.\n\nPertahankan prestasi! 💪",
                'variables' => json_encode(['name', 'bonus_amount', 'target_description', 'period']),
                'is_active' => true,
                'created_by' => null,
            ],
            
            // Category: Withdrawal (4 templates)
            [
                'code' => 'withdrawal_requested',
                'name' => 'Withdrawal Diminta',
                'category' => 'withdrawal',
                'subject' => 'Permintaan Withdrawal Diterima',
                'content' => "Halo *{{name}}*! 📝\n\n*Permintaan withdrawal Anda telah diterima.*\n\n💵 Jumlah: Rp {{amount}}\n🏦 Bank: {{bank_name}}\n💳 Rekening: {{account_number}}\n👤 A/n: {{account_name}}\n📅 Tanggal: {{date}}\n\nPermintaan Anda sedang diproses oleh admin.\nMohon tunggu konfirmasi selanjutnya.\n\nTerima kasih! 🙏",
                'variables' => json_encode(['name', 'amount', 'bank_name', 'account_number', 'account_name', 'date']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'withdrawal_approved',
                'name' => 'Withdrawal Disetujui',
                'category' => 'withdrawal',
                'subject' => 'Withdrawal Disetujui',
                'content' => "Halo *{{name}}*! ✅\n\n*Withdrawal Anda DISETUJUI!*\n\n💵 Jumlah: Rp {{amount}}\n🏦 Bank: {{bank_name}}\n💳 Rekening: {{account_number}}\n👤 A/n: {{account_name}}\n📅 Disetujui: {{date}}\n👨‍💼 Oleh: {{admin_name}}\n\nDana akan segera ditransfer.\nMohon cek rekening Anda dalam 1x24 jam.\n\nTerima kasih! 🙏",
                'variables' => json_encode(['name', 'amount', 'bank_name', 'account_number', 'account_name', 'date', 'admin_name']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'withdrawal_rejected',
                'name' => 'Withdrawal Ditolak',
                'category' => 'withdrawal',
                'subject' => 'Withdrawal Ditolak',
                'content' => "Halo *{{name}}*, ⚠️\n\nMaaf, withdrawal Anda *DITOLAK*.\n\n💵 Jumlah: Rp {{amount}}\n📅 Tanggal: {{date}}\n\n❌ *Alasan:*\n{{reason}}\n\nSaldo Anda dikembalikan.\nSilakan hubungi admin untuk informasi lebih lanjut.\n\nTerima kasih atas pengertiannya. 🙏",
                'variables' => json_encode(['name', 'amount', 'date', 'reason']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'withdrawal_processed',
                'name' => 'Withdrawal Diproses',
                'category' => 'withdrawal',
                'subject' => 'Transfer Sedang Diproses',
                'content' => "Halo *{{name}}*! 🏦\n\n*Transfer sedang diproses!*\n\n💵 Jumlah: Rp {{amount}}\n🏦 Bank: {{bank_name}}\n💳 Rekening: {{account_number}}\n\nDana sedang dalam proses transfer.\nMohon cek rekening Anda secara berkala.\n\nTerima kasih! 🙏",
                'variables' => json_encode(['name', 'amount', 'bank_name', 'account_number']),
                'is_active' => true,
                'created_by' => null,
            ],
            
            // Category: Admin (3 templates)
            [
                'code' => 'admin_new_member_alert',
                'name' => 'Alert Member Baru (Admin)',
                'category' => 'admin',
                'subject' => 'Member Baru Terdaftar',
                'content' => "🔔 *MEMBER BARU TERDAFTAR*\n\n👤 Nama: {{member_name}}\n📱 HP: {{member_phone}}\n🆔 Username: {{member_username}}\n💼 Sponsor: {{sponsor_name}}\n📅 Tanggal: {{date}}\n\n_Notifikasi otomatis dari sistem_",
                'variables' => json_encode(['member_name', 'member_phone', 'member_username', 'sponsor_name', 'date']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'admin_withdrawal_alert',
                'name' => 'Alert Withdrawal (Admin)',
                'category' => 'admin',
                'subject' => 'Permintaan Withdrawal Baru',
                'content' => "🔔 *WITHDRAWAL REQUEST*\n\n👤 Member: {{member_name}}\n💵 Jumlah: Rp {{amount}}\n🏦 Bank: {{bank_name}} - {{account_number}}\n📅 Tanggal: {{date}}\n\n⚠️ Perlu approval segera!\n\n_Notifikasi otomatis dari sistem_",
                'variables' => json_encode(['member_name', 'amount', 'bank_name', 'account_number', 'date']),
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'code' => 'admin_daily_summary',
                'name' => 'Ringkasan Harian (Admin)',
                'category' => 'admin',
                'subject' => 'Ringkasan Aktivitas Harian',
                'content' => "📊 *RINGKASAN HARIAN*\n📅 {{date}}\n\n👥 Member Baru: {{new_members}}\n💰 Total Komisi: Rp {{total_commission}}\n💸 Withdrawal Pending: {{pending_withdrawals}}\n💵 Withdrawal Approved: Rp {{approved_amount}}\n\n_Laporan otomatis dari sistem_",
                'variables' => json_encode(['date', 'new_members', 'total_commission', 'pending_withdrawals', 'approved_amount']),
                'is_active' => true,
                'created_by' => null,
            ],
            
            // Category: General (1 template)
            [
                'code' => 'system_announcement',
                'name' => 'Pengumuman Sistem',
                'category' => 'general',
                'subject' => 'Pengumuman Penting',
                'content' => "📢 *PENGUMUMAN*\n\n{{announcement_title}}\n\n{{announcement_content}}\n\n📅 {{date}}\n\n_Tim Sedekah AI MLM_",
                'variables' => json_encode(['announcement_title', 'announcement_content', 'date']),
                'is_active' => true,
                'created_by' => null,
            ],
        ];

        foreach ($templates as $template) {
            WhatsappTemplate::create($template);
        }
    }
}
