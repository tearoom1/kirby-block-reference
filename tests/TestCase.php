<?php

namespace TearoomOne\BlockReference\Tests;

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/block-reference-tests/' . uniqid();
        Dir::make($this->root . '/content');
    }

    protected function tearDown(): void
    {
        // no App::destroy(): it would also drop the plugin, which is only registered once
        Dir::remove($this->root);
    }

    /**
     * A site with a source page (blocks and layout field), a page that
     * references its blocks, a draft and an admin and an editor
     */
    protected function app(array $options = [], array $sourceExtra = []): App
    {
        $layout = [[
            'id' => 'layout-1',
            'columns' => [[
                'id' => 'column-1',
                'width' => '1/1',
                'blocks' => [
                    ['id' => 'in-layout', 'type' => 'text', 'content' => ['text' => 'Inside a layout']],
                ],
            ]],
        ]];

        return new App([
            'roots' => [
                'index'   => $this->root,
                'content' => $this->root . '/content',
                'media'   => $this->root . '/media',
                'cache'   => $this->root . '/cache',
                'accounts' => $this->root . '/accounts',
                // outside the test root: Kirby writes the session after tearDown
                'sessions' => sys_get_temp_dir() . '/block-reference-tests-sessions',
            ],
            'urls' => ['index' => 'https://example.test'],
            'options' => $options,
            'blueprints' => [
                'pages/default' => [
                    'fields' => [
                        'text' => ['type' => 'blocks'],
                        'grid' => ['type' => 'layout'],
                        'title2' => ['type' => 'text'],
                    ],
                ],
            ],
            'roles' => [
                ['name' => 'admin'],
                ['name' => 'editor'],
            ],
            'users' => [
                ['email' => 'admin@example.test', 'role' => 'admin'],
                ['email' => 'editor@example.test', 'role' => 'editor'],
            ],
            'site' => [
                'children' => [
                    [
                        'slug' => 'source',
                        'children' => [[
                            'slug' => 'child',
                            'content' => [
                                'title' => 'Child',
                                'text' => json_encode([
                                    ['id' => 'child-heading', 'type' => 'heading', 'content' => ['text' => 'From a subpage', 'level' => 'h3']],
                                ]),
                            ],
                        ]],
                        'content' => array_merge([
                            'title' => 'Source',
                            'text' => json_encode([
                                ['id' => 'heading-1', 'type' => 'heading', 'content' => ['text' => 'Shared heading', 'level' => 'h2']],
                                ['id' => 'ref-in-source', 'type' => 'reference', 'content' => ['targetPage' => 'other', 'targetBlock' => 'x']],
                            ]),
                            'grid' => json_encode($layout),
                        ], $sourceExtra),
                    ],
                    [
                        'slug' => 'target',
                        'content' => [
                            'title' => 'Target',
                            'text' => json_encode([
                                ['id' => 'ref-heading', 'type' => 'reference', 'content' => ['targetPage' => 'source', 'targetBlock' => 'heading-1']],
                                ['id' => 'ref-layout', 'type' => 'reference', 'content' => ['targetPage' => 'source', 'targetBlock' => 'in-layout']],
                                ['id' => 'ref-missing-page', 'type' => 'reference', 'content' => ['targetPage' => 'gone', 'targetBlock' => 'heading-1']],
                                ['id' => 'ref-missing-block', 'type' => 'reference', 'content' => ['targetPage' => 'source', 'targetBlock' => 'gone']],
                                ['id' => 'ref-child', 'type' => 'reference', 'content' => ['targetPage' => 'source/child', 'targetBlock' => 'child-heading']],
                                ['id' => 'ref-draft', 'type' => 'reference', 'content' => ['targetPage' => 'draft', 'targetBlock' => 'draft-heading']],
                            ]),
                        ],
                    ],
                ],
                'drafts' => [
                    [
                        'slug' => 'draft',
                        'content' => [
                            'title' => 'Draft',
                            'text' => json_encode([
                                ['id' => 'draft-heading', 'type' => 'heading', 'content' => ['text' => 'Not published yet', 'level' => 'h2']],
                            ]),
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Run a plugin API route like Kirby's API does (the route's own access checks still apply)
     */
    protected function api(App $kirby, string $pattern): mixed
    {
        foreach ($kirby->extensions('api')['routes'] ?? [] as $route) {
            if ($route['pattern'] === $pattern) {
                return $route['action']->call($kirby->api());
            }
        }
        $this->fail("No API route {$pattern}");
    }

    /**
     * Rendered HTML of a reference block on the target page
     */
    protected function render(App $kirby, string $blockId): string
    {
        $block = $kirby->page('target')->text()->toBlocks()->findBy('id', $blockId);
        return trim($block->toHtml());
    }
}
