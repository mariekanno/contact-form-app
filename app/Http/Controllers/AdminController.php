<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class AdminController extends Controller
{
    // お問い合わせ一覧画面を表示する
    public function index(IndexContactRequest $request)
    {

        // カテゴリとタグを一緒に取得する(N＋1問題対策)
        $query = Contact::with(['category', 'tags']);

        // キーワードが入力されている場合
        if ($request->filled('keyword')) {
            // 検索キーワードを取得する
            $keyword = $request->keyword;
            // 姓・名・メールアドレスで部分一致検索する
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別が指定されている場合(0:全ては除外)
        if ($request->filled('gender') && $request->gender != 0) {
            // 性別で絞り込む
            $query->where('gender', $request->gender);
        }

        // カテゴリが指定されている場合
        if ($request->filled('category_id')) {
            // カテゴリで絞り込む
            $query->where('category_id', $request->category_id);
        }

        // 日付が指定されている場合
        if ($request->filled('date')) {
            // 作成日で絞り込む
            $query->whereDate('created_at', $request->date);
        }

        // 検索結果を新しい順で7件ずつ取得する
        $contacts = $query->latest()->paginate(7);
        // カテゴリ一覧を取得する
        $categories = Category::all();
        // タグ一覧を取得する
        $tags = Tag::all();

        // 一覧画面を表示し、お問い合わせ・カテゴリ・タグを渡す
        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }

    // お問い合わせ詳細画面を表示する
    public function show(Contact $contact)
    {
        // カテゴリとタグを取得する
        $contact->load(['category', 'tags']);

        // 詳細画面を表示し、お問い合わせを渡す
        return view('admin.show', compact('contact'));
    }

    // お問い合わせを削除する
    public function destroy(Contact $contact)
    {
        // お問い合わせを削除する
        $contact->delete();

        // 一覧画面へリダイレクトする
        return redirect('/admin');
    }
}
