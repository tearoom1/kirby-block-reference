## [2.0.0](https://github.com/tearoom1/kirby-block-reference/compare/v1.1.0...v2.0.0) (2026-11-25)


### ⚠ BREAKING CHANGES

* the API routes getAllPages and blocks are removed. Custom
code must call block-reference/pages and block-reference/blocks instead.
Version 2 requires Kirby 6; blueprints and content need no changes.

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>
* requires Kirby 6 and PHP 8.3 or newer.

Co-Authored-By: Claude <noreply@anthropic.com>

### Features

* Kirby 6 support ([ef08005](https://github.com/tearoom1/kirby-block-reference/commit/ef08005644a18a45105e227cee398f83054b909f))
* remove the API route names of version 1 ([dc6d88a](https://github.com/tearoom1/kirby-block-reference/commit/dc6d88abef3a2a58fa62a652eb3b967b0a78619a))


### Bug Fixes

* clear block options on page change and retry a failed fetch ([ea2b250](https://github.com/tearoom1/kirby-block-reference/commit/ea2b2502f01ea49f40a9951e3a98928ba24c0b80))
* keep block choices current across languages and saves ([ada4cdd](https://github.com/tearoom1/kirby-block-reference/commit/ada4cdd5367e447fd8db81068cc4f46e2aa377e2))
* show block previews in the picker without HTML tags ([a8dddb6](https://github.com/tearoom1/kirby-block-reference/commit/a8dddb6ac8dc6ee8aff0babfc3a1a78004722694))

## [1.1.0](https://github.com/tearoom1/kirby-block-reference/compare/v1.0.9...v1.1.0) (2026-10-21)


### Features

* namespaced API, faster rendering and no draft blocks for visitors ([0181d45](https://github.com/tearoom1/kirby-block-reference/commit/0181d4542e233e2924c2cb9135372425f623657d))

## [1.0.9](https://github.com/tearoom1/kirby-block-reference/compare/v1.0.8...v1.0.9) (2026-06-10)


### Bug Fixes

* restrict block reference access ([193a783](https://github.com/tearoom1/kirby-block-reference/commit/193a783e2be849515baac3e1a0755acd7af66cdb))

## [1.0.8](https://github.com/tearoom1/kirby-block-reference/compare/v1.0.7...v1.0.8) (2026-06-10)


### Bug Fixes

* require panel auth on api routes and guard null target page ([6a7aabb](https://github.com/tearoom1/kirby-block-reference/commit/6a7aabbe5e95feabe3d7b8b416c3ce4724a0cfe9))

## [1.0.7](https://github.com/tearoom1/kirby-block-reference/compare/v1.0.6...v1.0.7) (2025-08-02)


### Bug Fixes

* reverted to use class loading in index.php ([8a7eee1](https://github.com/tearoom1/kirby-block-reference/commit/8a7eee11c45ed406171d32106a9472da98796aca))

