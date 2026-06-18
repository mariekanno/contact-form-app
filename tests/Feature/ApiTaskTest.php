<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTaskTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $override = []): array
    {
        $category = Category::factory()->create();

        return array_merge([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => fake()->unique()->safeEmail(),
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'detail' => 'お問い合わせ内容です',
        ], $override);
    }

    public function test_お問い合わせ一覧取得_ap_i_正常系(): void
    {
        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'links', 'meta']);
        $response->assertJsonCount(3, 'data');
    }

    public function test_お問い合わせ一覧取得_ap_i_キーワード検索_正常系(): void
    {
        Contact::factory()->create(['first_name' => '太郎', 'email' => 'taro@example.com']);
        Contact::factory()->create(['first_name' => '花子', 'email' => 'hanako@example.com']);

        $response = $this->getJson('/api/v1/contacts?keyword=太郎');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.email', 'taro@example.com');
    }

    public function test_お問い合わせ一覧取得_ap_i_性別検索_正常系(): void
    {
        Contact::factory()->create(['gender' => 1]);
        Contact::factory()->create(['gender' => 2]);

        $response = $this->getJson('/api/v1/contacts?gender=1');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.gender', 1);
    }

    public function test_お問い合わせ一覧取得_ap_i_カテゴリ検索_正常系(): void
    {
        $category = Category::factory()->create();

        Contact::factory()->create(['category_id' => $category->id]);
        Contact::factory()->create();

        $response = $this->getJson('/api/v1/contacts?category_id='.$category->id);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_お問い合わせ一覧取得_ap_i_日付検索_正常系(): void
    {
        Contact::factory()->create(['created_at' => '2026-06-18 10:00:00']);
        Contact::factory()->create(['created_at' => '2026-06-17 10:00:00']);

        $response = $this->getJson('/api/v1/contacts?date=2026-06-18');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_お問い合わせ一覧取得_ap_i_ページネーション_正常系(): void
    {
        Contact::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/contacts?per_page=2');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('meta.per_page', 2);
        $response->assertJsonPath('meta.total', 5);
    }

    public function test_お問い合わせ詳細取得_ap_i_正常系(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $contact->id);
    }

    public function test_お問い合わせ詳細取得_ap_i_存在しない_i_d_異常系(): void
    {
        $response = $this->getJson('/api/v1/contacts/99999');

        $response->assertStatus(404);
    }

    public function test_お問い合わせ作成_ap_i_正常系(): void
    {
        $tag = Tag::factory()->create();

        $payload = $this->validPayload([
            'email' => 'create@example.com',
            'tag_ids' => [$tag->id],
        ]);

        $response = $this->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('contacts', [
            'email' => 'create@example.com',
        ]);
    }

    public function test_お問い合わせ作成_ap_i_バリデーション異常系(): void
    {
        $response = $this->postJson('/api/v1/contacts', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'category_id',
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'detail',
        ]);
    }

    public function test_お問い合わせ更新_ap_i_正常系(): void
    {
        $contact = Contact::factory()->create();

        $payload = $this->validPayload([
            'email' => 'update@example.com',
            'first_name' => '更新',
            'last_name' => '太郎',
        ]);

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'email' => 'update@example.com',
            'first_name' => '更新',
        ]);
    }

    public function test_お問い合わせ更新_ap_i_バリデーション異常系(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'category_id',
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'detail',
        ]);
    }

    public function test_お問い合わせ削除_ap_i_正常系(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_お問い合わせ削除_ap_i_存在しない_i_d_異常系(): void
    {
        $response = $this->deleteJson('/api/v1/contacts/99999');

        $response->assertStatus(404);
    }
}
