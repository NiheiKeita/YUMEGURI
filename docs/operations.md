# Operations

ローカル開発・テスト・カバレッジ・CI 周りの運用情報。

## ローカル環境セットアップ

### Docker（推奨）

```bash
make up         # コンテナ起動
make bash       # app コンテナに入る
make stop       # 停止
make ps         # 状態確認
```

`make bash` 内で:
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=YumeguriSeeder
```

### macOS ネイティブ（軽量パス）

PHP 8.4 + composer がローカルにあれば、Docker 無しでも `vendor/` を入れて検証できる:

```bash
cd src
composer install --prefer-dist
cp .env.example .env
php artisan key:generate
mkdir -p database && touch database/database.sqlite

# .env を編集して以下に変更:
#   DB_CONNECTION=sqlite
#   DB_DATABASE=/absolute/path/to/database/database.sqlite

php artisan migrate
php artisan db:seed --class=YumeguriSeeder
```

## よく使うコマンド

`src/` 内で:

```bash
# PHP
composer phpcs .                               # コードスタイル
composer phpcs-fix                             # 自動修正
vendor/bin/phpstan analyze --memory-limit=2G   # 静的解析
vendor/bin/phpunit                             # テスト
vendor/bin/phpunit --coverage-text             # カバレッジ要 pcov/xdebug

# Frontend
npm run lint           # ESLint
npm run type-check     # tsc --noEmit
npm run build          # 本番ビルド
npm run storybook      # Storybook 起動 (http://localhost:6006)
npm run test-storybook # Vitest（Storybook test addon 経由）
npm run test-storybook -- --coverage  # カバレッジ
```

## テスト戦略

| レイヤ | 必須度 | カバレッジ目標 |
|---|---|---|
| Laravel Service | **必須** | **80%+** |
| Laravel Controller (Feature test) | 推奨 | — |
| Laravel Policy | 推奨 | — |
| React Component (Storybook test) | **必須** | **70%+** |
| React hooks | 推奨 | — |

PHP テストは `tests/{Unit,Feature}/` 配下に対応する階層で配置。実装ファイルと同じパス構造を保つ。

## SQLite in-memory（テスト用）

`phpunit.xml` で `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` を有効化済み。Feature test も DB 込みで走る。

留意点:
- `whereJsonContains` は SQLite でも動くが MySQL とは内部実装が異なる。差異が顕在化したら issue 化
- Eloquent の `date` cast は SQLite で `Y-m-d H:i:s` 形式で保存されることがあり、`updateOrCreate` の同値比較で問題になる → [`ReviewUpsertService`](../src/app/Services/Review/ReviewUpsertService.php) は `whereDate` ベースに書き直し済み

## カバレッジ計測

### PHP（pcov 経由）

CI（[unitTest.yml](../.github/workflows/unitTest.yml)）では shivammathur/setup-php@v2 で pcov 自動設定。ローカルは:

```bash
brew tap shivammathur/extensions
brew install shivammathur/extensions/pcov@8.4

php -d 'pcov.enabled=1' -d 'pcov.directory=app' \
    vendor/bin/phpunit --coverage-text
```

`phpunit.xml` の `<source>` で coverage 集計対象を制限している:
- 含む: `app/`
- 除外: pre-existing テンプレート（AdminUserController 等）、Laravel framework ブートストラップ、スクレイパー本体（実 DOM 依存で別 PR 担当）

### Test coverage

直近実測（2026-04 時点）:

```
Tests:    97 passed, 268 assertions
Lines:    82.71% (555/671)
Methods:  68.70%
Classes:  66.04%
```

YUMEGURI 関連の Service / Policy / Controller はほぼ 100%。Coverage が 80% を切ったら CI で警告（[codecov.yml](../codecov.yml)）。

### Frontend（Vitest）

```bash
npm run test-storybook -- --coverage
```

Storybook stories が自動的にテストケースとして扱われる。`tags: ['autodocs']` を全 Component/Page に付与済み。

ロジックのみのテスト（hooks 等）は `*.test.ts` で書く（別途 vitest `unit` project を将来追加予定）。

## CI

### unitTest.yml

| job | 内容 |
|---|---|
| `backend` | PHPStan / PHPCS / PHPUnit + Codecov + PR コメント（カバレッジ） |
| `frontend` | ESLint / TypeScript |
| `storybook` | Vitest（Playwright Chromium） + Codecov |

`permissions: pull-requests: write` を宣言済み。Codecov / カバレッジ PR コメントが forked PR でも動く。

### actionlint.yml

`reviewdog/action-actionlint@v1` で `.github/workflows/**` を lint。`fail_level: error` で警告と失敗を区別。`permissions: pull-requests: write` で reviewdog の PR コメント投稿を許可。

### dependabot

`.github/dependabot.yml` の minor/patch は自動マージ、major は手動レビュー。

## マイグレーション

```bash
# 適用
php artisan migrate

# 一度ロールバックして再適用（開発時）
php artisan migrate:fresh --seed
```

YUMEGURI のマイグレーションファイル:
- `2026_04_25_100001_extend_users_for_yumeguri.php` — users.role / users.invited_by
- `2026_04_25_100002_create_sentos_table.php`
- `2026_04_25_100003_create_sento_reviews_table.php`
- `2026_04_25_100004_create_sento_photos_table.php`
- `2026_04_25_100005_create_sento_edit_proposals_table.php`

> 既存テンプレートのマイグレーション（users / admin_users / failed_jobs / jobs）はそのまま残している。

## Seeder

`YumeguriSeeder` で以下を生成（開発用ダミーデータ）:
- admin 1名（`owner@yumeguri.test`）
- friends 3名（`invited_by` = admin）
- 銭湯 20件
- 各ユーザーに約 5件のレビュー + ランダムな写真

```bash
php artisan db:seed --class=YumeguriSeeder
```

`DatabaseSeeder` には組み込んでいない（テスト時のノイズを避ける）。

## デプロイ（未確定）

- 静的アセット: `npm run build` で `public/build/` に出力 → S3 + CloudFront 配信予定
- 画像ストレージ: `Storage::url()` 経由で S3 公開バケット URL を返す
- Geocoding API キー: `.env` の `GOOGLE_GEOCODING_KEY` に設定（`config/services.php` 経由）
- Cron: `sento:import` を週次バッチで動かす予定（手動編集レコードは保護されるので safe）

## トラブルシューティング

### 「Vite manifest not found」が PHPUnit で出る
→ `tests/TestCase::setUp()` で `withoutVite()` を呼ぶ（既に対応済み）。

### 「Route [login] not defined」
→ `App\Http\Middleware\Authenticate::redirectTo` が `route('user.login')` を返すように修正済み。`route('login')` を期待しないこと。

### Inertia testing で「page component file does not exist」
→ ディレクトリ型コンポーネント（`Web/Top/index.tsx`）対応のため `config/inertia.php` で `testing.ensure_pages_exist = false` に設定済み。

### コミット時に commitlint で弾かれる
→ Conventional Commits 必須。`feat:` / `fix:` / `chore:` / `test:` / `docs:` / `refactor:` 等のプレフィックスを付ける。
