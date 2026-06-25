<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // お問い合わせ一覧を取得する
    public function index(IndexContactRequest $request)
    {
        // categoryとtagsを一緒に取得する(N＋1問題対策)
        $contacts = Contact::with(['category', 'tags']);

        // キーワードが入力されている場合
        if ($request->filled('keyword')) {
            // キーワードを取得する
            $keyword = $request->keyword;

            // 名・姓・メールアドレスのいずれかにキーワードが含まれるデータを検索する
            $contacts->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        // 性別が選択されており、「0(全て)」以外が指定されている場合
        if ($request->filled('gender') && $request->gender != 0) {
            // 指定された性別(男性・女性・その他)で絞り込む
            $contacts->where('gender', $request->gender);
        }

        // カテゴリが指定されている場合
        if ($request->filled('category_id')) {
            // カテゴリIDで絞り込む
            $contacts->where('category_id', $request->category_id);
        }

        // 日付が指定されている場合
        if ($request->filled('date')) {
            // 作成日で絞り込む
            $contacts->whereDate('created_at', $request->date);
        }

        // リクエストから1ページあたりの表示件数を取得する(未指定の場合は20件)
        $perPage = $request->input('per_page', 20);

        // 作成日の新しい順に並び替え、指定件数ごとにページネーションする
        $contacts = $contacts
            ->latest()
            ->paginate($perPage);

        // お問い合わせ一覧をContactResourceで整形してJson形式で返す
        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    // お問い合わせを新規作成する
    public function store(StoreContactRequest $request)
    {
        // バリデーションを通過したお問い合わせデータを登録する
        $contact = Contact::create($request->validated());

        // タグIDが送信されている場合
        if ($request->has('tag_ids')) {
            // お問い合わせにタグを関連付ける
            $contact->tags()->sync($request->tag_ids);
        }

        // category・tagsを読み込み、作成したお問い合わせを201(Created)で返す
        return (new ContactResource($contact->load(['category', 'tags'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    // お問い合わせ詳細を取得する
    public function show(string $id)
    {
        // categoryとtagsを一緒に取得し、指定されたIDのお問い合わせを検索する(N＋1問題対策)
        $contact = Contact::with(['category', 'tags'])->find($id);

        // お問い合わせが存在しない場合
        if (! $contact) {
            // 404エラーとメッセージをJson形式で返す
            return response()->json([
                'message' => 'Contact not found',
            ], 404);
        }

        // お問い合わせ詳細をContactResourceで整形して返す
        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */

    // お問い合わせを更新する
    public function update(UpdateContactRequest $request, string $id)
    {
        // 指定されたIDのお問い合わせを検索する
        $contact = Contact::find($id);

        // お問い合わせが存在しない場合
        if (! $contact) {
            // 404エラーとメッセージをJson形式で返す
            return response()->json([
                'message' => 'Contact not found',
            ], 404);
        }

        // バリデーションを通過したデータでお問い合わせを更新する
        $contact->update($request->validated());

        // タグIDが送信されている場合
        if ($request->has('tag_ids')) {
            // お問い合わせにタグを関連付ける
            $contact->tags()->sync($request->tag_ids);
        }

        // categoryとtagsを読み込み、更新したお問い合わせを返す
        return new ContactResource(
            $contact->load(['category', 'tags'])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    // お問い合わせを削除する
    public function destroy(string $id)
    {
        // 指定されたIDのお問い合わせを検索する
        $contact = Contact::find($id);

        // お問い合わせが存在しない場合
        if (! $contact) {
            // 404エラーとメッセージをJson形式で返す
            return response()->json([
                'message' => 'Contact not found',
            ], 404);
        }

        // お問い合わせを削除する
        $contact->delete();

        // 削除成功を示す204　No Contentを返す
        return response()->noContent();
    }
}
