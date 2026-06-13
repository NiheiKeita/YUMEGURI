# Architecture

YUMEGURI の全体アーキテクチャと、レイヤ責務の方針。

## 1. システム構成

```
┌──────────┐     HTTP/Inertia     ┌────────────┐
│  Browser │ ◀──────────────────▶ │  nginx     │
└──────────┘                       └─────┬──────┘
                                         │ FastCGI
                                  ┌──────▼──────┐
                                  │  app (PHP)  │
                                  │  Laravel 12 │
                                  └──────┬──────┘
                                         │
                          ┌──────────────┼──────────────┐
                          ▼              ▼              ▼
                       ┌──────┐    ┌──────────┐    ┌─────────┐
                       │  DB  │    │ Storage  │    │External │
                       │MySQL │    │ (S3/loc) │    │API(Geo) │
                       └──────┘    └──────────┘    └─────────┘
```

`docker-compose.yml` で `app` / `nginx` / `db` / `swagger` の 4 コンテナを起動する。

## 2. ディレクトリ構成

```
.
├── docs/                          # 設計ドキュメント（本ディレクトリ）
├── document/db/db.dio             # DB 物理 ER 図（draw.io 形式）
├── docker/                        # Docker image 定義
├── swagger/                       # 静的 Swagger UI
└── src/                           # Laravel アプリケーション本体
    ├── app/
    │   ├── Console/Commands/      # artisan コマンド (sento:import 等)
    │   ├── Domain/Enum/           # ドメイン enum (UserRole, SentoStatus 等)
    │   ├── Http/
    │   │   ├── Controllers/{Web,Admin}/   # 入力受付・レスポンス組立のみ
    │   │   ├── Middleware/        # 認証・権限ガード
    │   │   ├── Requests/          # FormRequest によるバリデーション
    │   │   └── Resources/         # API/Inertia レスポンス整形
    │   ├── Models/                # Eloquent モデル
    │   ├── Policies/              # 認可ポリシー
    │   ├── Providers/             # DI バインディング
    │   └── Services/
    │       ├── Contracts/         # インターフェース（Geocoder 等）
    │       ├── Geocoding/         # Geocoding 実装
    │       ├── Proposal/          # 編集提案ユースケース
    │       ├── Review/            # 訪問記録ユースケース
    │       └── Sento/
    │           ├── Importer/      # スクレイプ → DB の取り込み
    │           ├── NearbySentoService.php
    │           └── SentoListService.php
    ├── resources/js/              # React + Inertia フロントエンド
    │   ├── Components/<Name>/index.tsx + index.stories.tsx
    │   ├── Pages/{Web,Admin}/<Page>/index.tsx + index.stories.tsx + hooks.ts
    │   ├── Layouts/               # レイアウトコンポーネント
    │   ├── hooks/                 # 汎用 hooks
    │   └── types/yumeguri.ts      # YUMEGURI 共通の TS 型
    ├── tests/{Unit,Feature}/      # PHPUnit テスト
    └── routes/web.php             # ルート定義（Inertia + 既存 Admin）
```

## 3. レイヤ責務

DDD 軽量導入の方針。以下の責務を明確に分離する。

| レイヤ | 場所 | 責務 |
|---|---|---|
| **Presentation (HTTP)** | `Http/Controllers`, `Http/Requests`, `Http/Resources` | リクエスト受信、入力検証、レスポンス整形。**ビジネスロジックを書かない**。1 メソッド 15 行以内が目安 |
| **Application (Service)** | `Services/<UseCase>Service.php` | ユースケースを表現する。コンストラクタインジェクションで依存を受け取る。`final class` を基本とする |
| **Domain** | `Domain/Enum`, `Domain/ValueObject` (将来), `Models` | ドメインルールを保持する。Enum は `label()` 等のドメインメソッドを持てる |
| **Infrastructure** | `Services/Geocoding`, 将来の `Repository/Eloquent*` | 外部 API・DB の具体実装。インターフェース (`Services/Contracts`) を経由して呼ぶ |

