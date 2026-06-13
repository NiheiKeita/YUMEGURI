# Permissions

YUMEGURI の認可設計。

## ロール定義

| ロール | 説明 | 認証経路 |
|---|---|---|
| `guest` | 未ログイン | — |
| `member` | 招待された友達 | `/login` (User guard) |
| `admin` | サイトオーナー（自分） | `/login` （User guard、`role=admin`） |

> 既存テンプレートの `AdminUser` モデルと `/admin/login` は別系統。サイト管理用の予備経路として残しているが、YUMEGURI ドメインのロール判定には関与しない。

## フェーズ別の閲覧範囲

| フェーズ | 状態 | 閲覧 | 記録 |
|---|---|---|---|
| **現在** | 招待制 | member 以上 | member 以上 |
| **将来** | 一般公開 | guest 含む全員 | member 以上 |

> 一般公開時は `routes/web.php` の閲覧系ルート（`/`, `/map`, `/sentos`, `/sentos/{id}`, `/users/{id}` 等）から `auth` middleware を外すことで切り替え可能な設計にしている。

## 機能別権限マトリクス

| 機能 | guest | member | admin |
|---|:-:|:-:|:-:|
| 銭湯一覧・詳細閲覧 | ✅(公開後) | ✅ | ✅ |
| マップ閲覧 | ✅(公開後) | ✅ | ✅ |
| 他ユーザーの記録閲覧 | ✅(公開後) | ✅ | ✅ |
| ユーザープロフィール閲覧 | ✅(公開後) | ✅ | ✅ |
| ユーザーマップ閲覧 | ✅(公開後) | ✅ | ✅ |
| 自分の訪問記録 | ❌ | ✅ | ✅ |
| 写真投稿 | ❌ | ✅ | ✅ |
| 銭湯情報の編集提案 | ❌ | ✅ | ✅ |
| 編集提案の承認・却下 | ❌ | ❌ | ✅ |
| 銭湯情報の直接編集 | ❌ | ❌ | ✅ |
| `/users/{自分}/nearby` 表示 | ❌ | 自分のみ | 自分のみ |

## ルート別ガード

| ルート | Middleware | 補足 |
|---|---|---|
| `GET /` | （閲覧系） | 招待制中は `auth` 適用検討 |
| `GET /map` | （閲覧系） | 同上 |
| `GET /sentos` | （閲覧系） | 同上 |
| `GET /sentos/{id}` | （閲覧系） | 同上 |
| `GET/POST /sentos/{id}/review` | `auth` | 訪問記録 |
| `GET/POST /sentos/{id}/propose` | `auth` | 編集提案 |
| `GET /sentos/{id}/edit` | `auth` + `admin` | 直接編集フォーム |
| `PATCH /sentos/{id}` | `auth` + `admin` | 直接更新 |
| `GET /nearby` | `auth` | 近くの未訪問銭湯 |
| `GET /users/{id}` | `auth` | プロフィール |
| `GET /users/{id}/map` | `auth` | ユーザーマップ |
| `GET /users/{id}/nearby` | `auth` + 自分判定 | Controller で 404 |
| `GET /users/{id}/photos` | `auth` | 写真一覧 |
| `GET /admin/proposals` | `auth` + `admin` | 提案管理 |
| `POST /admin/proposals/{id}/approve` | `auth` + `admin` | 承認 |
| `POST /admin/proposals/{id}/reject` | `auth` + `admin` | 却下 |

`admin` middleware = [`App\Http\Middleware\EnsureAdmin`](../src/app/Http/Middleware/EnsureAdmin.php)。`User` インスタンス かつ `isAdmin()` を満たさない場合は 403 を返す。

## Policy 一覧

Eloquent の `authorize()` / `Gate` から呼ばれる Policy。

### [SentoPolicy](../src/app/Policies/SentoPolicy.php)

| メソッド | 認可 |
|---|---|
| `update(User, Sento)` | admin のみ |
| `propose(User, Sento)` | 全 member |

### [SentoReviewPolicy](../src/app/Policies/SentoReviewPolicy.php)

| メソッド | 認可 |
|---|---|
| `create(User)` | 全 member |
| `update(User, SentoReview)` | レビュー所有者 または admin |
| `delete(User, SentoReview)` | レビュー所有者 または admin |

### [SentoPhotoPolicy](../src/app/Policies/SentoPhotoPolicy.php)

| メソッド | 認可 |
|---|---|
| `delete(User, SentoPhoto)` | 写真投稿者 または admin |

### [SentoEditProposalPolicy](../src/app/Policies/SentoEditProposalPolicy.php)

| メソッド | 認可 |
|---|---|
| `create(User)` | 全 member |
| `review(User, SentoEditProposal)` | admin のみ |
| `viewAny(User)` | admin のみ |

## Gate

`AuthServiceProvider::boot` で定義:

```php
Gate::define('admin', fn ($user) => $user?->isAdmin() === true);
```

主にビュー側の条件分岐や Blade 互換のチェックに使う。Controller 側は middleware/Policy を優先。

## 「自分のページのみ」判定

`/users/{id}/nearby` は仕様上 **自分のページのみ表示** とする。これは Policy ではなく Controller 内で行う:

```php
// UserProfileController::nearby
$viewer = $request->user();
abort_unless($viewer instanceof User && $viewer->id === $user->id, 404);
```

理由:
- リソース所有者判定は Policy より if 1行のほうが意図が明示的
- 404 を返すことで「このユーザーはあなたではない」とは漏らさない（Policy だと 403 になる）

## テスト

各 Policy は `tests/Unit/Policies/` 配下で単体テスト済み。Controller レベルの認可は `tests/Feature/Web/SentoFlowTest.php` 等の Feature test で end-to-end で検証している。
