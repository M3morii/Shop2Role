<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan user admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'adminuser',
            'password' => Hash::make('adminpassword'), // Ganti dengan password yang diinginkan
            'role' => 'admin', // Menetapkan role admin
        ]);
    }
}
