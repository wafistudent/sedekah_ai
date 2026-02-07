<?php
namespace App\Services;

use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    private $baseUrl = "https://api.waajo.id/go-omni-v2/public/whatsapp";
    public function msgNewMember()
    {
        $response = Http::withHeaders([
            'apikey' => env('WHATSAPP_API_KEY'),
        ])->post($this->baseUrl . '/send-text', [
            'recipient_number' => '6281357153031',
            'text'             => $this->newMemberMessage("test", "test"),
            'is_mode_safe'     => false,
        ]);

        return $response->status();
    }

    public function sendWelcomeMessage(array $userData)
    {
        $response = Http::withHeaders([
            'apikey' => env('WHATSAPP_API_KEY'),
        ])->post($this->baseUrl . '/send-text', [
            'recipient_number' => $userData['phone'],
            'text'             => $this->newMemberMessage($userData['id'], $userData['password']),
            'is_mode_safe'     => false,
        ]);

        WhatsappLog::create([
            'member_id' => $userData['id'],
            'phone'     => $userData['phone'],
            'message'   => $this->newMemberMessage($userData['id'], $userData['password']),
            'status'    => (int) $response->status(),
        ]);

        return $response->body();
    }

    public function newMemberMessage(string $username, string $password)
    {
        return "Selamat Datang di AiSosial.com
Terimakasih sudah bergabung Bersama kami.


Akses Produk Silahkan masuk melalui link
---
https://member.aisosial.com
---

Username : $username
Password : $password


Produk Rp 100.000
---
Bonus Affiliasi :
Level 1 Rp 40.000
Level 2 Rp 10.000
Level 3 Rp 6.000
Level 4 Rp 4.000
Level 5 Rp 3.500
Level 6 Rp 3.500
Level 7 Rp 3.000
Level 8 Rp 5.000
---

Total Rp 75.000


Bergabunglah dengan sistem afiliasi kami dan nikmati peluang menghasilkan pendapatan tambahan dengan cara yang mudah!
Dengan menjadi mitra afiliasi, Anda akan mendapatkan komisi dari setiap penjualan atau tindakan yang dilakukan melalui link afiliasi Anda.
Keuntungan lainnya, Anda tidak perlu mengelola produk, layanan, atau inventaris — cukup promosikan dan biarkan kami yang mengurus sisanya.
Semua proses pelacakan, pembayaran, dan dukungan sudah disediakan, sehingga Anda bisa fokus untuk meningkatkan penghasilan tanpa kerumitan. Jangan lewatkan kesempatan untuk menghasilkan uang secara fleksibel dari kenyamanan rumah Anda.
Bergabung sekarang dan mulailah menghasilkan!";
    }
}