### 依存方向

```
Presentation → Application → Domain
                    ↓
              Infrastructure
                    ↓
            (External: DB, API)
```

- **Domain は何にも依存しない** ← これを最重視
- Application は Contracts 経由で Infrastructure を呼ぶ（DI）
- Eloquent Model は現状 Domain として扱うが、将来 Repository 化を検討

## 4. DI 設計

Laravel IoC コンテナを利用。`AppServiceProvider::register()` でバインディングを集中管理する。

```php
// 例: Geocoder インターフェースを Google 実装にバインド
$this->app->bind(Geocoder::class, function ($app) {
    return new GoogleGeocoder(new Client(), config('services.google.geocoding_key', ''));
});
```

**禁止事項**:
- `app(...)` / `resolve(...)` 等の Service Locator 使用 → 必ずコンストラクタ注入
- Service から Eloquent Model を直接操作するのは現状許容しているが、将来 Repository インターフェースに置き換える方針

## 5. スクレイパーの Strategy パターン

複数都県のスクレイパーを `tag` で束ね、コマンドから一括取得できる。

```php
// AppServiceProvider::register
$this->app->tag([
    TokyoScraper::class,
    KanagawaScraper::class,
    SaitamaScraper::class,
    ChibaScraper::class,
], 'sento.scrapers');

// Console から
$scrapers = $this->laravel->tagged('sento.scrapers');
$importer = new SentoImportService($scrapers, $geocoder);
```

新都県を追加する際は `PrefectureScraper` を実装し、tag に追加するだけ。詳細は [data-import.md](./data-import.md)。

## 6. フロントエンドの構成原則

| 種類 | 場所 | 命名 |
|---|---|---|
| ページ（Inertia ルート対応） | `Pages/{Admin,Web}/<PageName>/index.tsx` | URL と 1:1 |
| 再利用 UI コンポーネント | `Components/<ComponentName>/index.tsx` | PascalCase |
| レイアウト | `Layouts/<LayoutName>/index.tsx` | PascalCase |
| 特定ページ専用の子 | `Pages/.../<Page>/components/<Name>/index.tsx` | スコープ閉じ |

### ファイル構成（必須）

```
<Name>/
├── index.tsx           # 実装（React.memo 必須、named export）
├── index.stories.tsx   # Storybook + 画面仕様
└── hooks.ts            # 複雑なロジック / state 切り出し（任意）
```

- 1ファイル `Foo.tsx` のフラット形式は **新規禁止**（既存はレガシー扱い）
- Storybook stories は **全 Component/Page 必須**
- 画面ページの仕様は story の `parameters.docs.description.component` に記述する（[Storybook 画面仕様規約](#7-storybook-画面仕様規約) 参照）

### 状態管理・データ取得

- フォーム: Inertia の `useForm` を `hooks.ts` の `useXxx()` に閉じ込める
- ナビゲーション: `router.get/post` を使う（axios 直呼び禁止）
- グローバル state: 現状不要。必要になったら `Context` で限定領域に閉じる

## 7. Storybook 画面仕様規約

各 Page story の `Meta.parameters.docs.description.component` に以下のテンプレートで画面仕様を記述する:

```ts
const meta: Meta<typeof Foo> = {
  title: 'pages/Web/Foo',
  component: Foo,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `
**画面名**: 銭湯詳細
**URL**: \`/sentos/{id}\`
**アクセス**: 一般公開フェーズ後は guest 可、現状は member 以上
**主な機能**:
- 銭湯の基本情報（名称・住所・営業時間 等）を表示
- 全ユーザーの感想を最新 50 件まで表示
- 「記録する」「情報を直す」ボタンで遷移

**Controller**: \`Web\\\\SentoController::show\`
**データ**: \`SentoResource\` 経由で SentoDetail（reviews + photos eager-loaded）
        `,
      },
    },
  },
}
```

詳細テンプレートは [api.md](./api.md) の各エンドポイント定義と対応させる。
