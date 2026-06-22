# COACHTECH お問い合わせフォーム

お問い合わせフォームの機能を実装したLaravelプロジェクトです。一般ユーザーがお問い合わせを送信でき、管理者がログイン後にその内容を確認・管理します。

## 作成者

菅野　まりえ

## 使用技術

- PHP 8.2
- Laravel 10.x
- MySQL 8.0
- Nginx
- Docker/Docker Compose/Laravel Sail
- Laravel Fortify(認証)
- phpMyAdmin

## ER図

```mermaid
erDiagram
    users {
        bigint_unsained id PK
        varchar_255 name
        varchar_255  email UK
        timestamp email_verified_at
        varchar_255 password
        varchar_100 remenber_token
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        varchar_255 content
        timestamp created_at
        timestamp updated_at
    }

    contacts {
        bigint_unsigned id PK
        bigint_unsigned category_id FK
        varchar_255 first_name
        varchar_255 last_name
        tinyint gender
        varchar_255 email
        varchar_11 tel
        varchar_255 address
        varchar_255 building
        varchar_120 detail
        timestamp created_at
        timestamp updated_at
    }

    tags {
        bigint_unsigned id PK
        varchar_50 name UK
        timestamp created_at
        timestamp updated_at
    }

    contact_tag {
        bigint_unsigned id PK
        bigint_unsigned contact_id FK UNIQUE(contact_id,tag_id)
        bigint_unsigned tag_id FK
        timestamp created_at
        timestamp updated_at
    }

    categories ||--o{ contacts : "has many"
    contacts ||--o{ contact_tag : "has many"
    tags ||--o{ contact_tag : "has many"
```

## 開発環境URL

http://localhost

## 動作環境

- Docker
- Docker Compose

※Windowsの場合はWSL2の利用を推奨します。

## 環境構築手順

1. **リポジトリをクローン**

    ```bash
    git clone https://github.com/mariekanno/contact-form-app.git
    ```

2. **.envファイルの準備**

    .env.exampleをコピーして.envを作成します。

   　cp .env.example .env

   .envファイル内の以下のDB接続情報を確認・設定します。.envファイル内の以下のDB接続情報を確認・設定します。.env.exampleのデフォルト値はSail向けではないため、以下のように変更してください。

   

2. **.envファイルの準備**

    ```bash
   cp .env.example .env

3. **Composer依存パッケージのインストール**

    ```bash
    composer install

4. **Laravel Sailの起動**

    ```bash
    ./vendor/bin/sail up -d

5. **アプリケーションキーの生成**

    ```bash
    ./vendor/bin/sail artisan key:generate

6. **データベースのマイグレーションと初期データ投入**

    ```bash
    ./vendor/bin/sail artisan migrate:fresh --seed

7. **フロントエンドのビルド**

    ```bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run build

8. **アプリケーションへのアクセス**

    http://localhost

## テスト用アカウント

メールアドレス:test@example.com
パスワード:password

## テスト実行

    ```bash
    ./vendor/bin/sail test

## 機能一覧
- お問い合わせフォーム
- お問い合わせ確認
- お問い合わせ送信
- 管理者登録
- ログイン/ログアウト
- お問い合わせ一覧表示
- お問い合わせ検索
- お問い合わせ詳細表示
- お問い合わせ削除
- タグCRUD
- CSV出力
- 公開API

## APIエンドポイント一覧

お問い合わせ情報を取得・登録・更新・削除するREST APIを実装しています。

| HTTPメソッド | URI | 概要 |
|---|---|---|
| GET | /api/v1/contacts | お問い合わせ一覧取得 |
| GET | /api/v1/contacts/{id} | お問い合わせ詳細取得 |
| POST | /api/v1/contacts | お問い合わせ作成 |
| PUT | /api/v1/contacts/{id}  | お問い合わせ更新 |
| DELETE | /api/v1/contacts/{id}  | お問い合わせ削除 |
