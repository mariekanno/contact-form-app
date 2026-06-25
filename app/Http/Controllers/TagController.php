<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    // タグ編集画面を表示する
    public function edit(Tag $tag)
    {
        // 編集するタグをビューに渡して表示する
        return view('admin.tags.edit', compact('tag'));
    }

    // タグを更新する
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        // バリデーション済みのタグ名で更新する
        $tag->update($request->only('name'));

        // 管理画面へリダイレクトする
        return redirect()->route('admin.index');
    }

    // タグを新規登録する
    public function store(UpdateTagRequest $request)
    {
        // バリデーション済みのタグ名で新規登録する
        Tag::create($request->only('name'));

        // 管理画面へリダイレクトする
        return redirect()->route('admin.index');
    }

    // タグを削除する
    public function destroy(Tag $tag)
    {
        // 指定したタグを削除する
        $tag->delete();

        // 管理画面へリダイレクトする
        return redirect()->route('admin.index');
    }
}
