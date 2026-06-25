<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
            // 姓(必須、255文字以内)
            'first_name' => 'required|string|max:255',
            // 名(必須、255文字以内)
            'last_name' => 'required|string|max:255',
            // 性別(必須、1:男性、2:女性、3:その他)
            'gender' => 'required|integer|in:1,2,3',
            // メールアドレス(必須、メール形式、255文字以内)
            'email' => 'required|string|email|max:255',
            // 電話番号(必須、10桁または11桁の数字)
            'tel' => 'required|string|regex:/^[0-9]{10,11}$/',
            // 住所(必須、255文字以内)
            'address' => 'required|string|max:255',
            // 建物名(任意、255文字以内)
            'building' => 'nullable|string|max:255',
            // カテゴリID(必須、categoriesテーブルに存在するID)
            'category_id' => 'required|integer|exists:categories,id',
            // お問い合わせ内容(必須、120文字以内)
            'detail' => 'required|string|max:120',
            // タグID配列(任意)
            'tag_ids' => 'nullable|array',
            // 各タグIDがtagsテーブルに存在するか確認
            'tag_ids.*' => 'integer|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => '姓を入力してください',
            'last_name.required' => '名を入力してください',
            'gender.required' => '性別を選択してください',
            'gender.integer' => '性別の値が不正です',
            'gender.in' => '性別の値が不正です',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'tel.required' => '電話番号を入力してください',
            'tel.regex' => '電話番号はハイフンなしの10～11桁で入力してください',
            'address.required' => '住所を入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.integer' => '選択されたカテゴリーが存在しません',
            'category_id.exists' => '選択されたカテゴリーが存在しません',
            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問い合わせ内容は120文字以内で入力してください',
            'tag_ids.*.exists' => '選択されたタグが存在しません',
            'tag_ids.*.integer' => '選択されたタグが存在しません',
        ];
    }
}
