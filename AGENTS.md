# AGENTS.md

このファイルは **AI コーディングアシスタント（Claude Code / OpenAI Codex / Cursor 等）共通の正** です。
Claude Code は [CLAUDE.md](./CLAUDE.md) から、Codex は本ファイルを直接参照します。

---

## 1. プロジェクト概要

- **構成**: Laravel 12 (PHP 8.4) + Inertia.js + React 18 + TypeScript 5 のモノレポ
- **目的**: Laravel と React を Inertia でつなぐモダン SPA のサンプル & ベースリポジトリ
- **ホスティング**: Docker Compose（app / nginx / db / swagger）

## 2. ディレクトリ構成（重要）

```
.
├── docker/               # Docker image 定義 (app / nginx / db / swagger)
├── document/             # 設計ドキュメント
├── src/                  # Laravel アプリケーション本体
│   ├── app/              # Laravel バックエンド
│   │   ├── Http/Controllers/{Admin,Web}/   # 画面・API コントローラ
│   │   ├── Models/                          # Eloquent モデル
│   │   ├── Services/                        # ビジネスロジック（将来 DDD 化）
│   │   │   └── Entity/                      # ドメインエンティティ
│   │   ├── Jobs/ / Mail/ / Exceptions/
│   │   └── Providers/                       # サービスプロバイダ（DI 登録場所）
│   ├── resources/js/     # React + Inertia フロントエンド
│   │   ├── Components/   # 再利用 UI コンポーネント
│   │   ├── Pages/        # 画面（Inertia ルートから呼ばれる）
│   │   │   ├── Admin/
│   │   │   └── Web/
│   │   ├── Layouts/      # レイアウトコンポーネント
│   │   ├── hooks/        # 汎用 hooks
│   │   └── types/        # 型定義
│   ├── msw/              # MSW ハンドラ
│   └── tests/            # PHPUnit テスト
└── swagger/              # Swagger 定義
```

---

## 3. React / Frontend ルール

### 3.1 コンポーネントの置き場

| 種類 | 場所 | 理由 |
|---|---|---|
| ページ（Inertia ルートに対応） | `resources/js/Pages/{Admin\|Web}/<PageName>/` | URL と 1:1 |
| 再利用可能 UI | `resources/js/Components/<ComponentName>/` | 画面横断で使う |
| レイアウト | `resources/js/Layouts/<LayoutName>/` | ページを包む |
| 特定ページ専用の子コンポーネント | `Pages/<Admin\|Web>/<Page>/components/<Name>/` | スコープを閉じる |

### 3.2 ファイル構成（**これを守る**）

各コンポーネント/ページは **ディレクトリ型** で作る:

```
<Name>/
├── index.tsx           # 実装
├── index.stories.tsx   # Storybook（必須）
└── hooks.ts            # ロジック（必要に応じて）
```

- `index.tsx` **1ファイルだけ**のフラット形式（例: `InputError.tsx`）は **レガシー**。**新規作成は必ずディレクトリ型**
- Storybook stories は **全コンポーネント/ページ必須**（見た目確認 + Vitest テスト対象）
- 複雑な state/副作用は `hooks.ts` に分離し、`index.tsx` は JSX に集中

### 3.3 命名規則

- コンポーネント名: **PascalCase** (`ModalView`, `PlanSelector`)
- hooks 名: `use` プレフィックス + PascalCase (`useLogin`, `useUserRegister`)
- ディレクトリ名: **PascalCase**
- TypeScript: 型は `type Props = {...}` 形式、interface は原則使わない
- export: `export const Foo = ...` の **named export** を基本に（`export default` は Inertia のページ用)

### 3.4 props の型

```tsx
type Props = {
    isOpen: boolean
    onClose: () => void
    title: string
    children: React.ReactNode
}

export const ModalView = React.memo<Props>(function ModalView({ ... }) { ... })
```

- HTML 属性を継承したい場合: `React.ButtonHTMLAttributes<HTMLButtonElement> & { ... }`
- **全ての再利用コンポーネントは `React.memo` でラップ**（既存コードの統一規約）

### 3.5 データ取得・フォーム

- Inertia の `useForm` / `router.post` を使う（axios を勝手に使わない）
- フォームロジックは **`hooks.ts` の `useXxx()` に閉じ込める**
- 戻り値オブジェクトでは `data, setData, processing, errors, submit` をそのまま返す

### 3.6 スタイリング

