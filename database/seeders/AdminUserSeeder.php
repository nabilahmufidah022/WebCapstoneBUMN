<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Jalankan penanaman data user admin baru.
     */
    public function run()
    {
        // Menyuntikkan akun admin Rumah BUMN
        User::create([
            'name' => 'Admin Rumah BUMN',
            'email' => 'admin@gmail.com', // Silakan ganti email admin kamu disini
            'password' => Hash::make('admin1234'), // Password admin kamu
            'usertype' => 'admin', // Mengunci hak akses tertinggi admin
            'is_active' => true,
        ]);
    }
}
