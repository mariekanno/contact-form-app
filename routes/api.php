<?php

// お問い合わせAPIコントローラーを読み込む
use App\Http\Controllers\Api\V1\ContactController;
// Requestクラスを使用する
use Illuminate\Http\Request;
// Routeクラスを使用する
use Illuminate\Support\Facades\Route;

// 認証済みユーザー情報を取得するAPI
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// APIバージョン1のルートグループ
Route::prefix('v1')->group(function () {
    // APIバージョン1として　contacts　リソースのCRUD API(一覧取得・詳細取得・登録・更新・削除)をまとめて定義する
    Route::apiResource('contacts', ContactController::class);
});
