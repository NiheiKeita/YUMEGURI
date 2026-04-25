# API / Inertia ルート仕様

YUMEGURI は基本的に Inertia.js でサーバーサイドレンダリングしているため、JSON API ではなく **Inertia Page Response** を返す。各エンドポイントの入出力契約をまとめる。

> 既存テンプレ由来の `swagger/openapi.yaml` は `/posts` 系の例示で、YUMEGURI とは無関係。本文書を YUMEGURI の API 仕様の正とする。

## 入出力の共通仕様

- Content-Type: `text/html` (Inertia visit) / `application/json` (XHR)
- リクエスト: フォーム POST / JSON
- レスポンス: Inertia component 名 + props
- バリデーション失敗時: 422 + `errors` オブジェクト
- 認証必須ルートで未認証時: 302 リダイレクト → `/login`
- 認可失敗時: 403

## Top（雑誌風トップ）

```
GET /
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Top` |
| アクセス | guest 可（招待制中は要 auth） |
| Controller | [`Web\TopController::index`](../src/app/Http/Controllers/Web/TopController.php) |

**Props**:
```ts
{
  latestReviews: SentoReview[]  // 最新訪問記録 12件、user/sento/photos eager-loaded
}
```

## 銭湯一覧

```
GET /sentos
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Sento/Index` |
| アクセス | guest 可（招待制中は要 auth） |
| Controller | [`Web\SentoController::index`](../src/app/Http/Controllers/Web/SentoController.php) |
| Service | [`SentoListService::paginate`](../src/app/Services/Sento/SentoListService.php) |

**Query**:
| キー | 型 | 説明 |
|---|---|---|
| `prefecture` | string | 「東京都」等。完全一致 |
| `city` | string | |
| `visited` | `visited` \| `unvisited` \| `all` | viewer 視点の訪問状態（要 auth） |
| `has_sauna` | bool | レビューにサウナ有無の記録がある |
| `has_mizuburo` | bool | 同上、水風呂 |
| `has_shampoo` / `has_soap` | bool | 銭湯マスタの設備 |
| `bath_types[]` | string[] | OR セマンティクス。いずれかを含むレビュー |
| `status` | `open` \| `closed_temp` \| `all` | デフォルト: 全営業中 |
| `sort` | `rating` \| `visited_at` \| `want_revisit` | デフォルト: `rating`（avg desc） |

**Props**:
```ts
{
  sentos: Paginated<SentoSummary>  // 1ページ20件
  filters: SentoIndexFilters       // 適用中のフィルタ
}
```

## 銭湯詳細

```
GET /sentos/{sento}
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Sento/Show` |
| アクセス | guest 可（招待制中は要 auth） |
| Controller | [`Web\SentoController::show`](../src/app/Http/Controllers/Web/SentoController.php) |

**Props**:
```ts
{
  sento: SentoDetail  // reviews 最新50件 + photos 最新30件 eager-loaded
}
```

## 訪問記録

```
GET  /sentos/{sento}/review     # フォーム表示（既存記録の編集も兼ねる）
POST /sentos/{sento}/review     # 作成・更新
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Sento/Review` |
| アクセス | 要 auth |
| Controller | [`Web\SentoReviewController`](../src/app/Http/Controllers/Web/SentoReviewController.php) |
| Service | [`ReviewUpsertService::execute`](../src/app/Services/Review/ReviewUpsertService.php) |
| Validation | [`SentoReviewStoreRequest`](../src/app/Http/Requests/SentoReviewStoreRequest.php) |

**GET Props**:
```ts
{
  sento: SentoDetail
  review: SentoReview | null  // 既存記録（最新の visited_at）
}
```

**POST Body**:
```ts
{
  visited_at: string    // YYYY-MM-DD（必須）
  rating: number        // 1〜5（必須）
  body?: string         // 5000 文字まで
  has_sauna?: bool
  sauna_temp?: number   // 30〜150
  has_mizuburo?: bool
  mizuburo_temp?: number  // 0〜40
  bath_types?: string[]   // 各 30 文字まで
  want_revisit?: bool
  crowding?: number     // 1〜5
  best_time?: string    // 50 文字まで
}
```

**動作**: `(user_id, sento_id, visited_at)` で一意。同日の再投稿は上書き。

**成功時**: 302 → `/sentos/{id}` (with flash `status: '記録を保存しました'`)

## 編集提案

```
GET  /sentos/{sento}/propose    # フォーム表示
POST /sentos/{sento}/propose    # 提案送信
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Sento/Propose` |
| アクセス | 要 auth |
| Controller | [`Web\SentoEditProposalController`](../src/app/Http/Controllers/Web/SentoEditProposalController.php) |
| Service | [`ProposalSubmitService::execute`](../src/app/Services/Proposal/ProposalSubmitService.php) |
| Validation | [`SentoEditProposalStoreRequest`](../src/app/Http/Requests/SentoEditProposalStoreRequest.php) |

**POST Body**:
```ts
{
  changes: {
    name?: string
    address?: string
    phone?: string
    hours?: string
    closed_days?: string
    price?: number
    nearest_station?: string
    walk_minutes?: number
    has_shampoo?: bool
    has_soap?: bool
  }
  reason?: string  // 500 文字まで
}
```

