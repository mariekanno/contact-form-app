<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // Seederを実行する
    public function run(): void
    {
        // 各Seederを順番に実行する
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
