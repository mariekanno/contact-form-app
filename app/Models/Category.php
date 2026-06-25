<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // 一括代入を許可するカラム
    protected $fillable = [
        // カテゴリ名
        'content',
    ];

    // Contactsモデルとのリレーションを定義する
    public function contacts(): HasMany
    {
        // Categoryは複数のContactを持つ
        return $this->hasMany(Contact::class);
    }
}
