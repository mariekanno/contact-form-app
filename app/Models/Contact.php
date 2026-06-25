<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    use HasFactory;

    // 一括代入を許可するカラム
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
        'category_id',
    ];

    // Categoryモデルとのリレーションを定義する
    public function category(): BelongsTo
    {
        // Contactは1つのCategoryに所属する
        return $this->belongsTo(Category::class);
    }

    // Tagモデルとのリレーションを定義する
    public function tags(): BelongsToMany
    {
        // Contactは複数のタグと関連付けられる
        return $this->belongsToMany(Tag::class);
    }
}
