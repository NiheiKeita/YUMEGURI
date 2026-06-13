# Data Import（スクレイピング）

各都県の銭湯組合公式サイトをスクレイピングして `sentos` テーブルに初期データを投入する。

## コマンド

```bash
# 全都県を一括取り込み
php artisan sento:import

# 特定都県のみ
php artisan sento:import --prefecture=tokyo
php artisan sento:import --prefecture=kanagawa
php artisan sento:import --prefecture=saitama
php artisan sento:import --prefecture=chiba
```

不正な値を渡すとエラー終了:
```
$ php artisan sento:import --prefecture=osaka
不正な --prefecture: osaka
```

実装: [`App\Console\Commands\ImportSentos`](../src/app/Console/Commands/ImportSentos.php)

## データソース

| 都県 | URL |
|---|---|
| 東京 | https://www.1010.or.jp/map/item |
| 神奈川 | https://k-o-i.jp |
| 埼玉 | 埼玉県浴場組合サイト |
| 千葉 | https://chiba1126sento.com |

各サイトから取得する情報:
- 銭湯名・ふりがな
- 住所
- アクセス（最寄り駅・徒歩分数）
- 営業時間・定休日
- 設備情報（シャンプー・ボディソープ備え付け 等）
- 画像 URL

緯度経度はサイト側で取れない場合が多いので、Google Geocoding API で住所から補完する。

## 設計（Strategy パターン）

### クラス図

```
                   ┌───────────────────────┐
                   │ <<interface>>          │
                   │ PrefectureScraper     │
                   │  +prefecture()         │
                   │  +scrape(): iterable   │
                   └─────────▲─────────────┘
                             │
              ┌──────────────┼──────────────┬──────────────┐
              │              │              │              │
       ┌──────┴──────┐ ┌─────┴──────┐ ┌─────┴──────┐ ┌─────┴──────┐
       │ TokyoScraper│ │KanagawaScr.│ │SaitamaScr. │ │ ChibaScr.  │
       └─────────────┘ └────────────┘ └────────────┘ └────────────┘

                  yields ▼ (one ScrapedSento per item)

                   ┌───────────────────────┐
                   │ ScrapedSento (DTO)     │
                   │  +name, address, ...   │
                   │  +toAttributes()       │
                   └─────────┬──────────────┘
                             │
              ┌──────────────┴──────────────┐
              │                             │
              ▼                             ▼
      ┌──────────────┐          ┌──────────────────┐
      │  Geocoder    │          │ SentoImportService│
      │  (interface) │◀─────────│  +execute()       │
      └──────────────┘  fills   └────────┬──────────┘
              ▲                          │
              │                          ▼
      ┌──────────────┐         ┌──────────────────┐
      │GoogleGeocoder│         │   Sento (Eloquent)│
      │   (impl)     │         │   upsert          │
      └──────────────┘         └──────────────────┘
```

### 主要クラス

| クラス | 責務 |
|---|---|
| [`PrefectureScraper`](../src/app/Services/Sento/Importer/Scrapers/PrefectureScraper.php) | 各都県スクレイパーが実装する interface |
| [`AbstractScraper`](../src/app/Services/Sento/Importer/Scrapers/AbstractScraper.php) | Guzzle クライアントの共通設定（User-Agent 等） |
| [`ScrapedSento`](../src/app/Services/Sento/Importer/ScrapedSento.php) | 抽出結果の中間表現 (DTO)。`toAttributes()` で Sento モデルへの代入用配列を返す |
| [`SentoImportService`](../src/app/Services/Sento/Importer/SentoImportService.php) | 全スクレイパーを iterate して upsert する |
| [`Geocoder`](../src/app/Services/Contracts/Geocoder.php) / [`GoogleGeocoder`](../src/app/Services/Geocoding/GoogleGeocoder.php) | 住所 → lat/lng |

### DI 配線

`AppServiceProvider::register` でスクレイパー4本を `sento.scrapers` タグで束ねる:

```php
$this->app->tag([
    TokyoScraper::class,
    KanagawaScraper::class,
    SaitamaScraper::class,
    ChibaScraper::class,
], 'sento.scrapers');
```

`ImportSentos::handle` で取得:
```php
$scrapers = $this->laravel->tagged('sento.scrapers');
```

新都県を追加する際は:
1. `PrefectureScraper` 実装クラスを作成
2. `Domain\Enum\Prefecture` に case を追加
3. `AppServiceProvider` の `tag()` 配列に追加

## 取り込みフロー

```
1. for each scraper (filtered by --prefecture):
2.   for each ScrapedSento yielded:
3.     既存 Sento を (prefecture, name, address) で検索
4.     既存があり is_manually_updated=true なら → SKIP
5.     既存に lat/lng なし or 新規 → Geocoder.geocode(address)
6.     DB transaction で create or update
7.   stats を集計
8. summary 出力（processed/created/updated/skipped）
```

実装: [`SentoImportService::execute`](../src/app/Services/Sento/Importer/SentoImportService.php)

## 手動編集の保護

YUMEGURI で最も重要な不変条件:

> **`Sento.is_manually_updated = true` のレコードは、再スクレイピングで上書きされない。**

これを踏むパターン:
- admin が `/sentos/{id}/edit` で直接編集 → save 時に `is_manually_updated=true` がセット
- member の編集提案を admin が `/admin/proposals/{id}/approve` で承認 → 同上

`SentoImportService` はこのフラグをチェックし、true のレコードは `skipped` として処理を飛ばす。Geocoder すら呼ばない（rate limit 節約）。

## エッジケース

| ケース | 動作 |
|---|---|
| Geocoder の API キー未設定 | `null` 返却 → lat/lng は null のまま保存 |
| Geocoding API がタイムアウト | catch して `null` 返却 → 同上 |
| スクレイピング途中で 1 件失敗 | 現状: try/catch なしで例外伝播。stats は途中まで記録 |
| 既存 Sento に lat/lng あり、新規 import | Geocoder は呼ばずスキップ（差分更新最小化） |

## 既知の制限

- 各 `Scraper::scrape()` は **現状空配列を返すスタブ**。実 DOM のパース実装は別 PR で行う
- レート制限・並列化は未実装。組合サイト側に配慮し sequential かつ低速で動かす設計
- 実行ログは `$this->info()` のみ。本番ではログファイルへ書き出す想定（Laravel `Log::channel('scraper')` を別 PR で）

## テスト

[`SentoImportServiceTest`](../src/tests/Unit/Services/Sento/Importer/SentoImportServiceTest.php) で以下をカバー:

- 新規作成 + Geocoding が呼ばれる
- 手動編集レコードは skip され、Geocoder も呼ばれない
- 既存に lat/lng あれば Geocoder は呼ばれない
- `--prefecture` で絞り込んだとき、対象外の Scraper が呼ばれない

[`ImportSentosCommandTest`](../src/tests/Feature/Console/ImportSentosCommandTest.php) でコマンド経由の end-to-end 動作も検証。
