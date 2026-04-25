# YUMEGURI ドキュメント

東京・神奈川・埼玉・千葉の銭湯を記録・公開する個人サイト「YUMEGURI（湯巡り）」の設計ドキュメント。

> 開発者向けの規約・コーディング標準は [AGENTS.md](../AGENTS.md) を参照。

## 目次

| ドキュメント | 内容 |
|---|---|
| [architecture.md](./architecture.md) | 全体アーキテクチャ、ディレクトリ構成、レイヤ責務 |
| [domain-model.md](./domain-model.md) | ER 図、Eloquent モデル、Domain Enum、Value Object |
| [permissions.md](./permissions.md) | ロール定義、機能別権限マトリクス、Policy 一覧 |
| [api.md](./api.md) | Inertia ルート定義、入出力契約、リソース整形 |
| [data-import.md](./data-import.md) | スクレイピング設計、`sento:import` コマンド、手動編集の保護 |
| [operations.md](./operations.md) | ローカル開発、テスト、カバレッジ、CI |
| [../document/db/db.dio](../document/db/db.dio) | DB 物理 ER 図（draw.io） |

## サイトコンセプト

- **対象エリア**: 東京・神奈川・埼玉・千葉
- **デザイン**: 雑誌風・和モダン・スマホファースト
- **公開フェーズ**:
  - **現在**: 招待制（friend のみ閲覧・記録可能）
  - **将来**: 一般公開（guest 含む全員が閲覧可能、記録は member 以上のみ）

## 技術スタック

| 層 | 技術 |
|---|---|
| バックエンド | Laravel 12 (PHP 8.4) |
| フロントエンド | Inertia.js + React 18 + TypeScript 5 |
| DB | MySQL（本番）/ SQLite in-memory（テスト） |
| 画像ストレージ | Laravel Storage (S3 or local) |
| マップ | Google Maps API（予定）/ Geocoding は Google |
| スクレイピング | Guzzle + DOM crawler（実装は別 PR） |
| ホスティング | Docker Compose（app / nginx / db / swagger） |

## 主要なフロー

1. **データ初期投入**: [`php artisan sento:import`](./data-import.md) で各都県組合サイトをスクレイピング → `sentos` テーブルに upsert
2. **訪問記録**: ユーザが [`/sentos/{id}/review`](./api.md#訪問記録) から記録 → `sento_reviews` に upsert（同一日付は上書き）
3. **編集提案**: member が [`/sentos/{id}/propose`](./api.md#編集提案) で修正案を送信 → admin が `/admin/proposals` で承認/却下
4. **承認時**: `sentos.is_manually_updated=true` でマークされ、再スクレイピング時に上書きされない

## 目標品質

| 領域 | 目標 |
|---|---|
| Service カバレッジ | 80%+（[operations.md](./operations.md#test-coverage) 参照） |
| Component カバレッジ | 70%+ |
| PHPStan | level 7 で warning 0 |
| ESLint | warning 0 |
| Storybook | 全 Component / Page に story + 画面仕様 |

## 拡張候補（未確定）

- スタンプラリー的な達成バッジ
- 都道府県・区ごとの制覇率表示
- 銭湯ごとのタイル絵・ペンキ絵記録
- OGP 対応のシェア機能
