<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => Str::uuid(),
                'first_name' => 'Xanders',
                'last_name' => 'San',
                'email' => 'xanders80@gmail.com',
                'email_verified_at' => now(),
                'password' => 'Asg*12175',
                'level_id' => 1,
                'access_group_id' => 1,
            ],
        ];

        foreach ($data as $item) {
            \App\Models\User::create($item);
        }

        $data = [
            [
                'id' => Str::uuid(),
                'first_name' => 'Wendy',
                'last_name' => 'Quintero',
                'email' => 'wendysmar@mail.com',
                'email_verified_at' => now(),
                'password' => 'Asg*12175',
                'level_id' => 2,
                'access_group_id' => 2,
            ],
        ];

        foreach ($data as $item) {
            \App\Models\User::create($item);
        }

        $data = [
            [
                'id' => Str::uuid(),
                'first_name' => 'Usuario',
                'last_name' => 'Pruebas',
                'email' => 'user@mail.com',
                'email_verified_at' => now(),
                'password' => 'Asg*12175',
                'level_id' => 3,
                'access_group_id' => 3,
            ],
        ];

        foreach ($data as $item) {
            \App\Models\User::create($item);
        }
    }
}
