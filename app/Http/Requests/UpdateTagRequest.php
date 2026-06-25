<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
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
    // 
    public function rules(): array
    {
        return [
            'name' => [
                // タグ名(必須)
                'required',
                // 文字列
                'string',
                // 50文字以内
                'max:50',
                // 自分自身のタグIDは除外して、他のタグ名と重複しないか確認する
                'unique:tags,name,'.optional($this->tag)->id,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は50文字以内で入力してください',
            'name.unique' => 'そのタグ名は既に使用されています',
        ];
    }
}
