<?php

namespace Database\Seeders;

use App\Models\User;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'name' => 'Crea7dosSantos',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password0'),
            'created_at' => new DateTime(),
        ]);
    }
}
