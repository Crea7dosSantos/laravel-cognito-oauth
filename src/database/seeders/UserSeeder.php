<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DateTime;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'cognito_username' => '62ae05a0b4280',
            'cognito_sub' => '50f8dd40-e5f5-40b6-b6cf-5a5292002e5d',
            'cognito_google_sub' => null,
            'cognito_apple_sub' => null,
            'name' => 'Crea7dosSantos',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password0'),
            'created_at' => new DateTime(),
        ]);
    }
}
