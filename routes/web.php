<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'index']);
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
Route::post('/contacts', [ContactController::class, 'store']);
Route::get('/thanks', [ContactController::class, 'thanks']);
Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        return '管理画面一覧(準備中)';
    })->name('admin.index');
});
