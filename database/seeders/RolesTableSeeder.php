<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan role "Admin" dan "Customer"
        DB::table('roles')->insert([
            ['role_name' => 'Admin'], // Role Admin
            ['role_name' => 'Customer'], // Role Customer
        ]);
    }
}
