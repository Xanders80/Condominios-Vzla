<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();

        $this->call([
            LevelSeeder::class,
            AccessGroupSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            AccessMenuSeeder::class,
            TypesSeeder::class,
            VzlaSeeder::class,
        ]);

        $this->command->info(trans('Seeder successfully.'));
        $this->command->info(trans('Please login with email: example@example.com and password: password123'));
    }
}
