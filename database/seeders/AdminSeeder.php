<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder
 * 
 * Seeds the default admin user
 * 
 * @package Database\Seeders
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['id' => 'admin'],
            [
                'email' => 'admin@sedekah.ai',
                'password' => Hash::make('password'),
                'name' => 'System Administrator',
                'phone' => '081234567890',
                'dana_name' => 'Admin',
                'dana_number' => '081234567890',
                'pin_point' => 999999,
                'status' => 'active',
            ]
        );

        // Assign admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create wallet for admin
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $admin->id],
            ['balance' => 0]
        );
    }
}
