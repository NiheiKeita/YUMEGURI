# Laravel × Inertia × React Example

[![unitTest](https://github.com/NiheiKeita/laravel-inertia-react-example/actions/workflows/unitTest.yml/badge.svg)](https://github.com/NiheiKeita/laravel-inertia-react-example/actions/workflows/unitTest.yml)
[![CodeQL](https://github.com/NiheiKeita/laravel-inertia-react-example/actions/workflows/codeql.yml/badge.svg)](https://github.com/NiheiKeita/laravel-inertia-react-example/actions/workflows/codeql.yml)
[![codecov](https://codecov.io/gh/NiheiKeita/laravel-inertia-react-example/branch/main/graph/badge.svg)](https://codecov.io/gh/NiheiKeita/laravel-inertia-react-example)
[![Dependabot](https://img.shields.io/badge/Dependabot-enabled-025E8C?logo=dependabot)](./.github/dependabot.yml)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![React](https://img.shields.io/badge/React-18-61DAFB?logo=react&logoColor=white)](https://react.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript&logoColor=white)](https://www.typescriptlang.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](./LICENSE)

Laravel + Inertia.js + React + TypeScript のモノレポ構成サンプルです。Storybook / MSW / PHPStan / PHPCS / ESLint / Playwright など一通り揃っています。

## 🏗 構成

| レイヤ | 技術 |
|---|---|
| Backend | Laravel 12 / PHP 8.4 |
| Frontend | React 18 / TypeScript 5 / Inertia.js |
| ビルド | Vite |
| UI 開発 | Storybook 8 / Tailwind CSS |
| API モック | MSW |
| テスト | PHPUnit / Storybook Test Runner (Playwright) |
| 静的解析 | PHPStan (Larastan) / PHPCS / ESLint |
| API ドキュメント | Swagger (zircote/swagger-php) |
| Infra | Docker Compose (app / nginx / db / swagger) |

## 🚀 セットアップ

```bash
# 1. 起動
make up

# 2. コンテナに入る
make bash

# 3. Laravel 初期設定
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate

# 4. フロントエンド
npm ci
npm run dev
```

| URL | 内容 |
|---|---|
| http://localhost:8081 | アプリ本体 |
| http://localhost:8082 | Swagger UI |
| http://localhost:6006 | Storybook (`npm run storybook`) |

## 🧪 開発コマンド（`src/` 内）

```bash
# PHP
composer phpcs .                 # コードスタイルチェック
composer phpcs-fix               # 自動修正
vendor/bin/phpstan analyze       # 静的解析
vendor/bin/phpunit               # テスト

# Frontend
npm run lint                     # ESLint
npm run type-check               # tsc --noEmit
npm run build                    # 本番ビルド
npm run storybook                # Storybook 起動
npm run test-storybook           # Storybook テスト
```

## 🔧 Makefile ショートカット

| コマンド | 内容 |
|---|---|
| `make up` | `docker compose up -d` |
| `make bash` | app コンテナに入る |
| `make stop` | コンテナ停止 |
| `make ps` | 状態確認 |

## 🤖 自動化

- **Dependabot**: 毎週月曜 9:00 (JST) に npm / Composer / Docker / Actions を自動チェック → minor/patch は自動マージ
- **CI (unitTest)**: PR と main push で 3並列ジョブ（backend / frontend / storybook）を実行
- **Codecov**: PR に PHP / React それぞれのカバレッジコメントを自動投稿（目標: backend 80%+ / frontend 70%+）
- **CodeQL**: JavaScript / TypeScript のセキュリティスキャン
- **actionlint**: GitHub Actions の lint
- **commitlint**: Conventional Commits 準拠チェック（commit-msg フックで強制）

## 📝 コミットルール

**Conventional Commits 必須**。husky の commit-msg フックで検証されるため、準拠しないコミットは失敗します。

| prefix | 用途 | 例 |
|---|---|---|
| `feat` | 新機能 | `feat: add user register page` |
| `fix` | バグ修正 | `fix: correct login redirect target` |
| `chore` | 雑務・依存更新 | `chore: bump storybook to 9.x` |
| `test` | テスト追加・修正 | `test: add UserRegisterService coverage` |
| `docs` | ドキュメント | `docs: update AGENTS.md` |
| `refactor` | 振る舞いを変えない整理 | `refactor: extract form logic to hooks` |
| `style` | フォーマットのみ | `style: apply phpcs-fix` |
| `ci` | CI 設定 | `ci: add codecov upload` |

詳細なプロジェクトルール（React/Laravel の構成、テスト方針、DI/DDD の方向性）は **[AGENTS.md](./AGENTS.md)** を参照。

## 🤖 AI アシスタント連携

- **[AGENTS.md](./AGENTS.md)** — Claude Code / Codex / Cursor 共通のプロジェクトルール（単一の正）
- **[CLAUDE.md](./CLAUDE.md)** — Claude Code 用（AGENTS.md を参照）
- **[.claude/skills/create-component/](./.claude/skills/create-component/)** — Claude Code の Skill: 規約に沿ったコンポーネント雛形生成

## 📁 ディレクトリ構成

```
.
├── docker/           # Docker イメージ定義 (app / nginx / db / swagger)
├── document/         # 設計ドキュメント
├── src/              # Laravel アプリケーション本体
│   ├── app/          # Laravel バックエンド
│   ├── resources/js/ # React + Inertia フロントエンド
│   ├── msw/          # MSW モック
│   └── tests/        # PHPUnit テスト
└── swagger/          # Swagger 定義
```

## 📝 License

MIT
