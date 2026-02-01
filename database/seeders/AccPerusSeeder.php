<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccPerusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['id' => 'member1'],
            [
                'email'       => 'member1@sedekah.ai',
                'password'    => Hash::make('password'),
                'name'        => 'Member 1',
                'phone'       => '081234567890',
                'dana_name'   => 'member1',
                'dana_number' => '081234567890',
                'pin_point'   => 0,
                'status'      => 'active',
            ]
        );

        $user->assignRole('member');
    }
}
