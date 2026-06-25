<?php

// 管理画面コントローラーを読み込む
use App\Http\Controllers\AdminController;
// お問い合わせフォームコントローラーを読み込む
use App\Http\Controllers\ContactController;
// タグ管理コントローラーを読み込む
use App\Http\Controllers\TagController;
// Routeクラスを使用する
use Illuminate\Support\Facades\Route;

// お問い合わせ入力画面を表示する
Route::get('/', [ContactController::class, 'index']);

// お問い合わせ確認画面へ遷移する
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);

// お問い合わせ内容を保存する
Route::post('/contacts', [ContactController::class, 'store']);

// サンクスページを表示する
Route::get('/thanks', [ContactController::class, 'thanks']);

// 検索条件に応じたCSVファイルを出力する
Route::get('/contacts/export', [ContactController::class, 'export']);

// 認証済みユーザーのみアクセス可能なルートグループ
Route::middleware('auth')->group(function () {

    // お問い合わせ一覧画面を表示する
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.index');
    // お問い合わせ詳細画面を表示する
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])
        ->name('admin.show');

    // お問い合わせを削除する
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])
        ->name('admin.destroy');

    // タグ編集画面を表示する
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])
        ->name('admin.tags.edit');

    // タグを新規登録する
    Route::post('/admin/tags', [TagController::class, 'store'])
        ->name('admin.tags.store');

    // タグ情報を編集する
    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])
        ->name('admin.tags.update');

    // タグを削除する
    Route::delete('/admin/tags/{tag}/', [TagController::class, 'destroy'])
        ->name('admin.tags.destroy');
});