- **Tailwind CSS のみ**。CSS-in-JS や styled-components は使わない
- Tailwind クラスの順序は `eslint-plugin-tailwindcss` に任せる（`npm run lint:fix`）
- 複雑な条件クラスは `clsx` のような util を **将来的に** 導入検討（現状は文字列結合）

### 3.7 Storybook

- 全コンポーネントに `index.stories.tsx` を配置
- `Meta` の `title` はディレクトリと対応させる:
  - `Components/ModalView` → `'components/ModalView'`
  - `Pages/Admin/UserList` → `'pages/admin/UserList'`
- `tags: ['autodocs']` を付ける（自動ドキュメント生成）
- `play` 関数で interaction テストを書ける（Vitest で実行される）
- **どうしても Vitest に乗らないもの**（例: react-slick 依存の Carousel）は `tags: ['!test']` で除外

---

## 4. Laravel / Backend ルール

### 4.1 現状と方針

**現状**: Controllers + Services + Models の3層。DI は Laravel の IoC コンテナ標準機能を利用。
**今後の方針**: **DDD 軽量導入 + DI 明示化** を目指す。

### 4.2 レイヤ構成（目標）

```
app/
├── Http/
│   ├── Controllers/{Admin,Web}/   # HTTP 層（入力検証・レスポンス組み立てのみ）
│   ├── Requests/                   # FormRequest によるバリデーション
│   └── Resources/                  # API Resource（整形レイヤ）
├── Services/                       # アプリケーション層（ユースケース単位）
│   ├── Contracts/                  # インターフェース定義（DI 用）
│   └── <UseCase>Service.php
├── Domain/                         # ドメイン層（新設予定）
│   ├── Entity/                     # ドメインエンティティ（不変条件を持つ）
│   ├── ValueObject/                # 値オブジェクト
│   └── Repository/                 # リポジトリインターフェース
├── Infrastructure/                 # インフラ層（新設予定）
│   └── Repository/                 # Eloquent を使った実装
├── Models/                         # Eloquent モデル（Infrastructure の一部扱い）
└── Providers/                      # DI バインディング
```

### 4.3 DI の書き方（重要）

- **新規 Service は必ずコンストラクタインジェクション**:

  ```php
  final class UserRegisterService
  {
      public function __construct(
          private readonly UserRepository $userRepository,
          private readonly MailService $mailService,
      ) {}
  }
  ```

- **具象型ではなくインターフェースを注入**（テストでモック可能に）:

  ```php
  // Services/Contracts/UserRepository.php（インターフェース）
  interface UserRepository { public function find(int $id): ?User; }

  // Infrastructure/Repository/EloquentUserRepository.php（実装）
  final class EloquentUserRepository implements UserRepository { ... }

  // Providers/AppServiceProvider.php（バインド）
  $this->app->bind(UserRepository::class, EloquentUserRepository::class);
  ```

- **Service Container の facade (`app()`, `resolve()`) 利用は禁止**（DI の利点を潰す）

### 4.4 Controller のルール

- **Controller は "薄く"** 保つ。ビジネスロジックは必ず Service に委譲
- FormRequest でバリデーション、Resource で整形、Service で処理
- 目安: **1 メソッド 15 行以内**

```php
public function store(RegisterRequest $request): RedirectResponse
{
    $this->userRegisterService->execute(
        RegisterInput::fromRequest($request)
    );
    return redirect()->route('admin.user.index');
}
```

### 4.5 命名規則

- クラス: **PascalCase** + 役割サフィックス (`UserRegisterService`, `UserRepository`)
- メソッド: **camelCase**、ユースケースは動詞始まり (`execute`, `register`, `findById`)
- Final class を基本に（継承は必要な時のみ）
- `readonly` プロパティを積極利用（不変性）

### 4.6 Eloquent の使い方

- Model 直呼びは Repository 層に閉じ込める（Service / Controller から Model を直接扱わない）
- 複雑な where 条件は **Query Scope** または **Query Object** に切り出す

---

## 5. テスト方針（必読）

### 5.1 要求レベル

| レイヤ | 必須度 | カバレッジ目標 |
|---|---|---|
| Laravel Service | **必須** | **80%+** |
| Laravel Controller (Feature test) | 推奨 | - |
| React Component (Storybook test) | **必須** | **70%+** |
| React hooks | 推奨 | - |

### 5.2 PHP テスト

- **PHPUnit** を使う（pestphp は現状導入していない）
- テストファイルは `src/tests/{Unit,Feature}/` に対応する階層で配置
- Service のテストは **依存をモックして単体テスト化**（Mockery 利用可）
- Feature test は **DB 込みで実行**（SQLite in-memory）

