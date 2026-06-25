<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // Contactテーブルにダミーデータを登録する
    public function run(): void
    {

        // Contactを20件作成し、作成した各Contactにタグを紐づける
        Contact::factory(20)->create()->each(function ($contact) {
            // ランダムに1～3件のタグIDを取得してContactに関連付ける
            $contact->tags()->attach(
                Tag::inRandomOrder()->limit(rand(1, 3))->pluck('id')
            );
        });
    }
}
