<?php

Kirby::plugin('tearoom1/kirby-block-reference', [
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
                'pattern' => 'getAllPages',
                'action' => function () {
                    return array_keys(site()->index()->toArray());
                }
            ],
            [
                'pattern' => 'blocks',
                'action' => function () {
                    $pageId = get('page');
                    $page = site()->index()->findBy('id', $pageId);
                    if (!$page || !$page->layout()) {
                        return [];
                    }
                    return $page->layout()->toLayouts()->toBlocks()->toArray();
                }
            ]
        ]
    ],
]);
