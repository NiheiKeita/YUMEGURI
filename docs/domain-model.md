# Domain Model

YUMEGURI の中核ドメインモデル。Eloquent モデル + Domain Enum で表現する。

## ER 図

```
┌──────────────┐         ┌──────────────────────┐
│    User      │1       *│   sento_reviews      │* 1┌──────────────┐
│              ├────────▶│                      │◀──┤    Sento     │
│ +role(enum)  │         │ +visited_at          │   │              │
│ +invited_by──┼─┐       │ +rating (1-5)        │   │ +prefecture  │
└──────────────┘ │       │ +has_sauna           │   │ +lat,lng     │
        ▲        │       │ +has_mizuburo        │   │ +has_shampoo │
        │        │       │ +bath_types (json)   │   │ +has_soap    │
        └────────┘       │ +want_revisit        │   │ +status(enum)│
       inviter           └──────────────────────┘   │ +is_manually │
                                  │                 │  _updated    │
                                  │ 1               └──────┬───────┘
                                  ▼                        │ 1
                         ┌──────────────────┐              │
                         │  sento_photos    │* 1           │
                         │ +path            │──────────────┘
                         │ +category(enum)  │
                         │ +caption         │
                         └──────────────────┘

                        ┌──────────────────────────┐
                        │ sento_edit_proposals     │
                        │ +changes (json)          │
                        │ +reason                  │
                        │ +status(pending/         │
                        │   approved/rejected)     │
                        │ proposed_by ──┐          │
                        │ reviewed_by ──┼─▶ User   │
                        │ sento_id ─────┼─▶ Sento  │
                        └──────────────────────────┘
```

物理 ER 図の draw.io 版は [`document/db/db.dio`](../document/db/db.dio)。

## エンティティ詳細

### User

サイトを利用する人。サイトオーナー（admin）と招待された友達（member）の2種類のロールを持つ。

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint PK | |
| name | string | 表示名 |
| email | string unique | |
| password | string | hashed |
| role | enum(`admin`, `member`) | YUMEGURI ドメインのロール |
| invited_by | FK users.id null | 招待者。一般公開後は null も許容 |
| tel | string null | 既存テンプレ由来 |
| password_token | string null | 既存テンプレのパスワード設定 token |
| email_verified_at | timestamp null | |
| created_at / updated_at / deleted_at | timestamp | SoftDeletes |

**関連**:
- `inviter()` → `User|null` (belongsTo self via `invited_by`)
- `sentoReviews()` → `HasMany<SentoReview>`
- `sentoPhotos()` → `HasMany<SentoPhoto>`
- `sentoEditProposals()` → `HasMany<SentoEditProposal>` (proposed_by)

**ドメインメソッド**:
- `isAdmin(): bool` — UserRole::Admin かどうか
- `promoteToAdmin(): void` — admin に昇格させる（明示メソッド経由のみ）
- `demoteToMember(): void` — member に降格

**セキュリティ**: `role` は意図的に `$fillable` から外している（mass assignment による権限昇格を防ぐ）。変更は `promoteToAdmin()` / `demoteToMember()` 経由でのみ。

> 既存テンプレートの `AdminUser` モデル（`/admin/login`）とは別系統。AdminUser は将来削除候補だが、現状は YUMEGURI 機能を妨げないよう温存。

### Sento（銭湯マスター）

スクレイピングで初期投入され、admin の手動編集や承認済み提案で上書きされる。

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint PK | |
| name / name_kana | string | 銭湯名・ふりがな |
| prefecture / city | string | 「東京都」「中央区」等 |
| address | string | |
| lat / lng | decimal(10,7) null | Geocoding API で補完 |
| phone / hours / closed_days | string null | |
| price | int null | 円 |
| nearest_station / walk_minutes | string / int null | |
| has_shampoo / has_soap | bool | 備え付けの有無 |
| status | enum | `open` / `closed_temp` / `closed_perm` |
| info_updated_at | date | 情報の最終更新日 |
| is_manually_updated | bool | **再スクレイピング時の上書き保護フラグ** |
| created_at / updated_at / deleted_at | timestamp | SoftDeletes |

**インデックス**:
- `(prefecture, city)` — 一覧フィルタ
- `status` — operating scope
- `(lat, lng)` — Nearby 検索の事前絞り込み

**関連**:
- `reviews()` → `HasMany<SentoReview>`
- `photos()` → `HasMany<SentoPhoto>`
- `editProposals()` → `HasMany<SentoEditProposal>`

**Query Scope**:
- `operating()` — `status != closed_perm` のみ。一覧/マップで使う
- `inPrefecture(string)` — 都道府県絞り込み

