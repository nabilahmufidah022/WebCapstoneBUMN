<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        User::create([
            'name' => 'Admin Rumah BUMN',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'usertype' => 'admin', 
        ]);
=======
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Rumah BUMN',
                'password' => Hash::make('admin123'),
                'usertype' => 'admin', // Sesuai kolom yang kamu punya
            ]
        );
>>>>>>> 6e2bcf580ad2c8728c9d146cc6897e8dc41740d7
    }
}
