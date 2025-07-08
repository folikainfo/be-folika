<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '123e4567-vwxy-8721-a981-421249743124',
            'name' => 'Testing',
            'email'      => 'testing@gmail.com',
            'password'   => Hash::make('password123'), 
            'image_url'  => "https://res.cloudinary.com/dm0lrdhi5/image/upload/v1751267694/users/ivwxf1tek82omqey5uhl.png",
            'image_public_id' => "users/ivwxf1tek82omqey5uhl"

        ]);
    }
}