**手動編集保護**: `is_manually_updated=true` のレコードは `sento:import` 再実行時に **skip** される（[data-import.md](./data-import.md) 参照）。

### SentoReview（訪問記録）

ユーザーの訪問体験。1ユーザー × 1銭湯 × 1訪問日でユニーク（同日上書き）。

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint PK | |
| sento_id | FK sentos.id | cascade delete |
| user_id | FK users.id | cascade delete |
| visited_at | date | 訪問日。同じ日付は上書き |
| rating | tinyint | ★1〜5（総合評価） |
| body | text null | フリーテキスト |
| has_sauna / sauna_temp | bool / int null | サウナ有無・温度℃ |
| has_mizuburo / mizuburo_temp | bool / int null | 水風呂有無・温度℃ |
| bath_types | json null | `["炭酸泉","薬湯","シルク"]` |
| want_revisit | bool | また行きたいか |
| crowding | tinyint null | 混雑度 1〜5 |
| best_time | string null | おすすめ時間帯 |
| created_at / updated_at / deleted_at | timestamp | SoftDeletes |

**インデックス**:
- `(user_id, visited_at)` — プロフィールページのソート
- `(sento_id, rating)` — 詳細ページの並び替え

**Service**: [`ReviewUpsertService`](../src/app/Services/Review/ReviewUpsertService.php)

### SentoPhoto

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint PK | |
| sento_id / user_id | FK | cascade delete |
| review_id | FK null | レビューと紐付ける場合 |
| path | string | Storage 内のパス |
| category | enum | `exterior` / `interior` / `locker` / `other` |
| caption | string null | |
| created_at / updated_at / deleted_at | timestamp | SoftDeletes |

**Computed**: `url` (accessor) — `Storage::url($path)` で公開 URL を返す。Resource で自動展開される。

### SentoEditProposal（編集提案）

member が銭湯情報の変更を提案し、admin が承認/却下する。

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint PK | |
| sento_id | FK | cascade delete |
| proposed_by | FK users.id | 提案者 |
| changes | json | `{ "hours": "14:00-23:30" }` 等のキー→新値 |
| reason | string null | 提案理由・根拠 |
| status | enum | `pending` / `approved` / `rejected` |
| reviewed_by | FK users.id null | 承認者（admin） |
| reviewed_at | timestamp null | |
| created_at / updated_at | timestamp | SoftDelete なし |

**Query Scope**: `pending()` — 未処理のみ

**承認フロー** ([`ProposalReviewService`](../src/app/Services/Proposal/ProposalReviewService.php)):
1. `Pending` でない場合は `RuntimeException`（二重承認防止）
2. `DB::transaction` 内で `lockForUpdate` で sento を取得
3. `changes` を fill して `is_manually_updated=true` を立てる
4. `info_updated_at` を today に更新
5. 提案を `Approved` に遷移、`reviewed_by` / `reviewed_at` を記録

## Domain Enum

### UserRole

```php
case Admin = 'admin';
case Member = 'member';

public function isAdmin(): bool;
```

### SentoStatus

```php
case Open = 'open';        // 営業中
case ClosedTemp = 'closed_temp';  // 休業中
case ClosedPerm = 'closed_perm';  // 廃業

public function label(): string;  // ローカライズ済み日本語
```

### PhotoCategory

```php
case Exterior = 'exterior';  // 外観
case Interior = 'interior';  // 内部
case Locker = 'locker';      // ロッカー
case Other = 'other';        // その他
```

### ProposalStatus

```php
case Pending = 'pending';
case Approved = 'approved';
case Rejected = 'rejected';
```

### Prefecture

```php
case Tokyo = 'tokyo';
case Kanagawa = 'kanagawa';
case Saitama = 'saitama';
case Chiba = 'chiba';

public function jaName(): string;  // 「東京都」等
```

`sento:import --prefecture` で受け取る値もこれと一致させる。

## 不変条件・整合性ルール

| 不変条件 | 保証手段 |
|---|---|
| 1ユーザー × 1銭湯 × 1訪問日 で重複しない | `ReviewUpsertService` が `whereDate` で既存検索 → upsert |
| 編集提案は `Pending` からのみ承認/却下できる | `ProposalReviewService::approve/reject` で status チェック |
| 手動編集された Sento はスクレイピングで上書きされない | `Sento::is_manually_updated` をチェックし `skipped` |
| ロール昇格は明示的なメソッド経由のみ | `User::$fillable` から `role` 除外 + `promoteToAdmin()` |
| 銭湯詳細編集は admin のみ | `admin` middleware + `SentoUpdateRequest::authorize` の二重 |

詳細な権限マトリクスは [permissions.md](./permissions.md)。
