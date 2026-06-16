<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'index']);

Route::post('/contacts/confirm', [ContactController::class, 'confirm']);

Route::post('/contacts', [ContactController::class, 'store']);

Route::get('/thanks', [ContactController::class, 'thanks']);

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.index');

    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])
        ->name('admin.show');

    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])
        ->name('admin.destroy');

    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])
        ->name('admin.tags.edit');

    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])
        ->name('admin.tags.update');

    Route::delete('/admin/tags/{tag}/', [TagController::class, 'destroy'])
        ->name('admin.tags.destroy');
});
