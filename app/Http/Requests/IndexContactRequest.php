<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // このリクエストを許可する
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    // お問い合わせ一覧検索のバリデーションルール
    public function rules(): array
    {
        return [
            // キーワード検索
            'keyword' => ['nullable', 'string', 'max:255'],
            // 性別検索(0:全て、1:男性、2:女性、3:その他)
            'gender' => ['nullable', 'integer', 'in:0,1,2,3'],
            // カテゴリ検索
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            // 日付検索
            'date' => ['nullable', 'date'],
            // 1ページあたりの表示件数
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
