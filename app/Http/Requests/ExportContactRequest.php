<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExportContactRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            // キーワード検索(任意、255文字以内)
            'keyword' => ['nullable', 'string', 'max:255'],
            // 性別検索(任意、1:男性、2:女性、3:その他)
            'gender' => ['nullable', 'integer', 'in:1,2,3'],
            // カテゴリ検索(任意、categoriesテーブルに存在するID)
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            // 日付(任意)
            'date' => ['nullable', 'date'],
        ];
    }
}
