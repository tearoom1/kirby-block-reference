<?php

load([
    'TearoomOne\\BlockReference\\BlockReference' => 'src/BlockReference.php',
], __DIR__);

use TearoomOne\BlockReference\BlockReference;

// Global helpers of earlier versions, kept for code that calls them.
// They are skipped if another plugin already uses the name.
if (!function_exists('getBlocksFromPage')) {
    /** @deprecated Use TearoomOne\BlockReference\BlockReference::blocksFromPage() */
    function getBlocksFromPage(\Kirby\Cms\Page $page): array
    {
        return BlockReference::blocksFromPage($page);
    }
}

if (!function_exists('blockReferenceCanAccess')) {
    /** @deprecated Use TearoomOne\BlockReference\BlockReference::canAccess() */
    function blockReferenceCanAccess(): bool
    {
        return BlockReference::canAccess();
    }
}

if (!function_exists('blockReferenceCanReadPage')) {
    /** @deprecated Use TearoomOne\BlockReference\BlockReference::canReadPage() */
    function blockReferenceCanReadPage(\Kirby\Cms\Page $page): bool
    {
        return BlockReference::canReadPage($page);
    }
}

Kirby::plugin('tearoom1/kirby-block-reference', [
    'options' => [
        'allowedRoles' => [],
        'renderDrafts' => false, // Render blocks of draft pages for visitors too (before 1.1 they always were)
    ],
    'pageMethods' => [
        'blockReferenceCanRead' => function (): bool {
            return BlockReference::canAccess() && BlockReference::canReadPage($this);
        },
    ],
    'blueprints' => [
        'blocks/reference' => __DIR__ . '/blueprints/blocks/reference.yml'
    ],
    'snippets' => [
        'blocks/reference' => __DIR__ . '/snippets/blocks/reference.php'
    ],
    'fields' => [
        'blockReference' => [
            'extends' => 'select'
        ]
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'block-reference/pages',
                'auth' => true,
                'action' => fn () => BlockReference::pageIds(),
            ],
            [
                'pattern' => 'block-reference/blocks',
                'auth' => true,
                'action' => fn () => BlockReference::blocksForPicker(get('page')),
            ],
            // Names of earlier versions, kept for custom code until the next major version
            [
                'pattern' => 'getAllPages',
                'auth' => true,
                'action' => fn () => BlockReference::pageIds(),
            ],
            [
                'pattern' => 'blocks',
                'auth' => true,
                'action' => fn () => BlockReference::blocksForPicker(get('page')),
            ],
        ]
    ],
]);
