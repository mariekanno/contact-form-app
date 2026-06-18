<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせフォーム確認ページ表示_正常系(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $formData = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'detail' => 'テストお問い合わせ',
            'tags' => $tags->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->post('/contacts/confirm', $formData);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');

        $response->assertSee('太郎');
        $response->assertSee('山田');
        $response->assertSee('test@example.com');
        $response->assertSee('09012345678');
        $response->assertSee('東京都渋谷区');
        $response->assertSee('テストビル');
        $response->assertSee('テストお問い合わせ');
    }

    public function test_お問い合わせ送信_正常系(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $formData = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'detail' => 'お問い合わせ内容です',
            'tags' => $tags->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->post('/contacts', $formData);

        // Assert
        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'email' => 'test@example.com',
            'first_name' => '太郎',
            'last_name' => '山田',
        ]);

        $contact = Contact::where('email', 'test@example.com')->first();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tags[0]->id,
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tags[1]->id,
        ]);
    }

    public function test_お問い合わせフォーム確認ページ表示_異常系(): void
    {
        // Arrange
        $category = Category::factory()->create();

        $formData = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'detail' => 'お問い合わせ内容です',
        ];

        // Act
        $response = $this->from('/contacts')
            ->post('/contacts/confirm', $formData);

        // Assert
        $response->assertRedirect('/contacts');

        $response->assertSessionHasErrors([
            'email',
        ]);
    }

    public function test_お問い合わせ送信_異常系(): void
    {
        // Arrange
        $category = Category::factory()->create();

        $formData = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'detail' => 'お問い合わせ内容です',
        ];

        // Act
        $response = $this->from('/contacts')
            ->post('/contacts', $formData);

        // Assert
        $response->assertRedirect('/contacts');

        $response->assertSessionHasErrors([
            'email',
        ]);

        $this->assertDatabaseMissing('contacts', [
            'email' => 'test',
        ]);
    }

    public function test_キーワード検索_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'yamada@example.com',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => '佐藤',
            'email' => 'sato@example.com',
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?keyword=山田');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.index');
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    public function test_性別検索_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'gender' => 1,
            'email' => 'male@example.com',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'gender' => 2,
            'email' => 'female@example.com',
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?gender=1');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('male@example.com');
        $response->assertDontSee('female@example.com');
    }

    public function test_カテゴリ検索_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $categoryA->id,
            'email' => 'category-a@example.com',
        ]);

        Contact::factory()->create([
            'category_id' => $categoryB->id,
            'email' => 'category-b@example.com',
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?category_id='.$categoryA->id);

        // Assert
        $response->assertStatus(200);
        $response->assertSee('category-a@example.com');
        $response->assertDontSee('category-b@example.com');
    }

    public function test_日付検索_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'email' => 'today@example.com',
            'created_at' => Carbon::parse('2026-06-18'),
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'email' => 'yesterday@example.com',
            'created_at' => Carbon::parse('2026-06-17'),
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?date=2026-06-18');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('today@example.com');
        $response->assertDontSee('yesterday@example.com');
    }

    public function test_検索結果が7件ごとにページネーションされる_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $contacts = Contact::factory()->count(8)->create([
            'category_id' => $category->id,
            'last_name' => '山田',
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?keyword=山田');

        // Assert
        $response->assertStatus(200);
        $response->assertSee($contacts[0]->email);
        $response->assertSee($contacts[6]->email);
        $response->assertDontSee($contacts[7]->email);
    }

    public function test_お問い合わせ詳細_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        $contact = Contact::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->get("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee($contact->email);
    }

    public function test_お問い合わせ削除_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        $contact = Contact::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->delete("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_認証済ユーザーがタグ編集画面へ遷移できる_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->get("/admin/tags/{$tag->id}/edit");

        // Assert
        $response->assertStatus(200);
        $response->assertSee($tag->name);
    }

    public function test_タグ作成_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->post('/admin/tags', [
                'name' => '新規タグ',
            ]);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'name' => '新規タグ',
        ]);
    }

    public function test_タグ更新_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        $tag = Tag::factory()->create([
            'name' => '変更前',
        ]);

        // Act
        $response = $this->actingAs($user)
            ->put("/admin/tags/{$tag->id}", [
                'name' => '変更後',
            ]);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '変更後',
        ]);
    }

    public function test_タグ削除_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->delete("/admin/tags/{$tag->id}");

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    public function test_問い合わせ一覧検索_不正な性別値_異常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/admin?gender=9');

        // Assert
        $response->assertSessionHasErrors(['gender']);
    }

    public function test_問い合わせ保存_電話番号形式不正_異常系(): void
    {
        // Arrange
        $category = Category::factory()->create();

        $formData = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '090-1234-5678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'detail' => 'お問い合わせ内容です',
        ];

        // Act
        $response = $this->from('/contacts')->post('/contacts', $formData);

        // Assert
        $response->assertRedirect('/contacts');
        $response->assertSessionHasErrors(['tel']);
    }

    public function test_タグ新規登録_必須入力_異常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->from('/admin')->post('/admin/tags', [
            'name' => '',
        ]);

        // Assert
        $response->assertRedirect('/admin');
        $response->assertSessionHasErrors(['name']);
    }

    public function test_タグ新規登録_文字数51文字_異常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->from('/admin')->post('/admin/tags', [
            'name' => str_repeat('あ', 51),
        ]);

        // Assert
        $response->assertRedirect('/admin');
        $response->assertSessionHasErrors(['name']);
    }

    public function test_タグ新規登録_重複_異常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        Tag::factory()->create([
            'name' => '重複タグ',
        ]);

        // Act
        $response = $this->actingAs($user)->from('/admin')->post('/admin/tags', [
            'name' => '重複タグ',
        ]);

        // Assert
        $response->assertRedirect('/admin');
        $response->assertSessionHasErrors(['name']);
    }

    public function test_タグ更新_自身の名前維持_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        $tag = Tag::factory()->create([
            'name' => '既存タグ',
        ]);

        // Act
        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", [
            'name' => '既存タグ',
        ]);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '既存タグ',
        ]);
    }

    public function test_タグ更新_他タグ名へ変更_異常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        Tag::factory()->create([
            'name' => '既にあるタグ',
        ]);

        $tag = Tag::factory()->create([
            'name' => '変更対象タグ',
        ]);

        // Act
        $response = $this->actingAs($user)->from('/admin')->put("/admin/tags/{$tag->id}", [
            'name' => '既にあるタグ',
        ]);

        // Assert
        $response->assertRedirect('/admin');
        $response->assertSessionHasErrors(['name']);
    }

    public function test_カテゴリ関係_has_many_正常系(): void
    {
        // Arrange
        $category = Category::factory()->create();

        Contact::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        // Act
        $contacts = $category->contacts;

        // Assert
        $this->assertCount(2, $category->contacts);
    }

    public function test_お問い合わせ関係_belongs_toとtags同期_正常系(): void
    {
        // Arrange
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $tags = Tag::factory()->count(2)->create();

        // Act
        $contact->tags()->sync($tags->pluck('id')->toArray());

        // Assert
        $this->assertTrue($contact->category->is($category));
        $this->assertCount(2, $contact->tags);
    }

    public function test_タグ関係_belongs_to_many_正常系(): void
    {
        // Arrange
        $tag = Tag::factory()->create();

        $contacts = Contact::factory()->count(2)->create();

        // Act
        foreach ($contacts as $contact) {
            $contact->tags()->attach($tag->id);
        }

        // Assert
        $this->assertCount(2, $tag->contacts);
    }

    public function test_お問い合わせフォーム入力ページ表示_正常系(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::factory()->create([
            'name' => '不良品',
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('不良品');
    }

    public function test_サンクスページ表示_正常系(): void
    {
        // Act
        $response = $this->get('/thanks');

        // Assert
        $response->assertStatus(200);
    }

    public function test_cs_vダウンロード_フィルタ条件付き_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        Contact::factory()->create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'taro@example.com',
        ]);

        Contact::factory()->create([
            'first_name' => '花子',
            'last_name' => '佐藤',
            'email' => 'hanako@example.com',
        ]);

        // Act
        $response = $this->actingAs($user)
            ->get('/contacts/export?keyword=太郎');

        // Assert
        $response->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('taro@example.com', $csv);
        $this->assertStringNotContainsString('hanako@example.com', $csv);
    }

    public function test_cs_vダウンロード_未指定時は新着順_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        $oldContact = Contact::factory()->create([
            'email' => 'old@example.com',
            'created_at' => now()->subDay(),
        ]);

        $newContact = Contact::factory()->create([
            'email' => 'new@example.com',
            'created_at' => now(),
        ]);
        // Act
        $response = $this->actingAs($user)
            ->get('/contacts/export');

        // Assert
        $response->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('new@example.com', $csv);
        $this->assertStringContainsString('old@example.com', $csv);

        $this->assertLessThan(
            strpos($csv, 'old@example.com'),
            strpos($csv, 'new@example.com')
        );
    }
}
