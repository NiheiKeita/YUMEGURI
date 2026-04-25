# CLAUDE.md

このファイルは Claude Code が本リポジトリで作業する際に自動で読み込む指示書です。

## 📘 正は [AGENTS.md](./AGENTS.md)

リポジトリ固有のコーディング規約・ディレクトリ構成・テスト方針などは **[AGENTS.md](./AGENTS.md) が単一の正** です。
Codex や Cursor 等の他の AI アシスタントも同じファイルを参照します。

**作業開始時にまず [AGENTS.md](./AGENTS.md) を必ず読んでください。**

## Claude Code 固有メモ

### 利用できる Skill

- `.claude/skills/create-component/` — 新しい React コンポーネントをディレクトリ型（`index.tsx` + `index.stories.tsx`）で雛形生成する

### テスト実行のショートハンド

- 「全テスト実行」と言われたら以下を順に実行:
  1. `composer phpcs .` (src/)
  2. `vendor/bin/phpstan analyze` (src/)
  3. `vendor/bin/phpunit` (src/)
  4. `npm run lint` (src/)
  5. `npm run type-check` (src/)
  6. `npm run test-storybook` (src/)

### コミット時

- Conventional Commits 必須（`commitlint` が commit-msg フックで強制）
- 作業完了後にユーザーが明示的に依頼した時のみコミット
