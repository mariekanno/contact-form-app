<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    // 一括代入を許可するカラム
    protected $fillable = [
        // タグ名
        'name',
    ];

    // Contactモデルとのリレーションを定義する
    public function contacts(): BelongsToMany
    {
        // Tagは複数のContactと関連付けられる(多対多)
        return $this->belongsToMany(Contact::class);
    }
}