**セキュリティ**: `changes` 内のキーは allowlist。`lat` / `lng` / `prefecture` / `status` 等の機密項目は提案できず、許可外キーは黙って捨てる。

## 銭湯直接編集（admin 専用）

```
GET   /sentos/{sento}/edit
PATCH /sentos/{sento}
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Sento/Edit` |
| アクセス | 要 auth + admin |
| Controller | [`Web\SentoController::edit/update`](../src/app/Http/Controllers/Web/SentoController.php) |
| Validation | [`SentoUpdateRequest`](../src/app/Http/Requests/SentoUpdateRequest.php) |

**PATCH 動作**: 更新後、`is_manually_updated=true` と `info_updated_at=today` を強制セット。これによりスクレイプ再実行時の上書き保護を有効化。

## マップ

```
GET /map
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Map` |
| アクセス | guest 可（招待制中は要 auth） |
| Controller | [`Web\MapController::index`](../src/app/Http/Controllers/Web/MapController.php) |

**Query**:
| キー | 型 | 説明 |
|---|---|---|
| `prefecture` | string | 都道府県絞り込み |

**Props**:
```ts
{
  pins: Pin[]  // 最大2000件、operating かつ lat/lng あり
}
```

> ピン数が増えたら別 API endpoint に切り出して fetch する設計に変更予定。

## 近くの未訪問銭湯

```
GET /nearby
```

| 項目 | 内容 |
|---|---|
| Component | `Web/Nearby` |
| アクセス | 要 auth |
| Controller | [`Web\NearbyController::index`](../src/app/Http/Controllers/Web/NearbyController.php) |
| Service | [`NearbySentoService::findUnvisited`](../src/app/Services/Sento/NearbySentoService.php) |

**Query**:
| キー | 型 | 説明 |
|---|---|---|
| `lat` | number | -90〜90、デフォルト 35.6812（東京駅） |
| `lng` | number | -180〜180、デフォルト 139.7671 |

**Props**:
```ts
{
  origin: { lat: number; lng: number }
  sentos: SentoSummary[]  // 距離 km 順 20件、未訪問のみ
}
```

距離計算は SQL での Haversine 近似式（小規模想定）。

## ユーザープロフィール

```
GET /users/{user}            # show
GET /users/{user}/map        # 訪問済み銭湯のピンマップ
GET /users/{user}/nearby     # 近くの未訪問銭湯（自分のみ表示）
GET /users/{user}/photos     # 投稿写真一覧
```

| 項目 | 内容 |
|---|---|
| Component | `Web/User/{Show,Map,Nearby,Photos}` |
| アクセス | 要 auth |
| Controller | [`Web\UserProfileController`](../src/app/Http/Controllers/Web/UserProfileController.php) |

**Props (Show)**:
```ts
{
  profile: { id: number; name: string }
  reviews: SentoReview[]  // 最新60件
  stats: {
    visited_count: number
    prefecture_count: number  // 訪問した都道府県数（distinct）
    city_count: number
  }
}
```

**Nearby 特有の挙動**: `/users/{他人}/nearby` は **404** を返す（仕様: 自分のページのみ表示）。

## 提案管理（admin）

```
GET  /admin/proposals
POST /admin/proposals/{proposal}/approve
POST /admin/proposals/{proposal}/reject
```

| 項目 | 内容 |
|---|---|
| Component | `Admin/Proposals/Index` |
| アクセス | 要 auth + admin |
| Controller | [`Admin\ProposalController`](../src/app/Http/Controllers/Admin/ProposalController.php) |
| Service | [`ProposalReviewService::approve/reject`](../src/app/Services/Proposal/ProposalReviewService.php) |

**承認時の副作用**:
1. `Sento` に `changes` を fill して save
2. `Sento.is_manually_updated = true`
3. `Sento.info_updated_at = today`
4. `SentoEditProposal.status = approved`、`reviewed_by/reviewed_at` 記録

これらは `DB::transaction` + `lockForUpdate` で同時承認を防ぐ。

**`Pending` 以外の提案を承認/却下しようとすると 500（`RuntimeException`）**。

## レスポンス整形（Resource）

すべての Inertia Props は API Resource 経由で整形される:

| Resource | 対応モデル |
|---|---|
| [`SentoResource`](../src/app/Http/Resources/SentoResource.php) | `Sento` |
| [`SentoReviewResource`](../src/app/Http/Resources/SentoReviewResource.php) | `SentoReview` |
| [`SentoPhotoResource`](../src/app/Http/Resources/SentoPhotoResource.php) | `SentoPhoto` |
| [`SentoEditProposalResource`](../src/app/Http/Resources/SentoEditProposalResource.php) | `SentoEditProposal` |

これに対応する TypeScript 型は [`resources/js/types/yumeguri.ts`](../src/resources/js/types/yumeguri.ts) で定義。

## エラーハンドリング

| ケース | レスポンス |
|---|---|
| バリデーション失敗 | 302 + flash errors（Inertia が `useForm.errors` に展開） |
| 未認証で `auth` route | 302 → `/login` |
| `admin` middleware 失敗 | 403 + Inertia error page |
| `User::Sento` route binding miss | 404 |
| `RuntimeException`（提案重複承認 等） | 500（フラッシュメッセージで処理する設計を後続 PR で検討） |
