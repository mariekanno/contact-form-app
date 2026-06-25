<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // Userテーブルにテストユーザーを登録する
    public function run(): void
    {
        // テストユーザーを作成する
        User::create([
            // ユーザー名
            'name' => 'Test User',
            // メールアドレス
            'email' => 'test@example.com',
            // パスワードをハッシュ化して保存する
            'password' => Hash::make('password'),
        ]);
    }
}
