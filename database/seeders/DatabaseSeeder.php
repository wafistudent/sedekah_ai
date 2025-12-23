<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 * 
 * Main seeder that orchestrates all other seeders
 * 
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CommissionConfigSeeder::class,
            AppSettingSeeder::class,
        ]);
    }
}