### 5.3 React テスト

- **Vitest + Storybook Test addon** を使う
- 各 story が 1 テストケースとして自動実行される
- ロジックのみのテスト（hooks など）は `*.test.ts` で書く（別途 vitest `unit` project を将来追加予定）

### 5.4 PR 作成前チェック

新しい機能を作ったら:

- [ ] Laravel: Service / UseCase の単体テストを追加した
- [ ] React: 新規コンポーネントに `index.stories.tsx` がある
- [ ] `composer phpcs .` が通る
- [ ] `vendor/bin/phpstan analyze` が通る
- [ ] `vendor/bin/phpunit` が通る
- [ ] `npm run lint` が通る
- [ ] `npm run type-check` が通る
- [ ] `npm run test-storybook` が通る

### 5.5 カバレッジレポート

- **PR にはカバレッジコメントが自動投稿**される（Codecov 経由）
- React と PHP で別々のカバレッジ値を表示
- カバレッジが **5% 以上下がる変更は原則 merge しない**

---

## 6. 実装順序（推奨ワークフロー）

新機能追加時の理想的な順序:

1. **ドメインモデル** を Entity / ValueObject で表現（新規導入時）
2. **Service のインターフェース** を書く（入出力の型を決める）
3. **Service の単体テスト** を先に書く（TDD 気味に）
4. **Service の実装** でテストを通す
5. **Controller + FormRequest** を追加
6. **Inertia Page + hooks** を実装
7. **Storybook** を書く（UI の独立確認）
8. **Feature test** で一気通貫の動作確認

---

## 7. Git / コミットルール

- ブランチ名: `feat/xxx`, `fix/xxx`, `chore/xxx`
- コミットメッセージは **Conventional Commits 必須**（`commitlint` で強制）
  - `feat: add user register service`
  - `fix: correct login redirect target`
  - `chore: bump storybook to 9.x`
  - `test: add coverage for UserRegisterService`
  - `docs: update AGENTS.md`
- **PR は小さく**（理想: 300 行未満の diff）
- Dependabot の minor/patch は自動マージ、major は手動レビュー

---

## 8. よく使うコマンド（`src/` 内で）

```bash
# PHP
composer phpcs .                  # スタイル
composer phpcs-fix                # 自動修正
vendor/bin/phpstan analyze        # 静的解析
vendor/bin/phpunit                # テスト
vendor/bin/phpunit --coverage-clover=coverage.xml  # カバレッジ

# Frontend
npm run lint                      # ESLint
npm run type-check                # tsc --noEmit
npm run build                     # 本番ビルド
npm run storybook                 # Storybook 起動
npm run test-storybook            # Vitest (1回)
npm run test-storybook:watch      # Vitest watch mode
npm run test-storybook -- --coverage  # カバレッジ付き
```

Docker ルートから:

```bash
make up     # コンテナ起動
make bash   # app コンテナに入る
make stop   # 停止
make ps     # 状態確認
```

---

## 9. やらないこと / NG リスト

- ❌ `any` 型（明示的な理由がある時のみ、コメント必須）
- ❌ Model を Controller / Service から直接操作（Repository 経由にしたい）
- ❌ `app()` / `resolve()` での Service 取得（コンストラクタ注入）
- ❌ axios での API 呼び出し（Inertia の `useForm` / `router` を使う）
- ❌ CSS-in-JS（Tailwind のみ）
- ❌ `console.log` を本番コードに残す
- ❌ Storybook なしのコンポーネント追加
- ❌ テストなしの Service 追加
- ❌ major バージョンアップの自動マージ
- ❌ 大きすぎる PR（500 行超は分割を検討）

---

## 10. AI アシスタントへの指示（このファイルを読んだ上での行動方針）

1. **新しいファイルを作る前に、既存の近いファイルを必ず1つ以上読む**（スタイル踏襲のため）
2. **ディレクトリ型構成（`index.tsx` + `index.stories.tsx`）を絶対に守る**
3. **新規 Service を作る時は DDD 方針（4.2 のレイヤ構成）に沿って配置**。既存コードに引きずられない
4. テストを書かずに「実装完了」と宣言しない
5. 不明点は推測せず、ユーザーに質問する（特にドメイン知識が絡む部分）
6. コメントは **WHY** を書く。**WHAT** は書かない（識別子で表現する）
