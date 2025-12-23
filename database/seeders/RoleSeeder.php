<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * RoleSeeder
 * 
 * Seeds the roles for the MLM application
 * 
 * @package Database\Seeders
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create admin role
        Role::firstOrCreate(['name' => 'admin']);

        // Create member role
        Role::firstOrCreate(['name' => 'member']);
    }
}
