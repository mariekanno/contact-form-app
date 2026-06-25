<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactController extends Controller
{
    // お問い合わせ入力画面を表示する
    public function index()
    {
        // カテゴリ一覧を取得する
        $categories = Category::all();
        // タグ一覧を取得する
        $tags = Tag::all();

        // 入力画面を表示し、カテゴリとタグを渡す
        return view('contact.index', compact('categories', 'tags'));
    }

    // お問い合わせ確認画面を表示する
    public function confirm(StoreContactRequest $request)
    {
        // バリデーション済みの入力データを取得する
        $validated = $request->validated();

        // 選択されたカテゴリ情報を取得する
        $category = Category::find($request->category_id);
        // 選択されたタグ情報を取得する
        $tags = Tag::find($request->tags);

        // 確認画面を表示し、入力内容・カテゴリ・タグを渡す
        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    // お問い合わせを保存する
    public function store(StoreContactRequest $request)
    {
        // お問い合わせ情報をcontactsテーブルに保存する
        $contact = Contact::create($request->all());
        // 選択されたタグを中間テーブル(contact_tag)に保存する
        $contact->tags()->attach($request->tag_ids);

        // サンクスページへリダイレクトする
        return redirect('/thanks');
    }

    // サンクスページを表示する
    public function thanks()
    {
        return view('contact.thanks');
    }

    // 検索条件に応じたお問い合わせをCSV出力する
    public function export(ExportContactRequest $request)
    {
        // カテゴリ情報も一緒に取得する(N＋1問題対策)
        $contacts = Contact::with('category');

        // キーワード検索が入力されている場合
        if ($request->filled('keyword')) {

            // 名前またはメールアドレスで部分一致検索する
            $contacts->where(function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('email', 'LIKE', "%{$request->keyword}%");
            });
        }

        // 性別が指定されている場合
        if ($request->gender) {
            // 性別で絞り込む
            $contacts->where('gender', $request->gender);
        }

        // カテゴリが指定されている場合
        if ($request->category_id) {
            // カテゴリで絞り込む
            $contacts->where('category_id', $request->category_id);
        }

        // 日付が指定されている場合
        if ($request->date) {
            // 作成日で絞り込む
            $contacts->whereDate('created_at', $request->date);
        }

        // 新しい順でお問い合わせデータを取得する
        $contacts = $contacts->latest()->get();

        // CSVヘッダーを定義する
        $csvHeader = [
            'ID',
            '氏名',
            '性別',
            'メールアドレス',
            '電話番号',
            '住所',
            '建物名',
            'お問い合わせの種類',
            'お問い合わせ内容',
            '作成日時',
        ];

        // CSV作成処理をコールバック変数として定義する
        $callback = function () use ($contacts, $csvHeader) {
            // CSV書き込み用のファイルを開く
            $file = fopen('php://output', 'w');

            // Excelで文字化けしないようBOMを出力する
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSVヘッダーを書き込む
            fputcsv($file, $csvHeader);

            // お問い合わせデータを1件ずつCSVへ書き込む
            foreach ($contacts as $contact) {
                // 1件分のデータをCSVへ書き込む
                fputcsv($file, [
                    $contact->id,
                    // 姓と名を結合して氏名として出力する
                    $contact->last_name.' '.$contact->first_name,
                    // 性別コードを文字列に変換して出力する
                    match ($contact->gender) {
                        1 => '男性',
                        2 => '女性',
                        3 => 'その他',
                    },
                    // メールアドレスを出力する
                    $contact->email,
                    // 電話番号を出力する
                    $contact->tel,
                    // 住所を出力する
                    $contact->address,
                    // 建物名を出力する
                    $contact->building,
                    // カテゴリ名を出力する
                    $contact->category?->content,
                    // お問い合わせ内容を出力する
                    $contact->detail,
                    // 作成日時を出力する
                    $contact->created_at,
                ]);
            }

            // ファイルを閉じる
            fclose($file);
        };

        // CSVファイルをダウンロードする
        return response()->streamDownload(
            $callback,
            // ダウンロード時のファイル名
            'contacts.csv'
        );
    }
}
