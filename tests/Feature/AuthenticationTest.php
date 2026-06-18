<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証済みユーザーが管理画面へ遷移できる_正常系(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->get('/admin');

        // Assert
        $response->assertStatus(200);
    }

    public function test_未認証ユーザーはログイン画面へリダイレクトされる_異常系(): void
    {
        // Act
        $response = $this->get('/admin');

        // Assert
        $response->assertRedirect('/login');
    }
}
