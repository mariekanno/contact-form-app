<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 既存のCategoryがあれば、その中からランダムなIDを取得する
            // 取得できなかった場合は、新しくCategoryを作成してIDを取得する
            'category_id' => Category::inRandomOrder()->first()?->id
                ?? Category::factory()->create()->id,
            // ランダムな名
            'first_name' => fake()->firstName(),
            // ランダムな姓
            'last_name' => fake()->lastName(),
            // ランダムな性別(1～3)
            'gender' => fake()->numberBetween(1, 3),
            // ランダムなメールアドレス
            'email' => fake()->safeEmail(),
            // ランダムな電話番号
            'tel' => fake()->numerify('090########'),
            // ランダムな住所
            'address' => fake()->address(),
            // ランダムな建物名(任意)
            'building' => fake()->optional()->secondaryAddress(),
            // ランダムなお問い合わせ内容
            'detail' => fake()->realText(50),
        ];
    }
}
